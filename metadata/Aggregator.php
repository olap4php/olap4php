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

use OLAP4PHP\Common\IEnum;

use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Metadata\Dictionary;

class Aggregator implements IEnum
{
   const SUM        = 0;
   const COUNT      = 1;
   const MIN        = 2;
   const MAX        = 3;
   const AVG        = 4;
   const XVAR       = 5;
   const STD        = 6;
   const CALCULATED = 7;
   const UNKNOWN    = 8;

   private $name;
   private $xmlaOrdinal;

   private static $constants;
   private static $dictionary;

   protected function __construct( $aggregatorType )
   {
      switch ( $aggregatorType )
      {
         case self::SUM:
            $this->name        = 'SUM';
            $this->xmlaOrdinal = 1;
            break;

         case self::COUNT:
            $this->name        = 'COUNT';
            $this->xmlaOrdinal = 2;
            break;

         case self::MIN:
            $this->name        = 'MIN';
            $this->xmlaOrdinal = 3;
            break;

         case self::MAX:
            $this->name        = 'MAX';
            $this->xmlaOrdinal = 4;
            break;

         case self::AVG:
            $this->name        = 'AVG';
            $this->xmlaOrdinal = 5;
            break;

         case self::XVAR:
            $this->name        = 'VAR';
            $this->xmlaOrdinal = 6;
            break;

         case self::STD:
            $this->name        = 'STD';
            $this->xmlaOrdinal = 7;
            break;

         case self::CALCULATED:
            $this->name        = 'CALCULATED';
            $this->xmlaOrdinal = 127;
            break;

         case self::UNKNOWN:
            $this->name        = 'UNKNOWN';
            $this->xmlaOrdinal = 0;
            break;

         default:
            throw new OLAPException("Aggregator type $aggregatorType not supported.");
      }
   }

   public static function getEnumConstants()
   {
      if ( !self::$constants )
      {
         self::$constants = array(
            new Aggregator(self::SUM),
            new Aggregator(self::COUNT),
            new Aggregator(self::MIN),
            new Aggregator(self::MAX),
            new Aggregator(self::AVG),
            new Aggregator(self::XVAR),
            new Aggregator(self::STD),
            new Aggregator(self::CALCULATED),
            new Aggregator(self::UNKNOWN)
         );
      }

      return self::$constants;
   }

   public static function getEnum( $constant )
   {
      if ( !self::$constants ) self::getEnumConstants();

      if ( !isset(self::$constants[$constant]) )
      {
         throw new \InvalidArgumentException ('Invalid Aggregator Constant');
      }

      return self::$constants[$constant];
   }

   public static function getDictionary()
   {
      if ( !self::$dictionary )
      {
         self::$dictionary = new Dictionary($this);
      }

      return self::$dictionary;
   }

   public function getDescription()
   {
      return "";
   }

   public function xmlaName()
   {
      return 'MDMEASURE_AGGR_' . $this->name;
   }

   public function xmlaOrdinal()
   {
      return $this->xmlaOrdinal;
   }

   public function name()
   {
      return $this->name;
   }
}
