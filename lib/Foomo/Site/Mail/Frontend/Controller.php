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

namespace Foomo\Site\Mail\Frontend;

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
	// ~ Public action methods
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function actionDefault()
	{
		trigger_error('Unhandled action', E_USER_ERROR);
	}

	/**
	 * @param string $name
	 * @param mixed $vars
	 */
	public function actionHtml($name, $vars)
	{
		$this->model->type = Model::TYPE_HTML;
		$this->model->name = $name;
		$this->model->vars = $vars;
	}

	/**
	 * @param string $name
	 * @param mixed $vars
	 */
	public function actionPlain($name, $vars)
	{
		$this->model->type = Model::TYPE_PLAIN;
		$this->model->name = $name;
		$this->model->vars = $vars;
	}
}
