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
		 * @var \core\_pdo $creds [Database connection for retrieving credentials]
		 */

		public function __construct(
			$store,
			$callname,
			$sandbox = false,
			$level = 583,
			$siteID = 0,
			$urn = 'urn:ebay:apis:eBLBaseComponents',
			$verbose = false,
			$charset = 'UTF-8',
			$type = 'text/xml',
			$boundary = 'MIME_boundary'
		) {
			$call_list = [
				'AddFixedPriceItem' => 'AddFixedPriceItemRequest',
				'GetOrdersRequest' => 'GetOrders',
				'AddItem' => 'AddItemRequest'
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
						'Content-Type' => "{$type}; boundary={$boundary}",
						'X-EBAY-API-COMPATIBILITY-LEVEL' => $level,
						'X-EBAY-API-CALL-NAME' => $callname,
						'X-EBAY-API-SITEID' => $siteID
					]
				),
				$call_list[$callname],
				$urn,
				$charset,
				$verbose
			);
		}
	}
?>
