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

namespace Foomo\Site;

use Foomo\Modules\Manager;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Module extends \Foomo\Modules\ModuleBase
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const NAME    = 'Foomo.Site';
	const VERSION = '1.0.0';

	//---------------------------------------------------------------------------------------------
	// ~ Overriden static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * Your module needs to be set up, before being used - this is the place to do it
	 */
	public static function initializeModule()
	{
	}

	/**
	 * Get a plain text description of what this module does
	 *
	 * @return string
	 */
	public static function getDescription()
	{
		return 'The Foomo.Site module';
	}

	/**
	 * get all the module resources
	 *
	 * @return \Foomo\Modules\Resource[]
	 */
	public static function getResources()
	{
		# default resources
		$resources = [
			\Foomo\Modules\Resource\Module::getResource('Foomo', '0.4.*'),
			\Foomo\Modules\Resource\Module::getResource('Foomo.Media', '0.3.*'),
			\Foomo\Modules\Resource\Module::getResource('Foomo.ContentServer', '0.1.*'),
		];

		# resources when enabled
		if (Manager::isEnabled(self::NAME)) {

			# resources for root module
			if (Manager::isEnabled(self::getRootModule())) {
				/* @var $siteConfigResource \Foomo\Modules\Resource\Config */
				$resources = array_merge(
					[
						# site module configs
						\Foomo\Modules\Resource\Config::getResource(self::getRootModule(), 'Foomo.ContentServer.config'),
						$siteConfigResource = \Foomo\Modules\Resource\Config::getResource(self::getRootModule(), 'Foomo.Site.config'),
					],
					$resources
				);

				# check for adapter resources
				if ($siteConfigResource->resourceValid()) {
					$siteConfig = self::getSiteConfig();
					foreach ($siteConfig->adapters as $adapter) {
						$resources = array_merge($adapter::getModuleResources(), $resources);
					}
				}
			}
		}

		return $resources;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return \Foomo\Site\DomainConfig
	 */
	public static function getSiteConfig()
	{
		 return self::getRootModuleConfig('Foomo.Site.config');
	}

	/**
	 * @return \Foomo\ContentServer\DomainConfig
	 */
	public static function getSiteContentServerProxyConfig()
	{
		return self::getRootModuleConfig(\Foomo\ContentServer\DomainConfig::NAME);
	}

	/**
	 * Returns the implementing site module name
	 *
	 * @return string
	 */
	public static function getRootModule()
	{
		return \Foomo\Modules\Manager::getDocumentRootModule();
	}

	/**
	 * Returns the implementing site module class name
	 *
	 * @return string|\Foomo\Modules\ModuleBase
	 */
	public static function getRootModuleClass()
	{
		return \Foomo\Modules\Manager::getModuleClassByName(static::getRootModule());
	}

	/**
	 * Return a config for the implementing site module
	 *
	 * @param string $name
	 * @param string $domain
	 * @return \Foomo\Config\AbstractConfig
	 */
	public static function getRootModuleConfig($name, $domain = '')
	{
		return \Foomo\Config::getConf(self::getRootModule(), $name, $domain);
	}
}
