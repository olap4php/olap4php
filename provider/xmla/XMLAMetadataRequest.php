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
use OLAP4PHP\Provider\XMLA\XMLAMetadataColumn;

class XMLAMetadataRequest
{
   const DISCOVER_DATASOURCES    = 'DISCOVER_DATASOURCES';
   const DISCOVER_SCHEMA_ROWSETS = 'DISCOVER_SCHEMA_ROWSETS';
   const DISCOVER_ENUMERATORS    = 'DISCOVER_ENUMERATORS';
   const DISCOVER_PROPERTIES     = 'DISCOVER_PROPERTIES';
   const DISCOVER_KEYWORDS       = 'DISCOVER_KEYWORDS';
   const DISCOVER_LITERALS       = 'DISCOVER_LITERALS';
   const DBSCHEMA_CATALOGS       = 'DBSCHEMA_CATALOGS';
   const DBSCHEMA_COLUMNS        = 'DBSCHEMA_COLUMNS';
   const DBSCHEMA_PROVIDER_TYPES = 'DBSCHEMA_PROVIDER_TYPES';
   const DBSCHEMA_TABLES         = 'DBSCHEMA_TABLES';
   const DBSCHEMA_TABLES_INFO    = 'DBSCHEMA_TABLES_INFO';
   const DBSCHEMA_SCHEMATA       = 'DBSCHEMA_SCHEMATA';
   const MDSCHEMA_ACTIONS        = 'MDSCHEMA_ACTIONS';
   const MDSCHEMA_CUBES          = 'MDSCHEMA_CUBES';
   const MDSCHEMA_DIMENSIONS     = 'MDSCHEMA_DIMENSIONS';
   const MDSCHEMA_FUNCTIONS      = 'MDSCHEMA_FUNCTIONS';
   const MDSCHEMA_HIERARCHIES    = 'MDSCHEMA_HIERARCHIES';
   const MDSCHEMA_LEVELS         = 'MDSCHEMA_LEVELS';
   const MDSCHEMA_MEASURES       = 'MDSCHEMA_MEASURES';
   const MDSCHEMA_MEMBERS        = 'MDSCHEMA_MEMBERS';
   const MDSCHEMA_PROPERTIES     = 'MDSCHEMA_PROPERTIES';
   const MDSCHEMA_SETS           = 'MDSCHEMA_SETS';

   /**
    * @var array of XMLAMetadataColumn
    */
   private $columns;

   /**
    * @var array of string => XMLAMetadataColumn
    */
   private $columnsByName;

   /**
    * @var string
    */
   private $requestName;

   /**
    * Indicates if a request can be cached or not
    * @var boolean
    */
   private $isCachable = true;

