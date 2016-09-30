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

namespace Foomo\Site\ContentSecurityPolicy;

class Report
{
	/**
	 * @param array $data
	 */
	public static function handleViolationReport($data)
	{
		trigger_error(json_encode($data, JSON_PRETTY_PRINT));
		self::addToReport($data['csp-report']);
	}

	/**
	 * @return string
	 */
	public static function getReportFile()
	{
		return \Foomo\Site\Module::getVarDir() . DIRECTORY_SEPARATOR . 'content-security-policy.serialized';
	}

	/**
	 * @return array|mixed
	 */
	public static function loadReport()
	{
		$file = self::getReportFile();
		if (file_exists($file)) {
			return unserialize(file_get_contents($file));
		} else {
			return [];
		}
	}

	/**
	 * @param mixed $item
	 * @return int
	 */
	public static function addToReport($item)
	{
		$report = self::loadReport();
		if (array_key_exists($item['blocked-uri'], $report)) {
			$report[$item['blocked-uri']]['lastNotified'] = time();
		} else {
			$report[$item['blocked-uri']] = array_merge (
				$item,
				[
					'firstNotified' => time(),
					'lastNotified' => time(),
					'adminNotified' => 0
				]
			);
		}
		return file_put_contents(self::getReportFile(), serialize($report));
	}

	public static function notifyAdmin() {
		$mailData = [];
		$report = self::loadReport();
		foreach ($report as $blockedUri => $item) {
			if ($item['adminNotified'] == 0) {
				$mailData[$item['blocked-uri']] = $item;
			}
		}

	}


}