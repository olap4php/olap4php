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
 *  Metadata supported property content types
 */
class PropertyContentType implements IXMLAConstant
{
   // Enumeration constants
   const REGULAR                   = 0;
   const ID                        = 1;
   const RELATION_TO_PARENT        = 2;
   const ROLLUP_OPERATOR           = 3;
   const ORG_TITLE                 = 4;
   const CAPTION                   = 5;
   const CAPTION_SHORT             = 6;
   const CAPTION_DESCRIPTION       = 7;
   const CAPTION_ABREVIATION       = 8;
   const WEB_URL                   = 9;
   const WEB_HTML                  = 10;
   const WEB_XML_OR_XSL            = 11;
   const WEB_MAIL_ALIAS            = 12;
   const ADDRESS                   = 13;
   const ADDRESS_STREET            = 14;
   const ADDRESS_HOUSE             = 15;
   const ADDRESS_CITY              = 16;
   const ADDRESS_STATE_OR_PROVINCE = 17;
   const ADDRESS_ZIP               = 18;
   const ADDRESS_QUARTER           = 19;
   const ADDRESS_COUNTRY           = 20;
   const ADDRESS_BUILDING          = 21;
   const ADDRESS_ROOM              = 22;
   const ADDRESS_FLOOR             = 23;
   const ADDRESS_FAX               = 24;
   const ADDRESS_PHONE             = 25;
   const GEO_CENTROID_X            = 26;
   const GEO_CENTROID_Y            = 27;
   const GEO_CENTROID_Z            = 28;
   const GEO_BOUNDARY_TOP          = 29;
   const GEO_BOUNDARY_LEFT         = 30;
   const GEO_BOUNDARY_BOTTOM       = 31;
   const GEO_BOUNDARY_RIGHT        = 32;
   const GEO_BOUNDARY_FRONT        = 33;
   const GEO_BOUNDARY_REAR         = 34;
   const GEO_BOUNDARY_POLYGON      = 35;
   const PHYSICAL_SIZE             = 36;
   const PHYSICAL_COLOR            = 37;
   const PHYSICAL_WEIGHT           = 38;
   const PHYSICAL_HEIGHT           = 39;
   const PHYSICAL_WIDTH            = 40;
   const PHYSICAL_DEPTH            = 41;
   const PHYSICAL_VOLUME           = 42;
   const PHYSICAL_DENSITY          = 43;
   const PERSON_FULL_NAME          = 44;
   const PERSON_FIRST_NAME         = 45;
   const PERSON_LAST_NAME          = 46;
   const PERSON_MIDDLE_NAME        = 47;
   const PERSON_DEMOGRAPHIC        = 48;
   const PERSON_CONTACT            = 49;
   const QTY_RANGE_LOW             = 50;
   const QTY_RANGE_HIGH            = 51;
   const FORMATTING_COLOR          = 52;
   const FORMATTING_ORDER          = 53;
   const FORMATTING_FONT           = 54;
   const FORMATTING_FONT_EFFECTS   = 55;
   const FORMATTING_FONT_SIZE      = 56;
   const FORMATTING_SUB_TOTAL      = 57;
   const DATE                      = 58;
   const DATE_START                = 59;
   const DATE_ENDED                = 60;
   const DATE_CANCELED             = 61;
   const DATE_MODIFIED             = 62;
   const DATE_DURATION             = 63;
   const VERSION                   = 64;
   const TIME_YEARS                = 65;
   const TIME_HALF_YEAR            = 66;
   const TIME_QUARTERS             = 67;
   const TIME_MONTHS               = 68;
   const TIME_WEEKS                = 69;
   const TIME_DAYS                 = 70;
   const TIME_HOURS                = 71;
   const TIME_MINUTES              = 72;
   const TIME_SECONDS              = 73;
   const TIME_UNDEFINED            = 74;


   // What constant do we rep ?
   private $constant;
   private $xmlaOrdinal;
   private $name;

