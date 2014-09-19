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
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
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
	 * List of allowed regions and their languages
	 * Note: The first region & language is used as the default
	 *
	 * @var array[string][string]
	 */
	public $locales = [
		'ch' => ['de']
	];
	/**
	 * List of allowed groups
	 *
	 * @var string[]
	 */
	public $groups = [];
	/**
	 * List of allowed states
	 *
	 * @var string[]
	 */
	public $states = [];
	/**
	 * Map of nodeIds
	 *
	 * @var string[string]
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
	 * @var string[string]
	 */
	public $navigations = [
		'main'   => [
			'id'        => '',
			'mimeTypes' => [],
			'expand'    => true,
		],
		'meta'   => [
			'id'        => '',
			'mimeTypes' => [],
			'expand'    => true,
		],
		'footer' => [
			'id'        => '',
			'mimeTypes' => [],
			'expand'    => true,
		],
	];
	/**
	 * Map of email addresses
	 *
	 * @var string[string]
	 */
	public $emails = [
		'debug' => '',
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
	 * Map of classes
	 *
	 * @var string[string]
	 */
	public $classes = [
		"router"   => "\\Foomo\\Site\\Router",
		"session"  => "\\Foomo\\Site\\Session",
		"frontend" => "\\Foomo\\Site\\Frontend",
	];

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns first configured region
	 *
	 * @return string
	 */
	public function getDefaultRegion()
	{
		return array_keys($this->locales)[0];
	}

	/**
	 * Returns first configured region's language
	 *
	 * @return string
	 */
	public function getDefaultLanguage()
	{
		return $this->locales[$this->getDefaultRegion()][0];
	}

	/**
	 * Returns first configured locale
	 *
	 * @return string
	 */
	public function getDefaultLocale()
	{
		return $this->getDefaultRegion() . '_' . strtoupper($this->getDefaultLanguage());
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
			trigger_error("NavigationId '$id'' does not exist!", E_USER_ERROR);
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
			trigger_error("Email '$id'' does not exist!", E_USER_ERROR);
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
			trigger_error("Class '$id'' does not exist!", E_USER_ERROR);
		}
		return $this->classes[$id];
	}

	/**
	 * Validates value against the configuration
	 *
	 * @param string $region
	 * @return bool
	 */
	public function isValidRegion($region)
	{
		return (isset($this->locales[$region]));
	}

	/**
	 * Validates value against the configuration
	 *
	 * @param string $region
	 * @param string $language
	 * @return bool
	 */
	public function isValidLanguage($region, $language)
	{
		return in_array($this->locales[$region], $language);
	}

	/**
	 * Validates value against the configuration
	 *
	 * @param string $state
	 * @return bool
	 */
	public function isValidState($state)
	{
		return in_array($state, $this->states);
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
