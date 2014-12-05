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

namespace Foomo\Site;

use Foomo\Config\Smtp;
use Foomo\MVC;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Mail
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $name
	 * @param string $to
	 * @param mixed  $vars
	 * @param array  $headers
	 * @param Smtp   $config
	 * @return bool
	 */
	public static function send($name, $to, $vars = null, $headers = [], $config = null)
	{
		$mailer = new \Foomo\Mailer();

		# get config
		if (is_null($config)) {
			$config = Module::getSMTPConfig();
		}
		$mailer->setSmtpConfig($config);

		if (!isset($headers["From"])) {
			$headers["From"] = $config->username;
		}

		# render templates
		$app = new \Foomo\Site\Mail\Frontend();
		$html = MVC::runAction($app, 'html', compact('name', 'vars'), null, false);
		$plain = MVC::runAction($app, 'plain', compact('name', 'vars'));

		# send mail
		$subject = Mail\Frontend::getTranslation($name)->_('MAIL_SUBJECT');
		$result = $mailer->sendMail($to, $subject, $plain, $html->output(), $headers);

		# log on errors
		if (!$result) {
			trigger_error($mailer->getLastError(), E_USER_WARNING);
		}

		return $result;
	}
}
