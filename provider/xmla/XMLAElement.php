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
use OLAP4PHP\Metadata\IMetadataElement;
use OLAP4PHP\Metadata\INamed;

use OLAP4PHP\Provider\XMLA\XMLAUtil;

/**
 * XMLA Implementation of IDimension Interface
 */
abstract class XMLAElement implements IMetadataElement, INamed
{
   protected $uniqueName;
   protected $name;
   protected $caption;
   protected $description;
   private $hash;

   public function __construct (
      $uniqueName,
      $name,
      $caption,
      $description
      )
   {
      assert ( $uniqueName != null );
      assert ( $description !== null );
      assert ( $name != null );
      assert ( $caption != null );
      $this->description = $description;
      $this->uniqueName = $uniqueName;
      $this->caption = $caption;
      $this->name = $name;
   }

   public function getName ( )
   {
      return $this->name;
   }

   public function getUniqueName ( )
   {
      return $this->uniqueName;
   }

   public function getCaption ( )
   {
      return $this->caption;
   }

   public function getDescription( )
   {
      return $this->description;
   }

   public function hashCode ( )
   {
      if ( empty( $this->hash ) )
      {
         $this->hash = XMLAUtil::javaStringHashCode( $this->uniqueName );
      }

      return $this->hash;
    }
}