<?php
	/**
	 * @author Chris Zuber
	 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 * @package mother_brain
	 * @version 2014-11-14
	 * @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/AddItem.html
	 */

	namespace eBay_API\Request;
	class AddItem extends \eBay_API\eBay_API_Call {
		public function __construct($store, array $item = null, $sandbox) {
			parent::__construct($store, ($sandbox) ? 'AddItem' : 'VerifyAddItem', $sandbox);

			$this->RequesterCredentials(
					\eBay_API\Credentials::token($store, ($sandbox) ? 'sandbox' : 'production')
			);
			$this->body = $this->body->appendChild(new \core\resources\XML_Node('Item'));
			$this->Site(
				$this::SITECODE
			)->Currency(
				$this::CURRENCY_ID
			);
		}

		public function test_request() {
			$this->SKU(
				'GT000849 SampleRequest'
			)->StartPrice(
				[
					10.37,
					$this->attribute('currencyID', $this::CURRENCY_ID)
				]
			)->Title(
				'Sample AddItem request'
			)->ItemSpecifics([
				'NameValueList' => [
					'Name' => 'Color',
					'Value' => 'Red'
				],
				'NameValueList ' => [
					'Name' => 'Size',
					'Value' => 'Large'
				]
			])->ListingDuration(
				'Days_10'
			)->ListingType(
				'Chinese'
			)/*->PaymentDetails([
				'DaysToFullPayment' => 'DaysToFullPayment',
				'DepositAmount' => [
					'DepositAmount',
					$this->attribute('currencyID', $this::CURRENCY_ID)
				],
				'DepositType' => 'DepositType',
				'HoursToDeposit' => 'HoursToDeposit'
			])*/->PaymentMethods(
				'VisaMC'
			)/*->PayPalEmailAddress(
				'theshirtgeek@kernrivercorp.com'
			)*/->PictureDetails([
				//'GalleryDuration' => 'Days_7',
				//'GalleryType' => 'Gallery',
				//'GalleryURL' => 'http://www.socalrafting.com/shirtgeek/images/shirt.jpg',
				'PhotoDisplay' => 'SuperSize',
				'PictureURL' => 'http://www.socalrafting.com/shirtgeek/images/shirt.jpg'
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
				<<<eot
				<h1>Description</h1>
				<ul>
					<li>
						List Item
					</li>
				</ul>
eot
			)->ConditionID(
				1000
			)->ReturnPolicy([
				'Description' => 'Description',
				'RefundOption' => 'ReturnsAccepted',
				'ReturnsAcceptedOption' => 'ReturnsAccepted',
				'ReturnsWithinOption' => 'Days_14',
				'ShippingCostPaidByOption' => 'Buyer'
			])->ScheduleTime(
				'19:00'
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
				//'MeasurementUnit' => $this::MEASUREMENT_SYSTEM,
				'CalculatedShippingRate' => [
					//'InternationalPackagingHandlingCosts' => 'InternationalPackagingHandlingCosts',
					'OriginatingPostalCode' => 93240,
					'PackageDepth' => [
						4,
						$this->create_attributes([
							'unit' => $this::LINEAR_UNIT,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'PackageLength' => [
						3,
						$this->create_attributes([
							'unit' => $this::LINEAR_UNIT,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'PackageWidth' => [
						1.5,
						$this->create_attributes([
							'unit' => $this::LINEAR_UNIT,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
							])
					],
					/*'PackagingHandlingCosts' => [
						'PackagingHandlingCosts',
						$this->create_attributes([
						'currencyID' => $this::CURRENCY_ID
					])],*/
					'ShippingIrregular' => 'false',
					'ShippingPackage' => 'PackageThickEnvelope',
					'WeightMajor' => [
						1,
						$this->create_attributes([
							'unit' => $this::WEIGHT_UNIT_MAJOR,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					],
					'WeightMinor' => [
						3,
						$this->create_attributes([
							'unit' => $this::WEIGHT_UNIT_MINOR,
							'measurementSystem' => $this::MEASUREMENT_SYSTEM
						])
					]
				],
				'ShippingServiceOptions' => [
					'FreeShipping' => 'false',
					'ShippingService' => 'USPSPriorityFlatRateEnvelope',
					'ShippingServiceAdditionalCost' => [
						4.95,
						$this->attribute('currencyID', $this::CURRENCY_ID)
					],
					'ShippingServiceCost' => [
						4.95,
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
			])->ShippingPackageDetails([
				'MeasurementUnit' => $this::MEASUREMENT_SYSTEM,
				'PackageDepth' => [
					4,
					$this->create_attributes([
						'unit' => $this::LINEAR_UNIT,
						'measurementSystem' => $this::MEASUREMENT_SYSTEM
					])
				],
				'PackageLength' => [
					3,
					$this->create_attributes([
						'unit' => $this::LINEAR_UNIT,
						'measurementSystem' => $this::MEASUREMENT_SYSTEM
					])
				],
				'PackageWidth' => [
					1.5,
					$this->create_attributes([
						'unit' => $this::LINEAR_UNIT,
						'measurementSystem' => $this::MEASUREMENT_SYSTEM
					])
				],
				'ShippingIrregular' => 'false',
				'ShippingPackage' => 'PackageThickEnvelope',
				'WeightMajor' => [
					1,
					$this->create_attributes([
						'unit' => $this::WEIGHT_UNIT_MAJOR,
						'measurementSystem' => $this::MEASUREMENT_SYSTEM
					])
				],
				'WeightMinor' => [
					3,
					$this->create_attributes([
						'unit' => $this::WEIGHT_UNIT_MINOR,
						'measurementSystem' => $this::MEASUREMENT_SYSTEM
					])
				]
			])->ShipToLocations(
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
