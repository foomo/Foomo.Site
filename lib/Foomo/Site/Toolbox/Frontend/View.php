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

namespace Foomo\Site\Toolbox\Frontend;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class View extends \Foomo\MVC\View
{
	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param array $variables
	 * @return string|void
	 */
	public function render($variables = [])
	{
		if ($this->partial == '') {
			$this->setup();
		}
		return parent::render($variables);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected methods
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	protected function setup()
	{
		\Foomo\HTMLDocument::getInstance()->addStylesheets(array(
			'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
		));
	}
}
