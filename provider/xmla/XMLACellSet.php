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
use OLAP4PHP\OLAP\ICell;
use OLAP4PHP\OLAP\ICellSet;
use OLAP4PHP\OLAP\IOLAPStatement;
use OLAP4PHP\Metadata\ICube;

// Classes / Objects
use \Exception;
use \DOMDocument;
use \DOMElement;
use \InvalidArgumentException;
use \OutOfBoundsException;
use OLAP4PHP\OLAP\Axis;
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Provider\XMLA\XMLACell;
use OLAP4PHP\Provider\XMLA\XMLACellProperty;
use OLAP4PHP\Provider\XMLA\XMLACellSetAxis;
use OLAP4PHP\Provider\XMLA\XMLAPositionMember;
use OLAP4PHP\Provider\XMLA\XMLAStatement;
use OLAP4PHP\Provider\XMLA\XMLAUtil;
use OLAP4PHP\Provider\XMLA\XMLAPosition;

/**
 * @brief XMLA CellSet implementation
 */
class XMLACellSet implements ICellSet
{
   /**
    * @var array The cell set's axis list
    */
   private $axisList;

   /**
    * @var array  The cell map
    */
   private $cellMap;

   /**
    * @var XMLACellSetAxis  The cell set's filter axis
    */
   private $filterAxis;

   /**
    * @var XMLACellSetMetaData
    */
   private $metaData;

   /**
    * @var XMLAStatement  Stores statement (query) associated with the cell set (result)
    */
   private $statement;

   /**
    * @var array
    */
   static private $standardProperties = array( 'UName', 'Caption', 'LName', 'LNum', 'DisplayInfo' );

   /**
    * @brief XMLA CellSet implementation
    *
    * @param $statement
    *
    * @throws OLAPException
    */
   public function __construct( IOLAPStatement $statement )
   {
      if ( !$statement instanceof XMLAStatement )
      {
         throw new OLAPException ('Unexpected OLAP statement implementation.');
      }

      $this->cellMap   = array();
      $this->statement = $statement;
   }


