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

use OLAP4PHP\Provider\XMLA\XMLAConnection;
use OLAP4PHP\OLAP\OLAPException;

/**
 * A weak implementation of a Java ResultSet, just using arrays.
 */
class ResultSet
{
   /**
    * @var array
    */
   private $data = array();

   public function __construct( array $headerList, array $rowList )
   {
      foreach ( $rowList as $row )
      {
         if ( !is_array( $row ) )
         {
            throw new OLAPException('ResultSet: a row of $rowList was not an array.');
         }

         foreach ( $headerList as $header )
         {
            if ( !isset($this->data[$header]) ) $this->data[$header] = array();

            $this->data[$header][] = array_shift( $row );
         }
      }
   }

   public function getColumnNames()
   {
      return array_keys( $this->data );
   }

   public function fetchRowAssoc()
   {
      $row   = array();
      $empty = TRUE;
      foreach ( $this->data as $header => $column )
      {
         if ( !empty($column) )
         {
            $empty        = FALSE;
            $row[$header] = array_shift( $this->data[$header] );
         }
      }

      if ( $empty ) return NULL;

      return $row;
   }

   public function getColumnAtIndex( $column, $index )
   {
      if ( !isset($this->data[$column]) || !isset($this->data[$column][$index]) ) return NULL;

      return $this->data[$column][$index];
   }
}
