<?php
	/**
	 * Create an eBay specific XML_API_Call, setting correct
	 * headers by appending credentials from database, using the correct
	 * URL according to sandbox/production, and using the right root
	 * element based on the call name
	 *
	 * @author Chris Zuber
	 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 * @package mother_brain
	 * @version 2014-11-13
	 *
	 * @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/ [eBay API Call Index]
	 *
	 * @todo HasMoreOrders logic (max 100 per page/request)
	 * @todo timestamp/ack check (Success)
	 * @todo Throw exception if $call not in $call_list
	*/

	namespace eBay_API;
	use \core\PDO as PDO;
	use \core\resources\XML_Node as Node;
	use \DOMElement as Element;
	abstract class eBay_API_Call extends \core\XML_API_Call {
		use \eBay_API\Resources;
		/**
		 * Dynamically construct an XML_API_Call with eBay specific paramaters
		 *
		 * @param string  $store    [User in database, used to get credentials]
		 * @param bool    $sandbox  [Sandbox or production]
		 * @param bool    $verbose  [Use CURLOPT_VERBOSE?]
		 *
		 * @var string $environment [Based on $sandbox, will be either 'production' or 'sandbox']
		 * @var string $url [URL to send request to, based on $sandbox]
		 * @var array $call_list [key => value array, mapping callnames to root elements]
		 * @var \core\PDO $creds [Database connection for retrieving credentials]
		 */

		const LEVEL = 583,
			SITEID = 0,
			SITECODE = 'US',
			URN = 'urn:ebay:apis:eBLBaseComponents',
			CHARSET = 'UTF-8',
			TYPE = 'text/xml',
			BOUNDARY = 'MIME_boundary',
			ERROR_LANG = 'en_US',
			WARNING_LEVEL = 'High',
			MEASUREMENT_SYSTEM = 'English',
			WEIGHT_UNIT_MAJOR = 'lb',
			WEIGHT_UNIT_MINOR = 'oz',
			LINEAR_UNIT = 'in',
			CURRENCY_ID = 'USD',
			DATETIME_FORMAT = 'Y-m-d\TH:i:s.000\Z',
			SANDBOX_URL = 'https://api.sandbox.ebay.com/ws/api.dll',
			PRODUCTION_URL = 'https://api.ebay.com/ws/api.dll';

			protected $store, $environment, $sandbox;

		/**
		 * Create a new XML_API call with eBay specific data
		 *
		 * @param string $store    [Store name making request]
		 * @param bool   $sandbox  [Production or sandbox environment]
		 * @param bool   $verbose  [Use verbose in cURL request]
		 */

		public function __construct(
			$store,
			$sandbox = false,
			$verbose = false
		) {
			$this->store = $store;
			$this->sandbox = $sandbox;
			$this->environment = ($this->sandbox) ? 'sandbox' : 'production';

			parent::__construct(
				($this->sandbox) ? $this::SANDBOX_URL : $this::PRODUCTION_URL,
				$this->setHeaders(),
				$this::CALLNAME . 'Request',
				$this::URN,
				$this::CHARSET,
				$verbose
			);

			$this->ErrorLanguage(
				$this::ERROR_LANG
			)->WarningLevel(
				$this::WARNING_LEVEL
			)->Version(
				$this::LEVEL
			);
		}
	}
?>
