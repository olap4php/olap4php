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

use \DOMElement;
use \InvalidArgumentException;
use OLAP4PHP\Common\Util;

/**
 * XMLA Connection Context that keeps.
 */
class XMLAConnectionContext
{
   /**
    * @var XMLAConnection
    */
   public $xmlaConnection;

   /**
    * @var XMLADatabaseMetaData
    */
   public $xmlaDatabaseMetaData;

   /**
    * @var XMLACatalog
    */
   public $xmlaCatalog;

   /**
    * @var XMLASchema
    */
   public $xmlaSchema;

   /**
    * @var XMLACube
    */
   public $xmlaCube;

   /**
    * @var XMLADimension
    */
   public $xmlaDimension;

   /**
    * @var XMLAHierarchy
    */
   public $xmlaHierarchy;

   /**
    * @var XMLALevel
    */
   public $xmlaLevel;

   /**
    * @var Logger Not null if logging is enabled for this context.
    */
   public $logger;


   /**
    * Creates a Context.
    *
    * @param XMLAConnection       Connection (must not be null)
    * @param XMLADatabaseMetaData DatabaseMetaData (may be null)
    * @param XMLACatalog          Catalog (may be null if DatabaseMetaData is null)
    * @param XMLASchema           Schema (may be null if Catalog is null)
    * @param XMLACube             Cube (may be null if Schema is null)
    * @param XMLADimension        Dimension (may be null if Cube is null)
    * @param XMLAHierarchy        Hierarchy (may be null if Dimension is null)
    * @param XMLALevel            Level (may be null if Hierarchy is null)
    */
   public function __construct(
      XMLAConnection $connection,
      XMLADatabaseMetaData $databaseMetaData = NULL,
      XMLACatalog $catalog = NULL,
      XMLASchema $schema = NULL,
      XMLACube $cube = NULL,
      XMLADimension $dimension = NULL,
      XMLAHierarchy $hierarchy = NULL,
      XMLALevel $level = NULL )
   {
      assert(
         ($databaseMetaData != null || $catalog == null)
            && ($catalog != null || $schema == null)
            && ($schema != null || $cube == null)
            && ($cube != null || $dimension == null)
            && ($dimension != null || $hierarchy == null)
            && ($hierarchy != null || $level == null) );

      $this->xmlaConnection       = $connection;
      $this->xmlaDatabaseMetaData = $databaseMetaData;
      $this->xmlaCatalog          = $catalog;
      $this->xmlaSchema           = $schema;
      $this->xmlaCube             = $cube;
      $this->xmlaDimension        = $dimension;
      $this->xmlaHierarchy        = $hierarchy;
      $this->xmlaLevel            = $level;
      $this->logger               = $connection->getLogger();
   }


   /**
    * Shorthand way to create a Context at Cube level or finer.
    *
    * @param XMLACube      Cube (must not be null)
    * @param XMLADimension Dimension (may be null)
    * @param XMLAHierarchy Hierarchy (may be null if Dimension is null)
    * @param XMLALevel     Level (may be null if Hierarchy is null)
    */
   static public function createAtGranule(
      XMLACube $cube,
      XMLADimension $dimension = NULL,
      XMLAHierarchy $hierarchy = NULL,
      XMLALevel $level = NULL )
   {
      return new XMLAConnectionContext (
         $cube->getSchema()->getCatalog()->getMetadata()->getConnection(),
         $cube->getSchema()->getCatalog()->getMetadata(),
         $cube->getSchema()->getCatalog(),
         $cube->getSchema(),
         $cube,
         $dimension,
         $hierarchy,
         $level);
   }

   /**
    * Shorthand way to create a Context at Level level.
    *
    * @param XMLALevel Level (must not be null)
    */
   static public function createAtLevel( XMLALevel $level )
   {
      return XMLAConnectionContext::createAtGranule(
         $level->getHierarchy()->getDimension()->getCube(),
         $level->getHierarchy()->getDimension(),
         $level->getHierarchy(),
         $level );
   }

   /**
    * @return XMLACube
    */
   public function getCube( DOMElement $row )
   {
      if ( $this->xmlaCube != null ) return $this->xmlaCube;

      throw new InvalidArgumentException(); // todo:
   }

