<?php
/**
 * Create an eBay specific XML_API_Call, setting correct
 * headers by appending credentials from database, using the correct
 * URL according to sandbox/production, and using the right root
 * element based on the call name
 *
 * @author Chris Zuber
 * @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
 * @package mother_brain
 * @version 2014-11-13
 *
 * @see http://developer.ebay.com/devzone/xml/docs/reference/ebay/ [eBay API Call Index]
 *
 * @todo HasMoreOrders logic (max 100 per page/request)
 * @todo timestamp/ack check (Success)
 * @todo Throw exception if $call not in $call_list
*/

namespace Kern_River_Corp\eBay_API;
use \shgysk8zer0\Core\PDO as PDO;
use \shgysk8zer0\Core\resources\XML_Node as Node;
use \DOMElement as Element;
abstract class eBay_API_Call extends \shgysk8zer0\Core\XML_API_Call
{
	use \Kern_River_Corp\eBay_API\Resources;

	/**
	 * Store/eBay user name
	 * @var string
	 */
	protected $store;

	/**
	 * Production or sandbox
	 * @var string
	 */
	protected $environment;

	/**
	 * Whether or not this is sandboxed server
	 * @var bool
	 */
	protected $sandbox;

	/**
	 * Create a new XML_API call with eBay specific data
	 *
	 * @param string $store    Store name making request
	 * @param bool   $sandbox  Production or sandbox environment
	 * @param bool   $verbose  Use verbose in cURL request
	 */

	public function __construct(
		$store,
		$sandbox = false,
		$verbose = false
	)
	{
		$this->store = $store;
		$this->sandbox = $sandbox;
		$this->environment = ($this->sandbox) ? 'sandbox' : 'production';

		parent::__construct(
			($this->sandbox) ? \Kern_River_Corp\eBay_API\Defs::SANDBOX_URL : \Kern_River_Corp\eBay_API\Defs::PRODUCTION_URL,
			$this->setHeaders(),
			$this::CALLNAME . 'Request',
			\Kern_River_Corp\eBay_API\Defs::URN,
			\Kern_River_Corp\eBay_API\Defs::CHARSET,
			$verbose
		);

		$this->ErrorLanguage(\Kern_River_Corp\eBay_API\Defs::ERROR_LANG)
			->WarningLevel(\Kern_River_Corp\eBay_API\Defs::WARNING_LEVEL)
			->Version(\Kern_River_Corp\eBay_API\Defs::LEVEL);
	}
}
