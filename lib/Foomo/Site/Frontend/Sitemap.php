<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Site\Frontend;

use Foomo\ContentServer\Vo\Content\RepoNode;
use Foomo\Site;

/**
 * Simple sitemap implementation
 *
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Sitemap
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var bool
	 */
	protected static $expandAll = true;
	/**
	 * @var string[]
	 */
	protected static $ignoreIds = [];
	/**
	 * @var string[]
	 */
	protected static $mimeTypes = [];
	/**
	 * @var string[]
	 */
	protected static $ignoreNames = [];
	/**
	 * @var string[]
	 */
	protected static $ignorePatterns = [];

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param RepoNode $node
	 * @param array    $attributes
	 * @return string
	 */
	public static function render($node, $attributes = [])
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
		$xml .= 'xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;
		$xml .= static::iterateNode($node, $attributes);
		$xml .= '</urlset>';
		return $xml;
	}

	/**
	 * Set headers, echos & exits
	 *
	 * @param RepoNode $node
	 * @param array    $attributes
	 * @return string
	 */
	public static function output($node, $attributes = [])
	{
		header('Content-Type: text/xml; charset=utf-8;');
		echo static::render($node, $attributes);
		exit;
	}

	/**
	 * Set headers, echos & exits
	 *
	 * @param string[] $uris Array of relative uri
	 */
	public static function outputIndex($uris)
	{
		header('Content-Type: text/xml; charset=utf-8;');
		$xml = '<sitemapindex>';
		foreach ($uris as $uri) {
			$xml .= '<sitemap><loc>' . Site::getConfig()->domain . $uri . '</loc></sitemap>';
		}
		$xml .= '</sitemapindex>';
		echo $xml;
		exit;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param RepoNode $node
	 * @param array    $attributes
	 * @return string
	 */
	protected static function iterateNode($node, array $attributes)
	{
		$ret = '';

		if (static::isVisible($node)) {
			$ret .= static::renderNode($node, $attributes);
		}

		if ($node->nodes) {
			foreach ($node->nodes as $childNode) {
				$ret .= static::iterateNode($childNode, $attributes);
			}
		}
		return $ret;
	}

	/**
	 * @param RepoNode $node
	 * @param array    $attributes
	 * @return string
	 */
	protected static function renderNode($node, array $attributes)
	{
		$ret = '<url>' . PHP_EOL;
		$uri = htmlentities($node->URI);
		if ($uri == '/') {
			$uri = '';
		}
		$attributes['loc'] = Site::getConfig()->domain . $uri;
		foreach ($attributes as $key => $value) {
			if (!empty($value)) {
				$ret .= "<" . $key . ">" . $value . "</" . $key . ">" . PHP_EOL;
			}
		}
		$ret .= '</url>' . PHP_EOL;
		return $ret;
	}

	/**
	 * @param RepoNode $node
	 * @return bool
	 */
	protected static function isVisible($node)
	{
		return (
			!$node->hidden ||
			in_array($node->id, static::$ignoreIds) ||
			in_array($node->name, static::$ignoreNames) ||
			in_array($node->mimeType, static::$mimeTypes) ||
			preg_match('/(' . implode('|', static::$ignorePatterns) . ')/i', $node->name) > 0
		);
	}
}
