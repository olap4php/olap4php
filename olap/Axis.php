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
namespace OLAP4PHP\OLAP;

use OLAP4PHP\OLAP\IAxis;

/**
 *
 */
class Axis implements IAxis
{
   private $ordinal;

   const FILTER   = -1;
   const COLUMNS  = 0;
   const ROWS     = 1;
   const PAGES    = 2;
   const CHAPTERS = 3;
   const SECTIONS = 4;


   public function __construct( $ordinal )
   {
      $this->ordinal = $ordinal;
   }


   public function axisOrdinal()
   {
      return $this->ordinal;
   }


   public function getCaption()
   {
      return $this->name();
   }


   public function isFilter()
   {
      return ($this->ordinal == Axis::FILTER) ? true : false;
   }


   public function name()
   {
      return 'AXIS(' . $this->ordinal . ')';
   }

   static public function getEnum( $constant )
   {
      return new Axis ($constant);
   }
}
