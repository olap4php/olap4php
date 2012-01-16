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


/**
 * XMLA Implementation of ICell Interface
 */
class XMLACell implements ICell
{
   private $cellSet;
   private $formattedValue;
   private $ordinal;
   private $propertyValues;
   private $value;


   /**
    * Construtor
    *
    * @param XMLACellSet $cellset
    * @param integer $ordinal
    * @param mixed $value
    * @param string $formattedValue
    * @param array $propertyValues
    */
   public function __construct (
        XMLACellSet $cellSet,
        $ordinal,
        $value,
        $formattedValue,
        $propertyValues )
    {
       $this->cellSet = $cellSet;
       $this->formattedValue = $formattedValue;
       $this->ordinal = $ordinal;
       $this->propertyValues = $propertyValues;
       $this->value = $value;
    }


   public function getCellSet ( )
   {
      return $this->cellSet;
   }

   public function getCoordinateList ( )
   {
      return $this->cellSet->ordinalToCoordinates ( $this->ordinal );
   }

   public function getErrorText ( )
   {
      return null;
   }

   public function getFormattedValue ( )
   {
      return $this->formattedValue;
   }

   public function getOrdinal ( )
   {
      return $this->ordinal;
   }

   public function getPropertyValue ( $property )
   {
      if ( isset ( $this->propertyValues [ $property ] ) )
         return $this->propertyValues [ $property ];
   }

   public function getValue ( )
   {
      return $this->value;
   }

   public function isEmpty ( )
   {
      return $this->isNull ( );
   }

   public function isError ( )
   {
      return false;
   }

   public function isNull ( )
   {
      if ( $this->value === null )
         return true;
   }
}