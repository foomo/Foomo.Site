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
		if (!empty($html = self::load($dimension, $nodeId))) {
			$doc = self::getDOMDocument($html);

			# replace apps
			self::replaceApps($doc, $baseURL);

			return $doc->saveHTML($doc->getElementsByTagName('div')->item(0));
		} else {
			throw new HTTPException(500, 'The content could not be loaded from the remote server!');
		}
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
		$oldNodes = [];
		$newNodes = [];

		# render apps and create nodes
		foreach ($doc->getElementsByTagName('app') as $element) {
			/* @var $element \DOMElement */
			$appDoc = new \DOMDocument();

			# get class name
			$appClassName = $element->getAttribute('class');

			# find & retrieve app data
			$appData = new \stdClass();
			foreach ($element->getElementsByTagName('script') as $script) {
				/* @var $script \DOMElement */
				if ($script->hasAttribute('rel') && $script->getAttribute('rel') == 'app-data') {
					$appData = (object) json_decode($script->textContent);
					$script->parentNode->removeChild($script);
					break;
				}
			}

			# retrieve inner app html
			$appData->html = self::getInnerHtml($doc, $element);

			# render app
			$appHtml = self::renderApp($appClassName, $appData, $baseURL);

			$appDoc->loadHTML('<div>' . $appHtml . '</div>');
			$appNode = $appDoc->getElementsByTagName('div')->item(0);
			$appNode = $doc->importNode($appNode, true);

			# add nodes
			$oldNodes[] = $element;
			$newNodes[] = $appNode;
		}

		# replace in dom
		foreach ($oldNodes as $key => $oldNode) {
			$fragment = $doc->createDocumentFragment();
			while ($newNodes[$key]->childNodes->length > 0) {
				$fragment->appendChild($newNodes[$key]->childNodes->item(0));
			}
			$oldNode->parentNode->replaceChild($fragment, $oldNode);
		}
	}

	/**
	 * @internal
	 * @Foomo\Cache\CacheResourceDescription
	 *
	 * @param string $dimension
	 * @param string $nodeId
	 * @return string
	 */
	public static function cachedLoad($dimension, $nodeId)
	{
		$url = Neos::getAdapterConfig()->getPathUrl('content') . '/' . $dimension . '/' . $nodeId;
		$json = json_decode(file_get_contents($url));
		$doc = self::getDOMDocument($json->html);

		# replace images & links
		self::replaceImages($doc);
		self::replaceLinks($dimension, $doc);

		return $doc->saveHTML($doc->getElementsByTagName('div')->item(0));
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
