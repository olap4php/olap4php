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
interface IXMLAConstant
{
   /**
     * Returns the name of this constant as specified by XMLA.
     *
     * <p>Often the name is an enumeration-specific prefix plus the name of
     * the Java enum constant. For example,
     * {@link org.olap4j.metadata.Dimension.Type} has
     * prefix "MD_DIMTYPE_", and therefore this method returns
     * "MD_DIMTYPE_PRODUCTS" for the enum constant
     * {@link org.olap4j.metadata.Dimension.Type#PRODUCTS}.
     *
     * @return string
     */
    public function xmlaName ();

    /**
     * Returns the description of this constant.
     *
     * @return string Description of this constant.
     */
    public function getDescription ();

    /**
     * Returns the code of this constant as specified by XMLA.
     *
     * <p>For example, the XMLA specification says that the ordinal of
     * MD_DIMTYPE_PRODUCTS is 8, and therefore this method returns 8
     * for {@link org.olap4j.metadata.Dimension.Type#PRODUCTS}.
     *
     * @return integer ordinal code as specified by XMLA.
     */
    public function xmlaOrdinal();

}