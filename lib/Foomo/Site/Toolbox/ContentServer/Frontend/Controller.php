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

namespace Foomo\Site\Toolbox\ContentServer\Frontend;

use Foomo\MVC;
use Foomo\Site\Module;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Controller
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var Model
	 */
	public $model;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $region
	 * @param string $language
	 */
	public function actionDefault($region = null, $language = null)
	{
		$this->model->setLocale($region, $language);
	}

	/**
	 *
	 * @param string $region
	 * @param string $language
	 */
	public function actionDump($region = null, $language = null)
	{
		$this->model->setLocale($region, $language);
	}

	/**
	 * Update
	 *
	 * @param string $region
	 * @param string $language
	 */
	public function actionUpdate($region = null, $language = null)
	{
		$result = Module::getSiteContentServerProxyConfig()->getProxy()->update();
		if ($result->success) {
			MVC::redirect('default', [$region, $language]);
		} else {
			MVC::abort();
			var_dump($result);
			exit;
		}
	}
}
