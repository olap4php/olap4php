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

/**
 *
 */
interface IMember extends IMetadataElement
{
   /**
    * @return array IMember
    */
   public function getAncestorMembers();


   /**
    * @return int
    */
   public function getChildMemberCount();


   /**
    * @return array IMember
    */
   public function getChildMembers();


   /**
    * @return IMember
    */
   public function getDataMember();


   /**
    * @return int
    */
   public function getDepth();


   /**
    * @return IDimension
    */
   public function getDimension();


   /**
    * @return IHierarchy
    */
   public function getHierarchy();


   /**
    * @return ILevel
    */
   public function getLevel();


   /**
    * @return IType
    */
   public function getMemberType();


   /**
    * @return int
    */
   public function getOrdinal();


   /**
    * @return IMember
    */
   public function getParentMember();


   /**
    * @return boolean
    */
   public function isHidden();

}

