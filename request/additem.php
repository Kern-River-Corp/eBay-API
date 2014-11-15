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
			)->ItemSpecifics([
				'NameValueList' => [
					'Name' => 'name',
					'Value' => 'value'
				]
			])->ListingDuration(
				''
			)->ListingType(
				''
			)->LiveAuction(
				''
			)->Location(
				''
			)->PaymentDetails([
				'DaysToFullPayment' => '',
				'DepositAmount' => '',
				'DepositType' => '',
				'HoursToDeposit' => ''
			])->PaymentMethods(
				''
			)->PayPalEmailAddress(
				''
			)->PictureDetails([
				'GalleryDuration' => '',
				'GalleryType' => '',
				'GalleryURL' => '',
				'PhotoDisplay' => '',
				'PictureURL' => ''
			])->PostalCode(
				''
			)->PrimaryCategory([
				'CategoryID' => ''
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
			)->ReturnPolicy([
				'Description' => '',
				'RefundOption' => '',
				'ReturnsAcceptedOption' => '',
				'ReturnsWithinOption' => '',
				'ShippingCostPaidByOption' => ''
			])->ScheduleTime(
				''
			)->SellerContactDetails([
				'CompanyName' => '',
				'County' => '',
				'PhoneAreaOrCityCode' => '',
				'PhoneCountryCode' => '',
				'PhoneLocalNumber' => '',
				'Street' => ''
				//'Street2' => ''
			])->SellerInventoryID(
				''
			)->ShippingDetails([
				'CalculatedShippingRate' => [
					'InternationalPackagingHandlingCosts' => '',
					'MeasurementUnit' => '',
					'OriginatingPostalCode' => '',
					'PackageDepth' => '',
					'PackageLength' => '',
					'PackageWidth' => '',
					'PackagingHandlingCosts' => '',
					'ShippingIrregular' => '',
					'ShippingPackage' => '',
					'WeightMajor' => '',
					'WeightMinor' => ''
				]
			])->ExcludeShipToLocation(
				''
			)->GlobalShipping(
				''
			)->InternationalShippingServiceOption([
				'ShippingService' => '',
				'ShippingServiceAdditionalCost' => '',
				'ShippingServiceCost' => '',
				'ShippingServicePriority' => '',
				'ShipToLocation' => ''
			])->PaymentInstructions(
				''
			)->ShippingServiceOptions([
				'FreeShipping' => '',
				'ShippingService' => '',
				'ShippingServiceAdditionalCost' => '',
				'ShippingServiceCost' => '',
				'ShippingServicePriority' => '',
				'ShippingSurcharge' => ''
			])->ShippingType(
				''
			)->ShippingPackageDetails([
				'MeasurementUnit' => '',
				'PackageDepth' => '',
				'PackageLength' => '',
				'PackageWidth' => '',
				'ShippingIrregular' => '',
				'ShippingPackage' => '',
				'WeightMajor' => '',
				'WeightMinor' => ''
			])->ShipToLocations(
				''
			)->Site(
				''
			)->SKU(
				''
			)->StartPrice(
				''
			)->Storefront([
				'StoreCategoryName' => '',
				'StoreCategoryID' => ''
			])->Title(
				''
			);
		}
	}
?>
