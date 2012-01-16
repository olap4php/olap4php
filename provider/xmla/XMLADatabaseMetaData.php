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

use OLAP4PHP\OLAP\IOLAPDatabaseMetaData;

use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Common\Wildcard;
use OLAP4PHP\Common\Properties;
use OLAP4PHP\Common\ResultSet;
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList as LazyMetadataList;
use OLAP4PHP\Provider\XMLA\XMLAMetadataRequest;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Provider\XMLA\Metadata\XMLACatalogHandler;

class XMLADatabaseMetaData implements IOLAPDatabaseMetaData
{
   /**
    * @var OLAP4PHP\Provider\XMLA\XMLAConnection
    */
   private $xmlaConnection;

   /**
    * @var array
    */
   private $catalogs;

   public function __construct( XMLAConnection $xmlaConnection )
   {
      $this->xmlaConnection = $xmlaConnection;
      // @todo - implements catalogs from olap4j
      $this->catalogs = new LazyMetadataList(
              new XMLAMetadataRequest( XMLAMetadataRequest::DBSCHEMA_CATALOGS ),
              new XMLAConnectionContext( $this->xmlaConnection, $this, NULL, NULL, NULL, NULL, NULL, NULL ),
              new XMLACatalogHandler(),
              NULL );
   }

   /**
    * @brief Returns an list of OLAP Catalog objects
    * 
    * @return LazyMetadataList
    */
   public function getCatalogObjects()
   {
      return $this->catalogs;
   }

   private function getMetadata( XMLAMetadataRequest $metadataRequest, array $patternValues = array() )
   {
      $context = new XMLAConnectionContext( $this->xmlaConnection );

      $predicateList = array();
      $compiledPatterns = array();
      foreach ( $patternValues as $key => $value )
      {
         $column = $metadataRequest->getColumn( $key );
         if ( $column === NULL ) throw new OLAPException( "Metadata Request {$metadataRequest->getName()} does not support column $key" );
         if ( empty( $value ) ) continue;

         if ( $value instanceof Wildcard )
         {
            $pattern = $value->getPattern();
            if ( strstr( $pattern, '%' ) === FALSE && strstr( $pattern, '_' ) === FALSE )
            {
               $compiledPatterns[$key] = $pattern;
            }
            else
            {
               $predicateList[$key] = XMLAUtil::wildcardToRegexp( array( $pattern ) );
            }
         }
         else
         {
            $compiledPatterns[$key] = $value;
         }
      }

      $requestString = $this->xmlaConnection->generateRequest( $context, $metadataRequest, $compiledPatterns );
      $root = $this->xmlaConnection->executeMetadataRequest( $requestString, $metadataRequest->isCachable() );

      $rowList = array();
      foreach ( $root->childNodes as $row )
      {
         if ( $row->namespaceURI != 'urn:schemas-microsoft-com:xml-analysis:rowset' ) continue;

         $valueList = array();
         foreach ( $predicateList as $key => $pattern )
         {
            $value = XMLAUtil::stringElement( $row, $key );
            if ( preg_match( "#$pattern#", $value ) == 0 ) continue 2;
         }

         foreach ( $metadataRequest->getColumns() as $column )
         {
            $valueList[] = XMLAUtil::stringElement( $row, $column->xmlaName );
         }

         $rowList[] = $valueList;
      }

      $headerList = $metadataRequest->getColumnNames();

      // @todo return olap4jConnection.factory.newFixedResultSet(olap4jConnection, headerList, rowList);
      //return;

      return new ResultSet( $headerList, $rowList );
   }

   public function wildcard( $string )
   {
      if ( empty( $string ) ) return NULL;
      return new Wildcard( $string );
   }
   
   public function getURL()
   {
      return $this->xmlaConnection->getURI();
   }

   public function isReadOnly()
   {
      return TRUE;
   }

   public function getDriverName()
   {
      return Properties::DRIVER_NAME;
   }

   public function getDriverVersion()
   {
      return Properties::DRIVER_VERSION;
   }

   public function getDriverMajorVersion()
   {
      return Properties::DRIVER_MAJOR_VERSION;
   }

   public function getDriverMinorVersion()
   {
      return Properties::DRIVER_MINOR_VERSION;
   }