   /*
   * @param DOMDocument $response Response document from XMLA execute request. The document is used to
   *  populate the set object.
   *
   * @throws OLAPException
   */
   public function populate( DOMDocument $response )
   {
      $envelope = $response->documentElement;

      assert( $envelope->localName == 'Envelope' );
      assert( $envelope->namespaceURI == XMLAUtil::SOAP_NS );

      $body  = XMLAUtil::findChild( $envelope, XMLAUtil::SOAP_NS, 'Body' );
      $fault = XMLAUtil::findChild( $body, XMLAUtil::SOAP_NS, 'Fault' );

      // Process all faults
      //
      if ( $fault )
      {
         $log = $this->statement->getLogger();
         if ( $log )
         {
            $log->error( __CLASS__, "Query Generated SOAP Fault" );
            $log->debug( __CLASS__, $response->saveXML() );
         }
         throw new OLAPException ('Query Fault: ' . $fault->textContent); // TODO add pretty exception
      }

      // Process execute response
      //
      $executeResponse = XMLAUtil::findChild( $body, XMLAUtil::XMLA_NS, 'ExecuteResponse' );
      $returnElement   = XMLAUtil::findChild( $executeResponse, XMLAUtil::XMLA_NS, 'return' );
      // <root> has children
      //   <xsd:schema/>
      //   <OlapInfo>
      //     <CubeInfo>
      //       <Cube>
      //         <CubeName>FOO</CubeName>
      //       </Cube>
      //     </CubeInfo>
      //     <AxesInfo>
      //       <AxisInfo/> ...
      //     </AxesInfo>
      //   </OlapInfo>
      //   <Axes>
      //      <Axis>
      //        <Tuples>
      //      </Axis>
      //      ...
      //   </Axes>
      //   <CellData>
      //      <Cell/>
      //      ...
      //   </CellData>
      $root = XMLAUtil::findChild( $returnElement, XMLAUtil::MDDATASET_NS, 'root' );

      // Process response meta data
      //
      $this->metaData = $this->createMetaData( $root );

      $axesNode = XMLAUtil::findChild( $root, XMLAUtil::MDDATASET_NS, 'Axes' );

      // First pass, gather up a list of member unique names to fetch
      // all at once.
      //
      //final MetadataReader metadataReader = metaData.cube.getMetadataReader();
      $memberMap   = array();
      $uniqueNames = array();

      foreach ( XMLAUtil::findChildren( $axesNode, XMLAUtil::MDDATASET_NS, 'Axis' ) as $axisNode )
      {
         $tuplesNode = XMLAUtil::findChild( $axisNode, XMLAUtil::MDDATASET_NS, 'Tuples' );

         foreach ( XMLAUtil::findChildren( $tuplesNode, XMLAUtil::MDDATASET_NS, 'Tuple' ) as $tupleNode )
         {
            foreach ( XMLAUtil::findChildren( $tupleNode, XMLAUtil::MDDATASET_NS, 'Member' ) as $memberNode )
            {
               $uniqueNames [] = XMLAUtil::stringElement( $memberNode, 'UName' );
            }
         }
      }

      // Fetch all members on all axes. Hopefully it can all be done in one
      // round trip, or they are in cache already.
      $metadataReader = $this->metaData->getCube()->getMetadataReader();
      $metadataReader->lookupMembersByUniqueName( $uniqueNames, $memberMap );

      // Second pass, populate the axis.
      foreach ( XMLAUtil::findChildren( $axesNode, XMLAUtil::MDDATASET_NS, 'Axis' ) as $axisNode )
      {
         $axisName      = $axisNode->getAttribute( 'name' );
         $axis          = $this->lookupAxis( $axisName );
         $positions     = array();
         $cellSetAxis   = new XMLACellSetAxis ($this, $axis, $positions);
         $axisPositions = $cellSetAxis->getPositions();

         if ( $axis->isFilter() ) // The filter axis is special
         {
            $this->filterAxis = $cellSetAxis;
         }
         else
         {
            $this->axisList [] = $cellSetAxis;
         }

         $tuplesNode = XMLAUtil::findChild( $axisNode, XMLAUtil::MDDATASET_NS, 'Tuples' );

         foreach ( XMLAUtil::findChildren( $tuplesNode, XMLAUtil::MDDATASET_NS, 'Tuple' ) as $tupleNode )
         {
            $members = array();

            foreach ( XMLAUtil::findChildren( $tupleNode, XMLAUtil::MDDATASET_NS, 'Member' ) as $memberNode )
            {
               $hierarchyName = $memberNode->getAttribute( 'Hierarchy' );
               $uname         = XMLAUtil::stringElement( $memberNode, 'UName' );
               $member        = (isset ($memberMap [$uname])) ? $memberMap [$uname] : null;

               if ( $member == null ) // Is the member not defined in the cube ?
               {
                  $caption   = XMLAUtil::stringElement( $memberNode, 'Caption' );
                  $lnum      = XMLAUtil::integerElement( $memberNode, 'LNum' ); // should we int type check this
                  $hierarchy = $this->lookupHierarchy( $this->metaData->getCube(), $hierarchyName );
                  $level     = $hierarchy->getLevels()->get( $lnum );

                  $member = new XMLAMemberUndefined (
                     $this, $level, $hierarchy, $lnum, $caption, $uname);

               }

               $propertyValues = array();
               foreach ( XMLAUtil::childElements( $memberNode ) as $childNode )
               {
                  $property = $cellSetAxis
                     ->getAxisMetaData()
                     ->lookupProperty(
                     $hierarchyName,
                     $childNode->localName );

                  if ( $property != null )
                  {
                     $value                                       = $childNode->textContent;
                     $propertyValues [$property->getUniqueName()] = $value;
                  }
               }

               if ( count( $propertyValues ) )
               {
                  $member = new XMLAPositionMember ($member, $propertyValues);
               }

               $members [] = $member;
            }

            $axisPositions[] = new XMLAPosition ($members, count( $axisPositions ));
         }
      }

      // OLAP4PHP requires a filter axis even if XMLA does not return one. If
      // XMLA does not return one, presumably there was no WHERE clause and
      // therefore the filter axis has a single position containing 0 members
      if ( $this->filterAxis == null )
      {
         $this->filterAxis =
            new XMLACellSetAxis (
               $this,
               Axis::getEnum( Axis::FILTER ),
               array( new XMLAPosition (array(), 0) ));
      }

      $cellDataNode = XMLAUtil::findChild( $root, XMLAUtil::MDDATASET_NS, 'CellData' );
      foreach ( XMLAUtil::findChildren( $cellDataNode, XMLAUtil::MDDATASET_NS, 'Cell' ) as $cell )
      {
         $propertyValues = array();
         $cellOrdinal    = $cell->getAttribute( 'CellOrdinal' );
         $value          = $this->getTypedValue( $cell );
         $formattedValue = XMLAUtil::stringElement( $cell, 'FmtValue' );
         $formatString   = XMLAUtil::stringElement( $cell, 'FormatString' );

         foreach ( XMLAUtil::childElements( $cell ) as $element )
         {
            $property = $this->metaData->getCellPropertiesByTag( $element->localName );
            if ( $property != null )
            {
               $this->propertyValues [$property->getUniqueName()] = $element->textContent;
            }
         }

         $this->cellMap [$cellOrdinal] =
            new XMLACell (
               $this,
               $cellOrdinal,
               $value,
               $formattedValue,
               $propertyValues);
      }
   }


