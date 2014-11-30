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

use Foomo\HTMLDocument;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class View extends \Foomo\MVC\View
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * Flag if the actual frontend view started to render
	 *
	 * @var bool
	 */
	protected static $rendering = false;
	/**
	 * @var array
	 */
	protected static $bundlesToAdd = [];
	/**
	 * @var string
	 */
	protected static $javascripts = [];

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
	 * @return Site\Env
	 */
	public function getEnv()
	{
		return Site::getEnv();
	}

	/**
	 * Returns the model's content rendering
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->model->getContentRendering();
	}

	/**
	 * Returns the current layout name
	 *
	 * @return string
	 */
	public function getLayoutPartial()
	{
		return 'layout/' . ((null != $layout = $this->model->getContentData('layout')) ? $layout : 'default');
	}

	/**
	 * Note: requires the mime type to look like: 'application/neos+page'
	 *
	 * @return string
	 */
	public function getContentPartial()
	{
		$type = explode('+', explode('/', $this->model->getContent()->mimeType)[1]);
		return $type[0] . DIRECTORY_SEPARATOR . $type[1];
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

	/**
	 * @inheritdoc
	 */
	public function render($variables = [])
	{
		# check if we are a partial or not
		if ($this->partial == '') {
			static::$rendering = true;
			$this->renderHead(
				HTMLDocument::getInstance(),
				(!\Foomo\Config::isProductionMode())
			);
		}
		# render
		$ret = parent::render($variables);

		if ($this->partial == '') {
			# add a view bundle containing the registerd bundles
			if (!empty(static::$bundlesToAdd)) {
				Site\Bundles::addMergedBundleToDoc('view', static::$bundlesToAdd);
			}
			# add javascripts
			if (!empty(static::$javascripts)) {
				HTMLDocument::getInstance()->addJavascriptToBody(trim('
					$(document).ready(function() {
						' . implode(PHP_EOL, array_reverse(static::$javascripts)) . '
					});
				'));
			}
		}

		return $ret;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Setup your HTML Document with all head data
	 * so we can send it off the browser for optimized page speed
	 *
	 * @param HTMLDocument $HTMLDoc
	 */
	protected function renderHead(HTMLDocument $HTMLDoc)
	{
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Add javascript executed on jquery's on load event
	 *
	 * @param $script
	 */
	public static function addJavascript($script)
	{
		static::$javascripts[] = str_replace('\t', '', $script);
	}

	/**
	 * Add a bundle to the `view` bundle
	 *
	 * @param string   $name
	 * @param array    $data
	 * @param string[] $services
	 * @param string   $module
	 */
	public static function addBundle($name, array $data = [], array $services = [], $module = null)
	{
		if (is_null($module)) {
			$module = Site\Module::getRootModule();
		}

		// don't add them to the doc yet since the frontend hasn't started rendering yet!
		if (static::$rendering) {
			Site\Bundles::addBundleToDoc($name, $module, $data, $services);
		} else {
			static::$bundlesToAdd[] = [$name, $module, $data, $services];
		}
	}
}
