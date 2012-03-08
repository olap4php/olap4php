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
namespace OLAP4PHP\Metadata;

use OLAP4PHP\Metadata\INamed;

/**
 *
 */
interface ICube
{
   /**
    * @return array IDimension
    */
   public function getDimensions();


   /**
    * @return array IHierarchy
    */
   public function getHierarchies();


   /**
    * @return array IMeasure
    */
   public function getMeasures();


   /**
    * @return array ISchema
    */
   public function getSchema();


   /**
    * @return array INamedSet
    */
   public function getSets();


   /**
    * @return array IMember
    */
   public function lookupMember( array $namedParts );


   /**
    * @return array IMember
    */
   public function lookupMembers( array $treeOps, array $nameParts );
}
