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
 *  Metadata supported standard member properties
 */
class StandardMemberProperty implements IProperty, IEnum
{
   // Enumeration constants
   const CATALOG_NAME          = 10;
   const SCHEMA_NAME           = 11;
   const CUBE_NAME             = 12;
   const DIMENSION_UNIQUE_NAME = 13;
   const HIERARCHY_UNIQUE_NAME = 14;
   const LEVEL_UNIQUE_NAME     = 15;
   const LEVEL_NUMBER          = 16;
   const MEMBER_ORDINAL        = 17;
   const MEMBER_NAME           = 18;
   const MEMBER_UNIQUE_NAME    = 19;
   const MEMBER_TYPE           = 20;
   const MEMBER_GUID           = 21;
   const MEMBER_CAPTION        = 22;
   const CHILDREN_CARDINALITY  = 23;
   const PARENT_LEVEL          = 24;
   const PARENT_UNIQUE_NAME    = 25;
   const PARENT_COUNT          = 26;
   const DESCRIPTION           = 27;
   const VISIBLE               = 28;
   const MEMBER_KEY            = 29;
   const IS_PLACEHOLDERMEMBER  = 30;
   const IS_DATAMEMBER         = 31;
   const VALUE                 = 41;
   const DEPTH                 = 43;
   const DISPLAY_INFO          = 44;

   // What constant do we rep ?
   private $constant;
   private $description;
   private $internal;
   private $name;
   private $type;
   
   private static $constants;
   
