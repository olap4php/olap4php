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
namespace OLAP4PHP\OLAP;

/**
 * @brief OLAP Database MetaData Interface
 *
 * NOTE: In olap4j, this extends the java.sql.DatabaseMetaData interface.  We will
 * add any methods implemented from that interface to this one, as it does not make
 * sense to start implementing base Java classes for the purposes of this project
 *
 * We also ignore the OlapWrapper interface from olap4j, as it is used to manage
 * differences between JDBC 3 and JDBC 4, which we will not worry about in PHP.
 *
 */
interface IOLAPDatabaseMetaData
{
   /**
    * @brief Gets the IOLAPConnection-based connection
    *
    * @return IOLAPConnection
    */
   public function getConnection();

   public function getSupportedCellSetListenerGranularities();

   public function getActions(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $actionNamePattern = null );

   public function getDatasources();

   public function getLiterals();

   public function getDatabaseProperties(
      $dataSourceName,
      $propertyNamePattern );

   public function getProperties(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $dimensionUniqueName = null,
      $hierarchyUniqueName = null,
      $levelUniqueName = null,
      $memberUniqueName = null,
      $propertyNamePattern = null );

   public function getMdxKeywords();

   public function getCubes(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null );

   public function getDimensions(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $dimensionNamePattern = null );

   public function getOlapFunctions(
      $functionNamePattern = null );

   public function getHierarchies(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $dimensionUniqueName = null,
      $hierarchyNamePattern = null );

   public function getLevels(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $dimensionUniqueName = null,
      $hierarchyUniqueName = null,
      $levelNamePattern = null );

   public function getMeasures(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $measureNamePattern = null,
      $measureUniqueName = null );

   public function getMembers(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $dimensionUniqueName = null,
      $hierarchyUniqueName = null,
      $levelUniqueName = null,
      $memberUniqueName = null,
      array $treeOps = array() );

   public function getSets(
      $catalog = null,
      $schemaPattern = null,
      $cubeNamePattern = null,
      $setNamePattern = null );
}
