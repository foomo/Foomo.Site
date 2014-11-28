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

use Foomo\MVC;
use Foomo\Site;
use Foomo\Site\Mail\Frontend;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class View extends \Foomo\MVC\View
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $layout = 'default';
	/**
	 * @var Model
	 */
	public $model;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns the current layout name
	 *
	 * @return string
	 */
	public function getLayoutPartial()
	{
		return 'layout/' . $this->layout . '-' . $this->model->type;
	}

	/**
	 * @return string
	 */
	public function getContentPartial()
	{
		return 'content/' . $this->model->name . '-' . $this->model->type;
	}

	/**
	 * @inheritdoc
	 */
	public function partial($name, $variables = array())
	{
		$rootFrontendClass = ltrim(Site::getConfig()->getClass('frontend'), '\\');
		$rootFrontendPartial = 'mail/' . $name;
		$template = MVC::getViewPartialTemplate($rootFrontendClass, $rootFrontendPartial);
		if (strpos($template->file, 'partialNotFound') !== false) {
			return parent::partial($name, $variables);
		} else {
			return parent::partial($rootFrontendPartial, $variables, $rootFrontendClass);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function _($msgId, $count = null)
	{
		if (!$this->translation) {
			$this->translation = Frontend::getTranslation($this->model->name);
		}
		return $this->translation->_($msgId, $count);
	}
}
