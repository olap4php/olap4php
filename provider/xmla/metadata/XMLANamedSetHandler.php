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
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Provider\XMLA\XMLANamedSet;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAMetadataHandler;
use OLAP4PHP\Common\NamedList;

class XMLANamedSetHandler extends XMLAMetadataHandler
{

   public function handle( DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      /*
      Example:

      <row>
          <CATALOG_NAME>FoodMart</CATALOG_NAME>
          <SCHEMA_NAME>FoodMart</SCHEMA_NAME>
          <CUBE_NAME>Warehouse</CUBE_NAME>
          <SET_NAME>[Top Sellers]</SET_NAME>
          <SCOPE>1</SCOPE>
      </row>

       */
      $setName = XMLAUtil::stringElement( $row, 'SET_NAME' );
      $list->add(
         new XMLANamedSet (
            $context->getCube( $row ), $setName) );
   }
}
