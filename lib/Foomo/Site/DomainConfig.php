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
	 * The first is used as the default, when initializing the session
	 *
	 * @var array[string][string]
	 */
	public $locales = [
		'ch' => ['de']
	];
	/**
	 * @var array[string]string
	 */
	public $contentIds = [
		'default' => '',
		'404'     => '',
		'403'     => '',
		'500'     => '',
	];
	/**
	 * @var array[string]string
	 */
	public $navigations = [
		'main'   => [
			'id'       => '',
			'mimeType' => [],
			'expand'   => true,
		],
		'meta'   => [
			'id'       => '',
			'mimeType' => [],
			'expand'   => true,
		],
		'footer' => [
			'id'       => '',
			'mimeType' => [],
			'expand'   => true,
		],
	];
	/**
	 * @var array[string]string
	 */
	public $emails = [
		'contact' => '',
	];
	/**
	 * @var \Foomo\Site\AdapterInterface[]
	 */
	public $adapters = [
		'Foomo\\Site\\Adapter\\Neos'
	];
	/**
	 * @var array[string]string
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
	 * @return string
	 */
	public function getDefaultRegion()
	{
		return array_keys($this->locales)[0];
	}

	/**
	 * @return string
	 */
	public function getDefaultLanguage()
	{
		return $this->locales[$this->getDefaultRegion()][0];
	}

	/**
	 * @return string
	 */
	public function getDefaultLocale()
	{
		return $this->getDefaultRegion() . '_' . strtoupper($this->getDefaultLanguage());
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function getContentId($id)
	{
		if (!isset($this->contentIds[$id])) {
			trigger_error("ContentId '$id'' does not exist!", E_USER_ERROR);
		}
		return $this->contentIds[$id];
	}

	/**
	 * @param $id
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
	 * @param $id
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
	 * @param $id
	 * @return string
	 */
	public function getClass($id)
	{
		if (!isset($this->classes[$id])) {
			trigger_error("Class '$id'' does not exist!", E_USER_ERROR);
		}
		return $this->classes[$id];
	}
}
