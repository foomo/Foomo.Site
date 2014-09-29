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

namespace Foomo\Site\Utils;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @todo: why can't I run these tests when destroying?
	 */
	public function setUp()
	{
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public test methods
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function testGetLocale()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '',
				'uri'      => ''
			],
			Uri::getLocale('')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '',
				'uri'      => '/'
			],
			Uri::getLocale('/')
		);
	}

	/**
	 *
	 */
	public function testGetLocaleWithDefaultRegion()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de',
				'uri'      => ''
			],
			Uri::getLocale('/de')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de',
				'uri'      => '/'
			],
			Uri::getLocale('/de/')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de',
				'uri'      => '/some/path'
			],
			Uri::getLocale('/de/some/path')
		);
	}

	/**
	 *
	 */
	public function testGetLocaleWithRegion()
	{
		$this->assertEquals(
			[
				'language' => 'en',
				'region'   => 'eu',
				'path'     => '/eu',
				'uri'      => ''
			],
			Uri::getLocale('/eu')
		);
		$this->assertEquals(
			[
				'language' => 'en',
				'region'   => 'eu',
				'path'     => '/eu',
				'uri'      => '/'
			],
			Uri::getLocale('/eu/')
		);
		$this->assertEquals(
			[
				'language' => 'en',
				'region'   => 'eu',
				'path'     => '/eu',
				'uri'      => '/some/path'
			],
			Uri::getLocale('/eu/some/path')
		);
	}

	/**
	 *
	 */
	public function testGetLocaleWithDefaultLocale()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de-de',
				'uri'      => ''
			],
			Uri::getLocale('/de-de')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de-de',
				'uri'      => '/'
			],
			Uri::getLocale('/de-de/')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de-de',
				'uri'      => '/some/path'
			],
			Uri::getLocale('/de-de/some/path')
		);
	}

	/**
	 *
	 */
	public function testGetLocaleWithLocale()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'eu',
				'path'     => '/eu-de',
				'uri'      => ''
			],
			Uri::getLocale('/eu-de')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'eu',
				'path'     => '/eu-de',
				'uri'      => '/'
			],
			Uri::getLocale('/eu-de/')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'eu',
				'path'     => '/eu-de',
				'uri'      => '/some/path'
			],
			Uri::getLocale('/eu-de/some/path')
		);
	}
}
