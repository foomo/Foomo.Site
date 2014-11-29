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

use Foomo\MVC;
use Foomo\Router\MVC\URLHandler;
use Foomo\Site;
use Foomo\ContentServer\Vo;
use Foomo\Timer;

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
	 * Simple default action handler
	 *
	 * @throws Site\Exception\HTTPException
	 */
	public function actionDefault()
	{
		Timer::addMarker('running default action');
		$this->loadSiteContent($_SERVER['REQUEST_URI']);

		# render the content
		# URLHandler::exposeClassId(true);
		URLHandler::strictParameterHandling(true);
		$this->model->renderContent();
		Timer::addMarker('rendered content on model');

		# validate path
		if (!Site\URLHandler::getMarkedAsResolved()) {
			throw new Site\Exception\HTTPException(404, 'Content not found!');
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $url
	 * @throws Site\Exception\HTTPException
	 */
	protected function loadSiteContent($url)
	{
		$url = parse_url($url);
		$config = Site::getConfig();

		# retrieve the content
		$content = Site\ContentServer\Client::getContent($url['path'], array_reverse($config->getDimensionIds()));
		Timer::addMarker('retrieved content from content server');

		# set content
		$this->model->setContent($content);

		if ($this->model->getContent()->URI == $url['path']) {
			Site\URLHandler::markAsResolved();
		}

		# validate status
		if ($content->status != Vo\Content\SiteContent::STATUS_OK) {
			throw new Site\Exception\HTTPException($content->status, 'Content server client result not OK!');
		}

	}
}