   /**
    * Constructor
    */
   protected function __construct ( $constant )
   {
      $this->constant = $constant;

      switch ( $constant )
      {
         /**
          * Definition of the property which
          * holds the name of the current catalog.
          */
         case self::CATALOG_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Optional. The name of the catalog to which this member belongs. ".
               "NULL if the provider does not support catalogs.";
            $this->name = 'CATALOG_NAME';
            break;

         /**
         * Definition of the property which
         * holds the name of the current schema.
         */
         case self::SCHEMA_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Optional. The name of the schema to which this member belongs. ".
               "NULL if the provider does not support schemas.";
            $this->name = 'SCHEMA_NAME';
            break;

         /**
         * Definition of the property which
         * holds the name of the current cube.
         */
         case self::CUBE_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Name of the cube to which this member belongs.";
            $this->name = 'CUBE_NAME';
            break;

         /**
         * Definition of the property which
         * holds the unique name of the current dimension.
         */
         case self::DIMENSION_UNIQUE_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Unique name of the dimension to which this member ".
               "belongs. For providers that generate unique names by ".
               "qualification, each component of this name is delimited.";
            $this->name = 'DIMENSION_UNIQUE_NAME';
            break;

         /**
         * Definition of the property which
         * holds the unique name of the current hierarchy.
         */
         case self::HIERARCHY_UNIQUE_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Unique name of the hierarchy. If the member belongs to ".
               "more than one hierarchy, there is one row for each hierarchy ".
               "to which it belongs. For providers that generate unique names ".
               "by qualification, each component of this name is delimited.";
            $this->name = 'HIERARCHY_UNIQUE_NAME';
            break;

         /**
         * Definition of the property which
         * holds the unique name of the current level.
         */
         case self::LEVEL_UNIQUE_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Unique name of the level to which the member belongs. ".
               "For providers that generate unique names by qualification, ".
               "each component of this name is delimited.";
            $this->name = 'LEVEL_UNIQUE_NAME';
            break;

         /**
         * Definition of the property which
         * holds the ordinal of the current level.
         */
         case self::LEVEL_NUMBER:
            $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
            $this->internal = false;
            $this->description =
               "Required. The distance of the member from the root of the ".
               "hierarchy. The root level is zero.";
            $this->name = 'LEVEL_NUMBER';
            break;

         /**
         * Definition of the property which
         * holds the ordinal of the current member.
         */
         case self::MEMBER_ORDINAL:
            $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
            $this->internal = false;
            $this->description =
               "Required. Ordinal number of the member. Sort rank of the member ".
               "when members of this dimension are sorted in their natural ".
               "sort order. If providers do not have the concept of natural ".
               "ordering, this should be the rank when sorted by MEMBER_NAME.";
            $this->name = 'MEMBER_ORDINAL';
            break;

         /**
         * Definition of the property which
         * holds the name of the current member.
         */
         case self::MEMBER_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Name of the member.";
            $this->name = 'MEMBER_NAME';
            break;

         /**
         * Definition of the property which
         * holds the unique name of the current member.
         */
         case self::MEMBER_UNIQUE_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Unique name of the member. For providers that generate ".
               "unique names by qualification, each component of this name is ".
               "delimited.";
            $this->name = 'MEMBER_UNIQUE_NAME';
            break;

         /**
         * Definition of the property which
         * holds the type of the member.
         */
         case self::MEMBER_TYPE:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Type of the member. Can be one of the following values: ".
               "MDMEMBER_Datatype.TYPE_REGULAR, MDMEMBER_Datatype.TYPE_ALL, ".
               "MDMEMBER_Datatype.TYPE_FORMULA, MDMEMBER_Datatype.TYPE_MEASURE, ".
               "MDMEMBER_Datatype.TYPE_UNKNOWN. MDMEMBER_Datatype.TYPE_FORMULA ".
               "takes precedence over MDMEMBER_Datatype.TYPE_MEASURE. ".
               "Therefore, if there is a formula (calculated) member on the ".
               "Measures dimension, it is listed as ".
               "MDMEMBER_Datatype.TYPE_FORMULA.";
            $this->name = 'MEMBER_TYPE';
            break;

         /**
         * Definition of the property which
         * holds the GUID of the member
         */
         case self::MEMBER_GUID:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Optional. Member GUID. NULL if no GUID exists.";
            $this->name = 'MEMBER_GUID';
            break;

         /**
         * Definition of the property which
         * holds the label or caption associated with the member, or the
         * member's name if no caption is defined.
         */
         case self::MEMBER_CAPTION:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. A label or caption associated with the member. Used ".
               "primarily for display purposes. If a caption does not exist, ".
               "MEMBER_NAME is returned.";
            $this->name = 'MEMBER_CAPTION';
            break;

         /**
         * Definition of the property which holds the
         * number of children this member has.
         */
         case self::CHILDREN_CARDINALITY:
            $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
            $this->internal = false;
            $this->description =
               "Required. Number of children that the member has. This can be an ".
               "estimate, so consumers should not rely on this to be the exact ".
               "count. Providers should return the best estimate possible.";
            $this->name = 'CHILDREN_CARDINALITY';
            break;

         /**
         * Definition of the property which holds the
         * distance from the root of the hierarchy of this member's parent.
         */
         case self::PARENT_LEVEL:
           $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
           $this->internal = false;
           "Required. The distance of the member's parent from the root level ".
           "of the hierarchy. The root level is zero.";
           $this->name = 'PARENT_LEVEL';
           break;

         /**
         * Definition of the property which holds the
         * Name of the current catalog.
         */
         case self::PARENT_UNIQUE_NAME:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description =
               "Required. Unique name of the member's parent. NULL is returned ".
               "for any members at the root level. For providers that generate ".
               "unique names by qualification, each component of this name is ".
               "delimited.";
            $this->name = 'PARENT_UNIQUE_NAME';
            break;

         /**
         * Definition of the property which holds the
         * number of parents that this member has. Generally 1, or 0
         * for root members.
         */
         case self::PARENT_COUNT:
            $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
            $this->internal = false;
            $this->description = 
               "Required. Number of parents that this member has.";
            $this->name = 'PARENT_COUNT';
            break;

         /**
         * Definition of the property which holds the
         * description of this member.
         */
         case self::DESCRIPTION:
            $this->type = Datatype::getEnum ( Datatype::STRING );
            $this->internal = false;
            $this->description = 
               "Optional. A human-readable description of the member.";
            $this->name = 'DESCRIPTION';
            break;

         /**
         * Definition of the internal property which holds the
         * name of the system property which determines whether to show a member
         * (especially a measure or calculated member) in a user interface such
         * as JPivot.
         */
         case self::VISIBLE:
            $this->type = Datatype::getEnum ( Datatype::BOOLEAN );
            $this->internal = true;
            $this->description = null;
            $this->name = 'VISIBLE';
            break;

         /**
         * Definition of the internal property which holds the
         * value of the member key in the original data type. MEMBER_KEY is for
         * backward-compatibility.  MEMBER_KEY has the same value as KEY0 for
         * non-composite keys, and MEMBER_KEY property is null for composite
         * keys.
         */
         case self::MEMBER_KEY:
            $this->type = Datatype::getEnum ( Datatype::VARIANT );
            $this->internal = true;
            $this->description =
               "Optional. The value of the member key. Null for composite keys.";
            $this->name = 'MEMBER_KEY';
            break;

         /**
         * Definition of the boolean property that indicates whether
         * a member is a placeholder member for an empty position in a
         * dimension hierarchy.
         */
         case self::IS_PLACEHOLDERMEMBER:
            $this->type = Datatype::getEnum ( Datatype::BOOLEAN );
            $this->internal = false;
            $this->description =
               "Required. Whether the member is a placeholder member for an empty ".
               "position in a dimension hierarchy.";
            $this->name = 'IS_PLACEHOLDERMEMBER';
            break;

         /**
         * Definition of the property that indicates whether the member is a
         * data member.
         */
         case self::IS_DATAMEMBER:
            $this->type = Datatype::getEnum ( Datatype::BOOLEAN );
            $this->internal = false;
            $this->description =
               "Required. whether the member is a data member";
            $this->name = 'IS_DATAMEMBER';
            break;

         /**
         * Definition of the property which
         * holds the level depth of a member.
         *
         * <p>Caution: Level depth of members in parent-child hierarchy isn't
         * from their levels.  It's calculated from the underlying data
         * dynamically.
         */
         case self::DEPTH:
            $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
            $this->internal = true;
            $this->description =
               "The level depth of a member";
            $this->name = 'DEPTH';
            break;

         /**
         * Definition of the property which
         * holds the DISPLAY_INFO required by XML/A.
         *
         * <p>Caution: This property's value is calculated based on a specified
         * MDX query, so its value is dynamic at runtime.
         */
         case self::DISPLAY_INFO:
            $this->type = Datatype::getEnum ( Datatype::UNSIGNED_INTEGER );
            $this->internal = false;
            $this->description =
               "Display instruction of a member for XML/A";
            $this->name = 'DISPLAY_INFO';
            break;

         /**
         * Definition of the property which
         * holds the value of a cell. Is usually numeric (since most measures
         * are numeric) but is occasionally another type.
         */
         case self::VALUE:
            $this->type = Datatype::getEnum ( Datatype::VARIANT );
            $this->internal = false;
            $this->description =
               "The unformatted value of the cell.";
            $this->name = 'VALUE';
            break;

         default:
            throw new InvalidArgumentException ( 'Unsupported standard member property.' );
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
            new StandardMemberProperty ( self::CATALOG_NAME ),
            new StandardMemberProperty ( self::SCHEMA_NAME ),
            new StandardMemberProperty ( self::CUBE_NAME ),
            new StandardMemberProperty ( self::DIMENSION_UNIQUE_NAME ),
            new StandardMemberProperty ( self::HIERARCHY_UNIQUE_NAME ),
            new StandardMemberProperty ( self::LEVEL_UNIQUE_NAME ),
            new StandardMemberProperty ( self::LEVEL_NUMBER ),
            new StandardMemberProperty ( self::MEMBER_ORDINAL ),
            new StandardMemberProperty ( self::MEMBER_NAME ),
            new StandardMemberProperty ( self::MEMBER_UNIQUE_NAME ),
            new StandardMemberProperty ( self::MEMBER_TYPE ),
            new StandardMemberProperty ( self::MEMBER_GUID ),
            new StandardMemberProperty ( self::MEMBER_CAPTION ),
            new StandardMemberProperty ( self::CHILDREN_CARDINALITY ),
            new StandardMemberProperty ( self::PARENT_LEVEL ),
            new StandardMemberProperty ( self::PARENT_UNIQUE_NAME ),
            new StandardMemberProperty ( self::PARENT_COUNT ),
            new StandardMemberProperty ( self::DESCRIPTION ),
            new StandardMemberProperty ( self::VISIBLE ),
            new StandardMemberProperty ( self::MEMBER_KEY ),
            new StandardMemberProperty ( self::IS_PLACEHOLDERMEMBER ),
            new StandardMemberProperty ( self::IS_DATAMEMBER ),
            new StandardMemberProperty ( self::VALUE ),
            new StandardMemberProperty ( self::DEPTH ),
            new StandardMemberProperty ( self::DISPLAY_INFO )
         );
      }

      return self::$constants;
   }

   /**
    * return string human readable description of the member property
    */
   public function getDescription ( )
   {
      return $this->description;
   }

   /**
    * return string
    */
   public function name ( )
   {
      return $this->name;
   }

   /**
    * @return ContentType
    */
   public function getContentType ( )
   {
   }

   /**
    * @return DataType
    */
   public function getDataType ( )
   {
   }

   /**
    * @return string
    */
   public function getCaption ( )
   {
   }

   /**
    * @return string
    */
   public function getUniqueName ( )
   {
   }


   /**
    * @return boolean
    */
   public function isVisible ( )
   {
      
   }

}