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

namespace Foomo\Site\Toolbox\ContentServer\Frontend;

use Foomo\ContentServer\Vo\Content\RepoNode;
use Foomo\Cache\Persistence\Expr;
use Foomo\Cache\Manager;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Model
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $region;
	/**
	 * @var string
	 */
	public $language;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $region
	 * @param string $language
	 * @return $this
	 */
	public function setLocale($region, $language)
	{
		$config = Site::getConfig();

		if (is_null($region) || !$config->isValidRegion($region)) {
			$region = $config->getDefaultRegion();
		}

		if (is_null($language) || !$config->isValidLanguage($region, $language)) {
			$language = $config->getDefaultLanguage($region);
		}

		$this->region = $region;
		$this->language = $language;

		return $this;
	}

	/**
	 * @return RepoNode
	 */
	public function getRepoNode()
	{
		return \Foomo\Site\Module::getSiteContentServerProxyConfig()->getProxy()->getRepo();
	}
}
