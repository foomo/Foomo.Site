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

		# validate path
		if (!URLHandler::getMarkedAsResolved()) {
			throw new Site\Exception\HTTPException(404, 'Content not found!');
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $url
	 * @param string[] $dimensions
	 * @param string[] $groups
	 * @throws Site\Exception\HTTPException
	 * @throws Site\Exception\ContentServerException
	 */
	protected function loadSiteContent($url, $dimensions = null, $groups = null)
	{
		\Foomo\Timer::start($topic = __METHOD__);
		$url = parse_url($url);

		if(empty($dimensions)) {
			$config = Site::getConfig();
			$dimensions = $config->getDimensionIds();
		}

		if (empty($groups)) {
			$env = Site::getEnv();
			$groups = $env::getGroups();
		}

		# retrieve the content
		$content = Site\ContentServer\Client::getContent($url['path'], array_reverse($dimensions), $groups);
		Timer::addMarker('retrieved content from content server');

		# validate content
		if(empty($content) || empty($content->status) || empty($content->dimension) || empty($content->item)) {
			throw new Site\Exception\ContentServerException(503, Site\Exception\ContentServerException::MSG_CONTENT_SERVER_UNAVAILABLE);
		}

		# set content
		$this->model->setContent($content);

		if ($this->model->getContent()->URI == $url['path']) {
			Site\Frontend\URLHandler::markAsResolved();
		}

		# validate status
		if ($content->status == Vo\Content\SiteContent::STATUS_FORBIDDEN) {
			throw new Site\Exception\HTTPException($content->status, 'Content server client forbids access!');
		} elseif ($content->status != Vo\Content\SiteContent::STATUS_OK) {
			throw new Site\Exception\HTTPException($content->status, 'Content server client result not OK!');
		}
		\Foomo\Timer::stop($topic);
	}
}
