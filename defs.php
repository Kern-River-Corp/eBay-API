<?php

namespace Kern_River_Corp\eBay_API;

/**
 * Constants for eBay API calls. Either extend or use externally as eBay_Constants::NAME
 */
abstract class Defs
{
	const LEVEL = 583;
	const SITEID = 0;
	const SITECODE = 'US';
	const URN = 'urn:ebay:apis:eBLBaseComponents';
	const CHARSET = 'UTF-8';
	const TYPE = 'text/xml';
	const BOUNDARY = 'MIME_boundary';
	const ERROR_LANG = 'en_US';
	const WARNING_LEVEL = 'High';
	const MEASUREMENT_SYSTEM = 'English';
	const WEIGHT_UNIT_MAJOR = 'lb';
	const WEIGHT_UNIT_MINOR = 'oz';
	const LINEAR_UNIT = 'in';
	const CURRENCY_ID = 'USD';
	const DATETIME_FORMAT = 'Y-m-d\TH:i:s.000\Z';
	const SANDBOX_URL = 'https://api.sandbox.ebay.com/ws/api.dll';
	const PRODUCTION_URL = 'https://api.ebay.com/ws/api.dll';
	const SLEEP = 5;
}
