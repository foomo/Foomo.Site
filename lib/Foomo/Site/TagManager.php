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
	public $pageData = [];
	/**
	 * @internal
	 * @var array
	 */
	public $eventData = [];

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

		$config = Module::getTagManagerConfig();

		if (!empty($config->optimizeId)) {
			$optimizeId = $config->optimizeId;
			$HTMLDoc->addStylesheet('.async-hide {opacity:0 !important}');
			$HTMLDoc->addJavascript("
				(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;
				h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
				(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);
				})(window,document.documentElement,'async-hide','dataLayer',2000,{'${optimizeId}':true});
			");
		}

		if (!empty($config->containers)) {
			$HTMLDoc->addJavascript("var dataLayer = dataLayer || [];" . PHP_EOL);
			# page data
			sort($this->pageData);
			foreach ($this->pageData as $data) {
				if (empty($data)) continue;
				$jsonData = json_encode((object) $data);
				$HTMLDoc->addJavascript("dataLayer.push(${jsonData});" . PHP_EOL);
			}
			$noScript = '';
			foreach ($config->containers as $container) {
				$containerId = $container['id'];
				$environment = "";
				if (
					isset($container['auth']) && !empty($container['auth']) &&
					isset($container['preview']) && !empty($container['preview'])
				) {
					$environment = "+'&gtm_auth=${container['auth']}&gtm_preview=${container['preview']}&gtm_cookies_win=x'";
				}
				$HTMLDoc->addJavascript("
					(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
					new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
					'https://www.googletagmanager.com/gtm.js?id='+i+dl${environment};f.parentNode.insertBefore(j,f);
					})(window,document,'script','dataLayer','${containerId}');"
				);
				$noScript .= "<iframe src=\"https://www.googletagmanager.com/ns.html?id=${containerId}\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe>" . PHP_EOL;
			}
			# event data
			sort($this->eventData);
			foreach ($this->eventData as $data) {
				if (empty($data)) continue;
				$jsonData = json_encode((object) $data);
				$HTMLDoc->addJavascript("dataLayer.push(${jsonData});" . PHP_EOL);
			}
			$HTMLDoc->addBody("
				<noscript>
					${noScript}
				</noscript>
			");
		}
		return $this;
	}

	/**
	 * @param $id mixed
	 * @param $key string
	 * @param $value mixed
	 * @return $this
	 */
	public function addToPage($id, $key, $value) {
		if (!isset($this->pageData[$id])) {
			$this->pageData[$id] = [];
		}
		$this->pageData[$id][$key] = $value;
		return $this;
	}

	/**
	 * @param $id mixed
	 * @param $value mixed
	 * @return $this
	 */
	public function pushToPage($id, $value) {
		$this->pageData[$id] = $value;
		return $this;
	}

	/**
	 * @param $id mixed
	 * @param $key string
	 * @param $value mixed
	 * @return $this
	 */
	public function addToEvent($id, $key, $value) {
		if (!isset($this->eventData[$id])) {
			$this->eventData[$id] = [];
		}
		$this->eventData[$id][$key] = $value;
		return $this;
	}

	/**
	 * @param $id mixed
	 * @param $value mixed
	 * @return $this
	 */
	public function pushToEvent($id, $value) {
		$this->eventData[$id] = $value;
		return $this;
	}
}
