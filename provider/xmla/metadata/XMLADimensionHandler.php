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

use OLAP4PHP\Common\NamedList;
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Metadata\DimensionType;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLACube;
use OLAP4PHP\Provider\XMLA\XMLADimension;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;

class XMLADimensionHandler extends XMLAMetadataHandler
{
   /**
    * @var XMLACube
    */
   private $cube;

   public function __construct( XMLACube $cube )
   {
      if ( empty( $cube ) || $cube == NULL ) throw new OLAPException( 'XMLADimensionMetadataHandler: $cube cannot be NULL' );
      $this->cube = $cube;
   }

   public function handle( \DOMElement $row, XMLAConnectionContext $context, NamedList $list )
   {
      $dimensionName = XMLAUtil::stringElement( $row, 'DIMENSION_NAME' );
      $dimensionUniqueName = XMLAUtil::stringElement( $row, 'DIMENSION_UNIQUE_NAME' );
      $dimensionCaption = XMLAUtil::stringElement( $row, 'DIMENSION_CAPTION' );
      $description = XMLAUtil::stringElement( $row, 'DESCRIPTION' );
      $dimensionType = XMLAUtil::integerElement( $row, 'DIMENSION_TYPE' );
      $type = DimensionType::getDictionary()->forOrdinal( $dimensionType );
      $defaultHierarchyUniqueName = XMLAUtil::stringElement( $row, 'DEFAULT_HIERARCHY' );
      $dimensionOrdinal = XMLAUtil::integerElement( $row, 'DIMENSION_ORDINAL' );

      $dimension = new XMLADimension(
              $this->cube,
              $dimensionUniqueName,
              $dimensionName,
              $dimensionCaption,
              $description,
              $type,
              $defaultHierarchyUniqueName,
              $dimensionOrdinal == NULL ? 0 : $dimensionOrdinal);

      $list->add( $dimension );

      $this->cube->dimensionsByUname[$dimension->getUniqueName()] = $dimension;
   }
}