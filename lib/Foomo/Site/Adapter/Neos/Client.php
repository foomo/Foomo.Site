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
use Foomo\Config;
use Foomo\Site\Adapter\AbstractBase;
use Foomo\Site\Adapter\AbstractClient;
use Foomo\Site\Adapter\ClientInterface;
use Foomo\Site\Adapter\Neos;
use Foomo\Site\Exception\HTTPException;
use Foomo\Site\Module;
use Foomo\Timer;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Client extends AbstractClient implements ClientInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function get($dimension, $nodeId, $baseURL, $domain=null)
	{
		\Foomo\Timer::addMarker('service neos content');
		\Foomo\Timer::start($topic = __METHOD__);

		$html = static::cachedLoad($dimension, $nodeId, $domain);
		if (!empty($html)) {
			$doc = static::getDOMDocument($html);

			# replace apps
			static::replaceApps($doc, $baseURL);

			# grep first div in body (if exists)
			$item = $doc->getElementsByTagName('div')->item(0);
			if(!is_null($item)) {
				$html = $doc->saveHTML($item);
				\Foomo\Timer::stop($topic);
				return $html;
			}
		}
		throw new HTTPException(500, 'The content could not be loaded from the remote server!');
	}

	/**
	 * @inheritdoc
	 */
	public static function load($dimension, $nodeId, $domain=null)
	{
		\Foomo\Timer::start($topic = __METHOD__);
		if(is_null($domain)) {
			$domain = Neos::getName();
		}
		$adapterConfig = AbstractBase::getAdapterConfig($domain);
		$url = $adapterConfig->getPathUrl('content') . '/' . $dimension . '/' . $nodeId;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$json = json_decode(curl_exec($ch));
		curl_close($ch);
		$doc = static::getDOMDocument($json->html);

		# replace images & links
		static::replaceImages($doc);
		static::replaceLinks($dimension, $doc);

		# grep first div in body (if exists)
		$item = $doc->getElementsByTagName('div')->item(0);
		if(!is_null($item)) {
			$html = $doc->saveHTML($item);
		} else {
			trigger_error('unable to fetch first div in body on node "' . $nodeId . '" in dimension "'.$dimension.'" from "'. $url . '" - maybe the cms response is empty in ' . __METHOD__, E_USER_WARNING);
			throw new HTTPException(500, 'The content could not be loaded from the remote server!');
		}

		\Foomo\Timer::stop($topic);
		return $html;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param \DOMDocument $doc
	 * @param string       $baseURL
	 *
	 * @throws HTTPException
	 */
	protected static function replaceApps(\DOMDocument $doc, $baseURL)
	{
		$appPaths = [];
		$xpath = new \DOMXpath($doc);

		foreach ($doc->getElementsByTagName('app') as $appNode) {
			/* @var $appNode \DOMElement */
			$appPaths[] = $appNode->getNodePath();
		}

		# sort paths by length so we render the nested first
		usort(
			$appPaths, function ($a, $b) {
				return strlen($b) - strlen($a);
			}
		);

		# render apps and create nodes
		foreach ($appPaths as $appPath) {

			/* @var $appNode \DOMElement */
			$appNode = $xpath->query($appPath)->item(0);
			$appNodeId = $appNode->getAttribute('id');

			# get app class name
			$appClassName = $appNode->getAttribute('class');

			# find & retrieve app data
			$appData = (object) array();
			if (null != $appNodeData = $doc->getElementById('data-' . $appNodeId)) {
				/* @var $appNodeData \DOMElement */
				$data = $appNodeData->textContent;
				if ($data) {
					$appData = json_decode($data);
				}
				$appNodeData->parentNode->removeChild($appNodeData);
			}

			# retrieve inner app html
			$appData->html = static::getInnerHtml($doc, $appNode);

			# render app
			$appHtml = static::renderApp($appClassName, $appData, $baseURL);
			$appDom = static::getDOMDocument($appHtml);

			# grep first div in body (if exists)
			$item = $appDom->getElementsByTagName('div')->item(0);

			# error handling
			if(is_null($item)) {
				trigger_error('unable to append the rendered app instance of class '.$appClassName.' to the given cms content in ' . __METHOD__ . ' while serving ' . $_SERVER['REQUEST_URI'], E_USER_WARNING);
				throw new HTTPException(500, 'The content could not be loaded from the remote server!');
			}

			# replace in dom
			$item = $doc->importNode($item, true);
			$appNode->parentNode->replaceChild($item, $appNode);
		}
	}

	/**
	 * @param \DOMDocument $doc
	 * @return mixed
	 */
	protected static function replaceImages(\DOMDocument $doc)
	{
		/* @var $image \DOMElement */
		foreach ($doc->getElementsByTagName("img") as $image) {
			# get media server type
			$type = $image->getAttribute('data-type');
			if(empty($type)) {
				$type = 'default';
			}
			$image->removeAttribute('data-type');
			# get last modified timestamp
			$time = $image->getAttribute('data-time');
			$image->removeAttribute('data-time');
			# get node id
			$nodeId = $image->getAttribute('data-src');
			$image->removeAttribute('data-src');
			# remove dimensions
			$image->removeAttribute('height');
			$image->removeAttribute('width');
			# get local uri
			$uri = Neos\SubRouter::getImageUri($type, $nodeId, $time);

			$image->setAttribute('src', $uri);
		}
	}

	/**
	 * Seems like this gets cached in static::load() ...
	 *
	 * @param string       $dimension
	 * @param \DOMDocument $doc
	 *
	 * @return string
	 */
	protected static function replaceLinks($dimension, \DOMDocument $doc)
	{
		$ids = [];
		$elements = [];


		# collect all ids
		foreach ($doc->getElementsByTagName('a') as $element) {
			/* @var $element \DOMElement */
			$href = $element->getAttribute('href');
			switch (true) {
				case (substr($href, 0, 7) == 'node://'):
				case (substr($href, 0, 7) == 'neos://'):
				case (substr($href, 0, 7) == 'nodeid:'):
					$ids = array_unique(array_merge($ids, [substr($href, 7)]));
					$elements[] = $element;
					break;
				case (preg_match('~^(?:f|ht)tps?://~i', $href) && !$element->hasAttribute('target')):
					// add target attr
					$element->setAttribute('target', '_blank');
					break;
			}
		}

		if (!empty($ids)) {
			# retrieve uris
			$uris = Module::getSiteContentServerProxyConfig()->getProxy()->getURIs($dimension, $ids);

			# replace hrefs
			foreach ($elements as $element) {
				$href = $element->getAttribute('href');
				$element->setAttribute('href', $uris->{substr($href, 7)});
			}
		}
	}
}
