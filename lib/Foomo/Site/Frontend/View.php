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

namespace Foomo\Site\Frontend;

use Foomo\Site;

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
	 * @var Model
	 */
	protected $model;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns rendered content by the client mapped by the content handler id
	 *
	 * @return string
	 */
	public function renderContent()
	{
		$session = Site::getSession();

		# retrieve adapter for current content
		$adapter = Site::getAdapter($this->model->getContentHandlerId());

		# get content
		return $adapter::getContent(
			$this->model->getContent()->item->id,
			$session::getRegion(),
			$session::getLanguage(),
			$session::getGroups(),
			$session::getState(),
			$this->model->getContent()->URI
		);
	}

	/**
	 * Returns a partial with the given partial data i.e.
	 *
	 * $partial = 'my/partial';
	 * $partial = ['my/partial', ['foo' => 'bar']];
	 * $partial = ['my/partial', ['foo' => 'bar'], 'ClassName'];
	 *
	 * @param string|array $partial
	 * @return string
	 */
	public function subPartial($partial)
	{
		$partial = (array) $partial;
		return $this->partial(
			$partial[0],
			(isset($partial[1])) ? $partial[1] : [],
			(isset($partial[2])) ? $partial[2] : ''
		);
	}
}