   /**
    * @brief XMLA Metadata Request Object
    *
    * Ex: new XMLAMetadataRequest( XMLAMetadataRequest::DISCOVER_DATASOURCES );
    *
    * in olap4j, this would have been: XmlaOlap4jConnection.MetadataRequest.DISCOVER_DATASOURCES
    *
    * @param string $requestType
    */
   public function __construct( $requestType )
   {
      switch ( $requestType )
      {
         case self::DBSCHEMA_CATALOGS:

            $this->columns = array( new XMLAMetadataColumn("CATALOG_NAME", "TABLE_CAT") );
            break;

         case
         $this->columns = array(
            new XMLAMetadataColumn("SCHEMA_NAME", "TABLE_SCHEM"),
            new XMLAMetadataColumn("CATALOG_NAME", "TABLE_CAT")
         );
            break;

         case self::DISCOVER_DATASOURCES:
            $this->columns = array(
               new XMLAMetadataColumn("DataSourceName"),
               new XMLAMetadataColumn("DataSourceDescription"),
               new XMLAMetadataColumn("URL"),
               new XMLAMetadataColumn("DataSourceInfo"),
               new XMLAMetadataColumn("ProviderName"),
               new XMLAMetadataColumn("ProviderType"),
               new XMLAMetadataColumn("AuthenticationMode")
            );
            break;

         case self::DISCOVER_SCHEMA_ROWSETS:
            $this->columns = array(
               new XMLAMetadataColumn("SchemaName"),
               new XMLAMetadataColumn("SchemaGuid"),
               new XMLAMetadataColumn("Restrictions"),
               new XMLAMetadataColumn("Description")
            );
            break;

         case self::DISCOVER_ENUMERATORS:
            $this->columns = array(
               new XMLAMetadataColumn("EnumName"),
               new XMLAMetadataColumn("EnumDescription"),
               new XMLAMetadataColumn("EnumType"),
               new XMLAMetadataColumn("ElementName"),
               new XMLAMetadataColumn("ElementDescription"),
               new XMLAMetadataColumn("ElementValue")
            );
            break;

         case self::DISCOVER_PROPERTIES:
            $this->columns = array(
               new XMLAMetadataColumn("PropertyName"),
               new XMLAMetadataColumn("PropertyDescription"),
               new XMLAMetadataColumn("PropertyType"),
               new XMLAMetadataColumn("PropertyAccessType"),
               new XMLAMetadataColumn("IsRequired"),
               new XMLAMetadataColumn("Value")
            );
            break;

         case self::DISCOVER_KEYWORDS:
            $this->columns = array(
               new XMLAMetadataColumn("Keyword")
            );
            break;

         case self::DISCOVER_LITERALS:
            $this->columns = array(
               new XMLAMetadataColumn("LiteralName"),
               new XMLAMetadataColumn("LiteralValue"),
               new XMLAMetadataColumn("LiteralInvalidChars"),
               new XMLAMetadataColumn("LiteralInvalidStartingChars"),
               new XMLAMetadataColumn("LiteralMaxLength")
            );
            break;

         case self::DBSCHEMA_CATALOGS:
            $this->columns    = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("DESCRIPTION"),
               new XMLAMetadataColumn("ROLES"),
               new XMLAMetadataColumn("DATE_MODIFIED")
            );
            $this->isCachable = false;
            break;

         case self::DBSCHEMA_COLUMNS:
            $this->columns = array(
               new XMLAMetadataColumn("TABLE_CATALOG"),
               new XMLAMetadataColumn("TABLE_SCHEMA"),
               new XMLAMetadataColumn("TABLE_NAME"),
               new XMLAMetadataColumn("COLUMN_NAME"),
               new XMLAMetadataColumn("ORDINAL_POSITION"),
               new XMLAMetadataColumn("COLUMN_HAS_DEFAULT"),
               new XMLAMetadataColumn("COLUMN_FLAGS"),
               new XMLAMetadataColumn("IS_NULLABLE"),
               new XMLAMetadataColumn("DATA_TYPE"),
               new XMLAMetadataColumn("CHARACTER_MAXIMUM_LENGTH"),
               new XMLAMetadataColumn("CHARACTER_OCTET_LENGTH"),
               new XMLAMetadataColumn("NUMERIC_PRECISION"),
               new XMLAMetadataColumn("NUMERIC_SCALE")
            );
            break;

         case self::DBSCHEMA_PROVIDER_TYPES:
            $this->columns = array(
               new XMLAMetadataColumn("TYPE_NAME"),
               new XMLAMetadataColumn("DATA_TYPE"),
               new XMLAMetadataColumn("COLUMN_SIZE"),
               new XMLAMetadataColumn("LITERAL_PREFIX"),
               new XMLAMetadataColumn("LITERAL_SUFFIX"),
               new XMLAMetadataColumn("IS_NULLABLE"),
               new XMLAMetadataColumn("CASE_SENSITIVE"),
               new XMLAMetadataColumn("SEARCHABLE"),
               new XMLAMetadataColumn("UNSIGNED_ATTRIBUTE"),
               new XMLAMetadataColumn("FIXED_PREC_SCALE"),
               new XMLAMetadataColumn("AUTO_UNIQUE_VALUE"),
               new XMLAMetadataColumn("IS_LONG"),
               new XMLAMetadataColumn("BEST_MATCH")
            );
            break;

         case self::DBSCHEMA_TABLES:
            $this->columns = array(
               new XMLAMetadataColumn("TABLE_CATALOG"),
               new XMLAMetadataColumn("TABLE_SCHEMA"),
               new XMLAMetadataColumn("TABLE_NAME"),
               new XMLAMetadataColumn("TABLE_TYPE"),
               new XMLAMetadataColumn("TABLE_GUID"),
               new XMLAMetadataColumn("DESCRIPTION"),
               new XMLAMetadataColumn("TABLE_PROPID"),
               new XMLAMetadataColumn("DATE_CREATED"),
               new XMLAMetadataColumn("DATE_MODIFIED")
            );
            break;

         case self::DBSCHEMA_TABLES_INFO:
            $this->columns = array(
               new XMLAMetadataColumn("TABLE_CATALOG"),
               new XMLAMetadataColumn("TABLE_SCHEMA"),
               new XMLAMetadataColumn("TABLE_NAME"),
               new XMLAMetadataColumn("TABLE_TYPE"),
               new XMLAMetadataColumn("TABLE_GUID"),
               new XMLAMetadataColumn("BOOKMARKS"),
               new XMLAMetadataColumn("BOOKMARK_TYPE"),
               new XMLAMetadataColumn("BOOKMARK_DATATYPE"),
               new XMLAMetadataColumn("BOOKMARK_MAXIMUM_LENGTH"),
               new XMLAMetadataColumn("BOOKMARK_INFORMATION"),
               new XMLAMetadataColumn("TABLE_VERSION"),
               new XMLAMetadataColumn("CARDINALITY"),
               new XMLAMetadataColumn("DESCRIPTION"),
               new XMLAMetadataColumn("TABLE_PROPID")
            );
            break;

         case self::DBSCHEMA_SCHEMATA:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("SCHEMA_OWNER")
            );
            break;

