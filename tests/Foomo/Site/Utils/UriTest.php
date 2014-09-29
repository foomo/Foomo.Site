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
	public function testParseLocale()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '',
				'uri'      => ''
			],
			Uri::parseLocale('')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '',
				'uri'      => '/'
			],
			Uri::parseLocale('/')
		);
	}

	/**
	 *
	 */
	public function testParseLocaleWithDefaultRegion()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de',
				'uri'      => ''
			],
			Uri::parseLocale('/de')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de',
				'uri'      => '/'
			],
			Uri::parseLocale('/de/')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de',
				'uri'      => '/some/path'
			],
			Uri::parseLocale('/de/some/path')
		);
	}

	/**
	 *
	 */
	public function testParseLocaleWithRegion()
	{
		$this->assertEquals(
			[
				'language' => 'en',
				'region'   => 'eu',
				'path'     => '/eu',
				'uri'      => ''
			],
			Uri::parseLocale('/eu')
		);
		$this->assertEquals(
			[
				'language' => 'en',
				'region'   => 'eu',
				'path'     => '/eu',
				'uri'      => '/'
			],
			Uri::parseLocale('/eu/')
		);
		$this->assertEquals(
			[
				'language' => 'en',
				'region'   => 'eu',
				'path'     => '/eu',
				'uri'      => '/some/path'
			],
			Uri::parseLocale('/eu/some/path')
		);
	}

	/**
	 *
	 */
	public function testParseLocaleWithDefaultLocale()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de-de',
				'uri'      => ''
			],
			Uri::parseLocale('/de-de')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de-de',
				'uri'      => '/'
			],
			Uri::parseLocale('/de-de/')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'de',
				'path'     => '/de-de',
				'uri'      => '/some/path'
			],
			Uri::parseLocale('/de-de/some/path')
		);
	}

	/**
	 *
	 */
	public function testParseLocaleWithLocale()
	{
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'eu',
				'path'     => '/eu-de',
				'uri'      => ''
			],
			Uri::parseLocale('/eu-de')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'eu',
				'path'     => '/eu-de',
				'uri'      => '/'
			],
			Uri::parseLocale('/eu-de/')
		);
		$this->assertEquals(
			[
				'language' => 'de',
				'region'   => 'eu',
				'path'     => '/eu-de',
				'uri'      => '/some/path'
			],
			Uri::parseLocale('/eu-de/some/path')
		);
	}

	/**
	 *
	 */
	public function testShortenLocaleWithDefaultLocale()
	{
		$this->assertEquals('/',  Uri::shortenLocale('/de-de/'));
		$this->assertEquals('/some/path',  Uri::shortenLocale('/de-de/some/path'));
	}

	/**
	 *
	 */
	public function testShortenLocaleWithDefaultLanguage()
	{
		$this->assertEquals('/eu/',  Uri::shortenLocale('/eu-en/'));
		$this->assertEquals('/eu/some/path',  Uri::shortenLocale('/eu-en/some/path'));
	}

	/**
	 *
	 */
	public function testShortenLocale()
	{
		$this->assertEquals('/eu-de/',  Uri::shortenLocale('/eu-de/'));
		$this->assertEquals('/eu-de/some/path',  Uri::shortenLocale('/eu-de/some/path'));
	}
}
