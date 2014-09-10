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

namespace Foomo\Site\ContentServer;

use Foomo\ContentServer\Vo\Content\RepoNode;
use Foomo\Config;
use Foomo\MVC;
use Foomo\SimpleData\VoMapper;
use Schild\Clustering;
use Schild\Module;
use Schild\Router;
use Schild\Services\Shop\Catalogue;
use Schild\Vo\Category;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Export
{
	const CREATION_LINK_PREFIX = 'creation://';

	public static function crawl()
	{
		try {
			$node = self::getRepositoryFromCMS();
			self::wireShop($node, null);
			$nodeDirectory = self::buildNodeDictionary($node);
			//			$node = self::replaceInternalUris($node, $nodeDirectory);
			$node = self::removePlaceholderNodes($node);

			//@todo: remove cached cluster landingpage uris
			//@todo: build cache cluster landingpage uris
			$node = self::buildLandingpageClusterDictionary($node, $nodeDirectory);

			return $node;
		} catch (\Exception $e) {
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			return false;
		}
	}



	/**
	 * get the content repository from a content management system
	 *
	 * @return RepoNode
	 */
	private static function getRepositoryFromCMS()
	{
		$repoEndpoint = Module::getSiteConfig()->contentRepo;
		//$repoEndpoint = Module::getBaseDir() . DIRECTORY_SEPARATOR . 'export.json';
		$nodes = json_decode(file_get_contents($repoEndpoint));
		return VoMapper::map($nodes, new RepoNode());
	}

	/**
	 * build a dictionary (hash map) with all nodes
	 * @param RepoNode $node
	 *
	 * @return array
	 */
	private static function buildNodeDictionary(RepoNode $node)
	{
		$directory = array();
		$callback = function(RepoNode $node, $parentNode) use (&$directory) {
			$directory[$node->id] = $node;
		};
		self::iterate($node, null, $callback);
		return $directory;
	}

	/**
	 * build a landingpage cluster dictionary recursively and add it to the root node
	 *
	 * @param RepoNode $node
	 * @param RepoNode[] $nodeDirectory
	 *
	 * @return RepoNode
	 */
	private static function buildLandingpageClusterDictionary(RepoNode $node, &$nodeDirectory)
	{
		$clusterDictionary = array();
		$callback = function(RepoNode $node, $parentNode) use (&$nodeDirectory, &$clusterDictionary) {
			//collect path IDs
			if(!is_null($parentNode)) {
				$node->tempPath = $parentNode->tempPath;
				$node->tempPath[] = $node->id;
			}

			//check cluster
			if($node->handler == Handler::HANDLER_LANDINGPAGE_FULLWIDTH
				&& isset($node->data)
				&& is_array($node->data)
				&& array_key_exists('triggerCluster', $node->data)
			) {
				$data = $node->data;
				if(!empty($data['triggerCluster'])) {
					$cluster = $data['triggerCluster'];

					//guess gender
					if(in_array(Module::getSiteConfig()->femaleId, $node->tempPath)) {
						$gender = \Schild\Vo\ProductData\Constants::GENDER_FEMALE;
					} else if(in_array(Module::getSiteConfig()->maleId, $node->tempPath)) {
						$gender = \Schild\Vo\ProductData\Constants::GENDER_MALE;
					} else {
						$gender = \Schild\Vo\ProductData\Constants::GENDER_UNISEX;
					}

					$id = Clustering::getClusterId($cluster, $gender);
					if(!array_key_exists($id, $clusterDictionary)) {
						$clusterNode = new RepoNode();
						$clusterNode->id = $id;
						$clusterNode->linkId = $node->id;
						$clusterNode->handler = 'cluster/dictionary';
						$clusterNode->mimeType = 'application/cluster+dictionary';
						$clusterNode->data = array('triggerCluster' => $cluster);
						foreach($node->URIs as $region => $languages) {
							$clusterNode->addRegion($region);
							foreach($languages as $language => $uri) {
								$clusterNode->addName($region, $language, ucfirst($cluster) . '('.$gender.')');
								$clusterNode->hide($region, $language, true);
							}
						}
						$clusterDictionary[$id] = $clusterNode;
					} else {
						trigger_error('landingpage for cluster "' . $cluster . '" and gender "' . $gender . '" already exists in cluster dictionary and will be ignored for node "' . $node->id . '"', E_USER_WARNING);
					}
				}
			}
		};

		//prepare path
		$node->tempPath = array($node->id);

		//iterate
		self::iterate($node, null, $callback);

		//add cluster dictionary
		$clusterRootNode = new RepoNode();
		$clusterRootNode->id = '__clusterDictionary';
		$clusterRootNode->linkId = $node->id;
		$clusterRootNode->handler = 'cluster/dictionary';
		$clusterRootNode->mimeType = 'application/cluster+dictionary';
		foreach($node->URIs as $region => $languages) {
			$clusterRootNode->addRegion($region);
			foreach($languages as $language => $uri) {
				$clusterRootNode->addName($region, $language, 'Landingpage Cluster Dictionary');
				$clusterRootNode->hide($region, $language, true);
			}
		}
		foreach($clusterDictionary as $clusterNode) {
			$clusterRootNode->addNode($clusterNode);
		}
		$node->addNode($clusterRootNode);
		return $node;
	}

	/**
	 * remove all placeholder nodes recursively
	 *
	 * @param RepoNode $node
	 *
	 * @return RepoNode
	 */
	private static function removePlaceholderNodes(RepoNode $node)
	{
		$callback = function(RepoNode $node, $parentNode) {
			if($node->mimeType == 'application/shop+placeholder') {
				/** @var $parentNode RepoNode */
				//$parentNode->removeNode($node);
				$node->mimeType = \Schild\ContentServer\MimeType::MIME_SITE_CATEGORY;
				//				$node->handler = \Schild\ContentServer\Handler::HANDLER_SITE_CONTENT;
			}
		};
		self::iterate($node, null, $callback);
		return $node;
	}



	//-----------------------------------------------------------------------------
	// ~ ITERATOR HELPER
	//-----------------------------------------------------------------------------

	/**
	 * @param RepoNode $node
	 * @param RepoNode $parentNode
	 * @param $callback
	 */
	private static function iterate($node, $parentNode, $callback)
	{
		call_user_func_array($callback, array($node, $parentNode));
		foreach($node as $childNode) {
			if(is_object($childNode) && $childNode instanceof RepoNode) {
				self::iterate($childNode, $node, $callback);
			} else {
				trigger_error('do not know how to handle this, cause this is not a RepoNode! ' . var_export($childNode), E_USER_WARNING);
			}
		}
	}



	//-----------------------------------------------------------------------------
	// ~ SHOP INTEGRATION
	//-----------------------------------------------------------------------------

	private static function wireShop(RepoNode $node, $parentNode)
	{
		if(!is_null($parentNode) && $node->mimeType == 'application/shop+placeholder'){
			// validate data
			$data = (object) $node->data;
			if(!is_null($data) && isset($data->ShopID)) {
				$clusteredCategories = \Schild\Catalogue\Export::getClusteredCategories($data->ShopID, Module::getSiteConfig()->allowedRegionLanguages, $parentNode->URIs);
				foreach($clusteredCategories as $cluster => $categoryNode) {
					$node->linkId = $categoryNode->linkId;
					foreach($categoryNode as $childNode) {
						$node->addNode($childNode);
					}
				}

				unset($data->teaserMap);
				$node->data = $data;
			} else {
				trigger_error(
					'no valid ShopID found on node ' . $node->id .
					' with names:' . var_export($node->names,true) .
					' and data :' . var_export($data,true))
				;
			}
		}
		foreach($node as $childNode) {
			if(is_object($childNode) && $childNode instanceof RepoNode) {
				self::wireShop($childNode, $node);
			} else {
				trigger_error('do not know how to handle this, cause this is not a RepoNode! ' . var_export($childNode), E_USER_WARNING);
			}
		}
	}



	//-----------------------------------------------------------------------------
	// ~ Private static helper
	//-----------------------------------------------------------------------------

	private static function addShopCategoryToNode(Category $category, RepoNode &$repoNode, $region, $language, $data, $handler = Handler::HANDLER_SHOP_CATEGORY, $mime = MimeType::MIME_SHOP_CATEGORY)
	{
		$categoryNode = self::getNodeFromRepoById($category->id, $repoNode);
		$categoryNode = self::mapCategoryToRepoNode($category, $categoryNode, $region, $language, $repoNode, $data, $handler, $mime);
		if(is_array($category->categories)) {
			foreach($category->categories as $childCategory) {
				self::addShopCategoryToNode($childCategory, $categoryNode, $region, $language, $data, $handler, $mime);
			}
		}
		$repoNode->addNode($categoryNode);
	}

	private static function mapCategoryToRepoNode(Category $category, RepoNode $categoryNode = null, $region, $language, RepoNode &$repoNode, $data, $handler, $mime)
	{
		if(is_null($categoryNode)) {
			$categoryNode = new RepoNode();
			$categoryNode->id = $category->id;
			$categoryNode->handler = $handler;
			$categoryNode->mimeType = $mime;

			//to avoid unpredictable side effects we need to clone the object
			$tempData = clone $data;
			if(!empty($category->bannerId)) {
				$tempData->bannerId = $category->bannerId;
			}
			if(isset($tempData->teaserMap) && !empty($tempData->teaserMap->{$category->id})) {
				$tempData->teaserId = $tempData->teaserMap->{$category->id};
			}
			unset($tempData->teaserMap);
			$categoryNode->data = $tempData;
		}
		$categoryNode->addRegion($region);
		$categoryNode->addName($region, $language, $category->name);
		$parentUri = self::getParentUri($repoNode, $region, $language);
		if($parentUri) {
			$categoryNode->addURI($region, $language, $parentUri . '/'.rawurlencode(str_replace(' ', '-', strtolower($category->name))));
		} else {
			$categoryNode->hide($region, $language);
		}
		return $categoryNode;
	}

	/**
	 * @param string $id
	 * @param RepoNode $node
	 *
	 * @return RepoNode|null
	 */
	private static function getNodeFromRepoById($id, RepoNode $node)
	{
		foreach($node as $childNode) {
			if($childNode->id == $id) {
				return $childNode;
			}
		}
		return null;
	}

	private static function getParentUri(RepoNode $repoNode, $region, $language)
	{
		if(array_key_exists($region, (array)$repoNode->URIs) && array_key_exists($language, (array)$repoNode->URIs[$region])) {
			return $repoNode->URIs[$region][$language];
		} else {
			trigger_error('uri rendering error: no valid parent uri found for shop category ' . $repoNode->id . ' with names ' . var_export($repoNode->names, true), E_USER_WARNING);
			return false;
		}
	}
}
