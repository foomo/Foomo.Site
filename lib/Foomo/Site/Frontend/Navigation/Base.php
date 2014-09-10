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

namespace Foomo\Site\Frontend\Navigation;

use Foomo\ContentServer\Neos;
use Foomo\ContentServer\Vo\Content;
use Foomo\Site\Module;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
abstract class Base
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const NAME    = 'BASE';

	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string[]
	 */
	protected static $allowedMimeTypes = [
		Neos\MimeType::MIME_SITE_CATEGORY,
		Neos\MimeType::MIME_SITE_EXTERNAL,
	];

	protected static $expand;

	//-------------------------------------------------------------------------
	// ~ Navigation rendering
	//-------------------------------------------------------------------------

	/**
	 * @param Content\Node $node
	 * @param array        $pathArray
	 * @param int          $depth
	 * @param bool         $expandAll
	 * @return mixed
	 */
	public static function render(Content\Node $node, $pathArray=[], $depth=0, $expandAll=true)
	{
		return static::renderNode($node, $simplePath, $depth, 0);
	}

	/**
	 * extract a flattened site path from site content
	 *
	 * @param Content\SiteContent $siteContent
	 *
	 * @return array
	 */
	public static function extractSimplePathFromSiteContent($siteContent)
	{
		return self::flattenPath(self::extractPathFromSiteContent($siteContent));
	}

	/**
	 * the path as flat as a pancake
	 *
	 * @param $path
	 *
	 * @return array
	 */
	public static function flattenPath($path)
	{
		$simplePath = [];
		foreach($path as $item) {
			$simplePath[] = $item->id;
		}
		return $simplePath;
	}

	/**
	 * extract the site path from site content
	 *
	 * @param $siteContent
	 *
	 * @return Content\Item[]
	 */
	public static function extractPathFromSiteContent($siteContent)
	{
		$path = [$siteContent->item];
		if(isset($siteContent->item) && is_array($siteContent->path)) {
			return array_merge($path, $siteContent->path);
		} else {
			return [];
		}
	}


	/**
	 * recursion node renderer
	 *
	 * @param Content\Node $node
	 * @param array        $path
	 * @param string       $html
	 * @param int          $level
	 * @return string
	 */
	protected static function renderNode($node, array $path, &$html = '', $level = 0)
	{
		if($level > 0) {
			$target = (self::isExternalLink(self::getLink($node))) ? '_blank' : '_top';
			$html .= '<li class="' . (self::isInPath($node, $path)?'active':'inactive') . '"><a href="' . self::getLink($node) . '" target="'.$target.'">' .  self::getName($node) . '</a></li>';
		}
		if(count($node) > 0) {
			if($level > 0) {
				$html .= '<ul>';
			}
			if(is_array($node->index)) {
				foreach($node->index as $id) {
					$nodes = $node->nodes;
					if(is_object($nodes)) {
						$nodes = (array)$node->nodes;
					}
					self::renderNode($nodes[$id], $path, $html, $level + 1);
				}
			}
			if($level > 0) {
				$html .= '</ul>';
			}
		}
		return $html;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public static function getRootId()
	{
		$class = get_called_class();
		return Module::getSiteConfig()->getNavigationId($class::NAME);
	}

	/**
	 * @return string[]
	 */
	public static function getAllowedMimeTypes()
	{
		$class = get_called_class();
		return $class::$allowedNavigationMimeTypes;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param $node
	 * @return string
	 */
	protected static function getLink($node)
	{
		return htmlentities($node->item->URI);
	}

	/**
	 * @param $link
	 * @return bool
	 */
	protected static function isExternalLink($link)
	{
		$host = parse_url($link, PHP_URL_HOST);
		if (!is_null($host) && $host != $_SERVER['HTTP_HOST']) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $node
	 * @return string
	 */
	protected static function getName($node)
	{
		return htmlentities($node->item->name);
	}

	/**
	 * @param       $node
	 * @param array $path
	 * @return bool
	 */
	protected static function isInPath($node, array $path)
	{
		if(is_array($path) && count($path) > 0 && is_object($path[0])) {
			$path = self::flattenPath($path);
		}
		return in_array($node->item->id, $path);
	}

	/**
	 * @param       $node
	 * @param array $path
	 * @return bool
	 */
	protected static function isFirstInPath($node, array $path)
	{
		if(is_array($path) && count($path) > 0 && is_object($path[0])) {
			return $node->item->id == $path[0]->id;
		} else {
			return $node->item->id == $path[0];
		}
	}
}
