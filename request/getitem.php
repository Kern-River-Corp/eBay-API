<?php
	/**
	* @author Chris Zuber
	* @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	* @package mother_brain
	* @version 2014-11-18
	* @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/AddItem.html
	*/

	namespace eBay_API\Request;
	use \core\resources\XML_Node as XML_Node;
	use \core\_pdo as PDO;
	class GetItem extends \eBay_API\eBay_API_Call {
		public function __construct($store, $sandbox = false) {
			parent::__construct($store, 'GetItem', $sandbox);

			$this->RequesterCredentials(
					\eBay_API\Credentials::token($store, ($sandbox) ? 'sandbox' : 'production')
			);
		}
	}
?>
