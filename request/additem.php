<?php
	/**
	 * @author Chris Zuber
	 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 * @package mother_brain
	 * @version 2014-11-14
	 * @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/AddItem.html
	 */

	namespace eBay_API\Request;
	use \core\resources\XML_Node as XML_Node;
	use \core\PDO as PDO;
	class AddItem extends \eBay_API\eBay_API_Call {
		public function __construct($store, $sandbox = false) {
			parent::__construct($store, ($sandbox) ? 'AddItem' : 'VerifyAddItem', $sandbox);

			$this->RequesterCredentials(
					\eBay_API\Credentials::token($store, ($sandbox) ? 'sandbox' : 'production')
			);
			$this->body = $this->body->appendChild(new XML_Node('Item'));
			$this->Site(
				$this::SITECODE
			)->Currency(
				$this::CURRENCY_ID
			);
		}

		public function get_data($prop) {
			static $db = null;
			if(is_null($db)) {
				$db = PDO::load('connect');
			}
		}

		private function set_details() {
			if(
				is_array($this->item)
				and !empty($this->item)
				and array_keys_exist(
					'SKU',
					'StartPrice',
					'Title',
					'PictureURL',
					$this->item
				) and is_url($this->item['PictureURL'])
			) {
				$this->item['StartPrice'] = [
					$this->item['StartPrice'],
					$this->attribute('currencyID', $this::CURRENCY_ID)
				];
				$this->item['PictureDetails'] = [
					[
						'PhotoDisplay' => 'SuperSize',
						'PictureURL' => $this->item['PictureDetails']
					]
				];
			}
		}

		public function test_request() {
			$SKU = 'GT000849 SampleRequest';
			$price = 10.37;
			$title = 'Sample AddItem request';
			$type = 'LS';
			$color = 'Red';
			$size = '3xl';
			$pictureURL = 'http://www.socalrafting.com/shirtgeek/images/shirt.jpg';
			$shippingCost = 4.95;
			$description = <<<eot
			<h1>Description</h1>
			<ul>
				<li>
					List Item
				</li>
			</ul>
eot;

			$this->SKU(
				$SKU
			)->StartPrice(
				[
					$price,
					$this->attribute('currencyID', $this::CURRENCY_ID)
				]
			)->Title(
				$title
			)->ItemSpecifics([
				'NameValueList' => [
					'Name' => 'Color',
					'Value' => $color
				],
				'NameValueList ' => [
					'Name' => 'Size',
					'Value' => $size
				]
			])->ListingDuration(
				'Days_10'
			)->ListingType(
				'Chinese'
			)->PaymentMethods(
				'VisaMC'
			)/*->PayPalEmailAddress(
				'theshirtgeek@kernrivercorp.com'
			)*/->PictureDetails([
				'PhotoDisplay' => 'SuperSize',
				'PictureURL' => $pictureURL
			])->PostalCode(
				93240
			)->Country(
				'US'
			)->Location(
				'Lake Isabella, CA'
			)->PrimaryCategory([
				'CategoryID' => 155193
			])->Quantity(
				1
			)->Description(
				$description
			)->ConditionID(
				1000
			)->ReturnPolicy(
				$this->return_policy()
			)->ScheduleTime(
				'+1 minute'
			)->DispatchTimeMax(
				3
			)->SellerContactDetails([
				'CompanyName' => 'theshirtgeek',
				'County' => 'US',
				'PhoneCountryCode' => 'US',
				'PhoneAreaOrCityCode' => 760,
				'PhoneLocalNumber' => 4174369,
				'Street' => '5112 Lake Isabella BLVD'
				//'Street2' => ''
			])->SellerInventoryID(
				''
			)->ShippingDetails([
				'CalculatedShippingRate' => $this->package_info($type, $size),
				'ShippingServiceOptions' => [
					'FreeShipping' => 'false',
					'ShippingService' => 'USPSPriorityFlatRateEnvelope',
					'ShippingServiceAdditionalCost' => [
						$shippingCost,
						$this->attribute('currencyID', $this::CURRENCY_ID)
					],
					'ShippingServiceCost' => [
						$shippingCost,
						$this->attribute('currencyID', $this::CURRENCY_ID)
					],
					'ShippingServicePriority' => 1,
					/*'ShippingSurcharge' => [
						0,
						$this->attribute('currencyID', $this::CURRENCY_ID)
					]*/
				],
				/*'InternationalShippingServiceOption' => [
					'ShippingService' => 'ShippingService',
					'ShippingServiceAdditionalCost' => [
						,
						$this->attribute('currencyID', $this::CURRENCY_ID)
					],
					'ShippingServiceCost' => [
						'ShippingServiceCost',
						$this->attribute('currencyID', $this::CURRENCY_ID)
					],
					'ShippingServicePriority' => 'ShippingServicePriority',
					'ShipToLocation' => 'ShipToLocation'
				],*/
				'ShippingType' => 'FlatDomesticCalculatedInternational',
				'GlobalShipping' => 'true'
			])->ShippingPackageDetails(
				$this->package_info($type, $size)
			)->ShipToLocations(
				'US'
			);

			/*->ExcludeShipToLocation(
				'ExcludeShipToLocation'
			)->PaymentInstructions(
				''
			)*//*->Storefront([
				'StoreCategoryName' => 'StoreCategoryName',
				'StoreCategoryID' => 'StoreCategoryID'
			])*/
			return $this;
		}
	}
?>
