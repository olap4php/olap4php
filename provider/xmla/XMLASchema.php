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

// Interfaces
use OLAP4PHP\Metadata\ISchema;

// Classes
use \InvalidArgumentException;
use OLAP4PHP\Provider\XMLA\Metadata\XMLACubeHandler;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList;

/**
 * XMLA Implementation of ISchema Interface
 */
class XMLASchema implements ISchema
{
   private $catalog;
   private $name;
   private $cubes;
   private $hash;

   /**
    * Constructor
    *
    * @param $catalog The schema's catalog
    * @param $name    The name of the schema
    *
    */
   public function __construct( XMLACatalog $catalog, $name )
   {
      if ( $catalog == null ) throw new InvalidArgumentException ('Catalog cannot be null.');
      if ( $name == null ) throw new InvalidArgumentException ('Name cannot be null.');

      $this->catalog = $catalog;
      $this->name    = $name;

      // Lazying loading ...
      $this->cubes = new LazyMetadataList(
         new XMLAMetadataRequest(XMLAMetadataRequest::MDSCHEMA_CUBES),
         new XMLAConnectionContext(
            $this->catalog->getMetaData()->getConnection(),
            $this->catalog->getMetaData(),
            $this->catalog,
            $this,
            NULL, NULL, NULL, NULL),
         new XMLACubeHandler());
   }

   public function hashCode()
   {
      if ( empty($this->hash) )
      {
         $this->hash = XMLAUtil::javaStringHashCode( $this->name );
      }

      return $this->hash;
   }

   public function equals( $obj )
   {
      if ( $obj instanceof XMLASchema )
      {
         return $obj->getName() == $this->name;
      }

      return FALSE;
   }

   /**
    * @return string
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * @return XMLACatalog
    */
   public function getCatalog()
   {
      return $this->catalog;
   }

   public function getCubes()
   {
      return $this->cubes;
   }

   public function getSharedDimensions()
   {
      return new NamedList();
   }

   public function getSupportedLocales()
   {
      return array();
   }
}
