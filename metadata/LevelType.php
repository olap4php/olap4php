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
 *  Metadata supported level types
 */
class LevelType implements IXMLAConstant
{
   // Enumeration constants
   const REGULAR                 = 'REGULAR';
   const ALL                     = 'ALL';
   const NULL                    = 'NULL';
   const TIME_YEARS              = 'TIME_YEARS';
   const TIME_HALF_YEAR          = 'TIME_HALF_YEAR';
   const TIME_QUARTERS           = 'TIME_QUARTERS';
   const TIME_MONTHS             = 'TIME_MONTHS';
   const TIME_WEEKS              = 'TIME_WEEKS';
   const TIME_DAYS               = 'TIME_DAYS';
   const TIME_HOURS              = 'TIME_HOURS';
   const TIME_MINUTES            = 'TIME_MINUTES';
   const TIME_SECONDS            = 'TIME_SECONDS';
   const TIME_UNDEFINED          = 'TIME_UNDEFINED';
   const GEO_CONTINENT           = 'GEO_CONTINENT';
   const GEO_REGION              = 'GEO_REGION';
   const GEO_COUNTRY             = 'GEO_COUNTRY';
   const GEO_STATE_OR_PROVINCE   = 'GEO_STATE_OR_PROVINCE';
   const GEO_COUNTY              = 'GEO_COUNTY';
   const GEO_CITY                = 'GEO_CITY';
   const GEO_POSTALCODE          = 'GEO_POSTALCODE';
   const GEO_POINT               = 'GEO_POINT';
   const ORG_UNIT                = 'ORG_UNIT';
   const BOM_RESOURCE            = 'BOM_RESOURCE';
   const QUANTITATIVE            = 'QUANTITATIVE';
   const ACCOUNT                 = 'ACCOUNT';
   const CUSTOMER                = 'CUSTOMER';
   const CUSTOMER_GROUP          = 'CUSTOMER_GROUP';
   const CUSTOMER_HOUSEHOLD      = 'CUSTOMER_HOUSEHOLD';
   const PRODUCT                 = 'PRODUCT';
   const PRODUCT_GROUP           = 'PRODUCT_GROUP';
   const SCENARIO                = 'SCENARIO';
   const UTILITY                 = 'UTILITY';
   const PERSON                  = 'PERSON';
   const COMPANY                 = 'COMPANY';
   const CURRENCY_SOURCE         = 'CURRENCY_SOURCE';
   const CURRENCY_DESTINATION    = 'CURRENCY_DESTINATION';
   const CHANNEL                 = 'CHANNEL';
   const REPRESENTATIVE          = 'REPRESENTATIVE';
   const PROMOTION               = 'PROMOTION';

   // What constant do we rep ?
   private $constant;
   private $xmlaOrdinal;

   private static $constants;
   private static $dictionary;

