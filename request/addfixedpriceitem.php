<?php
	/**
	* eBay API Call to AddFixedPriceItem
	*
	* @author Chris Zuber
	* @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	* @package mother_brain
	* @version 2014-11-14
	* @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/AddFixedPriceItem.html
	 */

	namespace eBay_API\Request;
	class AddFixedPriceItem extends \eBay_API\eBay_API_Call {
		/**
		 * Initialize parent and create request body
		 *
		 * @param string $store   [User in database, used to get credentials]
		 * @param array $items    [Array of items to add in request]
		 * @param bool $sandbox   [Sandbox | Production environment]
		 */

		public function __construct($store, array $item = null, $sandbox = false) {
			parent::__construct(
				$store,
				'AddFixedPriceItem',
				$sandbox
			);

			if(is_null($item) or !is_array($item) or empty($item)) {
				$item = [
					'CategoryMappingAllowed' => 'true',
					'Country' => 'US',
					'Currency' => 'USD',
					'Description' => 'Minimal fixed-price shoe listing with SKU, free shipping, 3-day dispatch time, return policy, and no Item Specifics. New Nike Shox Elite TB White/White-Black-Chrome. Size: Mens US 12, UK 11, Europe 46 (Medium, D, M). Condition: New in box.',
					'DispatchTimeMax' => 3,
					'InventoryTrackingMethod' => 'SKU',
					'ListingDuration' => 'Days_30',
					'ListingType' => 'FixedPriceItem',
					'Location' => 'San Jose, CA',
					//'PaymentMethods' => 'PayPal',
					//'PayPalEmailAddress' => 'theshirtgeek@kernrivercorp.com',
					'PaymentMethods' => 'VisaMC',
					'PrimaryCategory' => [
						'CategoryID' => 63850
					],
					'Quantity' => 6,
					'ConditionDescription' => 'New\Other',
					'Condition' => 'New',
					//'ConditinID' => 1000,
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
					'Title' => 'New Nike Shox Elite TB White Mens Basketball Shoes S 12'
				];
			}
			$this->RequesterCredentials([
				'eBayAuthToken' => 'AgAAAA**AQAAAA**aAAAAA**IATYUg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GhC5eEoQmdj6x9nY+seQ**uX4CAA**AAMAAA**yFCs8iMUnWdu9R5on5m8foKxvnOGplnDLeTeRHua/3Ip9V4pHoL52Xmg3uuWCDVrvfZbyCqlOiyMh+lUu/T37IstU9GyA8hoVD6FU0wHVIF0iqJVvdMVxZI0Op7ChMXawFO6Dt9IMc1HdAJS1ykwwxPpNc+cshLXpEVoAxsuiLlB310sKR2LUsm68vzwUfMBbvh6GfVN6TPhUZgqIi8ZOw2o5iocbOh/Xhx6hM/ho8lChI8mTirf7ORInOCbQ+KBMAUiEob9NwfLx26IhKtYBt6Rn2wFnILVttr4mTcgv9jYFEh4R+A6AN0CJwWDHFYhBO6qAW6AyuUB0KeY/x7klXPy8t3OcRrpl8/D8cpzz5vy0vjaiRbj+pylxCEbiOwXdthuYX2N+RuK1VgX9puDsc4I3srmOAZBGGQVSz/eNrmOYFq4eiUVyxQvu50vbg/B/tZWLbjVQslUjMtY19UvNLzPybKl8gP3uvY1q1+wD6gN2p4lZ5hx+m0Svy68o57Ufh1HTKj7l8KEaZXEMQ7MhSQPW9T9EYabDLPfR9Z50IFcjn6r2+uiw4aFjWQUgu9BnVQiMmeRjxlAvPFSf0gg/XzyR+nU606N8q9oQd2c5E0+56IUFZaMHkhDLYqxpK/4CXgdletAJN/qX086Ef3AgJ5plPquJHJDFUfw+C/tlUAUvG0Nj+ZUH48opkqXjwls7w6xgqncls6f9iaZZ4luOLKd4HSPz/IY6WdidZVy432HIKKc21D9cXYgaUE80agc'
			])->ErrorLanguage(
				'en_US'
			)->WarningLevel(
				'High'
			)->Version(
				583
			)->Item(
				$item
			);
			$this->getElementsByTagName(
				'ShippingServiceCost'
			)->item(0)->setAttribute('currencyID', 'USD');
		}
	}
?>
