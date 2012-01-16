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
namespace OLAP4PHP\Provider\Caching\Drivers;

use OLAP4PHP\Provider\Caching\IXMLACache;

/**
 * Description of MCache
 *
 * @author paulb
 */
class MCache extends \Memcached implements IXMLACache
{   
   /**
    * Will store the value within the cache under the specified key. The expiration
    * parameter can be used to control when the value is considered expired in the cache.
    */
   public function set( $key, $value, $expiration = null )
   {
      return parent::set( $key, $value, $expiration );
   }
   
   /**
    * Returns the item that was stored in the cache under the given key. This
    * returns the value stored in the cache or FALSE otherwise
    */
   public function get( $key )
   {
      return parent::get( $key );
   }
   
   /**
    * Deletes the key from the cache. Once the item has been marked as deleted, it
    * can no longer be accessed in the cache
    */
   public function delete( $key )
   {
      return parent::delete( $key );
   }
   
   //TODO:
   // Should expose a hasValue method which will get the value and return the getResult
   // Code() value. This will cache the value locally if found, since logically, the
   // next call to this class will probably be the get() on the key.
   // 
   // Currently most code is using the get() return type to determine if a value
   // exists; this makes it hard to store 'false' as it also indicates no data found
   // on a get() call.
   // 
   // Currently this is safe, as we are mostly storing objects and not primitive booleans

}

?>
