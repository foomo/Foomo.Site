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

namespace Foomo\Site\Adapter;

use Foomo\ContentServer\Vo;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
abstract class AbstractBase implements Site\AdapterInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function getModuleResources()
	{
		return [
			\Foomo\Modules\Resource\Config::getResource(
				Site\Module::getRootModule(),
				DomainConfig::NAME,
				static::getName()
			),
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function getAdapterConfig($domain=null)
	{
		if(is_null($domain)) {
			$domain = static::getName();
		}
		return Site\Module::getRootModuleConfig(DomainConfig::NAME, $domain);
	}

}
