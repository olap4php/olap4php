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
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Metadata\MemberType;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLALevel;
use OLAP4PHP\Provider\XMLA\XMLAMember;
use OLAP4PHP\Provider\XMLA\XMLAProperty;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;

class XMLAMemberHandler extends XMLAMetadataHandler
{
   /**
    * @var array
    */
   static private $excludedPropertyNames = array(
      'CATALOG_NAME',
      'CUBE_NAME',
      'DIMENSION_UNIQUE_NAME',
      'HIERARCHY_UNIQUE_NAME',
      'LEVEL_UNIQUE_NAME',
      'PARENT_LEVEL',
      'PARENT_COUNT',
      'MEMBER_KEY',
      'IS_PLACEHOLDERMEMBER',
      'IS_DATAMEMBER',
      'LEVEL_NUMBER',
      'MEMBER_ORDINAL',
      'PARENT_UNIQUE_NAME',
      'MEMBER_TYPE',
      'MEMBER_CAPTION',
      'CHILDREN_CARDINALITY',
      'DEPTH'
   );

   public function handle( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      $memberOrdinal       = XMLAUtil::integerElement( $row, 'LEVEL_NUMBER' );
      $memberUniqueName    = XMLAUtil::stringElement( $row, 'MEMBER_UNIQUE_NAME' );
      $memberName          = XMLAUtil::stringElement( $row, 'MEMBER_NAME' );
      $parentUniqueName    = XMLAUtil::stringElement( $row, 'PARENT_UNIQUE_NAME' );
      $memberTypeValues    = MemberType::values();
      $memberType          = $memberTypeValues [XMLAUtil::integerElement( $row, 'MEMBER_TYPE' )];
      $memberCaption       = XMLAUtil::stringElement( $row, 'MEMBER_CAPTION' );
      $childrenCardinality = XMLAUtil::integerElement( $row, 'CHILDREN_CARDINALITY' );

      $level = $context->getLevel( $row );

      $map = array();
      $this->addUserDefinedDimensionProperties( $row, $level, $map );

      $depth = XMLAUtil::integerElement( $row, 'DEPTH' );
      if ( $depth && $depth != $level->getDepth() )
      {
         $map['DEPTH'] = $depth;
      }

      $member = new XMLAMember (
         $level,
         $memberUniqueName,
         $memberName,
         $memberCaption,
         "",
         $parentUniqueName,
         $memberType,
         $childrenCardinality,
         $memberOrdinal,
         $map
      );

      $list->add( $member );
   }

   private function addUserDefinedDimensionProperties( DOMElement $row, XMLALevel $level = NULL, array &$map )
   {
      foreach ( $row->childNodes as $node )
      {
         if ( in_array( $node->localName, self::$excludedPropertyNames ) ) continue;

         foreach ( $level->getProperties() as $property )
         {
            if ( $property instanceof XMLAProperty && strcasecmp( $property->getName(), $node->localName ) == 0 )
            {
               $map[$property->getName()] = $node->nodeValue;
            }
         }
      }
   }
}
