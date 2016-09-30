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

	public $reportUri = 'https://schild-local-test.bestbytes.net/content-security-policy-violation';

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