   /**
    *
    * @return ResultSet
    */
   public function getSchemas()
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::DBSCHEMA_SCHEMATA ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getCatalogs()
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::DBSCHEMA_CATALOGS ) );
   }

   public function getConnection()
   {
      return $this->xmlaConnection;
   }

   /**
    *
    * @return ResultSet
    */
   public function getActions( $catalog = NULL, $schemaPattern = NULL, $cubeNamePattern = NULL, $actionNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_ACTIONS ),
              array(
                  "CATALOG_NAME" => $catalog,
                  "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
                  "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
                  "ACTION_NAME" => $this->wildcard( $actionNamePattern )
              ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getDatasources()
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::DISCOVER_DATASOURCES ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getLiterals()
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::DISCOVER_LITERALS ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getDatabaseProperties( $dataSourceName, $propertyNamePattern )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::DISCOVER_PROPERTIES ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getProperties(
           $catalog = NULL,
           $schemaPattern = NULL,
           $cubeNamePattern = NULL,
           $dimensionUniqueName = NULL,
           $hierarchyUniqueName = NULL,
           $levelUniqueName = NULL,
           $memberUniqueName = NULL,
           $propertyNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_PROPERTIES ), array(
            "CATALOG_NAME" => $catalog,
            "SCHEMA_NAME" => $this->wildcard($schemaPattern),
            "CUBE_NAME" => $this->wildcard($cubeNamePattern),
            "DIMENSION_UNIQUE_NAME" => $dimensionUniqueName,
            "HIERARCHY_UNIQUE_NAME" => $hierarchyUniqueName,
            "LEVEL_UNIQUE_NAME" => $levelUniqueName,
            "MEMBER_UNIQUE_NAME" => $memberUniqueName,
            "PROPERTY_NAME" => $this->wildcard($propertyNamePattern)
      ) );
   }

   /**
    *
    * @return string
    */
   public function getMdxKeywords()
   {
      $metadataRequest = new XMLAMetadataRequest( XMLAMetadataRequest::DISCOVER_KEYWORDS );
      $context = new XMLAConnectionContext();
      $context->xmlaConnection = $this->xmlaConnection;

      $request = $this->xmlaConnection->generateRequest( $context, $metadataRequest, array() );
      $root = $this->xmlaConnection->executeMetadataRequest( $request, $metadataRequest->isCachable() );
      
      $keywords = array();
      foreach ( $root->childNodes as $row )
      {
         $keyword = XMLAUtil::stringElement( $row, 'Keyword' );
         if ( !empty( $keyword ) ) $keywords[] = $keyword;
      }

      return implode( ',', $keywords );
   }

   /**
    *
    * @return ResultSet
    */
   public function getCubes( $catalog = NULL, $schemaPattern = NULL, $cubeNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_CUBES ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern )
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getDimensions( $catalog = NULL, $schemaPattern = NULL, $cubeNamePattern = NULL, $dimensionNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_DIMENSIONS ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
          "DIMENSION_NAME" => $this->wildcard( $dimensionNamePattern )
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getOlapFunctions( $functionNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_FUNCTIONS ), array(
          "FUNCTION_NAME" => $this->wildcard( $functionNamePattern )
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getHierarchies( $catalog = NULL, $schemaPattern = NULL, $cubeNamePattern = NULL, $dimensionUniqueName = NULL, $hierarchyNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_HIERARCHIES ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
          "DIMENSION_UNIQUE_NAME" => $dimensionUniqueName,
          "HIERARCHY_NAME" => $this->wildcard( $hierarchyNamePattern )
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getMeasures( $catalog = NULL, $schemaPattern = NULL, $cubeNamePattern = NULL, $measureNamePattern = NULL, $measureUniqueName = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_MEASURES ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
          "MEASURE_NAME" => $this->wildcard( $measureNamePattern ),
          "MEASURE_UNIQUE_NAME" => $measureUniqueName
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getMembers( 
           $catalog = NULL,
           $schemaPattern = NULL,
           $cubeNamePattern = NULL,
           $dimensionUniqueName = NULL,
           $hierarchyUniqueName = NULL,
           $levelUniqueName = NULL,
           $memberUniqueName = NULL,
           array $treeOps = array() )
   {
      $treeOpString = NULL;
      if ( !empty( $treeOps ) )
      {
         $op = 0;
         foreach ( $treeOps as $treeOp )
         {
            $to = new XMLATreeOp( $treeOp );
            $op |= $to->xmlaOrdinal();
         }

         $treeOpString = '' . $op;
      }

      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_MEMBERS ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
          "DIMENSION_UNIQUE_NAME" => $dimensionUniqueName,
          "HIERARCHY_UNIQUE_NAME" => $hierarchyUniqueName,
          "LEVEL_UNIQUE_NAME" => $levelUniqueName,
          "MEMBER_UNIQUE_NAME" => $memberUniqueName,
          "TREE_OP" => $treeOpString
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getLevels(
           $catalog = NULL,
           $schemaPattern = NULL,
           $cubeNamePattern = NULL,
           $dimensionUniqueName = NULL,
           $hierarchyUniqueName = NULL,
           $levelNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_LEVELS ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
          "DIMENSION_UNIQUE_NAME" => $dimensionUniqueName,
          "HIERARCHY_UNIQUE_NAME" => $hierarchyUniqueName,
          "LEVEL_NAME" => $this->wildcard( $levelNamePattern )
      ) );
   }

   /**
    *
    * @return ResultSet
    */
   public function getSets( $catalog = NULL, $schemaPattern = NULL, $cubeNamePattern = NULL, $setNamePattern = NULL )
   {
      return $this->getMetadata( new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_SETS ), array(
          "CATALOG_NAME" => $catalog,
          "SCHEMA_NAME" => $this->wildcard( $schemaPattern ),
          "CUBE_NAME" => $this->wildcard( $cubeNamePattern ),
          "SET_NAME" => $this->wildcard( $setNamePattern )
      ) );
   }

   public function getSupportedCellSetListenerGranularities()
   {
      return array();
   }

   public function __call( $name, $arguments )
   {
      throw new OLAPException( 'Unsupported Operation: ' . $name );
   }
}