   private static $constants;
   private static $dictionary;

   /**
    * Constructor
    */
   protected function __construct( $constant )
   {
      $this->constant = $constant;

      switch ( $constant )
      {
         case self::REGULAR:
            $this->xmlaOrdinal = 0x00;
            $this->name        = 'REGULAR';
            break;
         case self::ID:
            $this->xmlaOrdinal = 0x01;
            $this->name        = 'ID';
            break;
         case self::RELATION_TO_PARENT:
            $this->xmlaOrdinal = 0x02;
            $this->name        = 'RELATION_TO_PARENT';
            break;
         case self::ROLLUP_OPERATOR:
            $this->xmlaOrdinal = 0x03;
            $this->name        = 'ROLLUP_OPERATOR';
            break;
         case self::ORG_TITLE:
            $this->xmlaOrdinal = 0x11;
            $this->name        = 'ORG_TITLE';
            break;
         case self::CAPTION:
            $this->xmlaOrdinal = 0x21;
            $this->name        = 'CAPTION';
            break;
         case self::CAPTION_SHORT:
            $this->xmlaOrdinal = 0x22;
            $this->name        = 'CAPTION_SHORT';
            break;
         case self::CAPTION_DESCRIPTION:
            $this->xmlaOrdinal = 0x23;
            $this->name        = 'CAPTION_DESCRIPTION';
            break;
         case self::CAPTION_ABREVIATION:
            $this->xmlaOrdinal = 0x24;
            $this->name        = 'CAPTION_ABREVIATION';
            break;
         case self::WEB_URL:
            $this->xmlaOrdinal = 0x31;
            $this->name        = 'WEB_URL';
            break;
         case self::WEB_HTML:
            $this->xmlaOrdinal = 0x32;
            $this->name        = 'WEB_HTML';
            break;
         case self::WEB_XML_OR_XSL:
            $this->xmlaOrdinal = 0x33;
            $this->name        = 'WEB_XML_OR_XSL';
            break;
         case self::WEB_MAIL_ALIAS:
            $this->xmlaOrdinal = 0x34;
            $this->name        = 'WEB_MAIL_ALIAS';
            break;
         case self::ADDRESS:
            $this->xmlaOrdinal = 0x41;
            $this->name        = 'ADDRESS';
            break;
         case self::ADDRESS_STREET:
            $this->xmlaOrdinal = 0x42;
            $this->name        = 'ADDRESS_STREET';
            break;
         case self::ADDRESS_HOUSE:
            $this->xmlaOrdinal = 0x43;
            $this->name        = 'ADDRESS_HOUSE';
            break;
         case self::ADDRESS_CITY:
            $this->xmlaOrdinal = 0x44;
            $this->name        = 'ADDRESS_CITY';
            break;
         case self::ADDRESS_STATE_OR_PROVINCE:
            $this->xmlaOrdinal = 0x45;
            $this->name        = 'ADDRESS_STATE_OR_PROVINCE';
            break;
         case self::ADDRESS_ZIP:
            $this->xmlaOrdinal = 0x46;
            $this->name        = 'ADDRESS_ZIP';
            break;
         case self::ADDRESS_QUARTER:
            $this->xmlaOrdinal = 0x47;
            $this->name        = 'ADDRESS_QUARTER';
            break;
         case self::ADDRESS_COUNTRY:
            $this->xmlaOrdinal = 0x48;
            $this->name        = 'ADDRESS_COUNTRY';
            break;
         case self::ADDRESS_BUILDING:
            $this->xmlaOrdinal = 0x49;
            $this->name        = 'ADDRESS_BUILDING';
            break;
         case self::ADDRESS_ROOM:
            $this->xmlaOrdinal = 0x4A;
            $this->name        = 'ADDRESS_ROOM';
            break;
         case self::ADDRESS_FLOOR:
            $this->xmlaOrdinal = 0x4B;
            $this->name        = 'ADDRESS_FLOOR';
            break;
         case self::ADDRESS_FAX:
            $this->xmlaOrdinal = 0x4C;
            $this->name        = 'ADDRESS_FAX';
            break;
         case self::ADDRESS_PHONE:
            $this->xmlaOrdinal = 0x4D;
            $this->name        = 'ADDRESS_PHONE';
            break;
         case self::GEO_CENTROID_X:
            $this->xmlaOrdinal = 0x61;
            $this->name        = 'GEO_CENTROID_X';
            break;
         case self::GEO_CENTROID_Y:
            $this->xmlaOrdinal = 0x62;
            $this->name        = 'GEO_CENTROID_Y';
            break;
         case self::GEO_CENTROID_Z:
            $this->xmlaOrdinal = 0x63;
            $this->name        = 'GEO_CENTROID_Z';
            break;
         case self::GEO_BOUNDARY_TOP:
            $this->xmlaOrdinal = 0x64;
            $this->name        = 'GEO_BOUNDARY_TOP';
            break;
         case self::GEO_BOUNDARY_LEFT:
            $this->xmlaOrdinal = 0x65;
            $this->name        = 'GEO_BOUNDARY_LEFT';
            break;
         case self::GEO_BOUNDARY_BOTTOM:
            $this->xmlaOrdinal = 0x66;
            $this->name        = 'GEO_BOUNDARY_BOTTOM';
            break;
         case self::GEO_BOUNDARY_RIGHT:
            $this->xmlaOrdinal = 0x67;
            $this->name        = 'GEO_BOUNDARY_RIGHT';
            break;
         case self::GEO_BOUNDARY_FRONT:
            $this->xmlaOrdinal = 0x68;
            $this->name        = 'GEO_BOUNDARY_FRONT';
            break;
         case self::GEO_BOUNDARY_REAR:
            $this->xmlaOrdinal = 0x69;
            $this->name        = 'GEO_BOUNDARY_REAR';
            break;
         case self::GEO_BOUNDARY_POLYGON:
            $this->xmlaOrdinal = 0x6A;
            $this->name        = 'GEO_BOUNDARY_POLYGON';
            break;
         case self::PHYSICAL_SIZE:
            $this->xmlaOrdinal = 0x71;
            $this->name        = 'PHYSICAL_SIZE';
            break;
         case self::PHYSICAL_COLOR:
            $this->xmlaOrdinal = 0x72;
            $this->name        = 'PHYSICAL_COLOR';
            break;
         case self::PHYSICAL_WEIGHT:
            $this->xmlaOrdinal = 0x73;
            $this->name        = 'PHYSICAL_WEIGHT';
            break;
         case self::PHYSICAL_HEIGHT:
            $this->xmlaOrdinal = 0x74;
            $this->name        = 'PHYSICAL_HEIGHT';
            break;
         case self::PHYSICAL_WIDTH:
            $this->xmlaOrdinal = 0x75;
            $this->name        = 'PHYSICAL_WIDTH';
            break;
         case self::PHYSICAL_DEPTH:
            $this->xmlaOrdinal = 0x76;
            $this->name        = 'PHYSICAL_DEPTH';
            break;
         case self::PHYSICAL_VOLUME:
            $this->xmlaOrdinal = 0x77;
            $this->name        = 'PHYSICAL_VOLUME';
            break;
         case self::PHYSICAL_DENSITY:
            $this->xmlaOrdinal = 0x78;
            $this->name        = 'PHYSICAL_DENSITY';
            break;
         case self::PERSON_FULL_NAME:
            $this->xmlaOrdinal = 0x82;
            $this->name        = 'PERSON_FULL_NAME';
            break;
         case self::PERSON_FIRST_NAME:
            $this->xmlaOrdinal = 0x83;
            $this->name        = 'PERSON_FIRST_NAME';
            break;
         case self::PERSON_LAST_NAME:
            $this->xmlaOrdinal = 0x84;
            $this->name        = 'PERSON_LAST_NAME';
            break;
         case self::PERSON_MIDDLE_NAME:
            $this->xmlaOrdinal = 0x85;
            $this->name        = 'PERSON_MIDDLE_NAME';
            break;
         case self::PERSON_DEMOGRAPHIC:
            $this->xmlaOrdinal = 0x86;
            $this->name        = 'PERSON_DEMOGRAPHIC';
            break;
         case self::PERSON_CONTACT:
            $this->xmlaOrdinal = 0x87;
            $this->name        = 'PERSON_CONTACT';
            break;
         case self::QTY_RANGE_LOW:
            $this->xmlaOrdinal = 0x91;
            $this->name        = 'QTY_RANGE_LOW';
            break;
         case self::QTY_RANGE_HIGH:
            $this->xmlaOrdinal = 0x92;
            $this->name        = 'QTY_RANGE_HIGH';
            break;
         case self::FORMATTING_COLOR:
            $this->xmlaOrdinal = 0xA1;
            $this->name        = 'FORMATTING_COLOR';
            break;
         case self::FORMATTING_ORDER:
            $this->xmlaOrdinal = 0xA2;
            $this->name        = 'FORMATTING_ORDER';
            break;
         case self::FORMATTING_FONT:
            $this->xmlaOrdinal = 0xA3;
            $this->name        = 'FORMATTING_FONT';
            break;
         case self::FORMATTING_FONT_EFFECTS:
            $this->xmlaOrdinal = 0xA4;
            $this->name        = 'FORMATTING_FONT_EFFECTS';
            break;
         case self::FORMATTING_FONT_SIZE:
            $this->xmlaOrdinal = 0xA5;
            $this->name        = 'FORMATTING_FONT_SIZE';
            break;
         case self::FORMATTING_SUB_TOTAL:
            $this->xmlaOrdinal = 0xA6;
            $this->name        = 'FORMATTING_SUB_TOTAL';
            break;
         case self::DATE:
            $this->xmlaOrdinal = 0xB1;
            $this->name        = 'DATE';
            break;
         case self::DATE_START:
            $this->xmlaOrdinal = 0xB2;
            $this->name        = 'DATE_START';
            break;
         case self::DATE_ENDED:
            $this->xmlaOrdinal = 0xB3;
            $this->name        = 'DATE_ENDED';
            break;
         case self::DATE_CANCELED:
            $this->xmlaOrdinal = 0xB4;
            $this->name        = 'DATE_CANCELED';
            break;
         case self::DATE_MODIFIED:
            $this->xmlaOrdinal = 0xB5;
            $this->name        = 'DATE_MODIFIED';
            break;
         case self::DATE_DURATION:
            $this->xmlaOrdinal = 0xB6;
            $this->name        = 'DATE_DURATION';
            break;
         case self::VERSION:
            $this->xmlaOrdinal = 0xC1;
            $this->name        = 'VERSION';
            break;
         default:
            throw new InvalidArgumentException ('Unsupported property content type');
      }
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
            new PropertyContentType (self::REGULAR),
            new PropertyContentType (self::ID),
            new PropertyContentType (self::RELATION_TO_PARENT),
            new PropertyContentType (self::ROLLUP_OPERATOR),
            new PropertyContentType (self::ORG_TITLE),
            new PropertyContentType (self::CAPTION),
            new PropertyContentType (self::CAPTION_SHORT),
            new PropertyContentType (self::CAPTION_DESCRIPTION),
            new PropertyContentType (self::CAPTION_ABREVIATION),
            new PropertyContentType (self::WEB_URL),
            new PropertyContentType (self::WEB_HTML),
            new PropertyContentType (self::WEB_XML_OR_XSL),
            new PropertyContentType (self::WEB_MAIL_ALIAS),
            new PropertyContentType (self::ADDRESS),
            new PropertyContentType (self::ADDRESS_STREET),
            new PropertyContentType (self::ADDRESS_HOUSE),
            new PropertyContentType (self::ADDRESS_CITY),
            new PropertyContentType (self::ADDRESS_STATE_OR_PROVINCE),
            new PropertyContentType (self::ADDRESS_ZIP),
            new PropertyContentType (self::ADDRESS_QUARTER),
            new PropertyContentType (self::ADDRESS_COUNTRY),
            new PropertyContentType (self::ADDRESS_BUILDING),
            new PropertyContentType (self::ADDRESS_ROOM),
            new PropertyContentType (self::ADDRESS_FLOOR),
            new PropertyContentType (self::ADDRESS_FAX),
            new PropertyContentType (self::ADDRESS_PHONE),
            new PropertyContentType (self::GEO_CENTROID_X),
            new PropertyContentType (self::GEO_CENTROID_Y),
            new PropertyContentType (self::GEO_CENTROID_Z),
            new PropertyContentType (self::GEO_BOUNDARY_TOP),
            new PropertyContentType (self::GEO_BOUNDARY_LEFT),
            new PropertyContentType (self::GEO_BOUNDARY_BOTTOM),
            new PropertyContentType (self::GEO_BOUNDARY_RIGHT),
            new PropertyContentType (self::GEO_BOUNDARY_FRONT),
            new PropertyContentType (self::GEO_BOUNDARY_REAR),
            new PropertyContentType (self::GEO_BOUNDARY_POLYGON),
            new PropertyContentType (self::PHYSICAL_SIZE),
            new PropertyContentType (self::PHYSICAL_COLOR),
            new PropertyContentType (self::PHYSICAL_WEIGHT),
            new PropertyContentType (self::PHYSICAL_HEIGHT),
            new PropertyContentType (self::PHYSICAL_WIDTH),
            new PropertyContentType (self::PHYSICAL_DEPTH),
            new PropertyContentType (self::PHYSICAL_VOLUME),
            new PropertyContentType (self::PHYSICAL_DENSITY),
            new PropertyContentType (self::PERSON_FULL_NAME),
            new PropertyContentType (self::PERSON_FIRST_NAME),
            new PropertyContentType (self::PERSON_LAST_NAME),
            new PropertyContentType (self::PERSON_MIDDLE_NAME),
            new PropertyContentType (self::PERSON_DEMOGRAPHIC),
            new PropertyContentType (self::PERSON_CONTACT),
            new PropertyContentType (self::QTY_RANGE_LOW),
            new PropertyContentType (self::QTY_RANGE_HIGH),
            new PropertyContentType (self::FORMATTING_COLOR),
            new PropertyContentType (self::FORMATTING_ORDER),
            new PropertyContentType (self::FORMATTING_FONT),
            new PropertyContentType (self::FORMATTING_FONT_EFFECTS),
            new PropertyContentType (self::FORMATTING_FONT_SIZE),
            new PropertyContentType (self::FORMATTING_SUB_TOTAL),
            new PropertyContentType (self::DATE),
            new PropertyContentType (self::DATE_START),
            new PropertyContentType (self::DATE_ENDED),
            new PropertyContentType (self::DATE_CANCELED),
            new PropertyContentType (self::DATE_MODIFIED),
            new PropertyContentType (self::DATE_DURATION),
            new PropertyContentType (self::VERSION)
         );
      }

      return self::$constants;
   }

   public function xmlaName()
   {
      return 'MD_PROPTYPE_' . $this->constant;
   }

   /**
    * Human readable description of a Datatype instance.
    */
   public function getDescription()
   {
      return null;
   }

   /**
    * Unique identifier of a Datatype instance.
    */
   public function xmlaOrdinal()
   {
      return $this->xmlaOrdinal;
   }

   /**
    * @return Dictionary of all values
    */
   static public function getDictionary()
   {
      if ( !self::$dictionary )
      {
         self::$dictionary = new Dictionary ('OLAP4PHP\Metadata\PropertyContentType');
      }

      return self::$dictionary;
   }

   public function isTime()
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
