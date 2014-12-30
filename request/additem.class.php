<?php
	/**
	 * @author Chris Zuber
	 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 * @package mother_brain
	 * @version 2014-11-14
	 * @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/AddItem.html
	 */

	namespace Kern_River_Corp\eBay_API\Request;
	use \shgysk8zer0\Core\resources\XML_Node as XML_Node;
	use \Kern_River_Corp\eBay_API\Credentials as Credentials;

	class AddItem extends \Kern_River_Corp\eBay_API\eBay_API_Call {
		const PHOTODISPLAY = 'SuperSize';
		const CALLNAME = 'AddItem';

		public function __construct($store, $sandbox = false) {
			parent::__construct($store, $sandbox);

			$this->RequesterCredentials(
				Credentials::token($this->store, $this->environment)
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