   /**
    * @param DOMElement $cell
    *
    * @throws OLAPException
    */
   private function getTypedValue( DOMElement $cell )
   {
      $element = XMLAUtil::findChild( $cell, XMLAUtil::MDDATASET_NS, 'Value' );
      if ( $element == null ) // Cell is null.
      {
         return null;
      }

      // The object type is contained in xsi:type attribute.
      $type = $element->attributes->getNamedItem( 'xsi:type' );
      try
      {
         switch ( $type )
         {
            case 'xsd:int':
               return XMLAUtil::integerElement( $cell, 'Value' );
            case 'xsd:double':
               return XMLAUtil::doubleElement( $cell, 'Value' );
            case 'xsd:float':
               return XMLAUtil::floatElement( $cell, 'Value' );
            case 'xsd:long':
               return XMLAUtil::longElement( $cell, 'Value' );
            case 'xsd:boolean':
               return XMLAUtil::booleanElement( $cell, 'Value' );
            default:
               return XMLAUtil::stringElement( $cell, 'Value' );
         }
      }
      catch ( Exception $e )
      {
         throw new OLAPException (
            'Error while casting a cell value to the correct php type for' .
               ' its XSD type ' . $type
         );
      }
   }


   /**
    *
    * @param array $coordinates
    *
    * @return type
    * @throws InvalidArgumentException
    * @throws OutOfBoundsException
    */
   public function coordinatesToOrdinal( array $coordinates )
   {
      $axes = $this->getAxes();
      if ( count( $coordinates ) != count( $axes ) )
      {
         throw new InvalidArgumentException (
            'Coordinates have different dimension ' . count( $coordinates ) .
               ' than axes ' . count( $axes ));
      }

      $modulo  = 1;
      $ordinal = 0;
      $k       = 0;

      foreach ( $axes as $axis )
      {
         $coordinate = $coordinates [$k++];
         if ( !is_integer( $coordinate ) ||
            $coordinate < 0 ||
            $coordinate >= $axis->getPositionCount()
         )
         {
            throw new OutOfBoundsException (
               "Coordinate " . $coordinate .
                  " of axis " . $k .
                  " is out of range (" .
                  $this->getBoundsAsString() . ")");
         }

         $ordinal += $coordinate * $modulo;
         $modulo *= $axis->getPositionCount();
      }

      return $ordinal;
   }

