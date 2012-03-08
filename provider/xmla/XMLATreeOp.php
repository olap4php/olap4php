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

use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Common\IEnum;

class XMLATreeOp implements IEnum
{
   const CHILDREN    = 0;
   const SIBLINGS    = 1;
   const PARENT      = 2;
   const SELF        = 3;
   const DESCENDANTS = 4;
   const ANCESTORS   = 5;

   /**
    * @var string
    */
   private $name;

   /**
    * @var int
    */
   private $xmlaOrdinal;

   /**
    * @var string
    */
   private $description;

   private static $constants;


   protected function __construct( $treeOpType )
   {
      $this->name = $treeOpType;

      switch ( $treeOpType )
      {
         case self::ANCESTORS:
            $this->xmlaOrdinal = 32;
            $this->description = "Tree operation which returns all of the ancestors.";
            break;

         case self::CHILDREN:
            $this->xmlaOrdinal = 1;
            $this->description = "Tree operation which returns only the immediate children.";
            break;

         case self::DESCENDANTS:
            $this->xmlaOrdinal = 16;
            $this->description = "Tree operation which returns all of the descendants.";
            break;

         case self::SIBLINGS:
            $this->xmlaOrdinal = 2;
            $this->description = "Tree operation which returns members on the same level.";
            break;

         case self::PARENT:
            $this->xmlaOrdinal = 4;
            $this->description = "Tree operation which returns only the immediate parent.";
            break;

         case self::SELF:
            $this->xmlaOrdinal = 8;
            $this->description = "Tree operation which returns itself in the list of returned rows.";
            break;

         default:
            throw new OLAPException('Tree Operation of type ' . $treeOpType . ' not supported');
      }
   }

   static public function getEnum( $constant )
   {
      if ( !self::$constants )
      {
         self::getEnumConstants();
      }

      if ( !isset (self::$constants [$constant]) )
      {
         throw new \InvalidArgumentException ('Invalid constant.');
      }

      return self::$constants [$constant];
   }

   /**
    * return array Return the datatype enumeration constants
    */
   static public function getEnumConstants()
   {
      if ( !self::$constants )
      {
         // array of enums constants
         self::$constants = array(
            new XMLATreeOp (self::CHILDREN),
            new XMLATreeOp (self::SIBLINGS),
            new XMLATreeOp (self::PARENT),
            new XMLATreeOp (self::SELF),
            new XMLATreeOp (self::DESCENDANTS),
            new XMLATreeOp (self::ANCESTORS)
         );
      }

      return self::$constants;
   }

   public function name()
   {
   }

   public function xmlaName()
   {
      return 'MDTREEOP_' . $this->name;
   }

   public function xmlaOrdinal()
   {
      return $this->xmlaOrdinal;
   }

   public function getDescription()
   {
      return $this->description;
   }
}
