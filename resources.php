<?php
namespace Kern_River_Corp\eBay_API;
use \Kern_River_Corp\eBay_API\Credentials as Credentials;
use \shgysk8zer0\Core\PDO as PDO;
use \shgysk8zer0\Core\resources\XML_Node as Node;
use \DOMElement as Element;
trait Resources {

	/**
	 * Build the headers array, including eBay credentials & optional
	 * additional headers
	 *
	 * @param array $additional Optional additional headers ($name => $value)
	 *
	 * @return array
	 */

	public function setHeaders(array $additional = array())
	{
		return array_merge(
			Credentials::fetch($this->store, $this->environment),
			[
				'Content-Type' => $this::TYPE . '; boundary=' . $this::BOUNDARY,
				'X-EBAY-API-COMPATIBILITY-LEVEL' => $this::LEVEL,
				'X-EBAY-API-CALL-NAME' => $this::CALLNAME,
				'X-EBAY-API-SITEID' => $this::SITEID
			],
			$additional
		);
	}

	/**
	* Description may contain HTML, which needs to be contained within
	* CDATA in the XML request.
	*
	* @param string $content HTML for Description
	* @return \Kern_River_Corp\eBay_API\eBay_API_Call
	*/

	public function Description($content)
	{
		$parent = new Node('Description');
		$this->body->append($parent);
		$content = str_replace(["\r", "\r\n", "\n", "\t"], null, $content);
		$desc = $this->createCDATASection($content);
		$parent->appendChild($desc);
		return $this;
	}

	/**
	* eBay times need to be converted into UFC formatted GMT times
	* Automatically set ScheduleTime to this
	*
	* @param string $datetime Any date format that works with strtotime()
	*/

	public function ScheduleTime($datetime = null)
	{
		$this->body->appendChild(new Element('ScheduleTime', $this->convert_date($datetime)));
		return $this;
	}

	/**
	* eBay times need to be converted into UFC formatted GMT times
	*
	* @param  string $datetime Any date format that works with strtotime()
	*
	* @return string           Datetime in DATETIME_FORMAT
	*/

	protected function convert_date($datetime = 'Now')
	{
		return gmdate($this::DATETIME_FORMAT, strtotime($datetime));
	}

	/**
	* [return_policy description]
	*
	* @param  string $store Name of store to get return policy for
	*
	* @return array        [description]
	*/

	public function return_policy($store = null) {
		return get_object_vars(PDO::load('inventory_data')->prepare(
			"SELECT
				`ReturnsAcceptedOption`,
				`RefundOption`,
				`ReturnsWithinOption`,
				`Description`,
				`ShippingCostPaidByOption`
			FROM `inventory_data`.`ReturnPolicy`;
			WHERE `store` = :store
			AND `channel` = 'ebay'
			LIMIT 1;"
		)->execute([
			'store' => (isset($store)) ? $store : $this->store
		])->getResults(0));
	}

	/**
	* [package_info description]
	*
	* @param  string $type [description]
	* @param  string $size [description]
	*
	* @return array        [description]
	*/

	public function package_info($type, $size)
	{
		$result = PDO::load('inventory_data')->prepare(
			"SELECT
				`length`,
				`width`,
				`depth`,
				`oz` AS `weight`,
				`package`,
				`irregular`
			FROM `garment_weight_dimensions`
			WHERE `type` = :type
			AND `size` = :size;"
		)->execute([
			'type' => trim($type),
			'size' => trim($size)
		])->getResults(0);

		return (empty($result)) ? false : [
			'PackageDepth' => [
				(float)$result->depth,
				$this->create_attributes([
					'unit' => $this::LINEAR_UNIT,
					'measurementSystem' => $this::MEASUREMENT_SYSTEM
				])
			],
			'PackageLength' => [
				(float)$result->length,
				$this->create_attributes([
				'unit' => $this::LINEAR_UNIT,
				'measurementSystem' => $this::MEASUREMENT_SYSTEM
				])
			],
			'PackageWidth' => [
				(float)$result->width,
				$this->create_attributes([
					'unit' => $this::LINEAR_UNIT,
					'measurementSystem' => $this::MEASUREMENT_SYSTEM
				])
			],
			'WeightMajor' => [
				floor($result->weight / 16),
				$this->create_attributes([
					'unit' => $this::WEIGHT_UNIT_MAJOR,
					'measurementSystem' => $this::MEASUREMENT_SYSTEM
				])
			],
			'WeightMinor' => [
				$result->weight % 16,
				$this->create_attributes([
					'unit' => $this::WEIGHT_UNIT_MINOR,
					'measurementSystem' => $this::MEASUREMENT_SYSTEM
				])
			],
			'ShippingPackage' => $result->package,
			'ShippingIrregular' => $result->irregular
		];
	}

	/**
	* Get the CategoryID for listing on eBay
	*
	* @param string $name  code for the name. E.G. LS for Long Sleeve
	* @return array        'CategoryID' => $CategoryID
	*/

	public function getCategoryID($name)
	{
		return get_object_vars(PDO::load('inventory_data')->prepare(
			"SELECT `CategoryID`
			FROM `categories`
			WHERE `name` = :name
			LIMIT 1;"
		)->execute([
			'name' => $name
		])->getResults(0));
	}

	/**
	* Converts CategoryID into its name
	*
	* @param int $id StoreCategoryID from eBay
	* @return stdClass
	*/

	public function getCategory($id)
	{
		$results = PDO::load('inventory_data')->prepare(
			"SELECT
				`name` AS `Name`,
				`code` AS `Code`,
				`CategoryID`
			FROM `categories`
			WHERE `StoreCategoryID` = :id
			LIMIT 1;"
		)->execute([
			'id' => $id
		])->getResults(0);

		if (is_object($results)) {
			return $results;
		} else {
			return null;
		}
	}
}
