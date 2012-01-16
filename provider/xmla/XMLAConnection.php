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
use OLAP4PHP\OLAP\IOlapConnection;
use OLAP4PHP\Provider\Caching\IXMLACache;

// Classes
use \DOMDocument;
use \InvalidArgumentException;
use OLAP4PHP\Common\Logger;
use OLAP4PHP\Common\NamedList;
use OLAP4PHP\Provider\XMLA\XMLACatalog;
use OLAP4PHP\Provider\XMLA\XMLAConnectionContext;
use OLAP4PHP\Provider\XMLA\XMLADatabaseMetaData;
use OLAP4PHP\OLAP\OLAPException;


/**
 * XMLAConnection connection implementation
 */
class XMLAConnection implements IOLAPConnection
{
   ///! Properties Array - Catalog key
   const PROP_CATALOG      = 'catalog';

   ///! Properties Array - Data Source Info key
   const PROP_DATASOURCE   = 'dataSource';

   ///! OLAP Catalog to query
   private $catalog;

   ///! DataSourceInfo connection string
   private $dataSourceInfo;

   /**
    * @var boolean Connection debug flag
    */
   private $debug = false;

   ///! SOAP XMLA Endpoint URL
   private $uri;

   /**
    * @var XMLASchema
    */
   private $schema;

   /**
    * @var XMLADatabaseMetaData
    */
   private $databaseMetaData;

   /**
    * @var resource
    */
   private $stream;

   /**
    * @var resource
    */
   private $fp;

   /**
    * @var Logger
    */
   private $logger;
   
   /**
    * Flag to indicate if we want to enable performance logging. This flag is
    * only set when calling the setLogger method.
    * 
    * @var boolean
    */
   private $logPerformance;
   
   /**
    * An object which implements the IXMLACache interface
    * 
    * @var IXMLACache 
    */
   private $cache;
   
   /**
    * An array of object level cached data. This local cache will be used to avoid
    * any external cached hits.
    * 
    * @var array
    */
   private $localCache = array();

   /**
    * @brief Initializes an XMLA Connection
    *
    * Initializes an XMLA Connection with a SOAP Endpoint of $url, with OLAP Properties
    * defined in the $properties array.  The $properties array currently supports the
    * following keys:
    *
    * XmlaConnection::PROP_CATALOG     => OLAP Catalog Name
    * XmlaConnection::PROP_DATASOURCE  => Data Source Info string
    *
    * Using the Mondrian FoodMart demo as an example, the $properties array would be defined
    * as follows:
    *
    * $properties = array(
    *    XmlaConnection::PROP_CATALOG     => 'FoodMart',
    *    XmlaConnection::PROP_DATASOURCE  => 'Provider=Mondrian;DataSource=MondrianFoodMart;'
    * );
    *
    * @param string $url - URL of the XMLA SOAP Endpoint
    * @param array $properties - An array of connection properties
    */
   public function __construct( $url, array $properties = array() )
   {
      $this->setURI( $url );
      if ( isset( $properties[self::PROP_CATALOG] ) ) $this->setCatalog ( $properties[self::PROP_CATALOG] );
      if ( isset( $properties[self::PROP_DATASOURCE] ) ) $this->setdataSourceInfo ( $properties[self::PROP_DATASOURCE] );
      $this->databaseMetaData = new XMLADatabaseMetaData( $this );
   }


   /**
    * @brief Set the logger to be used. As well as sets if we want to log performance
    * numbers in olap execution.
    *
    * @param Logger $logger A logger
    */
   public function setLogger ( Logger $logger, $logPerformance = false )
   {
      $this->logger = $logger;
      $this->logPerformance = $logPerformance;
   }

   /**
    * Specifies if we have performance logging enabled. This will log performance
    * information on olap classes that support the usage of this flag.
    * 
    * @return boolean Flag to indicate if you want to have performance logging on
    */
   public function getLogPerformance()
   {
      return $this->logPerformance;
   }
   
   /**
    * Set the cache used on this connection
    * 
    * @param IXMLACache $cache 
    */
   public function setCache( IXMLACache $cache )
   {
      $this->cache = $cache;
   }
   
