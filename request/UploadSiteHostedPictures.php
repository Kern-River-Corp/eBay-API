<?php
	namespace \eBay_API\Request;
	class UploadSiteHostedPictures extends \eBay_API\eBay_API_Call {
		const MIN_HEIGHT = 2000;
		const MIN_WIDTH = 2000;

		public function __construct($store, $sandbox = false) {
			parent::__construct($store, 'UploadSiteHostedPictures', $sandbox);

			$this->RequesterCredentials(
					\eBay_API\Credentials::token($store, ($sandbox) ? 'sandbox' : 'production')
			)->set_headers([
				'Content-Disposition' => 'form-data; name="XML Payload'
			]);

		}
	}
?>