         case self::MDSCHEMA_ACTIONS:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("ACTION_NAME"),
               new XMLAMetadataColumn("COORDINATE"),
               new XMLAMetadataColumn("COORDINATE_TYPE")
            );
            break;

         case self::MDSCHEMA_CUBES:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("CUBE_TYPE"),
               new XMLAMetadataColumn("CUBE_GUID"),
               new XMLAMetadataColumn("CREATED_ON"),
               new XMLAMetadataColumn("LAST_SCHEMA_UPDATE"),
               new XMLAMetadataColumn("SCHEMA_UPDATED_BY"),
               new XMLAMetadataColumn("LAST_DATA_UPDATE"),
               new XMLAMetadataColumn("DATA_UPDATED_BY"),
               new XMLAMetadataColumn("IS_DRILLTHROUGH_ENABLED"),
               new XMLAMetadataColumn("IS_WRITE_ENABLED"),
               new XMLAMetadataColumn("IS_LINKABLE"),
               new XMLAMetadataColumn("IS_SQL_ENABLED"),
               new XMLAMetadataColumn("DESCRIPTION")
            );
            break;

         case self::MDSCHEMA_DIMENSIONS:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("DIMENSION_NAME"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_NAME"),
               new XMLAMetadataColumn("DIMENSION_GUID"),
               new XMLAMetadataColumn("DIMENSION_CAPTION"),
               new XMLAMetadataColumn("DIMENSION_ORDINAL"),
               new XMLAMetadataColumn("DIMENSION_TYPE"),
               new XMLAMetadataColumn("DIMENSION_CARDINALITY"),
               new XMLAMetadataColumn("DEFAULT_HIERARCHY"),
               new XMLAMetadataColumn("DESCRIPTION"),
               new XMLAMetadataColumn("IS_VIRTUAL"),
               new XMLAMetadataColumn("IS_READWRITE"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_SETTINGS"),
               new XMLAMetadataColumn("DIMENSION_MASTER_UNIQUE_NAME"),
               new XMLAMetadataColumn("DIMENSION_IS_VISIBLE")
            );
            break;

         case self::MDSCHEMA_FUNCTIONS:
            $this->columns = array(
               new XMLAMetadataColumn("FUNCTION_NAME"),
               new XMLAMetadataColumn("DESCRIPTION"),
               new XMLAMetadataColumn("PARAMETER_LIST"),
               new XMLAMetadataColumn("RETURN_TYPE"),
               new XMLAMetadataColumn("ORIGIN"),
               new XMLAMetadataColumn("INTERFACE_NAME"),
               new XMLAMetadataColumn("LIBRARY_NAME"),
               new XMLAMetadataColumn("CAPTION")
            );
            break;

         case self::MDSCHEMA_HIERARCHIES:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_NAME"),
               new XMLAMetadataColumn("HIERARCHY_NAME"),
               new XMLAMetadataColumn("HIERARCHY_UNIQUE_NAME"),
               new XMLAMetadataColumn("HIERARCHY_GUID"),
               new XMLAMetadataColumn("HIERARCHY_CAPTION"),
               new XMLAMetadataColumn("DIMENSION_TYPE"),
               new XMLAMetadataColumn("HIERARCHY_CARDINALITY"),
               new XMLAMetadataColumn("DEFAULT_MEMBER"),
               new XMLAMetadataColumn("ALL_MEMBER"),
               new XMLAMetadataColumn("DESCRIPTION"),
               new XMLAMetadataColumn("STRUCTURE"),
               new XMLAMetadataColumn("IS_VIRTUAL"),
               new XMLAMetadataColumn("IS_READWRITE"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_SETTINGS"),
               new XMLAMetadataColumn("DIMENSION_IS_VISIBLE"),
               new XMLAMetadataColumn("HIERARCHY_ORDINAL"),
               new XMLAMetadataColumn("DIMENSION_IS_SHARED"),
               new XMLAMetadataColumn("PARENT_CHILD")
            );
            break;

         case self::MDSCHEMA_LEVELS:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_NAME"),
               new XMLAMetadataColumn("HIERARCHY_UNIQUE_NAME"),
               new XMLAMetadataColumn("LEVEL_NAME"),
               new XMLAMetadataColumn("LEVEL_UNIQUE_NAME"),
               new XMLAMetadataColumn("LEVEL_GUID"),
               new XMLAMetadataColumn("LEVEL_CAPTION"),
               new XMLAMetadataColumn("LEVEL_NUMBER"),
               new XMLAMetadataColumn("LEVEL_CARDINALITY"),
               new XMLAMetadataColumn("LEVEL_TYPE"),
               new XMLAMetadataColumn("CUSTOM_ROLLUP_SETTINGS"),
               new XMLAMetadataColumn("LEVEL_UNIQUE_SETTINGS"),
               new XMLAMetadataColumn("LEVEL_IS_VISIBLE"),
               new XMLAMetadataColumn("DESCRIPTION")
            );
            break;

         case self::MDSCHEMA_MEASURES:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("MEASURE_NAME"),
               new XMLAMetadataColumn("MEASURE_UNIQUE_NAME"),
               new XMLAMetadataColumn("MEASURE_CAPTION"),
               new XMLAMetadataColumn("MEASURE_GUID"),
               new XMLAMetadataColumn("MEASURE_AGGREGATOR"),
               new XMLAMetadataColumn("DATA_TYPE"),
               new XMLAMetadataColumn("MEASURE_IS_VISIBLE"),
               new XMLAMetadataColumn("LEVELS_LIST"),
               new XMLAMetadataColumn("DESCRIPTION")
            );
            break;

         case self::MDSCHEMA_MEMBERS:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_NAME"),
               new XMLAMetadataColumn("HIERARCHY_UNIQUE_NAME"),
               new XMLAMetadataColumn("LEVEL_UNIQUE_NAME"),
               new XMLAMetadataColumn("LEVEL_NUMBER"),
               new XMLAMetadataColumn("MEMBER_ORDINAL"),
               new XMLAMetadataColumn("MEMBER_NAME"),
               new XMLAMetadataColumn("MEMBER_UNIQUE_NAME"),
               new XMLAMetadataColumn("MEMBER_TYPE"),
               new XMLAMetadataColumn("MEMBER_GUID"),
               new XMLAMetadataColumn("MEMBER_CAPTION"),
               new XMLAMetadataColumn("CHILDREN_CARDINALITY"),
               new XMLAMetadataColumn("PARENT_LEVEL"),
               new XMLAMetadataColumn("PARENT_UNIQUE_NAME"),
               new XMLAMetadataColumn("PARENT_COUNT"),
               new XMLAMetadataColumn("TREE_OP"),
               new XMLAMetadataColumn("DEPTH")
            );
            break;

         case self::MDSCHEMA_PROPERTIES:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("DIMENSION_UNIQUE_NAME"),
               new XMLAMetadataColumn("HIERARCHY_UNIQUE_NAME"),
               new XMLAMetadataColumn("LEVEL_UNIQUE_NAME"),
               new XMLAMetadataColumn("MEMBER_UNIQUE_NAME"),
               new XMLAMetadataColumn("PROPERTY_NAME"),
               new XMLAMetadataColumn("PROPERTY_CAPTION"),
               new XMLAMetadataColumn("PROPERTY_TYPE"),
               new XMLAMetadataColumn("DATA_TYPE"),
               new XMLAMetadataColumn("PROPERTY_CONTENT_TYPE"),
               new XMLAMetadataColumn("DESCRIPTION")
            );
            break;

         case self::MDSCHEMA_SETS:
            $this->columns = array(
               new XMLAMetadataColumn("CATALOG_NAME"),
               new XMLAMetadataColumn("SCHEMA_NAME"),
               new XMLAMetadataColumn("CUBE_NAME"),
               new XMLAMetadataColumn("SET_NAME"),
               new XMLAMetadataColumn("SCOPE")
            );
            break;

         default:
            throw new OLAPException('Unsupported metadata request type ' . $requestType);
      }

      $this->requestName = $requestType;
   }

   public function isCachable()
   {
      return $this->isCachable;
   }

   /**
    * @brief Returns TRUE if the request requires the Datasource Name, FALSE otherwise
    *
    * @return boolean
    */
   public function requiresDatasourceName()
   {
      return $this->requestName != XMLAMetadataRequest::DISCOVER_DATASOURCES;
   }

   /**
    * @brief Returns TRUE is the request requires the Catalog Name, FALSE otherwise
    *
    * @return boolean
    */
   public function requiresCatalogName()
   {
      return $this->requestName == XMLAMetadataRequest::MDSCHEMA_FUNCTIONS;
   }

   /**
    * @brief Returns TRUE is the request allows a Catalog Name to be present
    *
    * Currently always returns TRUE
    *
    * @return boolean
    */
   public function allowsCatalogName()
   {
      return TRUE;
   }

   /**
    * @brief Retrieves a column by name, or NULL if not found
    *
    * @param string $name - Column Name
    *
    * @return XMLAMetadataColumn
    */
   public function getColumn( $name )
   {
      if ( !$this->columnsByName ) $this->lazyIndexColumnsByName();
      return isset($this->columnsByName[$name]) ? $this->columnsByName[$name] : NULL;
   }

   public function getName()
   {
      return $this->requestName;
   }

   public function getColumns()
   {
      return $this->columns;
   }

   public function getColumnNames()
   {
      if ( !$this->columnsByName ) $this->lazyIndexColumnsByName();
      return array_keys( $this->columnsByName );
   }

   private function lazyIndexColumnsByName()
   {
      $this->columnsByName = array();
      foreach ( $this->columns as $column )
      {
         $this->columnsByName [$column->name] = $column;
      }
   }

}