   /**
    * Returns the cache used for this connection
    * 
    * @return IXMLACache An object implementing the IXMLACache interface
    */
   public function getCache( )
   {
      return $this->cache;
   }

   /**
    *
    * @return NamedList
    */
   public function getCatalogs ( )
   {
      return $this->databaseMetaData->getCatalogObjects();
   }


   /**
    * @brief Returns the name of the current OLAP Catalog being used by the connection
    *
    * @return string
    */
   public function getCatalog ( )
   {
      // REVIEW: All this logic to deduce and check catalog name should be
      // done on initialization (construction, or setCatalog), not here. This
      // method should be very quick.
      if ( $this->catalog == null )
      {
         // This means that no particular catalog name
         // was specified by the user.
         $catalogs = $this->getCatalogs ( );

         if ( $catalogs->size ( ) == 0 )
         {
            throw new OLAPException ( 'There is no catalog available.' );
         }
         else
         {
            $this->catalog = $catalogs->get ( 0 )->getName ( );
         }
      }
      else
      {
         // We must verify that the requested catalog name exists in the metadata.
         $catalog = $this->getCatalogs ( )->get ( $this->catalog );
         
         if ( $catalog != null )
         {
            $this->catalog = $catalog->getName ( );
         }
         else
         {
            throw new OLAPException ( 'There is no catalog named '.$this->catalog.' available.' );
         }
      }

      return $this->catalog;
   }

   /**
    * @brief Returns the current Data Source Info string for this connection
    *
    * @return string The Data Source Info string
    */
   public function getDataSourceInfo ( )
   {
      return $this->dataSourceInfo;
   }

   /**
    * @brief Returns the SOAP XMLA Endpoint URI
    *
    * @return string The SOAP XMLA Endpoint URI
    */
   public function getURI ( )
   {
      return $this->uri;
   }


   /**
    * @return XMLADatabaseMetaData
    */
   public function getMetaData ( )
   {
      return $this->databaseMetaData;
   }


   /**
    * @return Logger
    */
   public function getLogger ( )
   {
      return $this->logger;
   }


   /**
    *
    * @return XMLASchema
    */
   public function getSchema ( )
   {
      if ( empty( $this->schema ) )
      {
         $catalog = $this->databaseMetaData->getCatalogObjects()->get( $this->getCatalog() );
         $this->schema = $catalog->getSchemas()->get( 0 );
      }

      return $this->schema;
   }

   /**
    * @brief Sets the active OLAP Catalog for this connection
    *
    * @param string $catalog - The OLAP Catalog name
    */
   public function setCatalog ( $catalog )
   {
      $this->catalog = $catalog;
   }

   /**
    * @brief Sets the active Data Source Info string for this connection
    *
    * @param string $dataSourceInfo - The Data Source Info string
    */
   public function setDataSourceInfo ( $dataSourceInfo )
   {
      $this->dataSourceInfo = $dataSourceInfo;
   }

   /**
    * @brief Sets the active SOAP XMLA Endpoint URI
    *
    * @param string $uri - The SOAP XMLA Endpoint URI
    */
   public function setURI ( $uri )
   {
      $this->uri = $uri;
   }

   /**
    * @brief Submits a raw XMLA request
    *
    * NOTE: This is used as a proxy to the private sendXMLA method to maintain API naming
    * conventions with olap4j
    *
    * @param string $xmla
    * @return DOMDocument SOAP XMLA Response Data
    */
   public function submit( $xmla )
   {
      return $this->sendXMLA( $xmla );
   }

   /**
    * @brief Sends an XMLA SOAP Request to the defined endpoint, and returns the resulting XMLA
    *
    * @throws XMLAException
    *
    * @param mixed $xmlData - XML String or DOMDocument containing XMLA Discover or Execute node
    *
    * @return DOMDocument SOAP XMLA Response Data
    */
   private function sendXMLA( $xmlData )
   {
      $headers = "Content-type: text/xml";

      try
      {
         $response = $this->doPostRequest( $this->getURI(), $this->formatSoapRequest( $xmlData ), $headers . PHP_EOL );
      }
      catch ( OLAPException $e )
      {
         throw $e;
      }

      $doc = new \DOMDocument();
      @$doc->loadXML( $response );

      return $doc;
   }

