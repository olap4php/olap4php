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
interface IXMLAConstantDictionary
{
   /**
    * Returns the enumeration value with the given ordinal in the XMLA
    * specification, or null if there is no such.
    *
    * @param integer $xmlaOrdinal XMLA ordinal
    *
    * @return Enumeration value
    */
   public function forOrdinal( $xmlaOrdinal );

   /**
    * Returns the enumeration value with the given name in the XMLA
    * specification, or null if there is no such.
    *
    * @param string $xmlaName XMLA name
    *
    * @return Enumeration value
    */
   public function forName( $xmlaName );

   /**
    * Creates a set of values by parsing a mask.
    *
    * @param integer $xmlaOrdinalMask Bit mask
    *
    * @return array Set of E values
    */
   public function forMask( $xmlaOrdinalMask );

   /**
    * Converts a set of enum values to an integer by logical OR-ing their
    * codes.
    *
    * @param array $set Set of enum values
    *
    * @return integer Bitmap representing set of enum values
    */
   public function toMask( array $set );

   /**
    * Returns all values of the enum.
    *
    * <p>This method may be more efficient than
    * {@link Class#getEnumConstants()} because the latter is required to
    * create a new array every call to prevent corruption.
    *
    * @return array List of enum values
    */
   public function getValues();

   /**
    * Returns the class that the enum values belong to.
    *
    * @return object
    */
   public function getEnumClass();

}
