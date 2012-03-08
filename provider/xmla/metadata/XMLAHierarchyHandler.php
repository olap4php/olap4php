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
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLACube;
use OLAP4PHP\Provider\XMLA\XMLAHierarchy;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;

class XMLAHierarchyHandler extends XMLAMetadataHandler
{
   /**
    * @var XMLACube
    */
   private $cube;

   public function __construct( XMLACube &$cube )
   {
      if ( empty($cube) || $cube == NULL ) throw new OLAPException('XMLAHierarchyMetadataHandler: $cube cannot be NULL');
      $this->cube = &$cube;
   }

   public function handle( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      $hierarchyUniqueName     = XMLAUtil::stringElement( $row, 'HIERARCHY_UNIQUE_NAME' );
      $hierarchyName           = (XMLAUtil::stringElement( $row, 'HIERARCHY_NAME' ) == NULL) ? str_replace( array( '[', ']' ), array( '', '' ), $hierarchyUniqueName ) : XMLAUtil::stringElement( $row, 'HIERARCHY_NAME' );
      $hierarchyCaption        = XMLAUtil::stringElement( $row, 'HIERARCHY_CAPTION' );
      $description             = XMLAUtil::stringElement( $row, 'DESCRIPTION' );
      $allMember               = XMLAUtil::stringElement( $row, 'ALL_MEMBER' );
      $defaultMemberUniqueName = XMLAUtil::stringElement( $row, 'DEFAULT_MEMBER' );

      $hierarchy = new XMLAHierarchy(
         $context->getDimension( $row ),
         $hierarchyUniqueName,
         $hierarchyName,
         $hierarchyCaption,
         $description,
         $allMember != NULL,
         $defaultMemberUniqueName);

      $list->add( $hierarchy );

      $this->cube->hierarchiesByUname[$hierarchy->getUniqueName()] = $hierarchy;
   }
}
