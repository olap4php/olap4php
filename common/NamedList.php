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

// Interfaces used
use OLAP4PHP\Metadata\INamed;
use OLAP4PHP\MetaData\INamedList;

// Classes used
use \ArrayObject;
use \InvalidArgumentException;

/**
 *
 */
class NamedList extends ArrayObject implements INamedList
{
   public function get( $index )
   {
      if ( parent::offsetExists( $index ) ) return parent::offsetGet( $index );

      // trying to get by index
      if ( is_string( $index ) )
      {
         return parent::offsetExists( $index ) ? parent::offsetGet( $index ) : NULL;
      }
      else {
         if ( is_numeric( $index ) )
         {
            $index = (int)$index;
            if ( $index < 0 || $index >= parent::count() ) return NULL;

            $indexedArray = array_values( parent::getArrayCopy() );
            return $indexedArray[$index];
            //return $obj;
         }
      }

      throw new InvalidArgumentException ("Index $index does not exist in NamedList.");
   }


   public function indexOfName( $name )
   {
      $indexedKeys = array_keys( parent::getArrayCopy() );
      $key         = array_search( $name, $indexedKeys );
      if ( $key !== FALSE )
      {
         return $key;
      }
      else
      {
         return NULL;
      }
   }


   public function size()
   {
      return parent::count();
   }

   public function add( INamed $obj )
   {
      parent::offsetSet( $obj->getName(), $obj );
   }

   /**
    * Add all array object elements to object.
    *
    * @param ArrayObject $list
    */
   public function addAll( ArrayObject $list )
   {
      foreach ( $list as $key => $value )
      {
         parent::offsetSet( $key, $value );
      }
   }
}
