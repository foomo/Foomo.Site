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

namespace Foomo\Site\Adapter\Neos;

use Foomo\Cache;
use Foomo\Site\Adapter\Neos;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Content
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 *
	 * @todo shouldn't we cache the parse part too?
	 *
	 * @param string   $nodeId
	 * @param string   $region
	 * @param string   $language
	 * @param string[] $groups
	 * @param string   $state
	 * @param string   $baseURL
	 * @return string
	 */
	public static function get($nodeId, $region, $language, $groups, $state, $baseURL)
	{
		$json = self::load($nodeId, $region, $language, $groups, $state);

		if (empty($json)) {
			// @todo: shouldn't we throw an exception here?
			trigger_error(
				'No content retrieved for ' . $nodeId . ' in ' . $region . '_' . $language . ' with baseURL ' . $baseURL,
				E_USER_WARNING
			);
			return '';
		} else {
			$html = self::parse($json, $baseURL);
			$html = self::replaceImages($html);
			$html = self::replaceLinks($html, $region, $language);
			return $html;
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Load content from the remote content server
	 *
	 * @param string   $nodeId
	 * @param string   $region
	 * @param string   $language
	 * @param string[] $groups
	 * @param string   $state
	 * @return string
	 */
	private static function load($nodeId, $region, $language, $groups, $state)
	{
		return Cache\Proxy::call(__CLASS__, 'cachedLoad', [$nodeId, $region, $language, $groups, $state]);
	}

	/**
	 * @internal
	 * Foomo\Cache\CacheResourceDescription
	 *
	 * @todo: reenable caching
	 * @todo: what about region, language, groups, state ...?
	 *
	 * @param string   $nodeId
	 * @param string   $region
	 * @param string   $language
	 * @param string[] $groups
	 * @param string   $state
	 * @return string
	 */
	public static function cachedLoad($nodeId, $region, $language, $groups, $state)
	{
		$url = Neos::getAdapterConfig()->getPathUrl('content') . '/' . $nodeId;
		return json_decode(file_get_contents($url));
	}

	/**
	 * @param $json
	 * @param $baseURL
	 * @return mixed
	 */
	private static function parse($json, $baseURL)
	{
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML('<?xml encoding="UTF-8">' . $json->html);
		foreach (libxml_get_errors() as $xmlError) {
			/* @var $xmlError \libXMLError */
			if ($xmlError->code == 801 && strpos($xmlError->message, ' app ') !== false) {
				continue;
			} else {
				$errorLevel = E_USER_NOTICE;
				switch ($xmlError->level) {
					case LIBXML_ERR_FATAL:
						$errorLevel = E_USER_ERROR;
						break;
					case LIBXML_ERR_WARNING:
					case LIBXML_ERR_ERROR:
						$errorLevel = E_USER_WARNING;
						break;
				}
				if ($errorLevel == E_USER_ERROR) {
					// @todo: shouldn't we throw exceptions here too so we can handle it properly?
					trigger_error("libxml threw up: " . var_export($xmlError, true) . ' for html ' . $json->html, $errorLevel);
				}
			}
		}
		libxml_clear_errors();
		libxml_use_internal_errors(false);
		$appElements = [];

		$classNames = [];
		foreach ($doc->getElementsByTagName('app') as $appEl) {
			$appElements[] = $appEl;
			$classNames[] = $appEl->getAttribute('data-creation-app-class-name');
		}

		$appCounter = 0;
		$appReplacements = [];

		foreach ($appElements as $appEl) {
			/* @var $appEl DOMElement */
			$appDoc = new \DOMDocument();
			$appData = json_decode($appEl->getElementsByTagName('script')->item(0)->textContent);
			# render apps
			$key = '<!-- replace-foomo-app-' . ($appCounter++) . ' -->';
			$appReplacements[$key] = self::renderContentApp(
				$appEl->getAttribute('data-creation-app-class-name'),
				$appData,
				$baseURL
			);
			$appDoc->loadHTML('<?xml encoding="UTF-8"><div>' . $key . '</div>');
			$appNode = $appDoc->getElementsByTagName('div')->item(0);
			$appNode = $doc->importNode($appNode, true);
			$appEl->parentNode->replaceChild($appNode, $appEl);
		}

		$html = str_replace(
			array_keys($appReplacements),
			array_values($appReplacements),
			$doc->saveHTML($doc->getElementsByTagName('div')->item(0))
		);
		return $html;
	}

	/**
	 * @param string $appClassName
	 * @param mixed  $appData
	 * @param string $baseURL
	 *
	 * @return string
	 */
	private static function renderContentApp($appClassName, $appData, $baseURL)
	{
		$clientClassName = $appClassName;
		$clientClassNameMVC = $clientClassName . '\\Frontend';
		if (class_exists($clientClassName)) {
			return call_user_func_array([$clientClassName, 'run'], [$appData, $baseURL]);
		} else if (class_exists($clientClassNameMVC)) {
			return call_user_func_array([$clientClassNameMVC, 'run'], [$appData, $baseURL]);
		} else {
			//@todo: run mode specific error handling and error triggering
			trigger_error("Could not find app $appClassName", E_USER_WARNING);
			return '<h1>No App found ' . $appClassName . ' =&gt; ' . $clientClassName . '</h1>';
		}
	}

	/**
	 * @todo: is there a way not to set full?
	 *
	 * @param string $html
	 * @return mixed
	 */
	private static function replaceImages($html)
	{
		$pattern = '/\<img([^\>]*)data-src="([^"]*)"([^\>]*)src="([^"]*)"([^\>]*)\>/';
		$callback = function ($matches) {
			$imageUri = Neos\SubRouter::getImageUri('full', $matches[2]);
			return '<img' . $matches[1] . $matches[3] . ' src="' . $imageUri . '"' . $matches[5] . '>';
		};
		return preg_replace_callback($pattern, $callback, $html);
	}

	/**
	 * @todo: implement default link handling
	 *
	 * @param string $html
	 * @param string $region
	 * @param string $language
	 * @return string
	 */
	private static function replaceLinks($html, $region, $language)
	{
		return $html;
	}
}
