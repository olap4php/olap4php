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
namespace OLAP4PHP\Provider\XMLA\Metadata;

use \DOMElement;
use OLAP4PHP\Metadata\LevelType;
use OLAP4PHP\Provider\XMLA\XMLALevel;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLACube;

class XMLALevelHandler extends XMLAMetadataHandler
{
   const MDLEVEL_TYPE_CALCULATED = 0x0002;

   private $cubeForCallback;

   /**
    * Constructor
    */
   public function __construct ( XMLACube $cubeForCallback )
   {
      $this->cubeForCallback = $cubeForCallback;
   }

   public function handle ( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      /*
      Example:

      <row>
          <CATALOG_NAME>FoodMart</CATALOG_NAME>
          <SCHEMA_NAME>FoodMart</SCHEMA_NAME>
          <CUBE_NAME>Sales</CUBE_NAME>
          <DIMENSION_UNIQUE_NAME>[Customers]</DIMENSION_UNIQUE_NAME>
          <HIERARCHY_UNIQUE_NAME>[Customers]</HIERARCHY_UNIQUE_NAME>
          <LEVEL_NAME>(All)</LEVEL_NAME>
          <LEVEL_UNIQUE_NAME>[Customers].[(All)]</LEVEL_UNIQUE_NAME>
          <LEVEL_CAPTION>(All)</LEVEL_CAPTION>
          <LEVEL_NUMBER>0</LEVEL_NUMBER>
          <LEVEL_CARDINALITY>1</LEVEL_CARDINALITY>
          <LEVEL_TYPE>1</LEVEL_TYPE>
          <CUSTOM_ROLLUP_SETTINGS>0</CUSTOM_ROLLUP_SETTINGS>
          <LEVEL_UNIQUE_SETTINGS>3</LEVEL_UNIQUE_SETTINGS>
          <LEVEL_IS_VISIBLE>true</LEVEL_IS_VISIBLE>
          <DESCRIPTION>Sales Cube - Customers Hierarchy - (All)
          Level</DESCRIPTION>
      </row>

       */

      $levelUniqueName = XMLAUtil::stringElement ( $row, 'LEVEL_UNIQUE_NAME');
      // SAP BW doesn't return a HIERARCHY_NAME attribute,
      // so try to use the unique name instead
      $levelName =
          XMLAUtil::stringElement ( $row, 'LEVEL_NAME') == null
              ? ($levelUniqueName != null
                      ? ereg_replace ( "\\]$", "", ereg_replace ( "^\\[", "", $levelUniqueName ) )
                      : null)
              : XMLAUtil::stringElement ( $row, 'LEVEL_NAME' );
      $levelCaption = XMLAUtil::stringElement ( $row, 'LEVEL_CAPTION' );
      $description = XMLAUtil::stringElement ( $row, 'DESCRIPTION' );
      $levelNumber = XMLAUtil::integerElement ( $row, 'LEVEL_NUMBER' );
      $levelTypeCode = XMLAUtil::integerElement ( $row, 'LEVEL_TYPE' );

      $levelType = LevelType::getDictionary()->forOrdinal ( $levelTypeCode );
      $calculated = ( $levelTypeCode & self::MDLEVEL_TYPE_CALCULATED ) != 0;
      $levelCardinality = XMLAUtil::integerElement ( $row, 'LEVEL_CARDINALITY' );
      $level = new XMLALevel (
          $context->getHierarchy ( $row ), $levelUniqueName, $levelName,
          $levelCaption, $description, $levelNumber, $levelType,
          $calculated, $levelCardinality );
      $list->add ( $level );
      $this->cubeForCallback->levelsByUname [ $level->getUniqueName ( ) ] = $level;
   }
}