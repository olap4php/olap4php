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

/**
 *
 */
class DimensionType implements IXMLAConstant
{
   const UNKNOWN           = 0;
   const TIME              = 1;
   const MEASURE           = 2;
   const OTHER             = 3;
   const QUANTITATIVE      = 5;
   const ACCOUNTS          = 6;
   const CUSTOMERS         = 7;
   const PRODUCTS          = 8;
   const SCENARIO          = 9;
   const UTILITY           = 10;
   const CURRENCY          = 11;
   const RATES             = 12;
   const CHANNEL           = 13;
   const PROMOTION         = 14;
   const ORGANIZATION      = 15;
   const BILL_OF_MATERIALS = 16;
   const GEOGRAPHY         = 17;

   private $xmlaOrdinal;
   private $xmlaName;

   private static $constants;
   private static $dictionary;

   protected function __construct( $dimensionType )
   {
      switch( $dimensionType )
      {
         case self::UNKNOWN:
            $this->xmlaOrdinal = self::UNKNOWN;
            $this->xmlaName = 'UNKNOWN';
            break;

         case self::TIME:
            $this->xmlaOrdinal = self::TIME;
            $this->xmlaName = 'TIME';
            break;

         case self::MEASURE:
            $this->xmlaOrdinal = self::MEASURE;
            $this->xmlaName = 'MEASURE';
            break;

         case self::OTHER:
            $this->xmlaOrdinal = self::OTHER;
            $this->xmlaName = 'OTHER';
            break;

         case self::QUANTITATIVE:
            $this->xmlaOrdinal = self::QUANTITATIVE;
            $this->xmlaName = 'QUANTITATIVE';
            break;

         case self::ACCOUNTS:
            $this->xmlaOrdinal = self::ACCOUNTS;
            $this->xmlaName = 'ACCOUNTS';
            break;

         case self::CUSTOMERS:
            $this->xmlaOrdinal = self::CUSTOMERS;
            $this->xmlaName = 'CUSTOMERS';
            break;

         case self::PRODUCTS:
            $this->xmlaOrdinal = self::PRODUCTS;
            $this->xmlaName = 'PRODUCTS';
            break;

         case self::SCENARIO:
            $this->xmlaOrdinal = self::SCENARIO;
            $this->xmlaName = 'SCENARIO';
            break;

         case self::UTILITY:
            $this->xmlaOrdinal = self::UTILITY;
            $this->xmlaName = 'UTILITY';
            break;

         case self::CURRENCY:
            $this->xmlaOrdinal = self::CURRENCY;
            $this->xmlaName = 'CURRENCY';
            break;

         case self::RATES:
            $this->xmlaOrdinal = self::RATES;
            $this->xmlaName = 'RATES';
            break;

         case self::CHANNEL:
            $this->xmlaOrdinal = self::CHANNEL;
            $this->xmlaName = 'CHANNEL';
            break;

         case self::PROMOTION:
            $this->xmlaOrdinal = self::PROMOTION;
            $this->xmlaName = 'PROMOTION';
            break;

         case self::ORGANIZATION:
            $this->xmlaOrdinal = self::ORGANIZATION;
            $this->xmlaName = 'ORGANIZATION';
            break;

         case self::BILL_OF_MATERIALS:
            $this->xmlaOrdinal = self::BILL_OF_MATERIALS;
            $this->xmlaName = 'BILL_OF_MATERIALS';
            break;

         case self::GEOGRAPHY:
            $this->xmlaOrdinal = self::GEOGRAPHY;
            $this->xmlaName = 'GEOGRAPHY';
            break;

         default:
            throw new OLAPException( 'DimensionType ' . $dimensionType . ' not supported.' );
      }
   }


   static public function getEnum ( $constant )
   {
      if ( !self::$constants )
         self::getEnumConstants ( );

      if ( ! isset ( self::$constants [ $constant ] ) )
         throw new \InvalidArgumentException ( 'Invalid constant.' );

      return self::$constants [ $constant ];
   }


   /**
    *
    * @return array
    */
   static public function getEnumConstants()
   {
      if ( !self::$constants )
      {
         self::$constants = array (
            self::UNKNOWN           => new DimensionType ( self::UNKNOWN ),
            self::TIME              => new DimensionType ( self::TIME ),
            self::MEASURE           => new DimensionType ( self::MEASURE ),
            self::OTHER             => new DimensionType ( self::OTHER ),
            self::QUANTITATIVE      => new DimensionType ( self::QUANTITATIVE ),
            self::ACCOUNTS          => new DimensionType ( self::ACCOUNTS ),
            self::CUSTOMERS         => new DimensionType ( self::CUSTOMERS ),
            self::PRODUCTS          => new DimensionType ( self::PRODUCTS ),
            self::SCENARIO          => new DimensionType ( self::SCENARIO ),
            self::UTILITY           => new DimensionType ( self::UTILITY ),
            self::CURRENCY          => new DimensionType ( self::CURRENCY ),
            self::RATES             => new DimensionType ( self::RATES ),
            self::CHANNEL           => new DimensionType ( self::CHANNEL ),
            self::PROMOTION         => new DimensionType ( self::PROMOTION ),
            self::ORGANIZATION      => new DimensionType ( self::ORGANIZATION ),
            self::BILL_OF_MATERIALS => new DimensionType ( self::BILL_OF_MATERIALS ),
            self::GEOGRAPHY         => new DimensionType ( self::GEOGRAPHY )
         );
      }

      return self::$constants;
   }

   /**
    *
    * @return string
    */
   public function xmlaName()
   {
      return $this->xmlaName;
   }

   /**
    *
    * @return string
    */
   public function xmlaOrdinal()
   {
      return $this->xmlaOrdinal;
   }

   /**
    *
    * @return string
    */
   public function getDescription()
   {
      return '';
   }

   /**
    * @brief Return the value of the enum constant.
    */
   public function getConstant ( )
   {
      return $this->xmlaOrdinal;
   }

   /**
    *
    * @return Dictionary
    */
   public static function getDictionary()
   {
      if ( !self::$dictionary )
      {
         self::$dictionary = new Dictionary ( 'OLAP4PHP\Metadata\DimensionType' );
      }
      return self::$dictionary;
   }
}