<?php

\Foomo\Services\RPC::create(new \Foomo\Site\Service\Adapter())
	->clientNamespace('Foomo.Services.Adapter')
	->serializeWith(new \Foomo\Services\RPC\Serializer\JSON())
	->run()
;
