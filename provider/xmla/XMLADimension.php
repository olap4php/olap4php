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
use OLAP4PHP\Metadata\IDimension;

//Classes
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList;
use OLAP4PHP\Provider\XMLA\XMLAElement;
use OLAP4PHP\Provider\XMLA\XMLACube;
use OLAP4PHP\Provider\XMLA\XMLAMetadataRequest;
use OLAP4PHP\Metadata\DimensionType;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAHierarchyHandler;

/**
 * XMLA Implementation of IDimension Interface
 */
class XMLADimension extends XMLAElement implements IDimension
{
   /**
    *
    * @var DimensionType
    */
   private $type;

   private $defaultHierarchyUniqueName;

   private $cube;

   private $ordinal;

   private $dimensionRestrictions;

   private $hierarchies;

   /**
    * Constructor
    *
    * @param XMLACube      $cube
    * @param               $uniqueName
    * @param               $name
    * @param               $caption
    * @param               $description
    * @param DimensionType $type
    * @param               $defaultHierarchyUniqueName
    * @param               $ordinal
    */
   public function __construct(
      XMLACube $cube,
      $uniqueName,
      $name,
      $caption,
      $description,
      DimensionType $type,
      $defaultHierarchyUniqueName,
      $ordinal )
   {
      if ( empty($cube) || $cube == NULL ) throw new OLAPException('XMLADimension: $cube cannot be NULL');
      parent::__construct( $uniqueName, $name, $caption, $description );
      $this->defaultHierarchyUniqueName = $defaultHierarchyUniqueName;
      $this->cube                       = $cube;
      $this->type                       = $type;
      $this->ordinal                    = (int)$ordinal;

      $this->dimensionRestrictions = array(
         'CATALOG_NAME'          => $this->cube->getSchema()->getCatalog()->getName(),
         'SCHEMA_NAME'           => $this->cube->getSchema()->getName(),
         'CUBE_NAME'             => $this->cube->getName(),
         'DIMENSION_UNIQUE_NAME' => $this->getUniqueName()
      );

      $this->hierarchies = new LazyMetadataList(
         new XMLAMetadataRequest(XMLAMetadataRequest::MDSCHEMA_HIERARCHIES),
         new XMLAConnectionContext(
            $this->cube->getSchema()->getCatalog()->getMetadata()->getConnection(),
            $this->cube->getSchema()->getCatalog()->getMetadata(),
            $this->cube->getSchema()->getCatalog(),
            $this->cube->getSchema(),
            $this->cube,
            $this,
            NULL, NULL),
         new XMLAHierarchyHandler($this->cube),
         $this->dimensionRestrictions);
   }

   /**
    *
    * @return DimensionType
    */
   public function getDimensionType()
   {
      return $this->type;
   }

   /**
    * @brief Gets the XMLACube this XMLADimension uses
    *
    * @return XMLACube
    */
   public function getCube()
   {
      return $this->cube;
   }

   /**
    *
    * @return XMLAHierarchy
    */
   public function getDefaultHierarchy()
   {
      for ( $i = 0; $i < $this->hierarchies->size(); $i++ )
      {
         $hierarchy = $this->hierarchies->get( $i );
         if ( $hierarchy->getUniqueName() == $this->defaultHierarchyUniqueName ) return $hierarchy;
      }

      return $this->hierarchies->get( 0 );
   }


   /**
    *
    * @param mixed $obj
    *
    * @return boolean
    */
   public function equals( $obj )
   {
      if ( $obj instanceof XMLADimension )
      {
         return $obj->getUniqueName() == $this->uniqueName;
      }

      return FALSE;
   }

   /**
    *
    * @return int
    */
   public function getOrdinal()
   {
      return $this->ordinal;
   }

   /**
    *
    * @return LazyMetadataList
    */
   public function getHierarchies()
   {
      return $this->hierarchies;
   }


   public function isVisible()
   {
   }
}
