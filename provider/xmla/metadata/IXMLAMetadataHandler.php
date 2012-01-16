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

// Classes used
use \DOMElement;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Common\NamedList;


/**
 * @brief XMLA Metadata Handler
 */
interface IXMLAMetadataHandler
{
   /**
    * Converts an XML element from an XMLA result set into a XMLA metadata
    * object and appends it to a list of metadata objects.
    *
    * @param DOMElement $row XMLA element
    *
    * @param XMLAConnectionContext $context Context (schema, cube, dimension, etc.) that the
    * request was executed in and that the element will belong to
    *
    * @param $list List of metadata objects to append new metadata object
    *
    * @throws OlapException on error
    */
   public function handle ( DOMElement $row, XMLAConnectionContext $context, NamedList $list );

   /**
    * Sorts a list of metadata object.
    *
    * <p>For most object types, the order returned by XMLA is correct, and
    * this method will no-op.
    *
    * @param list List of metadata elements
    */
   public function sortList ( NamedList $list );
}