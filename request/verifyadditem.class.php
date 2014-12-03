<?php
	/**
	 * @author Chris Zuber
	 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 * @package mother_brain
	 * @version 2014-12-03
	 * @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/VerifyAddItem.html
	 */

	namespace eBay_API\Request;

	class VerifyAddItem extends \eBay_API\Request\AddItem {
		const CALLNAME = 'VerifyAddItem';

		public function __construct($store, $sandbox = false) {
			parent::__construct($store, $sandbox);
		}
	}
?>