   /**
    * @return XMLADimension
    */
   public function getDimension( DOMElement $row )
   {
      if ( $this->xmlaDimension != null )
      {
         return $this->xmlaDimension;
      }

      $dimensionUniqueName = XMLAUtil::stringElement( $row, 'DIMENSION_UNIQUE_NAME' );
      $dimensionsByUname   = $this->getCube( $row )->dimensionsByUname;
      $dimension           = isset ($dimensionsByUname [$dimensionUniqueName])
         ? $dimensionsByUname [$dimensionUniqueName]
         : null;

      // Apparently, the code has requested a member that is
      // not queried for yet.
      if ( $dimension == null )
      {
         $dimensionName = XMLAUtil::stringElement( $row, 'DIMENSION_NAME' );
         return $this->getCube( $row )->getDimensions()->get( $dimensionName );
      }
      return $dimension;
   }

   /**
    * @return XMLAHierarhcy
    */
   public function getHierarchy( DOMElement $row )
   {
      if ( $this->xmlaHierarchy != null )
      {
         return $this->xmlaHierarchy;
      }

      $hierarchyUniqueName = XMLAUtil::stringElement( $row, 'HIERARCHY_UNIQUE_NAME' );
      $hierarchiesByUname  = $this->getCube( $row )->hierarchiesByUname;
      $hierarchy           = isset ($hierarchiesByUname [$hierarchyUniqueName])
         ? $hierarchiesByUname [$hierarchyUniqueName]
         : null;

      if ( $hierarchy == null )
      {
         // Apparently, the code has requested a member that is
         // not queried for yet. We must force the initialization
         // of the dimension tree first.
         $dimensionUniqueName = XMLAUtil::stringElement( $row, 'DIMENSION_UNIQUE_NAME' );
         $parsedNames         = XMLAUtil::parseUniqueName( $dimensionUniqueName );
         $dimension           = $this->getCube( $row )->getDimensions()->get( $parsedNames[0] );
         $dimension->getHierarchies()->size();
         // Now we attempt to resolve again
         $hierarchiesByUname = $this->getCube( $row )->hierarchiesByUname;
         $hierarchy          = isset ($hierarchiesByUname [$hierarchyUniqueName])
            ? $hierarchiesByUname [$hierarchyUniqueName]
            : null;
      }

      return $hierarchy;
   }

   /**
    * @return XMLALevel Level (must not be null)
    */
   public function getLevel( DOMElement $row )
   {
      if ( $this->xmlaLevel != null )
      {
         return $this->xmlaLevel;
      }
      $levelUniqueName = XMLAUtil::stringElement( $row, 'LEVEL_UNIQUE_NAME' );
      $levelsByUname   = $this->getCube( $row )->levelsByUname;
      $level           = isset ($levelsByUname [$levelUniqueName])
         ? $levelsByUname [$levelUniqueName]
         : null;

      if ( $level == null )
      {
         // Apparently, the code has requested a member that has
         // not been queried yet. We must force the initialization
         // of the dimension tree first.
         $dimensionUniqueName = XMLAUtil::stringElement( $row, 'DIMENSION_UNIQUE_NAME' );
         $parsedUniqueName    = XMLAUtil::parseUniqueName( $dimensionUniqueName );
         $dimensionName       = $parsedUniqueName[0];
         //print $dimensionName;
         $dimension = $this->getCube( $row )->getDimensions()->get( $dimensionName );
         foreach ( $dimension->getHierarchies() as $hierarchy )
         {
            $hierarchy->getLevels()->size();
         }

         // Now we attempt to resolve again
         $level = $this->getCube( $row )->levelsByUname [$levelUniqueName];
      }
      return $level;
   }

   /**
    * @return XMLACatalog
    */
   public function getCatalog( DOMElement $row )
   {
      if ( $this->xmlaCatalog != null )
      {
         return $this->xmlaCatalog;
      }

      $catalogName = XMLAUtil::stringElement( $row, 'CATALOG_NAME' );
      return $this->xmlaConnection->getCatalogs()->get( $catalogName );
   }
}