   public function getAxes()
   {
      $copyAxisList = $this->axisList;
      return $copyAxisList;
   }


   public function getCellByOrdinal( $ordinal )
   {
      return $this->getCellInternal( $ordinal );
   }


   public function getCellByCoordinates( array $coordinates )
   {
      return $this->getCellInternal( $this->coordinatesToOrdinal( $coordinates ) );
   }


   public function getCellByPositions( array $positions )
   {
      if ( count( $positions ) != count( $this->getAxes() ) )
      {
         throw new InvalidArgumentException (
            "Cell coordinates should have dimension " . count( $this->getAxes() ));
      }

      $coords = array();
      foreach ( $positions as $position )
      {
         if ( !$position instanceof XMLAPosition )
         {
            throw new InvalidArgumentException ("Invalid position specified.");
         }

         $coords [] = $position->getOrdinal();
      }

      return $this->getCellByCoordinates( $coords ); //getCell(coords);
   }


   public function getFilterAxis()
   {
      return $this->filterAxis;
   }


   public function getMetaData()
   {
      return $this->metaData;
   }


   public function getStatement()
   {
      return $this->statement;
   }


   public function ordinalToCoordinates( $ordinal )
   {
      $axes   = $this->getAxes();
      $list   = array();
      $modulo = 1;

      foreach ( $axes as $axis )
      {
         $prevModulo = $modulo;
         $modulo *= $axis->getPositionCount();
         $list [] = (($ordinal % $modulo) / $prevModulo);
      }

      if ( $ordinal < 0 || $ordinal >= $modulo )
      {
         throw new OutOfBoundsException (
            'Cell ordinal (' . $ordinal .
               ') lies outside CellSet bounds (' .
               $this->getBoundsAsString() . ')'
         );
      }

      return $list;
   }

   public function getColumnAsArray( $columnOrdinal = 0 )
   {
      $result = array();

      $axes = $this->getAxes();
      if ( count( $axes ) < 2 )
      {
         // query without columns, just return the 1 series
         $axis = $axes [0];
         for ( $x = 0; $x < $axis->getPositionCount(); $x++ )
         {
            $cell     = $this->getCellByCoordinates( array( $x ) );
            $result[] = $cell->getFormattedValue();
         }
      }
      else
      {
         $rows = $axes[AXIS::ROWS];
         $cols = $axes[AXIS::COLUMNS];

         if ( $columnOrdinal >= $cols->getPositionCount() )
         {
            throw new \OutOfBoundsException("Column $columnOrdinal is out of bounds.");
         }

         for ( $x = 0; $x < $rows->getPositionCount(); $x++ )
         {
            $cell     = $this->getCellByCoordinates( array( $columnOrdinal, $x ) );
            $result[] = $cell->getFormattedValue();
         }
      }

      return $result;
   }

