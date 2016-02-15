<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Site;

use Foomo\HTMLDocument;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  nicola
 */
class Recaptcha
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var self
	 */
	protected static $inst;

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return static
	 */
	public static function getInstance()
	{
		if (is_null(static::$inst)) {
			static::$inst = new static();
		}
		return static::$inst;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Initialize reCAPTCHA by adding the API JS
	 * @param HTMLDocument $HTMLDoc
	 * @return self
	 */
	public function init($HTMLDoc = null)
	{
		if (is_null($HTMLDoc)) {
			$HTMLDoc = HTMLDocument::getInstance();
		}
		$env = \Foomo\Site::getEnv();
		$HTMLDoc->addJavascripts(["https://www.google.com/recaptcha/api.js?hl=" . $env::getLanguage() ]);
		return $this;
	}

	/**
	 * Get HTML widget for reCAPTCHA
	 * @return string HTML
	 */
	public function getWidget()
	{
		$config = Module::getRecaptchaConfig();
		return '<div class="g-recaptcha" data-sitekey="' . $config->siteKey . '"></div>';
	}

	/**
	 * Verify captcha input
	 * @param string $captchaResponseCode
	 * @return bool
	 */
	public static function verifyCaptcha($captchaResponseCode)
	{
		$config = Module::getRecaptchaConfig();

		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $config->secretKey . "&response=" . $captchaResponseCode . "&remoteip=" . $_SERVER['REMOTE_ADDR'] );
		$response = json_decode($response, true);

		return ($response["success"] === true) ? true : false;
	}
}
