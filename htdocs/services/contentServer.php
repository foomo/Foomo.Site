<?php

\Foomo\Services\RPC::create(new \Foomo\Site\Service\ContentServer())
	->clientNamespace('Foomo.Services.ContentServer')
	->serializeWith(new \Foomo\Services\RPC\Serializer\JSON())
	->run()
;
