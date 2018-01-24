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

namespace Foomo\Site\TagManager;

use Foomo\Config\AbstractConfig;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class DomainConfig extends AbstractConfig
{
	// ------------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Site.tagManager';

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * Google Analytics id
	 *
	 * @var string
	 */
	public $analyticsId = 'UA-XXXXXXX-XX';

	/**
	 * Google Optimize 360 id
	 *
	 * @var string
	 */
	public $optimizeId = 'GTM-XXXX';

	/**
	 * time Google Optimize will wait in milliseconds before showing the page
	 * 
	 * @var int
	 */
	public $optimizeWaitTime = 2000;

	/**
	 * GTM Container IDs
	 *
	 * @var string[]
	 */
	public $containers = [
		'id' => 'GTM-XXXX',
		'auth' => '',
		'preview' => ''
	];
}
