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
use OLAP4PHP\Provider\XMLA\IXMLAMember;
use OLAP4PHP\Metadata\INamed;

// Classes / Objects
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Metadata\MemberType;


/**
 * @brief XMLA Member Undefined Implementation
 *
 * This class is used to store members that are not defined in a cube.
 *
 *   * ie. Query Defined Calculated Members
 */
class XMLAMemberUndefined extends XMLAElement implements IXMLAMember, INamed
{
   private $cellSet;
   private $level;
   private $hierarchy;
   private $lnum;
   
   /**
    * Constructor
    *
    * @param XMLACellSet $cellSet
    * @param XMLALevel $level
    * @param XMLAHierarchy $hierarchy
    * @param int $lnum Level number
    * @param string $caption Caption
    * @param string $uname Member unique name
    */
   public function __construct (
      XMLACellSet $cellSet,
      XMLALevel $level,
      XMLAHierarchy $hierarchy,
      $lnum,
      $caption,
      $uname )
   {
      $this->cellSet    = $cellSet;
      $this->level      = $level;
      $this->hierarchy  = $hierarchy;
      $this->lnum       = $lnum;
      $this->caption    = $caption;
      $this->uniqueName = $uname;
   }

   public function getCube ( )
   {
      return $cellSet->getMetaData ( )->getCube ( );
   }

   public function getConnection()
   {
      return $this->getCatalog ( )->getMetaData ( )->getConnection ( );
   }

   public function getCatalog ( )
   {
      return $this->getCube ( )->getSchema ( )->getCatalog ( );
   }

   public function getPropertyValueMap ( )
   {
      return array ( );
   }

   public function getChildMembers ( )
   {
      return Olap4jUtil.emptyNamedList ( );
   }

   public function getChildMemberCount ( )
   {
      return 0;
   }

   public function getParentMember ( )
   {
      return null;
   }

   public function getLevel ( )
   {
      return $this->level;
   }

   public function getHierarchy ( )
   {
      return $this->hierarchy;
   }

   public function getDimension ( )
   {
      return $this->hierarchy->getDimension ( );
   }

   public function getMemberType ( )
   {
      return new MemberType ( MemberType::UNKNOWN );
   }

   public function isAll ( )
   {
      return false; // FIXME
   }

   public function isChildOrEqualTo( XMLAMember $member )
   {
      return false; // FIXME
   }

   public function isCalculated ( )
   {
      return false; // FIXME
   }

   public function getSolveOrder ( )
   {
      return 0; // FIXME
   }

   public function getExpression ( )
   {
      return null;
   }

   public function getAncestorMembers ( )
   {
      return array ( ); // FIXME
   }

   public function isCalculatedInQuery ( )
   {
      return true; // probably
   }

   public function getPropertyValue ( $property )
   {
      return null;
   }

   public function getPropertyFormattedValue ( $property )
   {
      return null;
   }

   public function setProperty ( IProperty $property, $value )
   {
      throw new \BadMethodCallException ( );
   }

   public function getProperties ( )
   {
      return new NamedList ( );
   }

   public function getOrdinal ( )
   {
      return -1; // FIXME
   }

   public function isHidden ( )
   {
      return false;
   }

   public function getDepth ( )
   {
      return $this->lnum;
   }

   public function getDataMember ( )
   {
      return null;
   }

   public function getName ( )
   {
      return $this->caption;
   }

}