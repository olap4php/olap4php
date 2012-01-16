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
namespace OLAP4PHP\Provider\XMLA\Metadata;

use OLAP4PHP\Provider\XMLA\XMLAMetadataRequest;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\OLAP\OLAPException;
use OLAP4PHP\Common\NamedList;

/**
 * @brief Lazy-loading Metadata List class
 *
 * Our implementation of the olap4j DeferredNamedList.
 * Kind of works differently in PHP, so the name didn't seem
 * quite right to adhere to.  Sorry guys.  We still love you.
 * In a platonic way, though.  Well, I can't speak for John ...
 *
 */
class LazyMetadataList extends NamedList
{
   const STATE_NEW         = 1;
   const STATE_POPULATING  = 2;
   const STATE_POPULATED   = 3;

   private $state;

   private $metadataRequest;

   private $context;

   private $handler;

   private $restrictions;

   public function __construct( XMLAMetadataRequest $metadataRequest, XMLAConnectionContext $context, IXMLAMetadataHandler $handler, array $restrictions = NULL )
   {
      $this->state = self::STATE_NEW;
      $this->metadataRequest = $metadataRequest;
      $this->context = $context;
      $this->handler = $handler;

      if ( !$restrictions )
         $this->restrictions = array();
      else
         $this->restrictions = $restrictions;
   }

   private function getList()
   {
      switch ( $this->state )
      {
         case self::STATE_POPULATING:
            throw new \Exception( 'Recursive Population: ' );
         case self::STATE_NEW:
            //print 'STATE NEW' . PHP_EOL;
            try
            {
               $this->state = self::STATE_POPULATING;
               $this->populateList( );
               $this->state = self::STATE_POPULATED;
            }
            catch ( OLAPException $e )
            {
               $this->state = self::STATE_NEW;
               throw new \Exception( $e->getMessage() );
            }
         case self::STATE_POPULATED:
         default:
            //print 'getList: returning $this' . PHP_EOL;
            return $this;
      }
   }

   public function get ( $index )
   {
      if ( $this->state == self::STATE_NEW )
         $this->getList ( );

      return parent::get( $index );

      /*
      if ( parent::offsetExists( $index ) ) return parent::offsetGet ( $index );

      // trying to get by index
      if ( is_numeric( $index ) )
      {
         $index = (int)$index;
         // since PHP arrays are actually hash tables, "index" doesn't really exist.
         // in this case, we emulate it with a counting foreach loop.  it's still fast.
         if ( $index <= 0 || $index >= parent::count () ) return NULL;

         $i = 0;
         foreach ( $this as $key => $obj )
         {
            if ( $i == $index ) return $obj;
            $i++;
         }
      }
      else
      {
         return parent::offsetExists ( $index ) ? parent::offsetGet ( $index ) : NULL;
      }
       *
       */
   }

   public function indexOfName( $name )
   {
      if ( $this->state == self::STATE_NEW )
              $this->getList();

      return parent::indexOfName( $name );
      /*
      $i = 0;
      foreach ( $this as $key => $obj )
      {
         if ( $key == $name ) return $i;
         $i++;
      }
       *
       */
   }

   public function size ( )
   {
      //if ( !parent::count () )
      if ( $this->state == self::STATE_NEW )
         $this->getList ( );
      return parent::size ( );
   }

   protected function populateList( )
   {
      //print 'LML::populateList' . PHP_EOL;
      $this->context->xmlaConnection
         ->populateList (
            $this,
            $this->context,
            $this->metadataRequest,
            $this->handler,
            $this->restrictions );

      //var_dump( $list );
   }

   public function getIterator()
   {
      //print 'LML::getIterator' . PHP_EOL;
      if ( $this->state == self::STATE_NEW )
      {
         //print 'getIterator()->getList(): ' . get_class( $this->handler ) . PHP_EOL;
         $this->getList();
      }
      return parent::getIterator();
   }
}