<?php
	/**
	 * @author Chris Zuber
	 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 * @package mother_brain
	 * @version 2014-11-14
	 * @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/GetOrders.html
	 */

	namespace Kern_River_Corp\eBay_API\Request;
	use \Kern_River_Corp\eBay_API\Credentials as Credentials;
	class GetOrders extends \Kern_River_Corp\eBay_API\eBay_API_Call {
		/**
		 * Get all items sold in $store since $since in $length seconds
		 *
		 * @param  string   $store      [Store name]
		 * @param  array    $selectors  [Array of properties to retrieve about orders]
		 * @param  mixed    $date       [Any valid date format, including timestamp]
		 * @param  integer  $days       [# of days to look back from $date]
		 * @param  integer  $page       [Page number]
		 * @param  boolean  $sandbox    [production or sandbox]
		 *
		 * @return array         [Array of orders]
		*/

		const CALLNAME = 'GetOrders';

		public function __construct(
			$store,
			array $selectors = null,
			$date = 'Today',
			$days = 1,
			$page = 1,
			$sandbox = false
		) {
			parent::__construct($store, $sandbox);
			if(is_null($selectors) or !is_array($selectors)) {
				$selectors = [
					'HasMoreOrders',
					'OrderID',
					'TransactionID',
					'OrderStatus',
					'BuyerUserID',
					'OrderLineItemID',
					'PaymentMethod',
					'eBayPaymentStatus',
					'SalesTaxPercent',
					'SalesTaxAmount',
					'CreatedTime',
					'CreatedDate',
					'SKU',
					'AmountPaid',
					'Title',
					'QuantityPurchased',
					'TransactionPrice',
					'Status',
					'ShippingService',
					'ShippingServiceCost',
					'ShippingServicePriority',
					'ExpeditedService',
					'ShippingTimeMin',
					'ShippingTimeMax',
					'Name',
					'Street1',
					'Street2',
					'CityName',
					'StateOrProvince',
					'Country',
					'CountryName',
					'Phone',
					'PostalCode',
					'AddressID',
					'AddressOwner'
				];
			}

			$this->RequesterCredentials(
			Credentials::token($this->store, $this->environment)
			)->CreateTimeFrom(
				date('Y-m-d', strtotime("-{$days} day", strtotime($date))) . 'T00:00:00.000Z'
			)->CreateTimeTo(
				date('Y-m-d', strtotime($date)) . 'T23:59:59.000Z'
			)->OrderRole(
				'Seller'
			)->OrderStatus(
				'Completed'
			)->Pagination([
				'EntriesPerPage' => 100,
				'PageNumber' => $page
			]);

			foreach($selectors as $selector) {
				$this->OutputSelector($selector);
			}
		}
	}
?>
