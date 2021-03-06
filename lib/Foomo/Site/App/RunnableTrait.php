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

namespace Foomo\Site\App;

use Foomo\MVC;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
trait RunnableTrait
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param array  $data
	 * @param string $baseUrl
	 * @return string
	 */
	public static function run($data, $baseUrl = null)
	{
		$app = new static;
		$app->setAppData($data);
		return MVC::run($app, $baseUrl, true, true, 'Foomo\\MVC\\URLHandler', false);
	}

	public static function runAndReturnInstance($data, $baseUrl = null)
	{
		$app = new static;
		$app->setAppData($data);
		return [
			"instance" => $app,
			"html" => MVC::run($app, $baseUrl, true, true, 'Foomo\\MVC\\URLHandler', false)
		];
	}

	/**
	 * @param array  $data
	 * @param string $action
	 * @param array  $parameters
	 * @param string $baseUrl
	 * @return string
	 */
	public static function runAction($data, $action, $parameters = [], $baseUrl = null)
	{
		$app = new static;
		$app->setAppData($data);
		return MVC::runAction($app, $action, $parameters, $baseUrl);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param $data
	 */
	protected function setAppData($data)
	{
		if (method_exists($this->model,'setData')) {
			$this->model->setData($data);
		} else {
			$this->model->data = $data;
		}
	}
}
