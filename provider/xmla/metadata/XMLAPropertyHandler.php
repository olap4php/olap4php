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
use OLAP4PHP\Metadata\DataType;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLAProperty;
use OLAP4PHP\Metadata\PropertyType;
use OLAP4PHP\Metadata\PropertyContentType;

class XMLAPropertyHandler extends XMLAMetadataHandler
{

   public function handle ( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      /*
      Example:

      <row>
          <CATALOG_NAME>FoodMart</CATALOG_NAME>
          <SCHEMA_NAME>FoodMart</SCHEMA_NAME>
          <CUBE_NAME>HR</CUBE_NAME>
          <DIMENSION_UNIQUE_NAME>[Store]</DIMENSION_UNIQUE_NAME>
          <HIERARCHY_UNIQUE_NAME>[Store]</HIERARCHY_UNIQUE_NAME>
          <LEVEL_UNIQUE_NAME>[Store].[Store Name]</LEVEL_UNIQUE_NAME>
          <PROPERTY_NAME>Store Manager</PROPERTY_NAME>
          <PROPERTY_CAPTION>Store Manager</PROPERTY_CAPTION>
          <PROPERTY_TYPE>1</PROPERTY_TYPE>
          <DATA_TYPE>130</DATA_TYPE>
          <PROPERTY_CONTENT_TYPE>0</PROPERTY_CONTENT_TYPE>
          <DESCRIPTION>HR Cube - Store Hierarchy - Store
              Name Level - Store Manager Property</DESCRIPTION>
      </row>
       */

      $description   = XMLAUtil::stringElement ( $row, 'DESCRIPTION' );
      $uniqueName    = XMLAUtil::stringElement ( $row, 'DESCRIPTION' );
      $caption       = XMLAUtil::stringElement ( $row, 'PROPERTY_CAPTION' );
      $name          = XMLAUtil::stringElement ( $row, 'PROPERTY_NAME' );
      $datatype      = null;

      $ordinalDatatype =
          DataType::getDictionary ( )->forName (
              XMLAUtil::stringElement ( $row, 'DATA_TYPE' ) );
      if ( $ordinalDatatype == null )
      {
         $datatype = DataType::getDictionary ( )->forOrdinal (
            XMLAUtil::integerElement ( $row, 'DATA_TYPE' ) );
      } 
      else
      {
         $datatype = $ordinalDatatype;
      }

      $contentTypeOrdinal =
          XMLAUtil::integerElement ( $row, 'PROPERTY_CONTENT_TYPE' );

      $contentType =
         $contentTypeOrdinal == null
            ? null
            : PropertyContentType::getDictionary ( )
                 ->forOrdinal ( $contentTypeOrdinal );

      $propertyType 
         = XMLAUtil::integerElement ( $row, 'PROPERTY_TYPE' );

      $type =
         PropertyType::getDictionary ( )->forMask ( $propertyType );
      $list->add (
         new XMLAProperty (
            $uniqueName, $name, $caption, $description, $datatype, $type,
            $contentType ) );
   }
}
