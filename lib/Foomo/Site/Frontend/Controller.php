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

use Foomo\ContentServer\Neos;
use Foomo\Site;
use Foomo\ContentServer\Vo;
use SebastianBergmann\Exporter\Exception;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
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
	 * @throws \Exception
	 */
	public function actionDefault()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);

		// @todo: here happened some crazy stuff
		$this->runCMS(rtrim($url["path"], "/"));
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param $uri
	 * @throws \Exception
	 */
	private function runCMS($uri)
	{
		$config = Site\Module::getSiteConfig();

		$envGroups = [];
		$envDefaults = Vo\Requests\Content\Env\Defaults::create(
			Site\Session::getRegion(),
			Site\Session::getLanguage()
		);
		$env = Vo\Requests\Content\Env::create($envDefaults, $envGroups);

		$mimeTypes = [
			Neos\MimeType::MIME_SITE_CATEGORY,
			Neos\MimeType::MIME_SITE_EXTERNAL,
		];

		# get content
		$content = Site::getContentServerProxy()->getContent(
			Vo\Requests\Content::create($uri, $env)
				->addNode('webRoot', $config->webRootId, $mimeTypes, true)
		);

		# 404 handling before all the rest is done
		if ($content->status === Vo\Content\SiteContent::STATUS_NOT_FOUND) {
			throw new Site\Exception\Content(
				Site\Exception\Content::MESSAGE_404,
				Site\Exception\Content::CODE_404
			);
		}

		# set content
		$this->model->setSiteContent($content);


		//handle landingpage redirects
//		$this->handleLandingpage();

		//update the last visited gender
//		$this->updateLastVisitedGender();

		//seo stuff
//		SEO::processSiteContent($this->model->siteContent);

//		Timer::stop(__METHOD__);
	}
}