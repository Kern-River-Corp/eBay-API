<?php
	/**
	* Class for uploading images to ebay using credentials
	* from a database
	*
	* @example:
	* $uploader = upload_to_ebay::load($store_name, $sandbox, $ini);
	* foreach($images as $image) {
	* 	$uploader->upload($img);
	* }
	* @author Chris Zuber <shgysk8zer0@gmail.com>
	* @copyright 2014, Chris Zuber
	* @license /LICENSE
	* @package mother_brain
	* @version 2014-11-21
	* @uses PDO
	*/

	namespace eBay_API\Request;
	class UploadSiteHostedPictures {
		const NS = 'urn:ebay:apis:eBLBaseComponents';
		const CHARSET = 'UTF-8';
		const LEVEL = 517;
		const SITEID = 0;
		const VERB = 'UploadSiteHostedPictures';
		const PICTURESET = 'Supersize';
		const BOUNDARY = 'MIME_boundary';
		const MIN_HEIGHT = 1000;
		const MIN_WIDTH = 1000;

		private $XML;
		public $serverUrl;

		/**
		* Class constructor
		*
		* Sets class variables required for upcoming cURL
		*
		* @param string $store
		* @param boolean $sandbox
		* @param string $con
		*/

		public function __construct($store = null, $sandbox = false, $con = 'ebay_api') {
			$this->store = $store;
			$this->sandbox = $sandbox;
			$this->con = $con;
			$this->serverUrl = ($sandbox) ? 'https://api.sandbox.ebay.com/ws/api.dll' : 'https://api.ebay.com/ws/api.dll';
		}

		public function bulidXML() {
			$this->XML = new \DOMDocument('1.0', $this::CHARSET);
			$root = new \DOMElement($this::VERB . 'Request', null, $this::NS);
			$this->XML->appendChild($root);
			$root->appendChild(new \DOMElement('Version', $this::LEVEL));
			$root->appendChild(new \DOMElement('PictureName', '$this::LEVEL'));
			$root->appendChild(new \DOMElement('PictureSet', $this::PICTURESET));
			$creds = $root->appendChild(new \DOMElement('RequesterCredentials'));
			$creds->appendChild(new \DOMElement(
				'eBayAuthToken',
				\eBay_API\Credentials::token(
					$this->store,
					($this->sandbox) ? 'sandbox' : 'production'
				)['eBayAuthToken'])
			);
			return $this->XML->saveXML();
		}

		/**
		* Prepares and send XML request. Returns response
		*
		* @param string $image (absolute path to image)
		* @return Simple_XML Object
		*/

		public function upload($image) {
			if(is_string($image) and file_exists($image)) {
				/**
				* As per eBay policy, images must be at least 1000x1000 px.
				*/
				$tmp = new \core\SimpleImage($image);
				$tmp->min_dim(1000, true);
				//$multiPartImageData = $tmp->image;
				$picNameIn = filename($image);
				$handle = fopen($image, 'r');		 // do a binary read of image
				$multiPartImageData = fread($handle, filesize($image));
				fclose($handle);

				///Build the request XML request which is first part of multi-part POST
				/*$xmlReq = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
				$xmlReq .= '<' . $this::VERB . 'Request xmlns="urn:ebay:apis:eBLBaseComponents">' . PHP_EOL;
				$xmlReq .= "<Version>{$this::LEVEL}</Version>" . PHP_EOL;
				$xmlReq .= "<PictureName>$picNameIn</PictureName>" . PHP_EOL;
				$xmlReq .= "<PictureSet>Supersize</PictureSet>" . PHP_EOL;
				$xmlReq .= "<RequesterCredentials><eBayAuthToken>$this->requestToken</eBayAuthToken></RequesterCredentials>" . PHP_EOL;
				$xmlReq .= '</' . $this::VERB . 'Request>';*/

				//$CRLF = "\r\n";

				// The complete POST consists of an XML request plus the binary image separated by boundaries
				$firstPart   = '';
				$firstPart  .= "--" . $this::BOUNDARY . PHP_EOL;
				$firstPart  .= 'Content-Disposition: form-data; name="XML Payload"' . PHP_EOL;
				$firstPart  .= 'Content-Type: text/xml;charset=utf-8' . PHP_EOL . PHP_EOL;
				$firstPart  .= $xmlReq;
				$firstPart  .= PHP_EOL;

				$secondPart .= "--" . $this::BOUNDARY . PHP_EOL;
				$secondPart .= 'Content-Disposition: form-data; name="dummy"; filename="dummy"' . PHP_EOL;
				$secondPart .= "Content-Transfer-Encoding: binary" . PHP_EOL;
				$secondPart .= "Content-Type: application/octet-stream" . PHP_EOL . PHP_EOL;
				$secondPart .= $multiPartImageData;
				$secondPart .= PHP_EOL;
				$secondPart .= "--" . $this::BOUNDARY. "--" . PHP_EOL;

				$fullPost = $firstPart . $secondPart;
				return  $this->sendHttpRequest($fullPost);   // send multi-part request and get string XML response
			}
		}

		/**
		* Specialized cURL request, including eBay specific headers
		* and multi-part request from upload()
		*
		* @param string $requestBody
		* @return Simple_XML Object
		*/

		private function sendHttpRequest($requestBody) {
			$requestBody = (string)$requestBody;
			$headers = [
				'Content-Type: multipart/form-data; boundary=' . $this::BOUNDARY,
				'Content-Length: ' . strlen($requestBody),
				'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this::LEVEL,  // API version
				'X-EBAY-API-DEV-NAME: ' . $this->devID,	 //set the keys
				'X-EBAY-API-APP-NAME: ' . $this->appID,
				'X-EBAY-API-CERT-NAME: ' . $this->certID,
				'X-EBAY-API-CALL-NAME: ' . $this::VERB,		// call to make
				'X-EBAY-API-SITEID: ' . $this::SITEID,	  // US = 0, DE = 77...
			];
			//initialize a CURL session - need CURL library enabled
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
			curl_setopt($connection, CURLOPT_TIMEOUT, 30 );
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($connection, CURLOPT_FAILONERROR, 0 );
			curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 1 );
			//curl_setopt($connection, CURLOPT_HEADER, 1 );		   // Uncomment these for debugging
			//curl_setopt($connection, CURLOPT_VERBOSE, true);		// Display communication with serve
			curl_setopt($connection, CURLOPT_USERAGENT, 'ebatns;xmlstyle;1.0' );
			curl_setopt($connection, CURLOPT_HTTP_VERSION, 1 );	   // HTTP version must be 1.0
			$response = curl_exec($connection);
			curl_close($connection);

			if($response) {
				$respXmlObj = simplexml_load_string($response);
				if($respXmlObj->Ack == 'Success' and is_url($respXmlObj->SiteHostedPictureDetails->FullURL)) {
					//Return the response object only if Ack is 'Success' & FullURL is a URL
					return $respXmlObj;
					//Will be interested in $respXmlObj->SiteHostedPictureDetails->FullURL
				}
				else {
					return false;
				}

			}
			else {
				return false;
			}
		}
	}
?>
