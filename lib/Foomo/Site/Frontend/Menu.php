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

use Foomo\ContentServer\Vo\Content\Item;
use Foomo\ContentServer\Vo\Content\Node;
use Foomo\Site;

/**
 * @todo    does `hidden` work as expected so we don't need to ignore stuff here?
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Menu
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
	protected static $ignoreNames = [];
	/**
	 * @var string[]
	 */
	protected static $ignorePatterns = [];

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param int $depth
	 * @param int $level
	 * @return string
	 */
	public static function render($node, $path = [], $depth = 0, $level = 0)
	{
		return static::renderNode($node, $path, $depth, $level);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Protected methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @param Node $node
	 * @param Item[] $path
	 * @param int $depth
	 * @param int $level
	 * @return string
	 */
	protected static function renderNode($node, array $path, $depth, $level)
	{
		if (!$node) {
			return '';
		}

		$ret = static::renderMenuOpen($node, $path, $level);

		foreach (Site\ContentServer\NodeIterator::getIterator($node) as $childNode) {
			if (!static::isVisible($childNode)) {
				continue;
			}

			$active = (\in_array($childNode->item, $path));
			if (static::$expandAll || $active) {
				$ret .= static::renderMenuItemOpen($childNode, $path, $level);
				$ret .= static::renderMenuItem($childNode, $path, $level);
				if (
					Site\ContentServer\NodeIterator::getIterator($childNode)->count() > 0 &&
					(static::$expandAll || $active) &&
					($depth == 0 || $depth > $level + 1)
				) {
					$ret .= static::renderNode($childNode, $path, $depth, $level + 1);
				}
				$ret .= static::renderMenuItemClose($childNode, $path, $level);
			}
		}

		$ret .= static::renderMenuClose($node, $path, $level);
		return $ret;
	}

	/**
	 * @param Node $node
	 * @return bool
	 */
	protected static function isVisible($node)
	{
		return !(
			in_array($node->item->id, static::$ignoreIds) ||
			in_array($node->item->name, static::$ignoreNames) ||
			(!empty(static::$ignorePatterns) && preg_match('/(' . implode('|', static::$ignorePatterns) . ')/i', $node->item->name))
		);
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuOpen($node, array $path, $level)
	{
		return '<ul>' . PHP_EOL;
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuItemOpen($node, array $path, $level)
	{
		$active = (\in_array($node->item, $path));
		return ($active) ? '<li class="active">' . PHP_EOL : '<li>' . PHP_EOL;
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuItem($node, array $path, $level, $index = 0)
	{
		$classes = [];

		if (\in_array($node->item, $path)) {
			$classes[] = 'active';
		}

		return '<a
			class="' . implode(' ', $classes) . '"
			href="' . static::getMenuItemUri($node, $path, $level) . '"
			target="' . static::getMenuItemTarget($node, $path, $level) . '"
			title="' . htmlentities($node->item->name) . '"
			>' . htmlentities($node->item->name) . '</a>' . PHP_EOL;
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function getMenuItemUri($node, array $path, $level)
	{
		if ($node->item->mimeType == 'application/neos+external') {
			return $node->item->data->url;
		} else {
			return $node->item->URI;
		}
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function getMenuItemTarget($node, array $path, $level)
	{
		if ($node->item->mimeType == 'application/neos+external') {
			return $node->item->data->target;
		} else {
			return '_self';
		}
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuItemClose($node, array $path, $level)
	{
		return '</li>' . PHP_EOL;
	}

	/**
	 * @param Node $node
	 * @param Item[] $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuClose($node, array $path, $level)
	{
		return '</ul>' . PHP_EOL;
	}
}
