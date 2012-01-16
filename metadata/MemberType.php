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

use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Common\IEnum;

/**
 *
 */
class MemberType implements IXMLAConstant, IEnum
{
   const UNKNOWN  = 0;
   const REGULAR  = 1;
   const ALL      = 2;
   const MEASURE  = 3;
   const FORMULA  = 4;
   const NULL     = 5;

   private $name;
   private $xmlaName;
   private $xmlaOrdinal;

   private static $values;
   private static $constants;
   private static $dictionary;

   public function __construct( $memberType )
   {
      switch( $memberType )
      {
         case self::UNKNOWN:
            $this->xmlaName = self::UNKNOWN;
            $this->xmlaOrdinal = 0;
            $this->name = 'UNKNOWN';
            break;

         case self::REGULAR:
            $this->xmlaName = self::REGULAR;
            $this->xmlaOrdinal = 1;
            $this->name = 'REGULAR';
            break;

         case self::ALL:
            $this->xmlaName = self::ALL;
            $this->xmlaOrdinal = 2;
            $this->name = 'ALL';
            break;

         case self::MEASURE:
            $this->xmlaName = self::MEASURE;
            $this->xmlaOrdinal = 3;
            $this->name = 'MEASURE';
            break;

         case self::FORMULA:
            $this->xmlaName = self::FORMULA;
            $this->xmlaOrdinal = 4;
            $this->name = 'FORMULA';
            break;

         case self::NULL:
            $this->xmlaName = self::NULL;
            $this->xmlaOrdinal = 5;
            $this->name = 'NULL';
            break;

         default:
            throw new OLAPException( 'MemberType ' . $memberType . ' is not supported.' );
      }
   }

   public function getDescription()
   {
      return '';
   }

   public function name ( )
   {
      return $this->name;
   }

   public function xmlaOrdinal()
   {
      return $this->xmlaOrdinal;
   }

   public function xmlaName()
   {
      return $this->xmlaName;
   }

   public function ordinal()
   {
      return $this->xmlaOrdinal;
   }

   static public function getEnum ( $constant )
   {
      if ( !self::$constants )
         self::getEnumConstants ( );

      if ( ! isset ( self::$constants [ $constant ] ) )
         throw new \InvalidArgumentException ( 'Invalid constant.' );

      return self::$constants [ $constant ];
   }

   static public function getEnumConstants()
   {
      if ( !self::$constants )
      {
         self::$constants = array(
            self::UNKNOWN  => new MemberType ( self::UNKNOWN ),
            self::REGULAR  => new MemberType ( self::REGULAR ),
            self::ALL      => new MemberType ( self::ALL ),
            self::MEASURE  => new MemberType ( self::MEASURE ),
            self::FORMULA  => new MemberType ( self::FORMULA ),
            self::NULL    => new MemberType ( self::NULL )
         );
      }

      return self::$constants;
   }

   public static function getDictionary()
   {
      if ( !self::$dictionary ) self::$dictionary = new Dictionary( $this );
      return self::$dictionary;
   }

   public static function values()
   {
      if ( !self::$values )
      {
         self::$values = array(
             self::UNKNOWN => new MemberType ( self::UNKNOWN ),
             self::REGULAR => new MemberType ( self::REGULAR ),
             self::ALL     => new MemberType ( self::ALL ),
             self::MEASURE => new MemberType ( self::MEASURE ),
             self::FORMULA => new MemberType ( self::FORMULA ),
             self::NULL   => new MemberType ( self::NULL )
         );
      }

      return self::$values;
   }
}
