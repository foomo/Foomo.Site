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
	 * Returns a site content data property
	 *
	 * @param string $property
	 * @return null|mixed
	 */
	public function getContentData($property)
	{
		if (isset($this->getContent()->data->$property)) {
			return $this->getContent()->data->$property;
		} else {
			return null;
		}
	}

	/**
	 * Returns a site content node
	 *
	 * @param string $id
	 * @return Vo\Content\Node
	 */
	public function getContentNode($id)
	{
		if (is_array($this->getContent()->nodes)) {
			return $this->getContent()->nodes[$id];
		} else {
			return $this->getContent()->nodes->{$id};
		}
	}

	/**
	 * Returns the site content rendering
	 *
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
		$env = Site::getEnv();

		$adapter = $this->getContentAdapter();

		# get content
		$rendering = $adapter::getContent(
			$this->getContent()->dimension,
			$this->getContent()->item->id,
			$this->getContent()->URI
		);

		# validate rendering
		if (empty($rendering)) {
			throw new Site\Exception\HTTPException(500, "Content couldn't be rendered");
		}

		$this->contentRendering = $rendering;
	}

	/**
	 * Returns the current content's adapter from it's mimeType
	 * Note: requires the mime type to look like: 'application/neos+page'
	 *
	 * @return bool|Site\AdapterInterface
	 * @throws Site\Exception\HTTPException
	 */
	public function getContentAdapter()
	{
		$adapterId = explode('+', explode('/', $this->getContent()->mimeType)[1])[0];

		$adapter = Site::getAdapter($adapterId);

		if ($adapter === false) {
			throw new Site\Exception\HTTPException(500, "Unknown content adapter for mimeType: $adapterId");
		}

		return $adapter;
	}
}
