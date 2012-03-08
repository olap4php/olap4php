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
 * @brief Holds a Wildcard pattern
 */
class Dictionary implements IXMLAConstantDictionary
{
   private $class;
   private $mapByName = array();
   private $mapByOrdinal = array();
   private $constants;

   // Static dictionary map by class
   private static $mapByClass = array();

   public function __construct( $class )
   {
      $this->class = $class;
      $this->init();
   }

   private function init()
   {
      if ( $this->constants != null )
      {
         return;
      }

      $classname       = $this->class;
      $this->constants = $classname::getEnumConstants();
      foreach ( $this->constants as $constant )
      {
         $this->mapByName [$constant->xmlaName()]       = $constant;
         $this->mapByOrdinal [$constant->xmlaOrdinal()] = $constant;
      }
   }

   public static function forClass( $class )
   {
      assert( $class != null );
      $dictionary = !isset (self::$mapByClass [$class]) ? null : self::$mapByClass [$class];
      if ( $dictionary == null )
      {
         $dictionary                = new Dictionary ($class);
         self::$mapByClass [$class] = $dictionary;
      }

      return $dictionary;
   }

   public function forOrdinal( $xmlaOrdinal )
   {
      //init();
      return isset ($this->mapByOrdinal [$xmlaOrdinal]) ? $this->mapByOrdinal [$xmlaOrdinal] : null;
   }

   public function forName( $xmlaName )
   {
      //$this->init();
      return isset ($this->mapByName [$xmlaName]) ? $this->mapByName [$xmlaName] : null;
   }

   public function forMask( $xmlaOrdinalMask )
   {
      //$this->init();
      $set = array();
      foreach ( $this->constants as $constant )
      {
         if ( ($xmlaOrdinalMask & $constant->xmlaOrdinal()) != 0 )
         {
            $set [] = $constant;
         }
      }
      return $set;
   }

   public function toMask( array $set )
   {
      $mask = 0;
      foreach ( $set as $enum )
      {
         $mask |= $enum->xmlaOrdinal();
      }

      return $mask;
   }

   public function getValues()
   {
      //$this->init();
      return $this->constants;
   }

   public function getEnumClass()
   {
      return $this->class;
   }
}
