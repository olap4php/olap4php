<?php
/**
 * olap4php
 * 
 * LICENSE
 * 
 * Licensed to SeeWind Design Corp. under one or more 
 * contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  SeeWind Design licenses 
 * this file to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @category   olap4php
 * @copyright  See NOTICE file
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2
 */
namespace OLAP4PHP\Provider\XMLA;

// Interfaces
use OLAP4PHP\OLAP\IOLAPStatement;
use OLAP4PHP\OLAP\IOLAPConnection;

// Classes / Objects
use OLAP4PHP\OLAP\OLAPException;

use OLAP4PHP\Common\Logger;
use SeeWind\Utils\DateTime;


/**
 * @brief XMLAStatement
 */
class XMLAStatement implements IOLAPStatement
{
   private $logger;
   
   /**
    * Flag to indicate if we have performance logging enabled
    *  
    * @var boolean
    */
   private $logPerformance;
   
   ///! XMLA Connection Object
   private $con;

   ///! OLAP Cell Set
   private $cellSet;

   ///! MDX Query Statement
   private $statement; 

   public function __construct( IOLAPConnection $xmlaConnection )
   {
      if ( empty( $xmlaConnection ) || !( $xmlaConnection instanceof XMLAConnection ) ) throw new OLAPException ( "XMLAConnection cannot be NULL" );
      $this->con = $xmlaConnection;
      $this->logger = $xmlaConnection->getLogger();
      $this->logPerformance = $xmlaConnection->getLogPerformance();
   }

  /**
   * @brief Executes an MDX Query via SOAP XMLA
   *
   * @param string $mdx - The MDX Query String to execute
   * @return XMLACellSet An XMLA Cell Set, which implements ICellSet
   */
   public function executeOlapQuery ( $mdx )
   {        
      $logPerformance = $this->logPerformance;
      
      if( $this->logger )
      {
         if( $logPerformance ) $startTime = DateTime::microtimeFloat();
         
         $this->logger->debug( __CLASS__, '[MDX QUERY]' . PHP_EOL . $mdx );
      }
      
      $this->statement = $mdx;

      $catalog = $this->con->getCatalog();
      $dataSourceInfo = $this->con->getDataSourceInfo();

      $queryXML = "
         <Execute xmlns=\"urn:schemas-microsoft-com:xml-analysis\">
            <Command>
               <Statement>
                  <![CDATA[
                     $mdx
                  ]]>
               </Statement>
            </Command>
            <Properties>
               <PropertyList>
                  <Catalog>$catalog</Catalog>
                  <DataSourceInfo>$dataSourceInfo</DataSourceInfo>
                  <Format>Multidimensional</Format>
                  <AxisFormat>TupleFormat</AxisFormat>
               </PropertyList>
            </Properties>
         </Execute>
      ";

      // submit the MDX query
      if( $logPerformance ) 
         $submitStartTime = DateTime::microtimeFloat();
      $dom = $this->con->submit( $queryXML );
      if( $logPerformance ) 
         $submitEndTime = DateTime::microtimeFloat() - $submitStartTime;

      // populate the MDX query results into a cellset
      if( $logPerformance ) 
         $populateStartTime = DateTime::microtimeFloat();
      if ( empty( $this->cellSet ) ) $this->cellSet = new XMLACellSet ( $this );
      $this->cellSet->populate ( $dom );
      if( $logPerformance ) 
         $populateEndTime = DateTime::microtimeFloat() - $populateStartTime;
      
      // log timing of method calls for performance tuning
      if( $logPerformance && $this->logger )
      {
         $this->logger->debug( __CLASS__, "[MDX SUBMIT TIME] " . $submitEndTime );
         $this->logger->debug( __CLASS__, "[MDX POPULATE TIME] " . $populateEndTime );
         $this->logger->debug( __CLASS__, "[MDX TOTAL TIME] " . ( DateTime::microtimeFloat()  - $startTime ) );
      }
      
      return $this->cellSet;
   }

   /**
    *
    * @return XMLAConnection
    */
   public function getConnection()
   {
      return $this->con;
   }


   /**
    * @brief Set the logger to be used.
    *
    * @param Logger $logger A logger
    */
   public function setLogger ( Logger $logger )
   {
      $this->logger = $logger;
   }


   /**
    * @return Logger
    */
   public function getLogger ( )
   {
      return $this->logger;
   }   

   
}