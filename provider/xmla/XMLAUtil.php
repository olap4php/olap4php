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
namespace OLAP4PHP\Provider\XMLA;

// Interfaces
//use OLAP4PHP\OLAP\ICellSet;

// Classes / Objects
use \DOMElement;
//use OLAP4PHP\OLAP\OLAPException;


/**
 * @brief XMLA CellSet implementation
 */
abstract class XMLAUtil
{
   const SOAP_NS      = "http://schemas.xmlsoap.org/soap/envelope/";
   const XMLA_PREFIX  = "xmla";
   const XMLA_NS      = "urn:schemas-microsoft-com:xml-analysis";
   const MDDATASET_NS = "urn:schemas-microsoft-com:xml-analysis:mddataset";
   const ROWSET_NS    = "urn:schemas-microsoft-com:xml-analysis:rowset";


   /**
    * Extract child element nodes from provided element node.
    *
    * @param DOMElement $node
    * 
    * @return array DOMElement
    */
   static public function childElements ( DOMElement $node )
   {
      $elements = array ( );

      foreach ( $node->childNodes as $node )
         if ( $node instanceof DOMElement )
            $elements [] = $node;

      return $elements;
   }


   /**
    * Find a child element of the specified element.
    *
    * @param DOMElement $element
    * @param $ns
    * @param $tag
    *
    * @return DOMElement
    */
   static public function findChild ( DOMElement $element, $ns, $tag )
   {
      foreach ( $element->childNodes as $child )
      {
         if ( $child instanceof DOMElement
              && $child->localName == $tag
              && ( $ns == null || $child->namespaceURI == $ns ) )
         {
            return $child;
         }
      }

      return null;
   }


   /**
    * @param $element blah
    * @param $ns blah blah
    * @param $tag blah blah blah
    *
    * @return array DomElement
    */
   static public function findChildren ( DOMElement $element, $ns, $tag )
   {
      $elements = array ( );

      foreach ( $element->childNodes as $node )
      {
         if ( $node->localName == $tag
              && ( $ns == null || $node->namespaceURI == $ns ) )
         {
            $elements [] = $node;
         }
      }

      return $elements;
    }


   /**
   * @param $row
   * @param $name
   *
   * @return string
   */
   static public function doubleElement ( DOMElement $row, $name )
   {
      return (float) XMLAUtil::stringElement ( $row, $name );
   }


   /**
   * @param $row
   * @param $name
   *
   * @return integer
   */
   static public function integerElement ( DOMElement $row, $name )
   {
      return (integer) XMLAUtil::stringElement ( $row, $name );
   }


   /**
   * @param $row
   * @param $name
   *
   * @return float
   */
   static public function floatElement ( DOMElement $row, $name )
   {
      return (float) XMLAUtil::stringElement ( $row, $name );
   }


   /**
    * @param $row
    * @param $name
    *
    * @return long
    */
   static public function longElement ( DOMElement $row, $name )
   {
      return (float) XMLAUtil::stringElement ( $row, $name );
   }


   /**
   * @param $row
   * @param $name
   *
   * @return boolean
   */
   static public function booleanElement ( DOMElement $row, $name )
   {
      return (boolean) XMLAUtil::stringElement ( $row, $name );
   }


   /**
   * @param $row
   * @param $name
   *
   * @return string
   */
   static public function stringElement ( DOMElement $row, $name )
   {
      foreach ( $row->childNodes as $node )
      {
         if ( $node->localName == $name )
         {
            return $node->textContent;
         }
      }

      return null;
   }

   /**
    * @brief Converts SQL-style pattern matches into PREG match strings
    * 
    * @param array $wildcards
    *
    * @return string
    */
   public static function wildcardToRegexp( array $wildcards )
   {
      $joined = implode( '|', $wildcards );
      $joined = preg_replace( '#_#', '.', $joined );
      $joined = preg_replace( '#%#', '.*', $joined );
   }

   /**
    * @brief PHP Implementation of java.lang.String.hashCode() method
    *
    * Java defines their hashCode metod as follows:
    *
    *   s[0]*31^(n-1) + s[1]*31^(n-2) + ... + s[n-1]
    *
    * where s[i] is the i-th char of the string and n is the length
    * of the string.
    *
    * This actually has a rather high collision rate, but likely done
    * as a tradeoff for speed assuming you won't usually have billions
    * of strings in a single hash table.
    *
    * @param string $string
    * @return int
    */
   public static function javaStringHashCode( $string )
   {
      $value = str_split( $string );
      $count = strlen( $string );

      $hash = 0;
      for ( $i = 0; $i < $count; $i++ )
      {
         $hash = 31 * $hash + ord( $value[$i] );
      }

      return $hash;
   }


   public static function gatherText ( DOMElement $element )
   {
      $buf = '';
      foreach ( $element->childNodes as $node )
         $buf .= $node->textContent;

      return $buf;
   }
}
