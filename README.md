# Foomo Site

> Foomo.Site is an abstract base module to create a site using the Foomo Content Server to obtain content from any source.

## Domain Config

```yaml
---
#-------------------------------------------------------------------------------
# Default derived from Foomo\Site\DomainConfig
#
# Foomo site is based on the concept of dimension.
# 
#-------------------------------------------------------------------------------
---
#-------------------------------------------------------------------------------
# Canonical domain name
# 
# type string
#-------------------------------------------------------------------------------
domain: http://www.mydomain.com
#-------------------------------------------------------------------------------
# List of allowed/configured dimension i.e.
# 
# type array string[string][string]
#-------------------------------------------------------------------------------
dimensions:
  en_US:
    region: us
    language: en
#-------------------------------------------------------------------------------
# List of allowed groups
# 
# type string[]
#-------------------------------------------------------------------------------
groups: []
#-------------------------------------------------------------------------------
# Map of nodeIds which will be used within the site
# 
# type array
#-------------------------------------------------------------------------------
nodeIds:
  default: ""
  404: ""
  403: ""
  500: ""
#-------------------------------------------------------------------------------
# Map of navigation request
# 
# type array string[string][mixed]
#-------------------------------------------------------------------------------
navigations:
  main:
    id: ""
    mimeTypes: []
    expand: true
  meta:
    id: ""
    mimeTypes: []
    expand: true
  footer:
    id: ""
    mimeTypes: []
    expand: true
#-------------------------------------------------------------------------------
# Map of email addresses
# 
# type array
#-------------------------------------------------------------------------------
emails:
  debug: ""
  contact: ""
#-------------------------------------------------------------------------------
# List of enabled adapters
# 
# type \Foomo\Site\AdapterInterface[]
#-------------------------------------------------------------------------------
adapters:
- Foomo\Site\Adapter\Neos
#-------------------------------------------------------------------------------
# Map of class names to use
# 
# type array
#-------------------------------------------------------------------------------
classes:
  env: \Foomo\Site\Env
  router: \Foomo\Site\Router
  frontend: \Foomo\Site\Frontend
  contentServer: \Foomo\Site\ContentServer
#-------------------------------------------------------------------------------
# List of enabled sub router classes
# 
# type \Foomo\Site\SubRouter[]
#-------------------------------------------------------------------------------
subRouters: []
...
```

## Backend

### Sub Router
---

Create a new Sub Router:

```
<?php

namespace Example\SubRouter;

/**
 * @author  franklin
 */
class Test extends \Foomo\Site\SubRouter
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	public static $prefix = '/test';

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->addRoutes(
			[
				'/foo/:bar' => 'placeholder',
				'/*'        => 'error',
			]
		);
	}	

	// --------------------------------------------------------------------------------------------
	// ~ Public route methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $bar
	 */
	public function placeholder($bar)
	{
	    echo $bar;
	}	
}
```

Enable it by configuring the `Foomo.Site.config` config:

```
...
subRouters:
  - \Example\SubRouter\Test
...
```

### Content Server
---

If you want to take control about what will in the content server for your site it's a good idea to provide you own export.

Enable the Content Server Sub Route and point the `Foomo.ContentServer.config` to your own domain:

```
...
repo: 'http://example.com/contentserver/export'
...
```

Create your custom Content Server class and overwrite the methods as needed:

```
<?php

namespace Example;

/**
 * 
 */
class ContentServer extends \Foomo\Site\ContentServer
{
    ....
}
```


### Content Server Sub Router
---

Add the `\Foomo\Site\SubRouter\ContentServer` to your site config to enable it.

#### `/export(/:format)`

Will call `export` on all configured adapters and merge them.



### Content Server Adapters
---

Adapters provide the functionality to retrieve the content from a vendor system.

It's job is to

* Provide a Domain Config
* Retrieve Content
* Add Sub Routes

#### Custom Adapter

To write a custom adapter create the adapter class an implement the `\Foomo\Site\AdapterInterface` or extend `\Foomo\Site\Adapter\AbstractBase`

```
<?php

namespace Example\Adapter;

use Foomo\ContentServer\Vo;
use Foomo\Site;

/**
 * 
 */
class Foo extends AbstractBase
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Return the name of the adapter i.e. `neos`
	 *
	 * @return string
	 */
	public static function getName() 
	{
	    return 'foo';
	}

	/**
	 * Returns list of resources required for the root module
	 *
	 * @return \Foomo\Modules\Resource[]
	 */
	static function getModuleResources() {...}

	/**
	 * Returns the adapters domain config
	 *
	 * @return \Foomo\Site\Adapter\DomainConfig
	 */
	static function getAdapterConfig() {...}

	/**
	 * Returns list of sub routes
	 *
	 * @return \Foomo\Site\Adapter\Neos\SubRouter[]
	 */
	static function getSubRoutes() {...}

	/**
	 * Returns a node's content from the remote content server
	 *
	 * @param SiteContent $siteContent
	 * @return mixed
	 */
	static function getContent($siteContent) {...}
}

```


