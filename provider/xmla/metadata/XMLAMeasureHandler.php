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
use OLAP4PHP\Provider\XMLA\XMLADimension;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Provider\XMLA\XMLAMeasure;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Metadata\DataType;
use OLAP4PHP\OLAP\OLAPException;

class XMLAMeasureHandler extends XMLAMetadataHandler
{
   private $dimension;

   /**
    * Constructor
    *
    * @param XMLADimension $dimension The measures dimension
    */
   public function __construct( XMLADimension $dimension )
   {
      $this->dimension = $dimension;
   }

   public function handle( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      /*
      Example:

      <row>
          <CATALOG_NAME>FoodMart</CATALOG_NAME>
          <SCHEMA_NAME>FoodMart</SCHEMA_NAME>
          <CUBE_NAME>Sales</CUBE_NAME>
          <MEASURE_NAME>Profit</MEASURE_NAME>
          <MEASURE_UNIQUE_NAME>[Measures].[Profit]</MEASURE_UNIQUE_NAME>
          <MEASURE_CAPTION>Profit</MEASURE_CAPTION>
          <MEASURE_AGGREGATOR>127</MEASURE_AGGREGATOR>
          <DATA_TYPE>130</DATA_TYPE>
          <MEASURE_IS_VISIBLE>true</MEASURE_IS_VISIBLE>
          <DESCRIPTION>Sales Cube - Profit Member</DESCRIPTION>
      </row>

       */

      $measureName       = XMLAUtil::stringElement( $row, 'MEASURE_NAME' );
      $measureUniqueName = XMLAUtil::stringElement( $row, 'MEASURE_UNIQUE_NAME' );
      $measureCaption    = XMLAUtil::stringElement( $row, 'MEASURE_CAPTION' );
      $description       = XMLAUtil::stringElement( $row, 'DESCRIPTION' );
      $measureAggregator = NULL;
      /*$measureAggregator =
          MeasureAggregator::getDictionary ( )::forOrdinal (
             XMLAUtil::integerElement (
                $row, 'MEASURE_AGGREGATOR' ) );*/

      // Figure out datatype
      $datatype        = NULL;
      $ordinalDatatype =
         DataType::getDictionary()->forName(
            XMLAUtil::stringElement( $row, 'DATA_TYPE' ) );
      if ( $ordinalDatatype == null )
      {
         $datatype = Datatype::getDictionary()->forOrdinal(
            XMLAUtil::integerElement( $row, 'DATA_TYPE' ) );
      }
      else
      {
         $datatype = $ordinalDatatype;
      }
      $measureIsVisible = XMLAUtil::booleanElement( $row, 'MEASURE_IS_VISIBLE' );

      $member =
         $context->getCube( $row )->getMetadataReader()
            ->lookupMemberByUniqueName(
            $measureUniqueName );

      if ( $member == null )
      {
         throw new OLAPException (
            'The server failed to resolve a member with the same unique name as a measure named ' .
               $measureUniqueName);
      }

      $list->add(
         new XMLAMeasure (
            $member->getLevel(), $measureUniqueName,
            $measureName, $measureCaption, $description, null,
            $measureAggregator, $datatype, $measureIsVisible,
            $member->getOrdinal()) );
   }


   /**
    * @param array XMLAMeasure
    */
   public function sortList( NamedList $list )
   {
      // TODO: Figure out how to do sorting
      //uasort ( $list, array ( 'self', 'sort' ) );
   }


   /**
    * Measure sort callback
    */
   static public function sort( XMLAMeasure $o1, XMLAMeasure $o2 )
   {
      return $o1->getOrdinal() - $o2->getOrdinal();
   }
}
