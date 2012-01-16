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
use OLAP4PHP\MetaData\IProperty;
use OLAP4PHP\OLAP\MetaData\INamed;

// Classes
use OLAP4PHP\Metadata\DataType;
use OLAP4PHP\Metadata\PropertyType;
use OLAP4PHP\Metadata\PropertyContentType;


/**
 * @brief XMLA Property Implementation
 */
class XMLAProperty extends XMLAElement implements IProperty, INamed
{
   private $datatype;
   private $type;
   private $contentType;

   /**
    * Constructor
    *
    * @param string $uniqueName
    * @param string $name
    * @param string $caption
    * @param string $description
    * @param Datatype $datatype
    * @param array PropertyType $type
    * @param PropertyContentType $contentType
    */
   public function __construct (
      $uniqueName,
      $name,
      $caption,
      $description,
      DataType $datatype,
      PropertyType $type,
      PropertyContentType $contentType
      )
   {
      parent::__construct ( $uniqueName, $name, $caption, $description );
      $this->contentType = $contentType;
      assert ( $datatype != null );
      assert ( $type != null );
      $this->datatype = $datatype;
      $this->type = $type;
   }

   /**
    * @return DataType
    */
   public function getDatatype ( )
   {
      return $this->datatype;
   }

   /**
    * @return PropertyType
    */
   public function getType ( )
   {
      return $this->type;
   }

   /**
    * @return PropertyContentType
    */
   public function getContentType ( )
   {
      return $this->contentType;
   }
}