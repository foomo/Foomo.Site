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
use Foomo\ContentServer\Vo;

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
	 * @todo check if applications can resolve the url
	 *
	 * @throws Site\Exception\HTTPException
	 */
	public function actionDefault()
	{
		$config = Site::getConfig();
		$url = parse_url($_SERVER['REQUEST_URI']);

		# retrieve the content
		$content = Site\ContentServer\Client::getContent($url['path'], array_reverse($config->getDimensionIds()));

		# set content
		$this->model->setContent($content);

		# validate status
		if ($content->status != Vo\Content\SiteContent::STATUS_OK) {
			throw new Site\Exception\HTTPException($content->status, 'Content server client result not OK!');
		}

		# validate path
		if ($this->model->getContent()->URI != $url['path']) {
			throw new Site\Exception\HTTPException(404, 'Content not found!');
		}

		# render the content
		$this->model->renderContent();
	}
}
