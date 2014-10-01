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
	/**
	 * @var string[]
	 */
	protected static $ignoreMimeTypes = [];

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param Node   $node
	 * @param Item[] $path
	 * @param int    $depth
	 * @return string
	 */
	public static function render($node, $path = [], $depth = 0)
	{
		return static::renderNode($node, $path, $depth, 0);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Protected methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @param Node   $node
	 * @param Item[] $path
	 * @param int    $depth
	 * @param int    $level
	 * @return string
	 */
	protected static function renderNode($node, array $path, $depth, $level)
	{
		$ret = static::renderMenuOpen($node, $path, $level);

		foreach ($node as $childNode) {

			if (!static::isVisible($childNode)) {
				continue;
			}

			$active = (\in_array($node->item, $path));

			$ret .= static::renderMenuItemOpen($childNode, $path, $level);
			$ret .= static::renderMenuItem($childNode, $path, $level);
			if (
				($depth == 0 || $depth > $level + 1) &&
				count($childNode) > 0 &&
				($depth == 0 || (static::$expandAll || $active))
			) {
				$ret .= static::renderNode($childNode, $path, $depth, $level + 1);
			}
			$ret .= static::renderMenuItemClose($childNode, $path, $level);
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
		//if (!$childRepoNode->isOnline()) continue;
		return (
			in_array($node->item->id, static::$ignoreIds) ||
			//in_array($node->mimeType, static::$ignoreMimeTypes) ||
			in_array($node->item->name, static::$ignoreNames) ||
			preg_match('/(' . implode('|', static::$ignorePatterns) . ')/i', $node->item->name) > 0
		);
	}

	/**
	 * @param Node    $node
	 * @param Item[]  $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuOpen($node, array $path, $level)
	{
		return ($level == 0) ? '<ul class="menu">' . PHP_EOL : '<ul class="submenu">' . PHP_EOL;
	}

	/**
	 * @param Node    $node
	 * @param Item[]  $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuItemOpen($node, array $path, $level)
	{
		$active = (\in_array($node->item, $path));
		return ($active) ? '<li class="item active">' . PHP_EOL : '<li class="item">' . PHP_EOL;
	}

	/**
	 * @param Node    $node
	 * @param Item[]  $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuItem($node, array $path, $level)
	{
		$classes = [];

		if (\in_array($node->item, $path)) {
			$classes[] = 'active';
		}

		return '<a
			class="' . implode(' ', $classes) . '"
			href="' . $node->item->URI . '"
			title="' . htmlentities($node->item->name) . '"
			>' .htmlentities($node->item->name) . '</a>' . PHP_EOL;
	}

	/**
	 * @param Node    $node
	 * @param Item[]  $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuItemClose($node, array $path, $level)
	{
		return '</li>' . PHP_EOL;
	}

	/**
	 * @param Node    $node
	 * @param Item[]  $path
	 * @param integer $level
	 * @return string
	 */
	protected static function renderMenuClose($node, array $path, $level)
	{
		return '</ul>' . PHP_EOL;
	}
}
