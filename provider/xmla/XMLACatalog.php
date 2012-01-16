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
use OLAP4PHP\Metadata\ICatalog;

//Classes
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList;
use OLAP4PHP\Provider\XMLA\XMLAMetadataRequest;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Provider\XMLA\Metadata\XMLACatalogSchemaHandler;


/**
 * XMLA Implementation of ICatalog Interface
 */
class XMLACatalog implements ICatalog
{
   private $databaseMetadata;
   private $name;
   private $schemas;
   private $hash;

   /**
    * Constructor
    *
    * @param XMLADatabaseMetaData $databaseMetaData
    * @param string $name
    */
   public function __construct ( XMLADatabaseMetadata $databaseMetaData, $name )
   {
      assert ( $databaseMetaData != NULL );
      assert ( $name != NULL );

      $this->databaseMetadata = $databaseMetaData;
      $this->name = $name;

      // From olap4j:
      //   Fetching the schemas is a tricky part. There are no XMLA requests to
      //   obtain the available schemas for a given catalog. We therefore need
      //   to ask for the cubes, restricting results on the catalog, and while
      //   iterating on the cubes, take the schema name from this recordset.
      //
      //   Many servers (SSAS for example) won't support the schema name column
      //   in the returned rowset. This has to be taken into account.
      //   
      // Lazy loading again
      $this->schemas = new LazyMetadataList(
              new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_CUBES ),
              new XMLAConnectionContext( $this->databaseMetadata->getConnection(), $this->databaseMetadata, $this, NULL, NULL, NULL, NULL, NULL ),
              new XMLACatalogSchemaHandler( $this->name ) );
   }

   public function hashCode()
   {
      if ( empty( $this->hash ) )
      {
         $this->hash = XMLAUtil::javaStringHashCode( $this->name );
      }

      return $this->hash;
   }

   public function equals( $obj )
   {
      if ( $obj instanceof XMLACatalog )
      {
         return $obj->getName() == $this->name;
      }

      return FALSE;
   }


   /**
    * @return XMLADatabaseMetaData
    */
   public function getMetaData ( )
   {
      return $this->databaseMetadata;
   }


   /**
    * @return string
    */
   public function getName ( )
   {
      return $this->name;
   }


   /**
    * @return LaztMetadataList
    */
   public function getSchemas ( )
   {
      return $this->schemas;
   }
}