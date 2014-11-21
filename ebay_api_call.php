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
	class eBay_API_Call extends \core\XML_API_Call {
		/**
		 * Dynamically construct an XML_API_Call with eBay specific paramaters
		 *
		 * @param string  $store    [User in database, used to get credentials]
		 * @param string  $callname [Which type of request?]
		 * @param bool    $sandbox  [Sandbox or production]
		 * @param integer $level    [X-EBAY-API-COMPATIBILITY-LEVEL]
		 * @param integer $siteID   [X-EBAY-API-SITEID]
		 * @param string  $urn      [Namespace to use]
		 * @param bool    $verbose  [Use CURLOPT_VERBOSE?]
		 * @param string  $charset  [Charset for XML]
		 * @param string  $type     [enctype of request body]
		 * @param string  $boundary [Designates a boundry in request body]
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
			DATETIME_FORMAT = 'Y-m-d\TH:i:s.000\Z';

			protected $store;

		/**
		 * Create a new XML_API call with eBay specific data
		 *
		 * @param string $store    [Store name making request]
		 * @param string $callname [Callname for request]
		 * @param bool   $sandbox  [Production or sandbox environment]
		 * @param bool   $verbose  [Use verbose in cURL request]
		 */

		public function __construct(
			$store,
			$callname = null,
			$sandbox = false,
			$verbose = false
		) {
			$this->store = $store;
			$call_list = [
				'AddFixedPriceItem' => 'AddFixedPriceItemRequest',
				'GetOrders' => 'GetOrdersRequest',
				'AddItem' => 'AddItemRequest',
				'VerifyAddItem' => 'VerifyAddItemRequest',
				'GetItem' => 'GetItemRequest',
				'UploadSiteHostedPictures' => 'UploadSiteHostedPicturesRequest'
			];

			if($sandbox) {
				$environment = 'sandbox';
				$url = 'https://api.sandbox.ebay.com/ws/api.dll';
			}
			else {
				$environment = 'production';
				$url = 'https://api.ebay.com/ws/api.dll';
			}

			parent::__construct(
				$url,
				array_merge(
					Credentials::fetch($store, $environment),
					[
						'Content-Type' => $this::TYPE . '; boundary=' . $this::BOUNDARY,
						'X-EBAY-API-COMPATIBILITY-LEVEL' => $this::LEVEL,
						'X-EBAY-API-CALL-NAME' => $callname,
						'X-EBAY-API-SITEID' => $this::SITEID
					]
				),
				(array_key_exists($callname, $call_list)) ? $call_list[$callname] : "{$callname}Request",
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

		/**
		 * Description may contain HTML, which needs to be contained within
		 * CDATA in the XML request.
		 *
		 * @param string $content [HTML for Description]
		 * @return \eBay_API\eBay_API_Call
		 */

		public function Description($content) {
			$parent = new \core\resources\XML_Node('Description');
			$this->body->append($parent);
			$content = str_replace(["\r", "\r\n", "\n", "\t"], null, $content);
			$desc = $this->createCDATASection($content);
			$parent->appendChild($desc);
			return $this;
		}

		/**
		 * eBay times need to be converted into UFC formatted GMT times
		 * Automatically set ScheduleTime to this
		 *
		 * @param string $datetime [Any date format that works with strtotime()]
		 */

		public function ScheduleTime($datetime = null) {
			$this->body->appendChild(new \DOMElement('ScheduleTime', $this->convert_date($datetime)));
			return $this;
		}

		/**
		 * eBay times need to be converted into UFC formatted GMT times
		 *
		 * @param  string $datetime [Any date format that works with strtotime()]
		 *
		 * @return string           [Datetime in DATETIME_FORMAT]
		 */

		private function convert_date($datetime = 'Now') {
			return gmdate($this::DATETIME_FORMAT, strtotime($datetime));
		}

		/**
		 * [return_policy description]
		 *
		 * @param  string $store [Name of store to get return policy for]
		 *
		 * @return array        [description]
		 */

		public function return_policy($store = null) {
			static $db = null;
			if(is_null($db)) {
				$db = \core\PDO::load('inventory_data');
				if($db->connected) {
					$db->prepare("
					SELECT
						`ReturnsAcceptedOption`,
						`RefundOption`,
						`ReturnsWithinOption`,
						`Description`,
						`ShippingCostPaidByOption`
					FROM `inventory_data`.`ReturnPolicy`;
						WHERE `store` = :store
						AND `channel` = 'ebay'
						LIMIT 1
					");
				}
			}
			if($db->connected) {
				return get_object_vars($db->bind([
					'store' => (isset($store)) ? $store : $this->store
				])->execute()->get_results(0));
			}
			else return 'Not connected';
		}

		/**
		 * [package_info description]
		 *
		 * @param  string $type [description]
		 * @param  string $size [description]
		 *
		 * @return array        [description]
		 */

		public function package_info($type, $size) {
			static $db = null;
			if(is_null($db)) {
				$db = \core\PDO::load('inventory_data');
				$db->prepare("
					SELECT
						`length`,
						`width`,
						`depth`,
						`oz` AS `weight`,
						`package`,
						`irregular`
					FROM `garment_weight_dimensions`
					WHERE `type` = :type
					AND `size` = :size
				");
			}
			if($db->connected) {
				$result = $db->bind([
					'type' => trim($type),
					'size' => trim($size)
				])->execute()->get_results(0);

				return (empty($result)) ? false : [
					'PackageDepth' => [
						(float)$result->depth,
						$this->create_attributes([
							'unit' => $this::LINEAR_UNIT,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'PackageLength' => [
						(float)$result->length,
						$this->create_attributes([
							'unit' => $this::LINEAR_UNIT,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'PackageWidth' => [
						(float)$result->width,
						$this->create_attributes([
							'unit' => $this::LINEAR_UNIT,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'WeightMajor' => [
						floor($result->weight / 16),
						$this->create_attributes([
							'unit' => $this::WEIGHT_UNIT_MAJOR,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'WeightMinor' => [
						$result->weight % 16,
						$this->create_attributes([
							'unit' => $this::WEIGHT_UNIT_MINOR,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'ShippingPackage' => $result->package,
					'ShippingIrregular' => $result->irregular
				];
			}
		}

		/**
		 * Get the CategoryID for listing on eBay
		 *
		 * @param string $name  [code for the name. E.G. LS for Long Sleeve]
		 * @return array        ['CategoryID' => $CategoryID]
		 */

		public function getCategoryID($name) {
			static $db = null;
			if(is_null($db)){
				$db = \core\PDO::load('inventory_data');
				$db->prepare("
					SELECT `CategoryID`
					FROM `categories`
					WHERE `name` = :name
					LIMIT 1
				");
			}

			return get_object_vars($db->bind([
				'name' => $name
			])->execute()->get_results(0));
		}
	}
?>
