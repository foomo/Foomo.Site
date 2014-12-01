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
	public static function get($dimension, $nodeId, $baseURL)
	{
		$html = self::cachedLoad($dimension, $nodeId);
		if (!empty($html)) {

			$doc = self::getDOMDocument($html);

			# replace apps
			self::replaceApps($doc, $baseURL);

			return $doc->saveHTML($doc->getElementsByTagName('div')->item(0));
		} else {
			throw new HTTPException(500, 'The content could not be loaded from the remote server!');
		}
	}

	/**
	 * @inheritdoc
	 */
	public static function load($dimension, $nodeId)
	{
		$url = Neos::getAdapterConfig()->getPathUrl('content') . '/' . $dimension . '/' . $nodeId;
		$json = json_decode(file_get_contents($url));
		$doc = self::getDOMDocument($json->html);

		# replace images & links
		self::replaceImages($doc);
		self::replaceLinks($dimension, $doc);

		return $doc->saveHTML($doc->getElementsByTagName('div')->item(0));
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param \DOMDocument $doc
	 * @param string       $baseURL
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
			$appData->html = self::getInnerHtml($doc, $appNode);

			# render app
			$appHtml = self::renderApp($appClassName, $appData, $baseURL);

			# create app dom document
			$appFragment = $doc->createDocumentFragment();
			$appFragment->appendXML($appHtml);

			# replace in dom
			$appNode->parentNode->replaceChild($appFragment, $appNode);
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
			$uri = Neos\SubRouter::getImageUri($type, $nodeId);
			if ($time) {
				$uri .= '/' . $time;
			}
			$image->setAttribute('src', $uri);
		}
	}

	/**
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
			if (substr($href, 0, 7) == 'neos://') {
				$ids = array_unique(array_merge($ids, [substr($href, 7)]));
				$elements[] = $element;
			} else if (preg_match('~^(?:f|ht)tps?://~i', $href)) {
				// add target attr
				$element->setAttribute('target', '_blank');
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
