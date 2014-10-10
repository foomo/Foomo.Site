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

use Foomo\Config\AbstractConfig;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class DomainConfig extends AbstractConfig
{
	
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Site.adapter';

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * Content server base url
	 *
	 * @var string
	 */
	public $server = 'http://www.contentserver.com';
	/**
	 * Map of paths
	 *
	 * @var string[]
	 */
	public $paths = [
		'image'      => '/contentserver/image',
		'asset'      => '/contentserver/asset',
		'content'    => '/contentserver/export',
		'repository' => '/contentserver/export',
	];
	/**
	 * List of enabled sub routers
	 *
	 * @var \Foomo\Site\SubRouter[]
	 */
	public $subRouters = [
		'Foomo\\Site\\Adapter\\MyAdapter\\SubRouter'
	];
	/**
	 * List of classes
	 *
	 * @var string[]
	 */
	public $classes = [
		'client' => 'Foomo\\Site\\Adapter\\MyAdapter\\Client'
	];

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns a configured path
	 *
	 * @param string $id
	 * @return string
	 */
	public function getPath($id)
	{
		if (!isset($this->paths[$id])) {
			trigger_error("Unknown path '$id'", E_USER_ERROR);
		}
		return $this->paths[$id];
	}

	/**
	 * Returns a configured path url
	 *
	 * @param string $id
	 * @return string
	 */
	public function getPathUrl($id)
	{
		return $this->server . $this->getPath($id);
	}

	/**
	 * Returns a configured class
	 *
	 * @param string $id
	 * @return string
	 */
	public function getClass($id)
	{
		if (!isset($this->classes[$id])) {
			trigger_error("Unknown class '$id'", E_USER_ERROR);
		}
		return $this->classes[$id];
	}
}
