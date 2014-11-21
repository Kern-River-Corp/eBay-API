<?php
	/**
	 * Class with protected method for retrieving eBay credentials from a
	 * database. Only accessible to \eBay_API_Call.
	 *
	 *  @author Chris Zuber
	 *  @copyright 2014, Chris Zuber <chris@kernrivercorp.com>
	 *  @package mother_brain
	 *  @version 2014-11-13
	 */

	namespace eBay_API;
	use \core\PDO as PDO;

	abstract class Credentials {
		private static $credentials = null, $tokens = null;
		const INI = 'ebay_api';

		/**
		 * Queries a database for credentials and returns as an associative array
		 *
		 * This static method is protected, so it is only accessible through
		 * eBay API Call class.
		 *
		 * It is also static, so requesting the same credentials again does not
		 * mean another database query. If, however, you use a different store
		 * or environment, the database will be queried and the results will
		 * be added to $credentials array
		 *
		 * @param  string $store       [User in database, used to get credentials]
		 * @param  string $environment [production or sandbox]
		 * @return array               [$key => $value array with eBay API headers credential]
		 */

		public static function fetch($store, $environment = 'production') {
			if(is_null(self::$credentials)) self::$credentials = [];
			if(!array_key_exists($store, self::$credentials)) self::$credentials[$store] = [];
			if(!array_key_exists($environment, self::$credentials[$store])) {
				$creds = PDO::load(self::INI);
				self::$credentials[$store][$environment] = get_object_vars($creds->prepare("
					SELECT
						`dev_key` AS `X-EBAY-API-DEV-NAME`,
						`app_key` AS `X-EBAY-API-APP-NAME`,
						`cert_id` AS `X-EBAY-API-CERT-NAME`
					FROM `{$creds->escape($environment)}`
					WHERE `user` = :user
					LIMIT 1
				")->bind([
					'user' => $store
				])->execute()->get_results(0));
			}
			return self::$credentials[$store][$environment];
		}

		/**
		 * Get eBayAuthToken for requested store & environment
		 *
		 * Also a static method using static multi-dimensional array. This one,
		 * however, is public, so it can be accessed outside of eBay_API_Call
		 * class.
		 *
		 * @param  string $store       [User in database, used to get credentials]
		 * @param  string $environment [production or sandbox]
		 * @return array              ['eBayAuthToken' => $token]
		 */

		public static function token($store, $environment = 'production') {
			if(is_null(self::$tokens)) self::$tokens = [];
			if(!array_key_exists($store, self::$tokens)) self::$tokens[$store] = [];
			if(!array_key_exists($environment, self::$tokens[$store])) {
				$creds = PDO::load(self::INI);
				self::$tokens[$store][$environment] = get_object_vars($creds->prepare("
					SELECT `token` as `eBayAuthToken`
					FROM `{$creds->escape($environment)}`
					WHERE `user` = :user
					LIMIT 1
				")->bind([
					'user' => $store
				])->execute()->get_results(0));
			}
			return self::$tokens[$store][$environment];
		}
	}
?>
