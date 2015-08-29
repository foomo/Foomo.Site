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

namespace Foomo\Site;

use Foomo\MVC\AbstractApp;
use Foomo\Translation;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Frontend extends AbstractApp
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var Frontend\Model
	 */
	public $model;
	/**
	 * @var Frontend\Controller
	 */
	public $controller;
	/**
	 * @var Frontend\View
	 */
	public $view;

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return \Foomo\Translation
	 */
	public static function getTranslation()
	{
		static $translation;
		if (is_null($translation)) {
			$translation = Translation::getModuleTranslation(
				Module::getRootModule(),
				Module::getSiteConfig()->getClass('frontend')
			);
		}
		return $translation;
	}
}