   /**
    * @param $root Response root element.
    *
    * @throws OLAPException
    */
   private function createMetaData( DOMElement $root )
   {
      $olapInfo     = XMLAUtil::findChild( $root, XMLAUtil::MDDATASET_NS, "OlapInfo" );
      $cubeInfo     = XMLAUtil::findChild( $olapInfo, XMLAUtil::MDDATASET_NS, "CubeInfo" );
      $cubeNode     = XMLAUtil::findChild( $cubeInfo, XMLAUtil::MDDATASET_NS, "Cube" );
      $cubeNameNode = XMLAUtil::findChild( $cubeNode, XMLAUtil::MDDATASET_NS, "CubeName" );
      $cubeName     = XMLAUtil::gatherText( $cubeNameNode );

      // REVIEW: If there are multiple cubes with the same name, we should
      // qualify by catalog and schema. Currently we just take the first.
      $cube =
         $this->lookupCube(
            $this->statement->getConnection()->getMetadata(),
            $cubeName );
      if ( $cube == null )
      {
         throw new OLAPException ("Internal error: cube '$cubeName' not found.");
      }
      // REVIEW: We should not modify the connection. It is not safe, because
      // connection might be shared between multiple statements with different
      // cubes. Caller should call
      //
      // connection.setCatalog(
      //   cellSet.getMetaData().getCube().getSchema().getCatalog().getName())
      //
      // before doing metadata queries.
      $this->statement->getConnection()->setCatalog(
         $cube->getSchema()->getCatalog()->getName() );

      $axesInfo           = XMLAUtil::findChild( $olapInfo, XMLAUtil::MDDATASET_NS, "AxesInfo" );
      $axisInfos          = XMLAUtil::findChildren( $axesInfo, XMLAUtil::MDDATASET_NS, "AxisInfo" );
      $axisMetaDataList   = array();
      $filterAxisMetaData = null;

      foreach ( $axisInfos as $axisInfo )
      {
         $axisName       = $axisInfo->getAttribute( 'name' );
         $axis           = $this->lookupAxis( $axisName );
         $hierarchyInfos = XMLAUtil::findChildren( $axisInfo, XMLAUtil::MDDATASET_NS, 'HierarchyInfo' );
         $hierarchyList  = array();

         /*
        <OlapInfo>
            <AxesInfo>
                <AxisInfo name="Axis0">
                    <HierarchyInfo name="Customers">
                        <UName name="[Customers].[MEMBER_UNIQUE_NAME]"/>
                        <Caption name="[Customers].[MEMBER_CAPTION]"/>
                        <LName name="[Customers].[LEVEL_UNIQUE_NAME]"/>
                        <LNum name="[Customers].[LEVEL_NUMBER]"/>
                        <DisplayInfo name="[Customers].[DISPLAY_INFO]"/>
                    </HierarchyInfo>
                </AxisInfo>
                ...
            </AxesInfo>
            <CellInfo>
                <Value name="VALUE"/>
                <FmtValue name="FORMATTED_VALUE"/>
                <FormatString name="FORMAT_STRING"/>
            </CellInfo>
        </OlapInfo>
         */
         $propertyList = array();
         foreach ( $hierarchyInfos as $hierarchyInfo )
         {
            $hierarchyName    = $hierarchyInfo->getAttribute( 'name' );
            $hierarchy        = $this->lookupHierarchy( $cube, $hierarchyName );
            $hierarchyList [] = $hierarchy;

            foreach ( XMLAUtil::childElements( $hierarchyInfo ) as $childNode )
            {
               $tag = $childNode->localName;
               if ( in_array( $tag, self::$standardProperties ) )
               {
                  continue;
               }

               $propertyUniqueName = $childNode->getAttribute( 'name' );
               $property           =
                  new XMLACellSetMemberProperty (
                     $propertyUniqueName,
                     $hierarchy,
                     $tag
                  );

               $propertyList [] = $property;
            }
         }

         $axisMetaData =
            new XMLACellSetAxisMetaData (
               $this->statement->getConnection(),
               $axis,
               $hierarchyList,
               $propertyList
            );

         if ( $axis->isFilter() )
         {
            $filterAxisMetaData = $axisMetaData;
         }
         else
         {
            $axisMetaDataList [] = $axisMetaData;
         }
      }

      if ( $filterAxisMetaData == null )
      {
         $filterAxisMetaData =
            new XMLACellSetAxisMetaData (
               $this->statement->getConnection(),
               Axis::getEnum( Axis::FILTER ),
               array(),
               array()
            );
      }

      $cellInfo       = XMLAUtil::findChild( $olapInfo, XMLAUtil::MDDATASET_NS, 'CellInfo' );
      $cellProperties = array();

      foreach ( XMLAUtil::childElements( $cellInfo ) as $element )
      {
         $cellProperties [] =
            new XMLACellProperty (
               $element->localName,
               $element->getAttribute( 'name' ));
      }

      return
         new XMLACellSetMetaData (
            $this->statement,
            $cube,
            $filterAxisMetaData,
            $axisMetaDataList,
            $cellProperties);
   }


