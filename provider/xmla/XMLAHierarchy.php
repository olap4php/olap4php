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
use OLAP4PHP\Metadata\IHierarchy;

//Classes
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLAElement;
use OLAP4PHP\Provider\XMLA\XMLALevel;
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList;
use OLAP4PHP\Provider\XMLA\XMLAMetadataRequest;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
//use OLAP4PHP\Provider\XMLA\Metadata\XMLALevel;
use OLAP4PHP\Provider\XMLA\Metadata\XMLALevelHandler;

/**
 * XMLA Implementation of IHierarchy Interface
 */
class XMLAHierarchy extends XMLAElement implements IHierarchy
{
   /**
    * @var XMLADimension
    */
   private $dimension;

   /**
    * @var boolean
    */
   private $all;

   /**
    * @var string
    */
   private $defaultMemberUniqueName;

   /**
    * @var array
    */
   private $hierarchyRestrictions;

   /**
    * @var LazyMetadataList
    */
   private $levels;

   /**
    * Constructor
    *
    * @param
    */
   public function __construct ( XMLADimension $dimension, $uniqueName, $name, $caption, $description, $all, $defaultMemberUniqueName )
   {
      if ( empty( $dimension ) || $dimension == NULL ) throw new OLAPException( 'XMLAHierarchy: $dimension cannot be NULL' );
      parent::__construct( $uniqueName, $name, $caption, $description );

      $this->dimension = $dimension;
      $this->all = (boolean)$all;
      $this->defaultMemberUniqueName = $defaultMemberUniqueName;

      $this->hierarchyRestrictions = array(
          'CATALOG_NAME' => $this->dimension->getCube()->getSchema()->getCatalog()->getName(),
          'SCHEMA_NAME' => $this->dimension->getCube()->getSchema()->getName(),
          'CUBE_NAME' => $this->dimension->getCube()->getName(),
          'DIMENSION_UNIQUE_NAME' => $this->dimension->getUniqueName(),
          'HIERARCHY_UNIQUE_NAME' => $this->getUniqueName()
      );

      $this->levels = new LazyMetadataList(
              new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_LEVELS ),
              new XMLAConnectionContext(
                      $this->dimension->getCube()->getSchema()->getCatalog()->getMetaData()->getConnection(),
                      $this->dimension->getCube()->getSchema()->getCatalog()->getMetaData(),
                      $this->dimension->getCube()->getSchema()->getCatalog(),
                      $this->dimension->getCube()->getSchema(),
                      $this->dimension->getCube(),
                      $this->dimension,
                      $this,
                      NULL ),
              new XMLALevelHandler( $this->dimension->getCube() ),
              $this->hierarchyRestrictions);
   }

   /**
    *
    * @return XMLADimension
    */
   public function getDimension()
   {
      return $this->dimension;
   }

   /**
    *
    * @return LazyMetadataList
    */
   public function getLevels()
   {
      return $this->levels;
   }

   /**
    *
    * @return boolean
    */
   public function hasAll()
   {
      return $this->all;
   }

   /**
    *
    * @return XMLAMember
    */
   public function getDefaultMember()
   {
      if ( empty( $this->defaultMemberUniqueName ) ) return NULL;

      return $this->dimension->getCube()->getMetadataReader()->lookupMemberByUniqueName( $this->defaultMemberUniqueName );
   }

   /**
    *
    * @return NamedList 
    */
   public function getRootMembers()
   {
      return new NamedList ( $this->dimension
                                  ->getCube ( )
                                  ->getMetadataReader ( )
                                  ->getLevelMembers ( $this->levels->get ( 0 ) ) );
   }

   /**
    *
    * @param mixed $obj
    * @return boolean
    */
   public function equals( $obj )
   {
      if ( $obj instanceof XMLAHierarchy )
      {
         return $obj->getUniqueName() == $this->uniqueName;
      }

      return FALSE;
   }

   public function isVisible()
   {
      return TRUE;
   }
}