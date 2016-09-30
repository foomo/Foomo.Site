<?php

namespace Foomo\Site\ContentSecurityPolicy;

use Foomo\Config\AbstractConfig;

/**
 * @author bostjanm
 */
class DomainConfig extends AbstractConfig
{
	const NAME = 'Foomo.Site.Content.Security.Policy.config';

	public $enabled = false;

	/**
	 * do not block content, just report
	 *
	 * @var bool
	 */
	public $notifyOnly = true;

	public $reportUri = '/content-security-policy-violation';

	public $defaultSrc = [
		"'self'",
		"'unsafe-inline'",
		"'unsafe-eval'",
		"*"
	];

	public $scriptSrc = [
		"'self'",
		"'unsafe-inline'",
		"'unsafe-eval'",
		"*"
	];

	public $styleSrc = [
		"'self'",
		"'unsafe-inline'",
		"*",
	];

	public $fontSrc = [
		"'self'",
	];

	public $imgSrc = [
		"'self'",
		"'unsafe-inline'",
		"*"
	];



}