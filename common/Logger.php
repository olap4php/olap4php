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

// Use classes
use \RuntimeException;

/**
 * OLAP4PHP Logger
 *
 * Used to wrap the injection of external loggers.
 *
 * This class currently only works with log4php.
 */
class Logger
{
   private $logger;

   /**
    * Construct a logger
    */
   public function __construct ( $logger )
   {
      $this->logger = $logger;
   }

   public function fatal ( $msg, $caller = NULL )
   {
      $this->logger->fatal ( $msg, $caller );
   }

   public function error ( $msg, $caller = NULL )
   {
      $this->logger->error ( $msg, $caller );
   }

   public function warn ( $msg, $caller = NULL )
   {
      $this->logger->warn ( $msg, $caller );
   }

   public function info ( $msg, $caller = NULL )
   {
      $this->logger->info ( $msg, $caller );
   }

   public function debug ( $msg, $caller = NULL )
   {
      $this->logger->debug ( $msg, $caller );
   }
}