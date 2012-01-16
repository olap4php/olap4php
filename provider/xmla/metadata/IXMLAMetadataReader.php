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

// Classes used
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLALevel;


/**
 * @brief XMLA Metadata Reader Interface
 */
interface IXMLAMetadataReader
{
   /**
    * Looks up a member by its unique name.
    *
    * @param string $memberUniqueName Unique name of member
    * @return XMLAMember, or null if not found
    * @throws OLAPException if error occurs
    */
   function lookupMemberByUniqueName ( $memberUniqueName );

   /**
    * Looks up a list of members by their unique name and writes the results
    * into a map.
    *
    * @param array $memberUniqueNames List of unique names of member
    *
    * @param array& $memberMap Reference to map that will be populated with members
    *
    * @throws OLAPException if error occurs
    */
   function lookupMembersByUniqueName ( array $memberUniqueNames, array& $memberMap );

   /**
    * Looks a member by its unique name and returns members related by
    * the specified tree-operations.
    *
    * @param array treeOps Collection of tree operations to travel relative to
    * given member in order to create list of members
    *
    * @param string memberUniqueName Unique name of member
    *
    * @param array IMember List to be populated with members related to the given
    * member, or empty set if the member is not found
    *
    * @throws OLAPException if error occurs
    */
   function lookupMemberRelatives ( array $treeOps, $memberUniqueName, NamedList $list );

   /**
    * Looks up members of a given level.
    *
    * @param XMLALevel level Level
    *
    * @throws OLAPException if error occurs
    *
    * @return NamedList
    */
   function getLevelMembers ( XMLALevel $level );
}