   /**
    * @brief Formats and XMLA string or DOMDocument for a SOAP Request
    *
    * @param DOMDocument $xmlData - DOMDocument or XML String
    * @return string Formatted XMLA SOAP Request
    */
   private function formatSoapRequest( $xmlData )
   {
      $doc = NULL;
      if ( $xmlData instanceof DOMDocument )
      {
         $doc = $xmlData;
      }
      else
      {
         $doc = new DOMDocument();
         @$doc->loadXML( $xmlData );
      }

      //$xpath = new DOMXPath( $doc );
      //$elements = @$xpath->query( '//SOAP-ENV:Envelope/SOAP-ENV:Body' );
      $elements = $doc->getElementsByTagNameNS( 'http://schemas.xmlsoap.org/soap/envelope/', 'Body' );
      if ( !$elements || $elements->length != 1 )
      {
         $retval = '';
         // we need to wrap our data in a SOAP Envelope
         /*$retval = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;*/
         $retval .= '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' . PHP_EOL;
         $retval .= '<SOAP-ENV:Header />' . PHP_EOL;
         $retval .= '<SOAP-ENV:Body>' . PHP_EOL;

         $retval .= $doc->saveXML( $doc->documentElement );

         $retval .= '</SOAP-ENV:Body>' . PHP_EOL;
         $retval .= '</SOAP-ENV:Envelope>' . PHP_EOL;

         return $retval;
      }
      else
      {
         return $doc->saveXML();
      }
   }

   /**
    * @brief POST data to a URL
    *
    * @param string $url - URL to POST $data to
    * @param string $data - Data to post (binary-safe)
    * @param string $optional_headers - Additional HTTP Headers, separated by \n
    * @return string Returned Data from $url
    */
   private function doPostRequest( $url, $data, $optional_headers = NULL )
   {
      //echo "DEBUG DATA: " . PHP_EOL . $data;
      $params = array( 'http' => array(
              'method' => 'POST',
              'content' => $data
              ) );

      if ( $optional_headers !== NULL )
      {
         $params['http']['header'] = $optional_headers;
      }

      if ( !$this->stream )
      {
         $this->stream = stream_context_create( $params );
      }
      else if ( !stream_context_set_option( $this->stream, $params ) )
      {
         throw new OLAPException( 'Cannot updated stream context parameters.' );
      }

      $this->fp = fopen( $url, 'rb', FALSE, $this->stream );

      if ( !$this->fp )
      {
         throw new OLAPException( "Cannot connect with $url" );
      }

      $response = stream_get_contents( $this->fp );
      if ( $response === FALSE )
      {
         throw new OLAPException( "Problem reading data from $url" );
      }

      fclose( $this->fp );
      //echo 'RESPONSE:' . PHP_EOL . $response;
      return $response;
   }


