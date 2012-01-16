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
use OLAP4PHP\Metadata\ICube;
use OLAP4PHP\Metadata\INamed;

// Classes
use OLAP4PHP\Provider\XMLA\Metadata\XMLACachingMetadataReader;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAMetadataReader;
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\Metadata\LazyMetadataList;
use OLAP4PHP\Provider\XMLA\Metadata\XMLADimensionHandler;
use OLAP4PHP\Provider\XMLA\Metadata\XMLAMeasureHandler;
use OLAP4PHP\Provider\XMLA\Metadata\XMLANamedSetHandler;


/**
 * XMLA Implementation of ICube Interface
 */
class XMLACube implements ICube, INamed
{
   private $description;
   private $dimensions;
   private $name;
   // array string => XMLAMeasure
   private $measuresMap;
   private $measures;
   private $metadataReader;
   private $namedSets;
   private $schema;
   private $hierarchies;

   public $hierarchiesByUname = array();
   public $levelsByUname = array ();
   public $dimensionsByUname = array();

   /**
    * Constructor
    *
    * @param XMLASchema Schema
    * @param string name Name
    * @param string description Description
    * @throws OLAPException
    */
   public function __construct ( XMLASchema $schema, $name, $description )
   {
      assert ( $schema != null );
      assert ( $name != null );
      assert ( $description != null );

      $this->schema = $schema;
      $this->name = $name;
      $this->description = $description;
      $this->metadataReader =
         new XMLACachingMetadataReader (
                new XMLAMetadataReader ( $this ),
                $this->measuresMap );
      
      $connection = $schema->getCatalog ( )->getMetaData( )->getConnection ( );
      $context = XMLAConnectionContext::createAtGranule ( $this, null, null, null );

      $restrictions = array (
         'CATALOG_NAME' => $schema->getCatalog ( )->getName( ),
         'SCHEMA_NAME'  => $schema->getName ( ),
         'CUBE_NAME'    => $this->getName ( )
      );

      $this->dimensions = new LazyMetadataList (
         new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_DIMENSIONS ),
         $context,
         new XMLADimensionHandler ( $this ),
         $restrictions );

      // populate measures up front; a measure is needed in every query
      $this->measures = new NamedList();
      $connection->populateList (
         $this->measures,
         $context,
         new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_MEASURES ),
         new XMLAMeasureHandler ( $this->getDimensions ( )->get ( 'Measures' ) ),
         $restrictions );

      for ( $i = 0; $i < $this->measures->size(); $i++ )
      {
         $measure = $this->measures->get( $i );
         $this->measuresMap[$measure->getUniqueName()] = $measure;
      }

     // populate named sets
     $this->namedSets = new LazyMetadataList (
         new XMLAMetadataRequest( XMLAMetadataRequest::MDSCHEMA_SETS ),
         $context,
         new XMLANamedSetHandler ( ),
         $restrictions );
   }

   /**
    *
    * @return XMLASchema
    */
   public function getSchema()
   {
      return $this->schema;
   }

   public function getName()
   {
      return $this->name;
   }

   public function getUniqueName()
   {
      return '[' . $this->name . ']';
   }

   public function getCaption( $locale )
   {
      return $this->name;
   }

   public function getDescription( $locale )
   {
      return $this->description;
   }

   /**
    * @return LazyMetadataList
    */
   public function getDimensions()
   {
      return $this->dimensions;
   }

   /**
    * @return NamedList
    */
   public function getHierarchies()
   {
      if ( empty ( $this->hierarchies ) )
      {
         $this->hierarchies = new NamedList();
         foreach ( $this->dimensions as $dimension )
         {
            $this->hierarchies->addAll( $dimension->getHierarchies ( ) );
         }
      }

      return $this->hierarchies;
   }

   public function getMeasures()
   {
      return $this->measures;
   }

   public function getSets()
   {
      return $this->namedSets;
   }

   public function getSupportedLocales()
   {
      return array( 'en-US' );
   }

   public function lookupMember( array $nameParts )
   {
      // not implemented as of yet, since it's part of the Query model in olap4j
      // in the first olap4php implementation, we are focusing on XMLA.
      //
      // in olap4j, see QueryDimension and QueryAxis
      //
      throw new OLAPException( 'XMLACube::lookupMember not implemented' );
   }

   public function lookupMembers( array $treeOps, array $nameParts )
   {
      // not implemented as of yet, since it's part of the Query model in olap4j
      // in the first olap4php implementation, we are focusing on XMLA.
      //
      // in olap4j, see QueryDimension and QueryAxis
      //
      throw new OLAPException( 'XMLACube::lookupMember not implemented' );
   }

   /**
    * @return IXMLAMetaDataReader
    */
   public function getMetadataReader ( )
   {
      return $this->metadataReader;
   }
}
