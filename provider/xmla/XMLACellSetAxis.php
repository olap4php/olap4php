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
use OLAP4PHP\OLAP\ICellSetAxis;

// Classes / Objects
use \ArrayObject;
use OLAP4PHP\OLAP\Axis;
use OLAP4PHP\Provider\XMLA\XMLACellSet;


/**
 * @brief XMLA CellSetAxis implementation
 */
class XMLACellSetAxis extends ArrayObject implements ICellSetAxis
{
   private $cellSet;
   private $axis;
   private $positions;
   private $positionsIter;


   /**
    * @param ICellSet $cellSet
    * @param IAxis    $axis
    * @param          array IPosition $positions
    */
   public function __construct( XMLACellSet $cellSet, Axis $axis, array $positions )
   {
      $this->cellSet   = $cellSet;
      $this->axis      = $axis;
      $this->positions = new ArrayObject($positions);
   }


   /**
    * @return XMLACellSetAxisMetaData
    */
   public function getAxisMetaData()
   {
      $cellSetMetaData = $this->cellSet->getMetaData();
      if ( $this->axis->isFilter() )
      {
         return $cellSetMetaData->getFilterAxisMetaData();
      }
      else
      {
         $axesMetaData = $cellSetMetaData->getAxesMetaData();
         return $axesMetaData [$this->axis->axisOrdinal()];
      }
   }


   /**
    * @return IAxis
    */
   public function getAxisOrdinal()
   {
      return $this->axis->axisOrdinal();
   }


   /**
    * @return XMLACellSet
    */
   public function getCellSet()
   {
      return $this->cellSet;
   }


   /**
    * @return int
    */
   public function getPositionCount()
   {
      return $this->getPositions()->count();
   }


   /**
    * @return array XMLAPosition
    */
   public function getPositions()
   {
      return $this->positions;
   }

   public function getIterator()
   {
      return $this->getPositions()->getIterator();
   }

   public function count()
   {
      return $this->getPositions()->count();
   }

   public function offsetGet( $offset )
   {
      return $this->getPositions()->offsetGet( $offset );
   }

   public function offsetSet( $offset, $value )
   {
      return $this->getPositions()->offsetSet( $offset, $value );
   }

   public function offsetExists( $offset )
   {
      return $this->getPositions()->offsetExists( $offset );
   }

   public function offsetUnset( $offset )
   {
      return $this->getPositions()->offsetUnset( $offset );
   }
}
