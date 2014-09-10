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
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Config extends AbstractConfig
{
	// ------------------------------------------------------------------------------------------------
	// ~ Contants
	// --------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Site.config';

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * the first is used as the default, when initializing the session
	 *
	 * @var array[string][string]
	 */
	public $allowedLocales = [
		'de' => ['de']
	];
	/**
	 * @var string
	 */
	public $contentRepo = '/contentserver/export';
	/**
	 * @var string
	 */
	public $webRootId;
	/**
	 * @var array[string]string
	 */
	public $contentIds = [
		'default' => '',
		'404' => '',
		'403' => '',
		'500' => '',
	];
	/**
	 * @var array[string]string
	 */
	public $navigationIds = [
		'main' => '',
		'meta' => '',
		'footer' => '',
	];
	/**
	 * @var array[string]string
	 */
	public $emails = [
		'contact' => '',
	];
	/**
	 * @var array[string]string
	 */
	public $classes = [
		"router" => "\\Foomo\\Site\\Router",
		"session" => "\\Foomo\\Site\\Session",
		"frontend" => "\\Foomo\\Site\\Frontend",
	];

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

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
	public function getNavigationId($id)
	{
		if (!isset($this->navigationIds[$id])) {
			trigger_error("NavigationId '$id'' does not exist!", E_USER_ERROR);
		}
		return $this->navigationIds[$id];
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