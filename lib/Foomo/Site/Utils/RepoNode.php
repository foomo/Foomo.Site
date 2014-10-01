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

namespace Foomo\Site\Utils;

use Foomo\Site;
use Foomo\ContentServer\Vo\Content;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class RepoNode
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param Content\RepoNode $repoNode
	 * @return null|string
	 */
	public static function getName(Content\RepoNode $repoNode)
	{
		return self::getLocalizedProperty($repoNode, 'names');
	}

	/**
	 * @param Content\RepoNode $repoNode
	 * @return boolean
	 */
	public static function getHidden(Content\RepoNode $repoNode)
	{
		return (self::getLocalizedProperty($repoNode, 'hidden') === true);
	}

	/**
	 * @param Content\RepoNode $repoNode
	 * @return null|string
	 */
	public static function getUri(Content\RepoNode $repoNode)
	{
		return self::getLocalizedProperty($repoNode, 'URIs');
	}

	/**
	 * @param Content\RepoNode $repoNode
	 * @return null|string
	 */
	public static function getLinkId(Content\RepoNode $repoNode)
	{
		return self::getLocalizedProperty($repoNode, 'linkIds');
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param Content\RepoNode $repoNode
	 * @param string           $property
	 * @return null|string
	 */
	private static function getLocalizedProperty(Content\RepoNode $repoNode, $property)
	{
		$session = Site::getSession();
		if (
			is_array($repoNode->$property) &&
			isset($repoNode->$property[$session::getRegion()]) &&
			isset($repoNode->$property[$session::getRegion()][$session::getLanguage()])
		) {
			return $repoNode->$property[$session::getRegion()][$session::getLanguage()];
		} else {
			return null;
		}
	}
}
