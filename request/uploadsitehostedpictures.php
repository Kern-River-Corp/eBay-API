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

	namespace Kern_River_Corp\eBay_API\Request;
	use \Kern_River_Corp\eBay_API\Credentials as Credentials;
	use \DOMDocument as DOMDocument;
	use \DOMElement as DOMElement;
	use \shgysk8zer0\Core\SimpleImage as SimpleImage;
	class UploadSiteHostedPictures {
		const CHARSET = 'UTF-8';
		const NS = 'urn:ebay:apis:eBLBaseComponents';
		const VERB = 'UploadSiteHostedPictures';
		const PICTURESET = 'Supersize';
		const LEVEL = 517;
		const SITEID = 0;
		const USER_AGENT = 'ebatns;xmlstyle;1.0';
		const BOUNDARY = 'MIME_boundary';
		const MIN_DIMENSIONS = 1000;

		protected $XML, $con, $serverUrl, $headers, $image;
		public $request;

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

		protected function buildXML() {
			$this->XML = new DOMDocument('1.0', $this::CHARSET);
			$root = new DOMElement($this::VERB . 'Request', null, $this::NS);
			$this->XML->appendChild($root);
			$root->appendChild(new DOMElement('Version', $this::LEVEL));
			$root->appendChild(new DOMElement('PictureName', basename($this->image)));
			$root->appendChild(new DOMElement('PictureSet', $this::PICTURESET));
			$creds = $root->appendChild(new DOMElement('RequesterCredentials'));
			$creds->appendChild(new DOMElement(
				'eBayAuthToken',
				Credentials::token(
					$this->store,
					($this->sandbox) ? 'sandbox' : 'production'
				)['eBayAuthToken'])
			);
			return $this;
		}

		protected function buildRequest() {
			//XML Request
			$this->request = "--" . $this::BOUNDARY . PHP_EOL;
			$this->request .= 'Content-Disposition: form-data; name="XML Payload"' . PHP_EOL;
			$this->request .= 'Content-Type: text/xml;charset=utf-8' . PHP_EOL . PHP_EOL;
			$this->request .= $this->XML->saveXML() . PHP_EOL;

			//Image as Binary
			$this->request .= "--" . $this::BOUNDARY . PHP_EOL;
			$this->request .= 'Content-Disposition: form-data; name="dummy"; filename="' . basename($this->image) . '"' . PHP_EOL;
			$this->request .= "Content-Transfer-Encoding: binary" . PHP_EOL;
			$this->request .= "Content-Type: application/octet-stream" . PHP_EOL . PHP_EOL;
			$this->request .= (string)$this->convertImage() . PHP_EOL;
			$this->request .= "--" . $this::BOUNDARY. "--" . PHP_EOL;
			return $this;
		}

		protected function buildHeaders() {
			$headers = array_merge([
				'Content-Type' => 'multipart/form-data; boundary=' . $this::BOUNDARY,
				'Content-Length' => $this->length(),
				'X-EBAY-API-COMPATIBILITY-LEVEL' => $this::LEVEL,
				'X-EBAY-API-CALL-NAME' => $this::VERB,
				'X-EBAY-API-SITEID' => $this::SITEID

			], Credentials::fetch($this->store, ($this->sandbox) ? 'sandbox' : 'production'));

			$this->headers = array_values(array_map(function($name, $value) {
				return "{$name}: $value";
			}, array_keys($headers), array_values($headers)));

			return $this;
		}

		protected function convertImage() {
			$img = new SimpleImage($this->image);
			$img->min_dim($this::MIN_DIMENSIONS);
			return $img->output(IMAGETYPE_JPEG, true);
		}

		public function upload($image) {
			$image = realpath($image);
			if(is_string($image) and file_exists($image)) {
				$this->image = $image;
				$this->buildXML();
				$this->buildRequest();
				$this->buildHeaders();
				return $this->cURL();
			}
		}

		public function length() {
			return strlen($this->request);
		}

		/**
		* Specialized cURL request, including eBay specific headers
		* and multi-part request from upload()
		*
		* @param string $requestBody
		* @return Simple_XML Object
		*/

		private function cURL() {
			//initialize a CURL session - need CURL library enabled
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
			curl_setopt($connection, CURLOPT_TIMEOUT, 30);
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $this->headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $this->request);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($connection, CURLOPT_FAILONERROR, 0);
			curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($connection, CURLOPT_HEADER, 1 );		   // Uncomment these for debugging
			//curl_setopt($connection, CURLOPT_VERBOSE, true);		// Display communication with serve
			curl_setopt($connection, CURLOPT_USERAGENT, $this::USER_AGENT);
			curl_setopt($connection, CURLOPT_HTTP_VERSION, 1 );	   // HTTP version must be 1.0
			$response = simplexml_load_string(curl_exec($connection));
			curl_close($connection);

			return $response;
		}
	}
?>
