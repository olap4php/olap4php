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
namespace OLAP4PHP\Metadata;

/**
 *  Metadata supported property types
 */
class PropertyType implements IXMLAConstant
{
   // Enumeration constants
   const MEMBER   = 'MEMBER';
   const CELL     = 'CELL';
   const SYSTEM   = 'SYSTEM';
   const BLOB     = 'BLOB';

   // What constant do we rep ?
   private $constant;
   private $xmlaOrdinal;

   private static $constants;
   private static $dictionary;

   /**
    * Constructor
    */
   public function __construct ( $constant )
   {
      $this->constant = $constant;

      switch ( $constant )
      {
         /**
          * Identifies a property of a member. This property can be used in the
          * DIMENSION PROPERTIES clause of the SELECT statement.
          */
         case self::MEMBER:
            $this->xmlaOrdinal = 1;
            break;

         /**
          * Identifies a property of a cell. This property can be used in the
          * CELL PROPERTIES clause that occurs at the end of the SELECT
          * statement.
          */
         case self::CELL:
            $this->xmlaOrdinal = 2;
            break;

         /**
          * Identifies an internal property.
          */
         case self::SYSTEM:
            $this->xmlaOrdinal = 4;
            break;

         /**
          * Identifies a property which contains a binary large object (blob).
          */
         case self::BLOB:
            $this->xmlaOrdinal =  8;
            break;

         default:
            throw new InvalidArgumentException ( 'Unsupported property type' );
      }
   }


   /**
    * return array Return the datatype enumeration constants
    */
   public function getEnumConstants ( )
   {
      if ( !self::$constants )
      {
         // array of enums constants
         self::$constants = array (
            new PropertyType ( self::MEMBER ),
            new PropertyType ( self::CELL ),
            new PropertyType ( self::SYSTEM ),
            new PropertyType ( self::BLOB )
         );
      }

      return self::$constants;
   }

   public function xmlaName ( )
   {
      return 'MD_PROPTYPE_'.$this->constant;
   }

   /**
    * Human readable description of a Datatype instance.
    */
   public function getDescription ( )
   {
      return null;
   }

   /**
    * Unique identifier of a Datatype instance.
    */
   public function xmlaOrdinal ( )
   {
      return $this->xmlaOrdinal;
   }

   /**
    * @return Dictionary of all values
    */
   static public function getDictionary ( )
   {
      if ( !self::$dictionary )
         self::$dictionary = new Dictionary ( $this );

      return self::$dictionary;
   }
}