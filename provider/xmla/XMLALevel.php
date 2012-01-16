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
use OLAP4PHP\Metadata\ILevel;
use OLAP4PHP\Metadata\INamed;

//Classes
use OLAP4PHP\Metadata\LevelType;
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAPropertyHandler;
use OLAP4PHP\Metadata\DimensionType;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAMemberHandler;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Metadata\StandardMemberProperty;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAMeasureHandler;


/**
 * XMLA Implementation of ILevel Interface
 */
class XMLALevel extends XMLAElement implements ILevel, INamed
{
   public  $hierarchy;
   private $depth;
   private $type;
   private $cardinality;
   private $propertyList;
   public  $memberList;
   private $calculated;

   /**
     * Constructor
     *
     * @param XMLAHierarchy $hierarchy
     * @param string $uniqueName Unique name
     * @param string $name Name
     * @param string $caption Caption
     * @param string $description Description
     * @param integer $depth Distance to root
     * @param LevelType $type Level type
     * @param boolean $calculated Whether level is calculated
     * @param integer $cardinality Number of members in this level
     */
   public function __construct (
      XMLAHierarchy $hierarchy,
      $uniqueName, 
      $name,
      $caption,
      $description,
      $depth,
      LevelType $type = NULL,
      $calculated,
      $cardinality
      )
   {
      parent::__construct ( $uniqueName, $name, $caption, $description );
      assert ( $hierarchy != null );
      $this->type = $type;
      $this->calculated = $calculated;
      $this->cardinality = $cardinality;
      $this->depth = $depth;
      $this->hierarchy = $hierarchy;

      $levelRestrictions = array (
         "CATALOG_NAME"          => $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( )->getName ( ),
         "SCHEMA_NAME"           => $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getName(),
         "CUBE_NAME"             => $hierarchy->getDimension ( )->getCube ( )->getName(),
         "DIMENSION_UNIQUE_NAME" => $hierarchy->getDimension ( )->getUniqueName(),
         "HIERARCHY_UNIQUE_NAME" => $hierarchy->getUniqueName(),
         "LEVEL_UNIQUE_NAME"     => $this->getUniqueName ( )
      );

      $this->propertyList = new LazyMetadataList (
         new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_PROPERTIES ),
         XMLAConnectionContext::createAtLevel ( $this ),
         new XMLAPropertyHandler ( ),
         $levelRestrictions );

      try
      {
         if ( $hierarchy->getDimension ( )->getDimensionType ( ) == DimensionType::getEnum ( DimensionType::MEASURE ) )
         {
            $restrictions = array (
               "CATALOG_NAME"          => $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( )->getName ( ),
               "SCHEMA_NAME"           => $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getName ( ),
               "CUBE_NAME"             => $hierarchy->getDimension ( )->getCube ( )->getName ( )
            );

            $this->memberList =
               new LazyMetadataList (
                  new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_MEASURES ),
                  new XMLAConnectionContext (
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( )->getMetadata ( )->getConnection ( ),
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( )->getMetadata ( ),
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( ),
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( ),
                      $hierarchy->getDimension ( )->getCube ( ),
                      $hierarchy->getDimension ( ),
                      $hierarchy,
                      $this ),
                  new XMLAMeasureHandler (
                      $hierarchy->getDimension ( ),
                      $restrictions ) );
         } 
         else
         {
            $this->memberList =
               new LazyMetadataList (
                 new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_MEMBERS ),
                 new XMLAConnectionContext (
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( )->getMetadata ( )->getConnection ( ),
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( )->getMetadata ( ),
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( )->getCatalog ( ),
                      $hierarchy->getDimension ( )->getCube ( )->getSchema ( ),
                      $hierarchy->getDimension ( )->getCube ( ),
                      $hierarchy->getDimension ( ),
                      $hierarchy,
                      $this ),
                 new XMLAMemberHandler ( ),
                 $levelRestrictions );
         }
      } 
      catch ( OlapException $e )
      {
         throw new RuntimeException ( "Programming error", $e );
      }
   }

   public function getDepth ( )
   {
      return $this->depth;
   }

   public function getHierarchy ( )
   {
      return $this->hierarchy;
   }

   public function getDimension ( )
   {
      return $this->hierarchy->getDimension ( );
   }

   public function isCalculated ( )
   {
      return $this->calculated;
   }

   /**
   * return LevelType
   */
   public function getLevelType ( )
   {
      return $this->type;
   }

   /**
    * @return NamedList
    */
   public function getProperties ( )
   {
      // standard properties first
      $list = new NamedList ( StandardMemberProperty::getEnumConstants ( ) );
      // then level-specific properties
      $list->addAll ( $this->propertyList );

      return $list;
   }

   public function getMembers ( )
   {
      return $this->memberList;
   }

   public function getCardinality ( )
   {
      return $this->cardinality;
   }

   public function isVisible()
   {
      return TRUE;
   }
}