   /**
    * Constructor
    */
   private function __construct ( $constant )
   {
      $this->constant = $constant;

      switch ( $constant )
      {
         /**
         * Indicates that the level is not related to time.
         */
        case self::REGULAR:
           $this->xmlaOrdinal = 0x0000;
           break;

        /**
         * Indicates that the level contains the 'all' member of its hierarchy.
         */
        case self::ALL:
           $this->xmlaOrdinal = 0x0001;
           break;
        /**
         * Indicates that a level holds the null member. Does not correspond to
         * an XMLA or OLE DB value.
         */
        case self::NULL:
           $this->xmlaOrdinal = -1;
           break;

        /**
         * Indicates that a level refers to years.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_YEARS:
           $this->xmlaOrdinal = 0x0014;
           break;

        /**
         * Indicates that a level refers to half years.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_HALF_YEAR:
           $this->xmlaOrdinal = 0x0024;
           break;

        /**
         * Indicates that a level refers to quarters.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_QUARTERS:
           $this->xmlaOrdinal = 0x0044;
           break;

        /**
         * Indicates that a level refers to months.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_MONTHS:
           $this->xmlaOrdinal = 0x0084;
           break;

        /**
         * Indicates that a level refers to weeks.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_WEEKS:
           $this->xmlaOrdinal = 0x0104;
           break;

        /**
         * Indicates that a level refers to days.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_DAYS:
           $this->xmlaOrdinal = 0x0204;
           break;

        /**
         * Indicates that a level refers to hours.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_HOURS:
           $this->xmlaOrdinal = 0x0304;
           break;

        /**
         * Indicates that a level refers to minutes.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_MINUTES:
           $this->xmlaOrdinal = 0x0404;
           break;

        /**
         * Indicates that a level refers to seconds.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_SECONDS:
           $this->xmlaOrdinal = 0x0804;
           break;

        /**
         * Indicates that a level refers to days.
         * It must be used in a dimension whose type is
         * {@link org.olap4j.metadata.Dimension.Type#TIME}.
         */
        case self::TIME_UNDEFINED:
           $this->xmlaOrdinal = 0x1004;
           break;

        case self::GEO_CONTINENT:
           $this->xmlaOrdinal = 0x2001;
           break;

        case self::GEO_REGION:
           $this->xmlaOrdinal = 0x2002;
           break;

        case self::GEO_COUNTRY:
           $this->xmlaOrdinal = 0x2003;
           break;
        case self::GEO_STATE_OR_PROVINCE:
           $this->xmlaOrdinal = 0x2004;
           break;
        case self::GEO_COUNTY:
           $this->xmlaOrdinal = 0x2005;
           break;
        case self::GEO_CITY:
           $this->xmlaOrdinal = 0x2006;
           break;
        case self::GEO_POSTALCODE:
           $this->xmlaOrdinal = 0x2007;
           break;
        case self::GEO_POINT:
           $this->xmlaOrdinal = 0x2008;
           break;
        case self::ORG_UNIT:
           $this->xmlaOrdinal = 0x1011;
           break;
        case self::BOM_RESOURCE:
           $this->xmlaOrdinal = 0x1012;
           break;
        case self::QUANTITATIVE:
           $this->xmlaOrdinal = 0x1013;
           break;
        case self::ACCOUNT:
           $this->xmlaOrdinal = 0x1014;
           break;
        case self::CUSTOMER:
           $this->xmlaOrdinal = 0x1021;
           break;
        case self::CUSTOMER_GROUP:
           $this->xmlaOrdinal = 0x1022;
           break;
        case self::CUSTOMER_HOUSEHOLD:
           $this->xmlaOrdinal = 0x1023;
           break;
        case self::PRODUCT:
           $this->xmlaOrdinal = 0x1031;
           break;
        case self::PRODUCT_GROUP:
           $this->xmlaOrdinal = 0x1032;
           break;
        case self::SCENARIO:
           $this->xmlaOrdinal = 0x1015;
           break;
        case self::UTILITY:
           $this->xmlaOrdinal = 0x1016;
           break;
        case self::PERSON:
           $this->xmlaOrdinal = 0x1041;
           break;
        case self::COMPANY:
           $this->xmlaOrdinal = 0x1042;
           break;
        case self::CURRENCY_SOURCE:
           $this->xmlaOrdinal = 0x1051;
           break;
        case self::CURRENCY_DESTINATION:
           $this->xmlaOrdinal = 0x1052;
           break;
        case self::CHANNEL:
           $this->xmlaOrdinal = 0x1061;
           break;
        case self::REPRESENTATIVE:
           $this->xmlaOrdinal = 0x1062;
           break;
        case self::PROMOTION:
           $this->xmlaOrdinal = 0x1071;
           break;
        default:
           throw new InvalidArgumentException ( 'Unsupported level type' );
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
            new LevelType ( self::REGULAR ),
            new LevelType ( self::ALL ),
            new LevelType ( self::NULL ),
            new LevelType ( self::TIME_YEARS ),
            new LevelType ( self::TIME_HALF_YEAR ),
            new LevelType ( self::TIME_QUARTERS ),
            new LevelType ( self::TIME_MONTHS ),
            new LevelType ( self::TIME_WEEKS ),
            new LevelType ( self::TIME_DAYS ),
            new LevelType ( self::TIME_HOURS ),
            new LevelType ( self::TIME_MINUTES ),
            new LevelType ( self::TIME_SECONDS ),
            new LevelType ( self::TIME_UNDEFINED ),
            new LevelType ( self::GEO_CONTINENT ),
            new LevelType ( self::GEO_REGION ),
            new LevelType ( self::GEO_COUNTRY ),
            new LevelType ( self::GEO_STATE_OR_PROVINCE ),
            new LevelType ( self::GEO_COUNTY ),
            new LevelType ( self::GEO_CITY ),
            new LevelType ( self::GEO_POSTALCODE ),
            new LevelType ( self::GEO_POINT ),
            new LevelType ( self::ORG_UNIT ),
            new LevelType ( self::BOM_RESOURCE ),
            new LevelType ( self::QUANTITATIVE ),
            new LevelType ( self::ACCOUNT ),
            new LevelType ( self::CUSTOMER ),
            new LevelType ( self::CUSTOMER_GROUP ),
            new LevelType ( self::CUSTOMER_HOUSEHOLD ),
            new LevelType ( self::PRODUCT ),
            new LevelType ( self::PRODUCT_GROUP ),
            new LevelType ( self::SCENARIO ),
            new LevelType ( self::UTILITY ),
            new LevelType ( self::PERSON ),
            new LevelType ( self::COMPANY ),
            new LevelType ( self::CURRENCY_SOURCE ),
            new LevelType ( self::CURRENCY_DESTINATION ),
            new LevelType ( self::CHANNEL ),
            new LevelType ( self::REPRESENTATIVE ),
            new LevelType ( self::PROMOTION )
         );
      }

      return self::$constants;
   }

   
   public function xmlaName ( )
   {
      return 'MDLEVEL_TYPE_'.$this->constant;
   }

   /**
    * Human readable description of a Datatype instance.
    */
   public function getDescription ( )
   {
      return '';
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
         self::$dictionary = new Dictionary ( 'OLAP4PHP\Metadata\LevelType' );

      return self::$dictionary;
   }

   public function isTime ( )
   {
      switch ( $this->constant )
      {
         case self::TIME_YEARS:
         case self::TIME_HALF_YEAR:
         case self::TIME_QUARTERS:
         case self::TIME_MONTHS:
         case self::TIME_WEEKS:
         case self::TIME_DAYS:
         case self::TIME_HOURS:
         case self::TIME_MINUTES:
         case self::TIME_SECONDS:
         case self::TIME_UNDEFINED:
             return true;
         default:
             return false;
      }
   }
}