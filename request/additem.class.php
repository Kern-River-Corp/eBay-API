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
	use \eBay_API\Credentials as Credentials;

	class AddItem extends \eBay_API\eBay_API_Call {
		const PHOTODISPLAY = 'SuperSize';
		const SANDBOX_REQUEST = 'VerifyAddItem';
		const PRODUCTION_REQUEST = 'AddItem';

		public function __construct($store, $sandbox = false) {
			parent::__construct($store, ($sandbox) ? $this::PRODUCTION_REQUEST : $this::SANDBOX_REQUEST, $sandbox);

			$this->RequesterCredentials(
					Credentials::token($store, ($sandbox) ? 'sandbox' : 'production')
			);
			$this->body = $this->body->appendChild(new XML_Node('Item'));
			$this->Site(
				$this::SITECODE
			)->Currency(
				$this::CURRENCY_ID
			);
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
						'PhotoDisplay' => $this::PHOTODISPLAY,
						'PictureURL' => $this->item['PictureDetails']
					]
				];
			}
		}
	}
?>
