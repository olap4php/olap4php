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

// Use Interfaces
use OLAP4PHP\Metadata\IMeasure;
use OLAP4PHP\Metadata\INamed;

// Classes / Objects
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Metadata\Aggregator;
use OLAP4PHP\Metadata\MemberType;
use OLAP4PHP\Metadata\DimensionType;

/**
 * @brief XMLA Measure Implementation
 */
class XMLAMeasure extends XMLAMember implements IMeasure, INamed
{
   private $aggregator;
   private $datatype;
   private $visible;

   /*
    * Constructor
    *
    * @param XMLALevel $level
    * @param $uniqueName
    * @param $name
    * @param $caption
    * @param $description
    * @param $parentMemberUniqueName
    * @param $aggregator
    * @param $datatype
    * @param $visible
    * @param $ordinal
    *
    */
   public function __construct (
      XMLALevel $level,
      $uniqueName,
      $name,
      $caption,
      $description,
      $parentMemberUniqueName,
      $aggregator,
      $datatype,
      $visible,
      $ordinal
      )
   {
      parent::__construct (
         $level,
         $uniqueName,
         $name,
         $caption,
         $description,
         $parentMemberUniqueName,
         $aggregator == Aggregator::getEnum ( Aggregator::CALCULATED )
            ? MemberType::getEnum ( MemberType::FORMULA )
            : MemberType::getEnum ( MemberType::MEASURE ),
         0,
         $ordinal,
         array ( )
      );

      assert ( $level->getHierarchy ( )->getDimension ( )->getDimensionType ( )
               == DimensionType::getEnum ( DimensionType::MEASURE ) );

      $this->aggregator = $aggregator;
      $this->datatype = $datatype;
      $this->visible = $visible;
   }

   public function getAggregator ( )
   {
      return $this->aggregator;
   }

   public function getDatatype ( )
   {
      return $this->datatype;
   }

   public function isVisible ( )
   {
      return $this->visible;
   }
}