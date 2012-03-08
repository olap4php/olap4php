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
use OLAP4PHP\Metadata\INamed;

// Classes / Objects
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Metadata\MemberType;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLATreeOp;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLAProperty;

/**
 * @brief XMLA Member Implementation
 */
class XMLAMember extends XMLAElement implements IXMLAMember, INamed
{
   private $childMemberCount;

   /**
    * @var XMLALevel
    */
   private $level;
   private $ordinal;
   private $parentMemberUniqueName;
   private $parentMember;
   private $propertyValueMap;
   private $type;
   private $hash;

   /// array [ IProperty ] => Object

   /**
    * Constructor
    */
   public function __construct(
      XMLALevel $level,
      $uniqueName,
      $name,
      $caption,
      $description,
      $parentMemberUniqueName,
      MemberType $type,
      $childMemberCount,
      $ordinal,
      array $propertyValueMap
   )
   {
      parent::__construct( $uniqueName, $name, $caption, $description );
      $this->ordinal = $ordinal;
      assert( $level != null );
      assert( $type != null );
      $this->level                  = $level;
      $this->parentMemberUniqueName = $parentMemberUniqueName;
      $this->type                   = $type;
      $this->childMemberCount       = $childMemberCount;
      $this->propertyValueMap       = $propertyValueMap;
   }

   public function hashCode()
   {
      if ( empty($this->hash) ) $this->hash = XMLAUtil::javaStringHashCode( $this->uniqueName );
      return $this->hash;
   }

   public function equals( $obj )
   {
      if ( $obj instanceof XMLAMember )
      {
         return $obj->getName() == $this->name;
      }

      return FALSE;
   }

   /**
    * @return array XMLAMember
    */
   public function getAncestorMembers()
   {
      $list = array();
      $m    = $this->getParentMember();
      while ( $m != NULL )
      {
         $list[] = $m;
         $m      = $m->getParentMember();
      }

      return $list;
   }


   /**
    * @return int
    */
   public function getChildMemberCount()
   {
      return $this->childMemberCount;
   }


   /**
    * @return NamedList
    */
   public function getChildMembers()
   {
      $list = new NamedList();
      $cube = $this->getCube();
      $cube->getMetadataReader()->lookupMemberRelatives( new XMLATreeOp(XMLATreeOp::CHILDREN), $this->uniqueName, $list );
      return $list;
   }

   /**
    * @return XMLADimension
    */
   public function getDimension()
   {
      return $this->level->getHierarchy()->getDimension();
   }


   /**
    * @return XMLAHierarchy
    */
   public function getHierarchy()
   {
      return $this->level->getHierarchy();
   }


   /**
    * @return XMLALevel
    */
   public function getLevel()
   {
      return $this->level;
   }


   /**
    * @return MemberType
    */
   public function getMemberType()
   {
      return $this->type;
   }


   /**
    * @return XMLAMember
    */
   public function getParentMember()
   {
      if ( empty($this->parentMemberUniqueName) ) return NULL;

      if ( empty($this->parentMember) )
      {
         $this->parentMember = $this->getCube()->getMetadataReader()->lookupMemberByUniqueName( $this->parentMemberUniqueName );
      }

      return $this->parentMember;
   }

   public function isAll()
   {
      $values = MemberType::values();
      return $this->type === $values[2];
   }

   public function isCalculated()
   {
      $values = MemberType::values();
      return $this->type === $values[4];
   }

   /**
    *
    * @param string $property
    *
    * @return mixed
    */
   public function getPropertyValue( $property )
   {
      return $this->_getPropertyValue( $property, $this, $this->propertyValueMap );
   }

   /**
    *
    * @param string     $property
    * @param XMLAMember $member
    * @param array      $map
    *
    * @return mixed
    */
   protected function _getPropertyValue( $property, XMLAMember $member, array $map )
   {
      if ( isset($map[$property]) ) return $map[$property];

      switch ( $property )
      {
         case 'MEMBER_CAPTION':
            return $member->getCaption();

         case 'MEMBER_NAME':
            return $member->getName();

         case 'MEMBER_UNIQUE_NAME':
            return $member->getUniqueName();

         case 'CATALOG_NAME':
            return $member->getCatalog()->getName();

         case 'CHILDREN_CARDINALITY':
            return $member->getChildMemberCount();

         case 'CUBE_NAME':
            return $member->getCube()->getName();

         case 'DEPTH':
            return $member->getDepth();

         case 'DESCRIPTION':
            return $member->getDescription();

         case 'DIMENSION_UNIQUE_NAME':
            return $member->getDimension()->getUniqueName();

         case 'DISPLAY_INFO':
            return NULL;

         case 'HIERARCHY_UNIQUE_NAME':
            return $member->getHierarchy()->getUniqueName();

         case 'LEVEL_NUMBER':
            return $member->getLevel()->getDepth();

         case 'LEVEL_UNIQUE_NAME':
            return $member->getLevel()->getUniqueName();

         case 'MEMBER_GUID':
            return NULL;

         case 'MEMBER_ORDINAL':
            return $member->getOrdinal();

         case 'MEMBER_TYPE':
            return $member->getMemberType();

         case 'PARENT_COUNT':
            return 1;

         case 'PARENT_LEVEL':
            return ($member->getParentMember() == NULL) ? 0 : $member->getParentMember()->getDepth();

         case 'PARENT_UNIQUE_NAME':
            return ($member->getParentMember() == NULL) ? NULL : $member->getParentMember()->getUniqueName();

         case 'SCHEMA_NAME':
            return $member->getCube()->getSchema()->getName();

         case 'VALUE':
            return NULL;
      }

      return NULL;
   }

   /**
    *
    * @return XMLACube
    */
   public function getCube()
   {
      return $this->getLevel()->getHierarchy()->getDimension()->getCube();
   }

   /**
    *
    * @return XMLACatalog
    */
   public function getCatalog()
   {
      return $this->level->getHierarchy()->getDimension()->getCube()->getSchema()->getCatalog();
   }

   /**
    *
    * @return XMLAConnection
    */
   public function getConnection()
   {
      return $this->getCatalog()->getMetaData()->getConnection();
   }

   public function setProperty( $property, $obj )
   {
      $this->propertyValueMap[$property] = $obj;
   }

   public function getProperties()
   {
      return $this->level->getProperties();
   }

   public function getOrdinal()
   {
      return $this->ordinal;
   }

   public function getDepth()
   {
      $depth = $this->getPropetyValue( 'DEPTH' );
      if ( !$depth )
      {
         return (int)$this->level->getDepth();
      }

      return (int)$depth;
   }

   public function isVisible()
   {
      return TRUE;
   }

   public function getPropertyValueMap()
   {

   }

   public function getDataMember()
   {

   }

   public function isHidden()
   {
      return FALSE;
   }
}
