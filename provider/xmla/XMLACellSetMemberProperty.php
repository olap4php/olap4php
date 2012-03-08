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
use OLAP4PHP\Metadata\IProperty;


/**
 * @brief XMLA Cell Set Member Property Implementation
 */
class XMLACellSetMemberProperty implements IProperty
{
   private $hierarchy;
   private $name;
   private $uniqueName;


   /**
    * @param string        $uniqueName
    * @param XMLAHierarchy $hierarchy
    * @param string        $name
    */
   public function __construct( $uniqueName, XMLAHierarchy $hierarchy, $name )
   {
      $this->uniqueName = $uniqueName;
      $this->hierarchy  = $hierarchy;
      $this->name       = $name;
   }


   /**
    * @return ContentType
    */
   public function getContentType()
   {
   }


   /**
    * @return DataType
    */
   public function getDataType()
   {
   }

   /**
    * @return string
    */
   public function getCaption()
   {
   }


   /**
    * @return string
    */
   public function getDescription()
   {
   }


   /**
    * @return string
    */
   public function getUniqueName()
   {
      return $this->uniqueName;
   }
}
