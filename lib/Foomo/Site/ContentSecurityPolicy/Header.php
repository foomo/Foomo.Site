<?php

namespace Foomo\Site\ContentSecurityPolicy;

class Header
{
	/**
	 * output the content security policy header
	 */
	public static function emit()
	{
		$config = \Foomo\Config::getConf(\Foomo\Site\Module::getRootModule(), \Foomo\Site\ContentSecurityPolicy\DomainConfig::NAME);

		if ($config && $config->enabled) {
			$props = ['defaultSrc' => 'default-src', 'scriptSrc' => 'script-src', 'styleSrc' => 'style-src', 'fontSrc' => 'font-src', 'imgSrc' => 'img-src'];
			$headerVal = '';
			foreach ($props as $prop => $attr) {
				if (!empty($config->$prop)) {
					$headerVal .= $attr . ' ' . implode(' ', $config->$prop) . ';';
				}
			}

			if (!empty($config->reportUri)) {
				$headerVal .= 'report-uri ' . $config->reportUri . ';';
			}

			if (!empty($headerVal)) {
				if ($config->notifyOnly) {
					header('Content-Security-Policy-Report-Only: ' . $headerVal);
				} else {
					header('Content-Security-Policy: ' . $headerVal);
				}
			}
		}
	}

}