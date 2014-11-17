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
		}
	}
?>
