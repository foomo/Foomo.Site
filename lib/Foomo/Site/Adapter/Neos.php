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

use Foomo\Config;
use Foomo\ContentServer\Vo;
use Foomo\Modules\Resource;
use Foomo\Site;
use Foomo\MVC;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Neos extends AbstractBase implements Site\ContentServerInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function getName()
	{
		return 'neos';
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubRoutes()
	{
		$routes = [];

		foreach (static::getAdapterConfig()->subRouters as $subRouter) {
			$routes[$subRouter::$prefix] = $subRouter::getSubRoute();
		}
		return $routes;
	}

	/**
	 * @inheritdoc
	 */
	public static function getModuleResources()
	{
		return [
			Resource\Config::getResource(
				Site\Module::getRootModule(),
				Site\Adapter\Neos\DomainConfig::NAME,
				static::getName()
			),
		];
	}

	/**
	 * @inheritdoc
	 * @return \Foomo\Site\Adapter\Neos\DomainConfig
	 */
	public static function getAdapterConfig($domain=null)
	{
		if(is_null($domain)) {
			$domain = static::getName();
		}
		return Site\Module::getRootModuleConfig(Site\Adapter\Neos\DomainConfig::NAME, $domain);
	}

	/**
	 * @inheritdoc
	 * @throws Site\Exception\HTTPException
	 */
	public static function getContent($siteContent)
	{
		if ($siteContent->mimeType == 'application/neos+external') {
			if (!isset($siteContent->data) && !isset($siteContent->data->url)) {
				throw new Site\Exception\HTTPException(500, 'Could not resolve external link');
			}
			MVC::abort();
			$location = htmlentities($siteContent->data->url);
			header("Location: $location", true, 301);
			exit;
		} else {
			$domain = Site::getAdapterDomainName($siteContent);
			$adapterConfig = static::getAdapterConfig($domain);
			/* @var $client ClientInterface */
			$client = $adapterConfig->getClass('client');
			$data = ["workspace" => $adapterConfig->workspace];
			return $client::get($siteContent->dimension, $siteContent->item->id, $siteContent->URI, $domain, $data);
		}
	}

	/**
	 * @inheritdoc
	 */
	public static function export()
	{
		$config = static::getAdapterConfig();
		$repositoryURL = $config->getPathUrl('repository');
		$url = $repositoryURL . '?workspace=' . $config->workspace;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (Config::isProductionMode()));
		return json_decode(curl_exec($ch));
	}
}