   /**
    *
    * Used by internal implementation - we need for member data in CellSet.
    *
    * This is invoked when building XMLACellSet
    *
    * @param NamedList $list - reference
    * @param XMLAConnectionContext $context
    * @param XMLAMetaDataRequest $metadataRequest
    * @param IXMLAMetadataHandler $handler
    * @param array $restrictions
    *
    * @return array
    *
    * @throws OLAPException
    */
   function populateList ( NamedList $list,
                           XMLAConnectionContext $context,
                           XMLAMetadataRequest $metadataRequest,
                           $handler,
                           array $restrictions )
   {                 
      $request = $this->generateRequest ( $context, $metadataRequest, $restrictions );
         $root = $this->executeMetadataRequest ( $request, $metadataRequest->isCachable() );
                  
         //print 'Connection: before foreach' . PHP_EOL;
         foreach ( XMLAUtil::childElements ( $root ) as $element )
         {
            if ( $element->localName == 'row' )
            {
               $handler->handle ( $element, $context, $list );
            }
         }

         //print 'Connection: before sortList' . PHP_EOL;
         //print '$handler class: ' . get_class( $handler ) . PHP_EOL;
         $handler->sortList ( $list );
      }
      
               
   /**
     * Executes an XMLA metadata request and returns the root element of the
     * response.
     *
     * @param $request XMLA request string
     * @return DOMElement Root element of the response
     * @throws OLAPException on error
     */
   public function executeMetadataRequest ( $request, $cachable = true )
   {
      // check the cache for data before executing the meta data request
      $requestHash = crc32( $request );
      
      if( array_key_exists( $requestHash, $this->localCache ) )
      {         
         return $this->localCache[ $requestHash ];
      }
      else if( $cachable && $this->cache != null && ( ( $cachedResponseXML = $this->cache->get( $requestHash ) ) != false ) )
      {
         if ( $this->logger && $this->debug )
         {
            $this->logger->debug ( __CLASS__, '********************** Cache hit **********************' );
         }
         
         $doc = new \DOMDocument();
         $doc->loadXML( $cachedResponseXML );
      }
      else
      {
         if ( $this->logger && $this->debug )
         {
            $this->logger->debug ( __CLASS__, '********************** SENDING REQUEST **********************' );
            $this->logger->debug ( __CLASS__, $request );
         }

         $doc = $this->sendXMLA ( $request );

         if ( $this->logger && $this->debug )
         {
            $this->logger->debug ( __CLASS__, '******* RECEIVED RESPONSE *******' );
            $this->logger->debug ( __CLASS__, $doc->saveXML ( ) );
         }
         
         $cachableXML = $doc->saveXML();
         
         if( $cachable && $this->cache != null )
            $this->cache->set( $requestHash, $cachableXML );
         
      }

      // <SOAP-ENV:Envelope>
      //   <SOAP-ENV:Header/>
      //   <SOAP-ENV:Body>
      //     <xmla:DiscoverResponse>
      //       <xmla:return>
      //         <root>
      //           (see below)
      //         </root>
      //       <xmla:return>
      //     </xmla:DiscoverResponse>
      //   </SOAP-ENV:Body>
      // </SOAP-ENV:Envelope>
      $envelope = $doc->documentElement;
      //if (DEBUG) {
      //   System.out.println("** SERVER RESPONSE :");
      //   System.out.println(XmlaOlap4jUtil.toString(doc, true));
      //}
      assert ( $envelope->localName == 'Envelope' );
      assert ( $envelope->namespaceURI == XMLAUtil::SOAP_NS );
      $body = XMLAUtil::findChild ( $envelope, XMLAUtil::SOAP_NS, 'Body' );
      $fault = XMLAUtil::findChild ( $body, XMLAUtil::SOAP_NS, 'Fault' );

      if ( $fault != null )
      {
         // had an error, need to invalidate the cached item so we don't end up
         // caching invalid data
         if( $cachable && $this->cache != null )
            $this->cache->delete( $requestHash );
         
         /*
         <SOAP-ENV:Fault>
            <faultcode>SOAP-ENV:Client.00HSBC01</faultcode>
            <faultstring>XMLA connection datasource not found</faultstring>
            <faultactor>Mondrian</faultactor>
            <detail>
                <XA:error xmlns:XA="http://mondrian.sourceforge.net">
                    <code>00HSBC01</code>
                    <desc>The Mondrian XML: Mondrian Error:Internal
                        error: no catalog named 'LOCALDB'</desc>
                </XA:error>
            </detail>
         </SOAP-ENV:Fault>
          */
         // TODO: log doc to logfile
         $fault = $fault->ownerDocument->saveXML ( $fault );
         //$request = $request->ownerDocument->saveXML ( $request );
         throw new OLAPException ( 'XMLA provider gave exception: '.$fault.' Request ['.$request.']' );
         //throw getHelper().createException(
         //    "XMLA provider gave exception: "
         //    + XmlaOlap4jUtil.prettyPrint(fault)
         //    + "\n"
         //    + "Request was:\n"
         //    + request);
      }

      $discoverResponse = XMLAUtil::findChild ( $body, XMLAUtil::XMLA_NS, 'DiscoverResponse' );
      $returnElement = XMLAUtil::findChild ( $discoverResponse, XMLAUtil::XMLA_NS, 'return' );
      $rootElement = XMLAUtil::findChild ( $returnElement, XMLAUtil::ROWSET_NS, 'root' );
      
      // cache this element locally for future retrieval
      $this->localCache[ $requestHash ] = $rootElement;
      
      return $rootElement;
   }


