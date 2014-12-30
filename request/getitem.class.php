<?php
	/**
	* @author Chris Zuber
	* @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	* @package mother_brain
	* @version 2014-11-18
	* @link http://developer.ebay.com/devzone/xml/docs/reference/ebay/GetItem.html
	*/

	namespace Kern_River_Corp\eBay_API\Request;
	use \Kern_River_Corp\eBay_API\Credentials as Credentials;
	class GetItem extends \Kern_River_Corp\eBay_API\eBay_API_Call {
		/**
		 * Construct a new eBay API request of the correct type
		 *
		 * @param string $store   [Store name to get credentials for]
		 * @param bool   $sandbox [Production or sandbox environment]
		 */

		const CALLNAME = 'GetItem';

		public function __construct($store, $sandbox = false) {
			parent::__construct($store, $sandbox);

			$this->RequesterCredentials(
				Credentials::token($this->store, $this->environment)
			);
		}
	}
?>
