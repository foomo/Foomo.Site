<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Site;

use Foomo\HTMLDocument;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Analytics
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var self
	 */
	protected static $inst;

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var array
	 */
	protected $cmds = [];

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return static
	 */
	public static function getInstance()
	{
		if (is_null(static::$inst)) {
			static::$inst = new static();
		}
		return static::$inst;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param HTMLDocument $HTMLDoc
	 * @return self
	 */
	public function addToHTMLDoc($HTMLDoc = null)
	{
		if (is_null($HTMLDoc)) {
			$HTMLDoc = HTMLDocument::getInstance();
		}
		$HTMLDoc->addJavascriptToBody($this->getScript());
		return $this;
	}

	/**
	 * @param string $trackingId
	 * @param string $type
	 * @return self
	 */
	public function addCreate($trackingId=null, $type='auto')
	{
		if (is_null($trackingId)) {
			$trackingId = Module::getAnalyticsConfig()->trackingId;
		}
		$this->add(['create', $trackingId, $type]);
		return $this;
	}

	/**
	 * @param string $type
	 * @return self
	 */
	public function addSend($type='pageview')
	{
		$this->add(['send', $type]);
		return $this;
	}

	/**
	 * @param string $cmd
	 * @param mixed $value
	 * @return self
	 */
	public function addSet($cmd, $value)
	{
		$this->add(['set', $cmd, $value]);
		return $this;
	}

	/**
	 * @param mixed $value
	 * @return self
	 */
	public function addRequire($value)
	{
		$this->add(['require', $value]);
		return $this;
	}

	/**
	 * @param string[] $cmd
	 * @return self
	 */
	public function add(array $cmd)
	{
		foreach ($cmd as $key => $option) {
			switch (true) {
				case is_bool($option):
					$cmd[$key] = ($option) ? 'true' : 'false';
					break;
				case is_string($option):
					$cmd[$key] = '"' . $option . '"';
					break;
				case is_array($option):
				case is_object($option):
					$cmd[$key] = json_encode($option);
					break;
				default:
					break;
			}
		}
		$this->cmds[] = $cmd;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getScript()
	{
		if (!empty(Module::getAnalyticsConfig()->trackingId)) {
			$script = "
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		";

			# add commands
			foreach ($this->cmds as $cmd) {
				$script .= "ga(" . implode(",", $cmd) . ");" . PHP_EOL;
			}
		} else {
			$script = '';
		}
		return $script;
	}
}
