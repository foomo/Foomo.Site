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

use Foomo\ContentServer\Vo;
use Foomo\Site;
use Foomo\Site\Adapter\Neos;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Model
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var Vo\Content\SiteContent
	 */
	protected $content;
	/**
	 * @var string HTML
	 */
	protected $contentRendering;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param bool $full
	 * @return Vo\Content\Item[]
	 */
	public function getContentPath($full = false)
	{
		if ($full) {
			return array_merge(
				[$this->getContent()->item],
				$this->getContent()->path
			);
		} else {
			return $this->getContent()->path;
		}
	}

	/**
	 * @return Vo\Content\SiteContent
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param Vo\Content\SiteContent $content
	 * @return $this
	 */
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * @param string $property
	 * @param string $region
	 * @param string $language
	 * @return null|mixed
	 */
	public function getContentData($property, $region = null, $language = null)
	{
		$session = Site::getSession();

		if (is_null($region)) {
			$region = $session::getRegion();
		}

		if (is_null($language)) {
			$language = $session::getLanguage();
		}

		if (
			isset($this->getContent()->data->$region) &&
			isset($this->getContent()->data->$region->$language) &&
			isset($this->getContent()->data->$region->$language->$property)
		) {
			return $this->getContent()->data->$region->$language->$property;
		} else {
			return null;
		}
	}

	/**
	 * @param string $id
	 * @return Vo\Content\Node
	 */
	public function getContentNode($id)
	{
		return $this->getContent()->nodes[$id];
	}

	/**
	 * @return string
	 * @throws Site\Exception\HTTPException
	 */
	public function getContentRendering()
	{
		if (is_null($this->contentRendering)) {
			$this->renderContent();
		}
		return $this->contentRendering;
	}

	/**
	 * Note: The content needs to be rendered "before" the
	 * view because otherwise we can't throw exceptions...
	 *
	 * @throws Site\Exception\HTTPException
	 */
	public function renderContent()
	{
		$session = Site::getSession();

		# retrieve adapter for current content
		$handlerId = $this->getContentHandlerId();

		$adapter = Site::getAdapter($handlerId);

		if ($adapter === false) {
			throw new Site\Exception\HTTPException(500, "Unknown content handler: $handlerId");
		}

		# get content
		// @todo: what about groups & state!?
		$rendering = $adapter::getContent(
			$this->getContent()->item->id,
			$session::getRegion(),
			$session::getLanguage(),
			$this->getContent()->URI
		);

		# validate rendering
		if (empty($rendering)) {
			throw new Site\Exception\HTTPException(500, "Content couldn't be rendered");
		}

		$this->contentRendering = $rendering;
	}

	/**
	 * Returns the current content's handler id
	 *
	 * @return string
	 */
	public function getContentHandlerId()
	{
		return explode('/', $this->getContent()->handler)[0];
	}
}
