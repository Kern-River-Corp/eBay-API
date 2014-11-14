<?php
	/**
	* eBay API Call to AddFixedPriceItem
	*
	* @author Chris Zuber
	* @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	* @package mother_brain
	* @version 2014-11-14
	 */

	namespace eBay_API\Request;
	class AddFixedPriceItems extends \eBay_API\eBay_API_Call {
		/**
		 * Initialize parent and create request body
		 *
		 * @param string $store   [User in database, used to get credentials]
		 * @param array $items    [Array of items to add in request]
		 * @param bool $sandbox   [Sandbox | Production environment]
		 */

		public function __construct($store, array $items = null, $sandbox = false) {
			parent::__construct(
				$store,
				'AddFixedPriceItem',
				$sandbox
			);

			if(is_null($items) or !is_array($items) or empty($items)) {
				$items = [
					[
						'CategoryMappingAllowed' => 'true',
						'Country' => 'US',
						'Currency' => 'USD',
						'Description' => 'Minimal fixed-price shoe listing with SKU, free shipping, 3-day dispatch time, return policy, and no Item Specifics. New Nike Shox Elite TB White/White-Black-Chrome. Size: Mens US 12, UK 11, Europe 46 (Medium, D, M). Condition: New in box.',
						'DispatchTimeMax' => 3,
						'InventoryTrackingMethod' => 'SKU',
						'ListingDuration' => 'Days_30',
						'ListingType' => 'FixedPriceItem',
						'Location' => 'San Jose, CA',
						'PaymentMethods' => 'PayPal',
						'PayPalEmailAddress' => 'MegaOnlineMerchant@gmail.com',
						'PrimaryCategory' => [
							'CategoryID' => 63850
						],
						'Quantity' => 6,
						'ReturnPolicy' => [
							'ReturnsAcceptedOption' => 'ReturnsAccepted',
							'RefundOption' => 'MoneyBack',
							'ReturnsWithinOption' => 'Days_30',
							'Description' => 'Text description of return policy details here.',
							'ShippingCostPaidByOption' => 'Buyer',
						],
						'ShippingDetails' => [
							'ShippingType' => 'Flat',
							'ShippingServiceOptions' => [
								'ShippingServicePriority' => 1,
								'ShippingService' => 'USPSPriority',
								'ShippingServiceCost' => '0.0',
								'ShippingServiceAdditionalCost' => '0.00',
								'FreeShipping' => 'true'
							]
						],
						'Site' => 'US',
						'SKU' => '1122334455-14',
						'StartPrice' => '50.00',
						'Title' => 'New Nike Shox Elite TB White Mens Basketball Shoes S 12',
						'UUID' => '7d004a30b0f511ddad8b0807654c9a55'
					]
				];
			}

			$n = 0;

			foreach($items as $item) {
				$this->AddFixedPriceItemRequest([
					'ErrorLanguage' => 'en_US',
					'WarningLevel' => 'High',
					'Version' => 583,
					'Item' => $item
				]);
				$this->getElementsByTagName(
					'ShippingServiceCost'
				)->item($n++)->setAttribute('currencyID', 'USD');
			}
		}
	}
?>