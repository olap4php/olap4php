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

use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Metadata\DimensionType;
use OLAP4PHP\Provider\XMLA\XMLALevel;

/**
 * @brief XMLA Metadata Reader Implementation
 *
 * This class will someday cache the meta data for a cube.
 */
class XMLACachingMetadataReader extends XMLADelegatingMetadataReader
{
   /// string => XMLAMeasure
   private $measureMap = array();

   /// string => XMLAMember
   private $memberMap = array();

   /* park this one
   private final Map<
      XmlaOlap4jLevel,
      SoftReference<List<XmlaOlap4jMember>>> levelMemberListMap =
      new HashMap<
         XmlaOlap4jLevel,
         SoftReference<List<XmlaOlap4jMember>>>(); */

   /**
    * Constructor
    *
    * @param IXMLAMetadataReader $metadataReader
    * @param array               $measureMap
    *
    */
   public function __construct( IXMLAMetadataReader $metadataReader, array $measureMap = NULL )
   {
      parent::__construct( $metadataReader );

      if ( !$measureMap )
      {
         $this->measureMap = array();
      }
      else
      {
         $this->measureMap = $measureMap;
      }
   }


   /**
    * Looks up a member by its unique name.
    *
    * @param string $memberUniqueName Unique name of member
    *
    * @return XMLAMember, or null if not found
    * @throws OLAPException if error occurs
    */
   public function lookupMemberByUniqueName( $memberUniqueName )
   {
      // First, look in measures map.
      if ( isset ($this->measureMap [$memberUniqueName]) )
      {
         return $this->measureMap [$memberUniqueName];
      }

      // Next, look in the member cache.
      if ( isset ($this->memberMap [$memberUniqueName]) )
      {
         return $this->memberMap [$memberUniqueName];
      }

      // Next, pass the lookup up the reader composition
      $member = parent::lookupMemberByUniqueName( $memberUniqueName );
      if ( $member != null
         && $member->getDimension()->getDimensionType()
            !== DimensionType::getEnum( DimensionType::MEASURE )
      )
      {
         $this->memberMap [$memberUniqueName] = $member;
      }

      return $member;
   }


   /**
    * Looks up a list of members by their unique name and writes the results
    * into a map.
    *
    * @param array $memberUniqueNames List of unique names of member
    *
    * @param array $memberMap         Map to populate with members
    *
    * @throws OLAPException if error occurs
    */
   public function lookupMembersByUniqueName( array $memberUniqueNames, array& $memberMap )
   {
      $remainingMemberUniqueNames = array();
      foreach ( $memberUniqueNames as $memberUniqueName )
      {
         // First, look in measures map.
         if ( isset ($this->measureMap [$memberUniqueName]) )
         {
            $memberMap [$memberUniqueName] = $this->measureMap [$memberUniqueName];
            continue;
         }

         // Next, look in cache.
         if ( isset ($this->memberMap [$memberUniqueName]) )
         {
            $memberMap [$memberUniqueName] = $this->memberMap [$memberUniqueName];
            continue;
         }

         $remainingMemberUniqueNames [] = $memberUniqueName;
      }

      // If any of the member names were not in the cache, look them up
      // by delegating.
      if ( !empty ($remainingMemberUniqueNames) )
      {
         parent::lookupMembersByUniqueName( $remainingMemberUniqueNames, $memberMap );

         // Add the previously missing members into the cache.
         foreach ( $remainingMemberUniqueNames as $memberName )
         {
            if ( isset ($memberMap [$memberName]) )
            {
               $member = $memberMap [$memberName];

               if ( !($member instanceof IMeasure)
                  && $member->getDimension()->getDimensionType()->getConstant() != DimensionType::MEASURE
               )
               {
                  $this->memberMap [$memberName] = $member;
               }
            }
         }
      }
   }


   /**
    * Looks a member by its unique name and returns members related by
    * the specified tree-operations.
    *
    * @param array  treeOps Collection of tree operations to travel relative to
    *               given member in order to create list of members
    *
    * @param string memberUniqueName Unique name of member
    *
    * @param array  IMember List to be populated with members related to the given
    *               member, or empty set if the member is not found
    *
    * @throws OLAPException if error occurs
    */
   public function lookupMemberRelatives( array $treeOps, $memberUniqueName, NamedList $list )
   {
      throw new \BadMethodCallException ('Note yet implemented');
   }

   /**
    * Looks up members of a given level.
    *
    * @param XMLALevel level Level
    *
    * @throws OLAPException if error occurs
    *
    * @return array of members at in the level
    */
   public function getLevelMembers( XMLALevel $level )
   {
      throw new \BadMethodCallException ('Note yet implemented');
   }
}
