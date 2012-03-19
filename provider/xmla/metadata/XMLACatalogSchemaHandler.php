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
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAMetadataHandler;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Provider\XMLA\XMLASchema;

class XMLACatalogSchemaHandler extends XMLAMetadataHandler
{
   private $catalogName;

   public function __construct( $catalogName )
   {
      if ( empty($catalogName) ) throw new \RuntimeException ('The Catalog Schema Handler requires a catalog name.');
      $this->catalogName = $catalogName;
   }

   public function handle( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      $schemaName  = (XMLAUtil::stringElement( $row, "SCHEMA_NAME" ) === NULL) ? '' : XMLAUtil::stringElement( $row, "SCHEMA_NAME" );
      $catalogName = XMLAUtil::stringElement( $row, "CATALOG_NAME" );
      if ( !$catalogName && XMLAUtil::stringElement( $row, "CUBE_NAME" ) )
      {
         $catalogName = XMLAUtil::stringElement( $row, "CUBE_NAME" );
      }

      //print $catalogName . ':' . $schemaName . PHP_EOL;

      //echo 'XMLACatalogSchmeHandler: before if';
      if ( $this->catalogName == $catalogName && $list->get( $schemaName ) === NULL )
      {
         //echo 'XMLACatalogSchmeHandler: inside if';
         if ( $schemaName !== null )
         {
            $list->add( new XMLASchema($context->getCatalog( $row ), $schemaName) );
         }
      }
   }
}