   private function lookupCube( XMLADatabaseMetaData $databaseMetaData, $cubeName )
   {
      $catalog = $databaseMetaData->getCatalogObjects()->get( $this->statement->getConnection()->getCatalog() );
      foreach ( $catalog->getSchemas() as $schema )
      {
         foreach ( $schema->getCubes() as $cube )
         {
            if ( $cubeName == $cube->getName() )
            {
               return $cube;
            }

            if ( $cubeName == '[' . $cube->getName() . ']' )
            {
               return $cube;
            }
         }
      }

      return null;
   }


   /**
    * @param $axisName
    *
    * @throws OLAPException
    */
   private function lookupAxis( $axisName )
   {
      if ( substr( $axisName, 0, 4 ) == 'Axis' )
      {
         $ordinal = (int)str_replace( 'Axis', '', $axisName );
         return Axis::getEnum( $ordinal );
      }
      else
      {
         return Axis::getEnum( Axis::FILTER );
      }

      assert( false ); // should never get here
   }


   /**
    * Looks up a hierarchy in a cube with a given name or, failing that, a
    * given unique name. Throws if not found.
    *
    * @param ICube  $cube
    * @param string $hierarchyName Name (or unique name) of hierarchy.
    *
    * @return IHierarchy
    * @throws OLAPException on error
    */
   private function lookupHierarchy( XMLACube $cube, $hierarchyName )
   {
      $hierarchy = $cube->getHierarchies()->get( $hierarchyName );
      if ( $hierarchy == null )
      {
         foreach ( $cube->getHierarchies() as $hierarchy1 )
         {
            if ( $hierarchy1->getUniqueName() == $hierarchyName )
            {
               $hierarchy = $hierarchy1;
               break;
            }
         }

         if ( $hierarchy == null )
         {
            throw new OLAPException (
               "Internal error: hierarchy '" . $hierarchyName .
                  "' not found in cube '" . $cube->getName() . "'"
            );
         }
      }

      return $hierarchy;
   }


   /**
    * Returns a cell given its ordinal.
    *
    * @param integer $ordinal The cell's ordinal
    *
    * @return XMLACell
    * @throws OutOfBoundsException if ordinal is not in range
    */
   private function getCellInternal( $ordinal )
   {
      $cell = isset ($this->cellMap [$ordinal])
         ? $this->cellMap [$ordinal]
         : null;

      if ( $cell == null )
      {
         if ( $ordinal < 0 || $ordinal >= $this->maxOrdinal() )
         {
            throw new OutOfBoundsException ();
         }
         else
         {
            // Cell is within bounds, but is not held in the cache because
            // it has no value. Manufacture a cell with an empty value.
            return new XMLACell (
               $this,
               $ordinal,
               null,
               '',
               array()
            );
         }
      }

      return $cell;
   }


   /**
    * Returns the ordinal of the last cell in this cell set. This is the
    * product of the cardinalities of all axes.
    *
    * @return integer Ordinal of last cell in cell set.
    */
   private function maxOrdinal()
   {
      $modulo = 1;
      foreach ( $this->axisList as $cellSetAxis )
      {
         $modulo *= $cellSetAxis->getPositionCount();
      }

      return $modulo;
   }


   /**
    * Returns a string describing the maximum coordinates of this cell set;
    * for example "2, 3" for a cell set with 2 columns and 3 rows.
    *
    * @return description of cell set bounds
    */
   private function getBoundsAsString()
   {
      $buf = null;
      $k   = 0;

      foreach ( $this->getAxes() as $axis )
      {
         if ( $k++ > 0 )
         {
            $buf .= ", ";
         }

         $buf .= $axis->getPositionCount();
      }

      return $buf;
   }
}
