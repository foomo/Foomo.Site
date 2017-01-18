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
 * @author  franklin
 */
class TagManager
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var self
	 */
	protected static $inst;

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @internal
	 * @var array
	 */
	public $data = [];

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
	 * @param HTMLDocument $HTMLDoc
	 * @return self
	 */
	public function addToHTMLDoc($HTMLDoc = null)
	{
		if (is_null($HTMLDoc)) {
			$HTMLDoc = HTMLDocument::getInstance();
		}

		$data = json_encode((object) $this->data);
		$containerId = Module::getTagManagerConfig()->containerId;

		$HTMLDoc->addJavascript("<script>dataLayer = [${data}]</script>");
		$HTMLDoc->addJavascript("<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${containerId}');</script>");
		$HTMLDoc->addBody("<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=${containerId}\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>");
		return $this;
	}

	/**
	 * @param $key string
	 * @param $value mixed
	 * @return $this
	 */
	public function addData($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}
}
