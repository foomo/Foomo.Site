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

use Foomo\ContentServer\Vo\Content\Node;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Main extends Base
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const NAME = 'main';
	const EXPAND = true;

	/**
	 * @var string
	 */
	public static $currentNodeId;

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * recursion node renderer
	 *
	 * @param Node   $node
	 * @param array  $path
	 * @param string $html
	 * @param int    $level
	 * @return string
	 */
	protected static function renderNode(Node $node, array $path, &$html = '', $level = 0)
	{
		if ($level < 2) {
			if($level > 0) {
				//handle active and inactive state and add a fancy custom style to the sale main menu entry
				if(self::isInPath($node, $path)) {
					$class = 'active';
					self::$currentNodeId = $node->item->id;
				} else {
					$class = 'inactive';
				}

				if(strtolower(self::getName($node)) == 'sale') {
					$class .= ' sale';
				}
				$class .= ' level'.$level;

				if($node->item->id == \Schild\Module::getSiteConfig()->femaleId) {
					$gender = \Schild\Vo\ProductData\Constants::GENDER_FEMALE;
				} else if($node->item->id == \Schild\Module::getSiteConfig()->maleId) {
					$gender = \Schild\Vo\ProductData\Constants::GENDER_MALE;
				} else {
					$gender = \Schild\Vo\ProductData\Constants::GENDER_UNISEX;
				}

				$target = (self::isExternalLink(self::getLink($node))) ? '_blank' : '_top';
				$html .= '<li class="'.$class.' '.$gender.'" data-gender="'.$gender.'" data-node-id="'.htmlspecialchars($node->item->id).'">';
				$html .= '<a href="' . self::getLink($node) . '" target="'.$target.'">' .  self::getName($node) . '</a>';
			}

			if(count($node) > 0) {

				if(is_array($node->index)) {
					foreach($node->index as $id) {
						$nodes = $node->nodes;
						if(is_object($nodes)) {
							$nodes = (array)$node->nodes;
						}
						self::renderNode($nodes[$id], $path, $html, $level + 1);
					}
				}

			}

			if($level > 0) {
				$html .= '</li>';
			}
		}

		return $html;
	}
}