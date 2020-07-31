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

use Foomo\Config\AbstractConfig;

/**
 * Foomo site is based on the concept of dimension.
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class DomainConfig extends AbstractConfig
{
	// ------------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Site.config';

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * Canonical domain name
	 *
	 * @var string
	 */
	public $domain = 'http://www.mydomain.com';
	/**
	 * List of allowed/configured dimension i.e.
	 *
	 * @var array string[string][string]
	 */
	public $dimensions = [
		'en_US' => ['region' => 'us', 'language' => 'en'],
	];
	/**
	 * List of allowed groups
	 *
	 * @var string[]
	 */
	public $groups = [];
	/**
	 * Map of nodeIds which will be used within the site
	 *
	 * @var array
	 */
	public $nodeIds = [
		'default' => '',
		'404'     => '',
		'403'     => '',
		'500'     => '',
	];
	/**
	 * Map of navigation request
	 *
	 * @var array string[string][mixed]
	 */
	public $navigations = [
		'main'   => [
			'id'         => '',
			'mimeTypes'  => [],
			'dataFields' => [],
			'expand'     => true,
		],
		'meta'   => [
			'id'         => '',
			'mimeTypes'  => [],
			'dataFields' => [],
			'expand'     => true,
		],
		'footer' => [
			'id'         => '',
			'mimeTypes'  => [],
			'dataFields' => [],
			'expand'     => true,
		],
	];
	/**
	 * Map of email addresses
	 *
	 * @var array
	 */
	public $emails = [
		'debug'   => '',
		'contact' => '',
	];
	/**
	 * List of enabled adapters
	 *
	 * @var \Foomo\Site\AdapterInterface[]
	 */
	public $adapters = [
		'Foomo\\Site\\Adapter\\Neos'
	];
	/**
	 * Map of class names to use
	 *
	 * @var array
	 */
	public $classes = [
		"env"           => "\\Foomo\\Site\\Env",
		"router"        => "\\Foomo\\Site\\Router",
		"frontend"      => "\\Foomo\\Site\\Frontend",
		"contentServer" => "\\Foomo\\Site\\ContentServer",
	];
	/**
	 * List of enabled sub router classes
	 *
	 * @var \Foomo\Site\SubRouter[]
	 */
	public $subRouters = [];

	/**
	 * if true it will set a flag (file) that indicates a cronjob should update the contentserver
	 *
	 * @var bool
	 */
	public $updateContentServerAfterContentAdapterUpdate = false;
	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns configured dimension keys
	 *
	 * @return string[]
	 */
	public function getDimensionIds()
	{
		return array_keys($this->dimensions);
	}

	/**
	 * Returns property value for a given dimension
	 *
	 * @param string $dimensionId
	 * @param string $property
	 * @return string|null
	 */
	public function getDimensionValue($dimensionId, $property)
	{
		if (!$this->isValidDimension($dimensionId)) {
			trigger_error("Dimension '$dimensionId' does not exist!", E_USER_ERROR);
		}
		return (isset($this->getDimension($dimensionId)[$property])) ? $this->getDimension($dimensionId)[$property] : null;
	}

	/**
	 * Validates value against the configuration
	 *
	 * @param string $dimensionId
	 * @return bool
	 */
	public function isValidDimension($dimensionId)
	{
		return array_key_exists($dimensionId, $this->dimensions);
	}

	/**
	 * @param string $dimensionId
	 * @return array
	 */
	public function getDimension($dimensionId)
	{
		if (!$this->isValidDimension($dimensionId)) {
			trigger_error("Dimension '$dimensionId' does not exist!", E_USER_ERROR);
		}
		return $this->dimensions[$dimensionId];
	}

	/**
	 * Returns all dimensions containing the given value
	 *
	 * @param string $property
	 * @param string $value
	 * @return array
	 */
	public function findDimensionsWithValue($property, $value)
	{
		return $this->findDimensionsWithValues([$property => $value]);
	}

	/**
	 * Return whether a dimension exists with the given values
	 *
	 * @param array $values
	 * @return array
	 */
	public function findDimensionsWithValues(array $values)
	{
		$ret = [];
		foreach ($this->dimensions as $dimensionId => $dimension) {
			$hasValues = true;
			foreach ($values as $property => $value) {
				if (!isset($dimension[$property]) || $dimension[$property] != $value) {
					$hasValues = false;
					break;
				}
			}
			if ($hasValues) {
				$ret[$dimensionId] = $dimension;
			}
		}
		return $ret;
	}

	/**
	 * Returns all dimension values for the given property
	 *
	 * @param string $property
	 * @return array
	 */
	public function getAllDimensionValues($property)
	{
		$ret = [];
		foreach ($this->dimensions as $id => $dimension) {
			$ret[$id] = (array_key_exists($property, $dimension)) ? $dimension[$property] : null;
		}
		return $ret;
	}

	/**
	 * Return all dimension properties
	 *
	 * @return string[]
	 */
	public function getAllDimensionProperties()
	{
		$ret = [];
		foreach ($this->dimensions as $dimension) {
			$ret = array_unique(array_merge($ret, array_keys($dimension)));
		}
		return $ret;
	}

	/**
	 * Return a configured nodeId
	 *
	 * @param string $id
	 * @return string
	 */
	public function getNodeId($id)
	{
		if (!isset($this->nodeIds[$id])) {
			trigger_error("NodeId '$id'' does not exist!", E_USER_ERROR);
		}
		return $this->nodeIds[$id];
	}

	/**
	 * @param string $id
	 * @return string
	 */
	public function getNavigation($id)
	{
		if (!isset($this->navigations[$id])) {
			trigger_error("NavigationId '$id' does not exist!", E_USER_ERROR);
		}
		return $this->navigations[$id];
	}

	/**
	 * @param string $id
	 * @return string
	 */
	public function getEmail($id)
	{
		if (!isset($this->emails[$id])) {
			trigger_error("Email '$id' does not exist!", E_USER_ERROR);
		}
		return $this->emails[$id];
	}

	/**
	 * @param string $id
	 * @return string
	 */
	public function getClass($id)
	{
		if (!isset($this->classes[$id])) {
			trigger_error("Class '$id' does not exist!", E_USER_ERROR);
		}
		return $this->classes[$id];
	}

	/**
	 * Validates value against the configuration
	 *
	 * @param string $group
	 * @return bool
	 */
	public function isValidGroup($group)
	{
		return in_array($group, $this->groups);
	}

	/**
	 * Validates value against the configuration
	 *
	 * @param string[] $groups
	 * @return bool
	 */
	public function areValidGroups($groups)
	{
		return (count(array_diff($groups, $this->groups)) == 0);
	}
}
