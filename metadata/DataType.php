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

/**
 *  Metadata Model supported datatypes
 */
class DataType implements IXMLAConstant, IEnum
{
   // Enumeration constants
   const INTEGER           = 0;
   const DOUBLE            = 1;
   const CURRENCY          = 2;
   const BOOLEAN           = 3;
   const VARIANT           = 4;
   const UNSIGNED_SHORT    = 5;
   const UNSIGNED_INTEGER  = 6;
   const LARGE_INTEGER     = 7;
   const STRING            = 8;
   const ACCP              = 9;
   const CHAR              = 10;
   const CUKY              = 11;
   const CURR              = 12;
   const DATS              = 13;
   const DEC               = 14;
   const FLTP              = 15;
   const INT1              = 16;
   const INT2              = 17;
   const INT4              = 18;
   const LCHR              = 19;
   const NUMC              = 20;
   const PREC              = 21;
   const QUAN              = 22;
   const SSTR              = 23;
   const STRG              = 24;
   const TIMS              = 25;
   const VARC              = 26;
   const UNIT              = 27;

   // What constant do we rep ?
   private $constant;
   private $dbTypeIndicator;
   private $description;
   private $name;
   private $xmlaOrdinal;

   private static $constants;
   private static $dictionary;

   /**
    * Constructor
    *
    * @param $constant
    */
   protected function __construct ( $constant )
   {
      $this->constant = $constant;

      switch ( $constant )
      {
         /*
          * The following values exactly match VARENUM
          * in Automation and may be used in VARIANT.
          */
         case self::INTEGER:
            $this->xmlaOrdinal = 3;
            $this->dbTypeIndicator = 'DBTYPE_I4';
            $this->description = 'A four-byte, signed integer: INTEGER';
            $this->name = 'INTEGER';
            break;
         case self::DOUBLE:
            $this->xmlaOrdinal = 5;
            $this->dbTypeIndicator = 'DBTYPE_R8';
            $this->description = 'A double-precision floating-point value: Double';
            $this->name = 'DOUBLE';
            break;
         case self::CURRENCY:
            $this->xmlaOrdinal = 6;
            $this->dbTypeIndicator = 'DBTYPE_CY';
            $this->description = 'A currency value: LARGE_INTEGER, Currency is a fixed-point number with '.
                                 'four digits to the right of the decimal point. It is stored in an '.
                                 'eight-byte signed integer, scaled by 10,000.';
            $this->name = 'CURRENCY';
            break;
         case self::BOOLEAN:
            $this->xmlaOrdinal = 11;
            $this->dbTypeIndicator = 'DBTYPE_BOOL';
            $this->description = 'A Boolean value stored in the same way as in Automation: VARIANT_BOOL; '.
                                 '0 means false and ~0 (bitwise, the value is not 0; that is, all bits '.
                                 'are set to 1) means true.';
            $this->name = 'BOOLEAN';
            break;
         /**
          * Used by SQL Server for value.
          */
         case self::VARIANT:
            $this->xmlaOrdinal = 12;
            $this->dbTypeIndicator = 'DBTYPE_VARIANT';
            $this->description = 'An Automation VARIANT';
            $this->name = 'VARIANT';
            break;
         /**
          * Used by SQL Server for font size.
          */
         case self::UNSIGNED_SHORT:
            $this->xmlaOrdinal = 18;
            $this->dbTypeIndicator = 'DBTYPE_UI2';
            $this->description = 'A two-byte, unsigned integer';
            $this->name = 'UNSIGNED_SHORT';
            break;
         /**
          * Used by SQL Server for colors, font flags and cell ordinal.
          */
         case self::UNSIGNED_INTEGER:
            $this->xmlaOrdinal = 19;
            $this->dbTypeIndicator = 'DBTYPE_UI4';
            $this->description = 'A four-byte, unsigned integer';
            $this->name = 'UNSIGNED_INTEGER';
            break;
         /*
          * The following values exactly match VARENUM
          * in Automation but cannot be used in VARIANT.
          */
         case self::LARGE_INTEGER:
            $this->xmlaOrdinal = 20;
            $this->dbTypeIndicator = 'DBTYPE_I8';
            $this->description = 'An eight-byte, signed integer: LARGE_INTEGER';
            $this->name = 'LARGE_INTEGER';
            break;
         /*
          * The following values are not in VARENUM in OLE.
          */
         case self::STRING:
            $this->xmlaOrdinal = 130;
            $this->dbTypeIndicator = 'DBTYPE_WSTR';
            $this->description = 'A null-terminated Unicode character string: wchar_t[length]; If '.
                                 'DBTYPE_WSTR is used by itself, the number of bytes allocated '.
                                 'for the string, including the null-termination character, is '.
                                 'specified by cbMaxLen in the DBBINDING structure. If '.
                                 'DBTYPE_WSTR is combined with DBTYPE_BYREF, the number of bytes '.
                                 'allocated for the string, including the null-termination character, '.
                                 'is at least the length of the string plus two. In either case, the '.
                                 'actual length of the string is determined from the bound length '.
                                 'value. The maximum length of the string is the number of allocated '.
                                 'bytes divided by sizeof(wchar_t) and truncated to the nearest '.
                                 'integer.';
            $this->name = 'STRING';
            break;
         /**
          * Used by SAP BW. Represents a Character
          */
         case self::ACCP:
            $this->xmlaOrdinal = 1000;
            $this->dbTypeIndicator = 'ACCP';
            $this->description = 'SAP BW Character';
            $this->name = 'ACCP';
            break;
         /**
          * Used by SAP BW. Represents a CHAR
          */
         case self::CHAR:
            $this->xmlaOrdinal = 1001;
            $this->dbTypeIndicator = 'CHAR';
            $this->description = 'SAP BW CHAR';
            $this->name = 'CHAR';
            break;
         /**
          * Used by SAP BW. Represents a CHAR
          */
         case self::CUKY:
            $this->xmlaOrdinal = 1002;
            $this->dbTypeIndicator = 'CUKY';
            $this->description = 'SAP BW CHAR';
            $this->name = 'CUKY';
            break;
         /**
          * Used by SAP BW. Represents a Currency - Packed decimal, Integer
          */
         case self::CURR:
            $this->xmlaOrdinal = 1003;
            $this->dbTypeIndicator = 'CURR';
            $this->description = 'SAP BW Currency - Packed decimal, Integer';
            $this->name = 'CURR';
            break;
         /**
          * Used by SAP BW. Represents a Date
          */
         case self::DATS:
            $this->xmlaOrdinal = 1004;
            $this->dbTypeIndicator = 'DATS';
            $this->description = 'SAP BW Date';
            $this->name = 'DATS';
            break;
         /**
          * Used by SAP BW. Represents a Decimal
          */
         case self::DEC:
            $this->xmlaOrdinal = 1005;
            $this->dbTypeIndicator = 'DEC';
            $this->description = 'SAP BW Decimal';
            $this->name = 'DEC';
            break;
         /**
          * Used by SAP BW. Represents a Point
          */
         case self::FLTP:
            $this->xmlaOrdinal = 1006;
            $this->dbTypeIndicator = 'FLTP';
            $this->description = 'SAP BW Floating Point';
            $this->name = 'FLTP';
            break;
         /**
          * Used by SAP BW. Represents a Byte
          */
         case self::INT1:
            $this->xmlaOrdinal = 1007;
            $this->dbTypeIndicator = 'INT1';
            $this->description = 'SAP BW Byte';
            $this->name = 'INT1';
            break;
         /**
          * Used by SAP BW. Represents a Small integer
          */
         case self::INT2:
            $this->xmlaOrdinal = 1008;
            $this->dbTypeIndicator = 'INT2';
            $this->description = 'SAP BW Small integer';
            $this->name = 'INT2';
            break;
         /**
          * Used by SAP BW. Represents an Integer
          */
         case self::INT4:
            $this->xmlaOrdinal = 1009;
            $this->dbTypeIndicator = 'INT4';
            $this->description = 'SAP BW Integer';
            $this->name = 'INT4';
            break;
         /**
          * Used by SAP BW. Represents a Text
          */
         case self::LCHR:
            $this->xmlaOrdinal = 1010;
            $this->dbTypeIndicator = 'LCHR';
            $this->description = 'SAP BW Text';
            $this->name = 'LCHR';
            break;
         /**
          * Used by SAP BW. Represents a Numeric
          */
         case self::NUMC:
            $this->xmlaOrdinal = 1011;
            $this->dbTypeIndicator = 'NUMC';
            $this->description = 'SAP BW Numeric';
            $this->name = 'NUMC';
            break;
         /**
          * Used by SAP BW. Represents a Tiny Int
          */
         case self::PREC:
            $this->xmlaOrdinal = 1012;
            $this->dbTypeIndicator = 'PREC';
            $this->description = 'SAP BW Tiny Int';
            $this->name = 'PREC';
            break;
         /**
          * Used by SAP BW. Represents a QUAN Integer
          */
         case self::QUAN:
            $this->xmlaOrdinal = 1013;
            $this->dbTypeIndicator = 'QUAN';
            $this->description = 'SAP BW QUAN Integer';
            $this->name = 'QUAN';
            break;
         /**
          * Used by SAP BW. Represents a String
          */
         case self::SSTR:
            $this->xmlaOrdinal = 1014;
            $this->dbTypeIndicator = 'SSTR';
            $this->description = 'SAP BW String';
            $this->name = 'SSTR';
            break;
         /**
          * Used by SAP BW. Represents a Long String
          */
         case self::STRG:
            $this->xmlaOrdinal = 1015;
            $this->dbTypeIndicator = 'STRG';
            $this->description = 'SAP BW Long String';
            $this->name = 'STRG';
            break;
         /**
          * Used by SAP BW. Represents a Time
          */
         case self::TIMS:
            $this->xmlaOrdinal = 1016;
            $this->dbTypeIndicator = 'TIMS';
            $this->description = 'SAP BW Time';
            $this->name = 'TIMS';
            break;
         /**
          * Used by SAP BW. Represents a Varchar
          */
         case self::VARC:
            $this->xmlaOrdinal = 1017;
            $this->dbTypeIndicator = 'VARC';
            $this->description = 'SAP BW Varchar';
            $this->name = 'VARC';
            break;
         /**
          * Used by SAP BW. Represents a Long String for Units
          */
         case self::UNIT:
            $this->xmlaOrdinal = 1018;
            $this->dbTypeIndicator = 'UNIT';
            $this->description = 'SAP BW Long String for Units';
            $this->name = 'UNIT';
            break;
         default:
            throw new InvalidArgumentException ( 'Unsupported data type' );
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
    * return array Return the datatype enumeration constants
    */
   static public function getEnumConstants ( )
   {
      if ( !self::$constants )
      {
         // array of enums constants
         self::$constants = array (
            new DataType ( self::INTEGER ),
            new DataType ( self::DOUBLE ),
            new DataType ( self::CURRENCY ),
            new DataType ( self::BOOLEAN ),
            new DataType ( self::VARIANT ),
            new DataType ( self::UNSIGNED_SHORT ),
            new DataType ( self::UNSIGNED_INTEGER ),
            new DataType ( self::LARGE_INTEGER ),
            new DataType ( self::STRING ),
            new DataType ( self::ACCP ),
            new DataType ( self::CHAR ),
            new DataType ( self::CUKY ),
            new DataType ( self::CURR ),
            new DataType ( self::DATS ),
            new DataType ( self::DEC ),
            new DataType ( self::FLTP ),
            new DataType ( self::INT1 ),
            new DataType ( self::INT2 ),
            new DataType ( self::INT4 ),
            new DataType ( self::LCHR ),
            new DataType ( self::NUMC ),
            new DataType ( self::PREC ),
            new DataType ( self::QUAN ),
            new DataType ( self::SSTR ),
            new DataType ( self::STRG ),
            new DataType ( self::TIMS ),
            new DataType ( self::VARC ),
            new DataType ( self::UNIT )
         );
      }

      return self::$constants;
   }

   public function name ( )
   {
      return $this->name;
   }

   public function xmlaName ( )
   {
      return $this->dbTypeIndicator;
   }

   /**
    * Human readable description of a Datatype instance.
    */
   public function getDescription ( )
   {
      return $this->description;
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
         self::$dictionary = new Dictionary ( 'OLAP4PHP\Metadata\DataType' );

      return self::$dictionary;
   }
}