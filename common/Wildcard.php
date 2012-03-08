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
namespace OLAP4PHP\Common;

use OLAP4PHP\OLAP\OLAPException;

/**
 * @brief Holds a Wildcard pattern
 */
class Wildcard
{
   /**
    * @var string
    */
   private $pattern;

   /**
    * @brief Class Constructor
    *
    * @param string $pattern
    */
   public function __construct( $pattern )
   {
      if ( empty($pattern) ) throw new OLAPException('Cannot create an empty Wildcard pattern');

      $this->pattern = $pattern;
   }

   /**
    * @brief Returns the wildcard pattern
    *
    * @return string
    */
   public function getPattern()
   {
      return $this->pattern;
   }
}