   /**
    *
    * @param XMLAConnectionContext $context
    * @param XMLAMetadataRequest $metadataRequest
    * @param array $restrictions
    *
    * @throws
    */
   public function generateRequest ( XMLAConnectionContext $context,
                                     XMLAMetadataRequest $metadataRequest,
                                     array $restrictions )
   {
      $content = "Data";
      $buf = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
      /*$buf = "<?xml version=\"1.0\"?>\n".*/
             "<SOAP-ENV:Envelope\n".
             "    xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"\n".
             "    SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n".
             "  <SOAP-ENV:Body>\n".
             "    <Discover xmlns=\"urn:schemas-microsoft-com:xml-analysis\"\n".
             "        SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n".
             "    <RequestType>";
      $buf .= $metadataRequest->getName ( );
      $buf .= "</RequestType>\n".
              "    <Restrictions>\n".
              "      <RestrictionList>\n";

      $restrictedCatalogName = null;
      if ( ! empty ( $restrictions ) )
      {
         foreach ( $restrictions as $restriction => $value )
         {
            $buf .= "<$restriction>";
            $buf .= htmlspecialchars ( $value );
            $buf .= "</$restriction>";

            // To remind ourselves to generate a <Catalog> restriction
            // if the request supports it.
            if ( $restriction == 'CATALOG_NAME' )
            {
               $restrictedCatalogName = $value;
            }
         }
      }

      $buf .= "      </RestrictionList>\n".
              "    </Restrictions>\n".
              "    <Properties>\n".
              "      <PropertyList>\n";

      // Add the datasource node only if this request requires it.
      if ( $metadataRequest->requiresDatasourceName ( ) )
      {
         $buf .= ( "        <DataSourceInfo>" );
         $buf .= htmlspecialchars ( $context->xmlaConnection->getDataSourceInfo ( ) );
         $buf .= ( "</DataSourceInfo>" );
      }

      $requestCatalogName = null;
      if ( $restrictedCatalogName != null
           && strlen ( $restrictedCatalogName ) > 0 )
      {
         $requestCatalogName = $restrictedCatalogName;
      }

      // If the request requires catalog name, and one wasn't specified in the
      // restrictions, use the connection's current catalog.
      if ($context->xmlaCatalog != null )
      {
         $requestCatalogName = $context->xmlaCatalog->getName ( );
      }

      if ( $requestCatalogName == null
           && $metadataRequest->requiresCatalogName ( ) )
      {
         $requestCatalogName = $context->xmlaConnection->getCatalog ( );
      }

      // Add the catalog node only if this request has specified it as a
      // restriction.
      //
      // For low-level objects like cube, the restriction is optional; you can
      // specify null to not restrict, "" to match cubes whose catalog name is
      // empty, or a string (not interpreted as a wild card). (See
      // IOlapDatabaseMetaData.getCubes API doc for more details.) We assume
      // that the request provides the restriction only if it is valid.
      //
      // For high level objects like data source and catalog, the catalog
      // restriction does not make sense.
      if ( $requestCatalogName != null
           && $metadataRequest->allowsCatalogName ( ) )
      {
         $buf .= PHP_EOL . "        <Catalog>";
         $buf .= htmlspecialchars ( $requestCatalogName );
         $buf .= "</Catalog>\n";
      }

      $buf .= PHP_EOL . "        <Content>";
      $buf .= htmlspecialchars ( $content );
      $buf .=
         "</Content>\n".
         "      </PropertyList>\n".
         "    </Properties>\n".
         "    </Discover>\n".
         "</SOAP-ENV:Body>\n".
         "</SOAP-ENV:Envelope>";

      return $buf;
   }
}