### Mail
---

Add a SMTP Domain Config as a dependency and configure:

```
...
\Foomo\Modules\Resource\Config::getResource(\Foomo\Site\Module::getRootModule(), 'Foomo.smtp'),
...
```

Add localization files:

```
Example/local/Example/Mail/Test/en.yml
```

Add view files:

```
Example/views/Example/Frontend/partials/mail
    /content
        /test-html.tpl
        /test-plain.tpl
    /layout
        /default-html.tpl
        /default-plain.tpl
```

Example layout:

```
<?php
/* @var $model \Foomo\Site\Mail\Frontend\Model */
/* @var $view \Foomo\Site\Mail\Frontend\View */
/* @var $content string */
echo $content;
```

Example content:

```
<?php
/* @var $model \Foomo\Site\Mail\Frontend\Model */
/* @var $view \Foomo\Site\Mail\Frontend\View */
// You can switch the layout here
// $view->layout = 'myLayout';
?>
This is <?= $foo; >
```


Example usage:

```
\Foomo\Site\Mail::send(
    'test',
	\Foomo\Site::getConfig()->getEmail('test'),
	['foo' => 'bar']
);
```

#### Analytics
---

Add the Domain Config as a dependency and configure:

```
...
\Foomo\Modules\Resource\Config::getResource(\Foomo\Site\Module::getRootModule(), 'Foomo.Site.analytics'),
...
```

Example usage:

```
\Foomo\Site\Analytics::getInstance()
    ->addCreate()
	->addSet('anonymizeIp', true)
	->addSend()
	->addToHTMLDoc();
```

#### Sitemap
---

To output a sitemap you need to pass a dimension into the Sitemap class:

```
$dimension = 'YOUR_DIMENSION';
$repoNodes = \Foomo\Site\Module::getSiteContentServerProxyConfig()->getProxy()->getRepo();
\Foomo\Site\Sitemap::output($repoNodes->$dimension);
```

To change the actual rending you can extend the class and overwrite the corresponding methods.

```
<?php

namespace Example;

/**
 * 
 */
class Sitemap extends \Foomo\Site\Sitemap
{
    ...	
}
```

If dealing with several dimensions you can output a sitemap index like this:

```
$uris = [];
foreach (\Foomo\Site::getConfig()->dimensions as $dimension => $value) {
    $uris[] = '/sitemap_' . $value['SOME_ID'] . '.xml';
}
\Foomo\Site\Sitemap::outputIndex($uris);
```

*Note: Make sure you handle these routes inside your router.*


#### Bundles
---

In a default implementation there a two places where Bundles could be added. Either while rendering the content from returned by the adapter client or in the actual frontend rendering. Since most of the time you would actually expect the content being rendered within the frontend rendering we need to delay the actual adding of the Bundles. To do so, call the `\Foomo\Site\Frontend\View::addBundle();` method.

```
\Foomo\Site\Frontend\View::addBundle(
    'test',
    ['language' => 'en']
    ['Example.Services.Test'],
    'Example'
);
```

This works with JavaScript to:

```
\Foomo\Site\Frontend\View::addJavascript('
    Example.Apps.Test.App.run({el: $("#myelement")});
');
```

*Note: this wraps it automaticall inside a JQuery onLoad method*

This call will add try to add:

* JS Bundle (`Example/js/test.js`)
* SASS Bundle (`Example/sass/test.scss`)
* TypeScript Lib Bundle (`Example/typescript/libs/test/bundle.ts.tpl`)
* TypeScript App Bundle (`Example/typescript/apps/test/bundle.ts.tpl`)
* JS Service Bundle (containing the given services)

Inside your `View` class you can add the bundle directly:

```
/**
 * @inheritdoc
 */
protected function renderHead(HTMLDocument $HTMLDoc)
{
    ...
    \Foomo\Site\Bundles::addBundleToDoc(
        'site',
        \Example\Module::NAME,
        ['language' => 'en'],
        ['Example.Services.Site']
    );
    $this->addJavascript(
        'Example.Apps.Site.App.init({ foo:'bar' });'
    );
    ...
}
```

## Neos


## Frontend

### Social Links
