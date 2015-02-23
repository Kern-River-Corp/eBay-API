<?php

namespace Kern_River_Corp\eBay_API;

/**
 * Constants for eBay API calls. Either extend or use externally
 */
abstract class eBay_Constants
{
	const LEVEL = 583,
		SITEID = 0,
		SITECODE = 'US',
		URN = 'urn:ebay:apis:eBLBaseComponents',
		CHARSET = 'UTF-8',
		TYPE = 'text/xml',
		BOUNDARY = 'MIME_boundary',
		ERROR_LANG = 'en_US',
		WARNING_LEVEL = 'High',
		MEASUREMENT_SYSTEM = 'English',
		WEIGHT_UNIT_MAJOR = 'lb',
		WEIGHT_UNIT_MINOR = 'oz',
		LINEAR_UNIT = 'in',
		CURRENCY_ID = 'USD',
		DATETIME_FORMAT = 'Y-m-d\TH:i:s.000\Z',
		SANDBOX_URL = 'https://api.sandbox.ebay.com/ws/api.dll',
		PRODUCTION_URL = 'https://api.ebay.com/ws/api.dll',
		SLEEP = 5;
}
