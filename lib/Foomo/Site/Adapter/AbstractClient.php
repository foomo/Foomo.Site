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

namespace Foomo\Site\Adapter;

use Foomo\Cache\Proxy;
use Foomo\Site\Adapter\Neos;
use Foomo\Site\Exception\HTTPException;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
abstract class AbstractClient
{
	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Load content from the remote content server
	 *
	 * @param string $dimension
	 * @param string $nodeId
	 * @param string $domain
	 * @return string
	 */
	protected static function cachedLoad($dimension, $nodeId, $domain=null)
	{
		return Proxy::call(
			'Foomo\Site\Adapter',
			'cachedLoadClientContent',
			[
				get_called_class(),
				$dimension,
				$nodeId,
				$domain
			]
		);
	}

	/**
	 * @param string $html
	 * @return \DOMDocument
	 * @throws HTTPException
	 */
	protected static function getDOMDocument($html)
	{
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML('<!DOCTYPE html>' . PHP_EOL . '<html><head><meta charset="UTF-8"></head><body>'.$html.'</body></html>');
		/* @var $xmlError \libXMLError */
		foreach (libxml_get_errors() as $xmlError) {
			// error list is defined here
			// 	http://xmlsoft.org/html/libxml-xmlerror.html

			if (
				$xmlError->level == LIBXML_ERR_ERROR &&
				!in_array(
					$xmlError->code,
					[
						801, // XML_HTML_UNKNOWN_TAG
						68,  // XML_HTML_UNKNOWN_TAG
						513, //  XML_DTD_ID_REDEFINED
						23, // XML_FROM_VALID = 23 : The XML DTD validation with valid context
						76
					]
				)
			) {
				switch($xmlError->level) {
					case LIBXML_ERR_FATAL:
					case LIBXML_ERR_ERROR:
						trigger_error('badly invalid XML: ' . var_export($xmlError, true), E_USER_WARNING);
						//throw new HTTPException(500, $xmlError->message);
						break;
					default:
						trigger_error('invalid XML: ' . var_export($xmlError, true), E_USER_WARNING);
				}
			}
		}
		libxml_clear_errors();
		libxml_use_internal_errors(false);
		return $doc;
	}

	/**
	 * @param string $className
	 * @param mixed  $data
	 * @param string $baseURL
	 * @return string
	 */
	protected static function renderApp($className, $data, $baseURL)
	{
		$classes = [
			$className,
			$className . '\\Frontend',
		];
		foreach ($classes as $class) {
			if (class_exists($class)) {
				return call_user_func_array([$class, 'run'], [$data, $baseURL]);
			}
		}
		return '<pre>No App found for: ' . $className . '</pre>';
	}

	/**
	 * @param \DOMDocument $doc
	 * @param \DOMNode     $node
	 * @return string
	 */
	protected static function getInnerHtml(\DOMDocument $doc, \DOMNode $node)
	{
		$html = '';
		foreach ($node->childNodes as $childNode) {
			$html .= $doc->saveXML($childNode);
		}
		return $html;
	}
}
