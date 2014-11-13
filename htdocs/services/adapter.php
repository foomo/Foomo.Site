<?php

function sendHeaderStatus($status)
{
	header('HTTP/1.0 ' . $status);
	header('Status: ' . $status);
	echo $status;
	exit;
}

if (class_exists('Foomo\\Site') && !\Foomo\Modules\Manager::makeIsRunning()) {
	if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['explain'])) {
		$doc = new \Foomo\HTMLDocument();
		$doc->addBody('<h1>Foomo Site REST Service</h1><p>No documentation available.</p>');
		echo $doc->output();
	} else {
		$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
		$restUri = str_replace($_SERVER['SCRIPT_NAME'] . '/', '', $_SERVER['REQUEST_URI']);

		$uriParameters = explode('/', $restUri);
		$actionName = array_shift($uriParameters);
		$method = $requestMethod . ucfirst($actionName);

		if (method_exists('Foomo\\Site\\Service\\Adapter', $method)) {
			//Target our class
			$reflector = new ReflectionClass('Foomo\\Site\\Service\\Adapter');

			//Get the parameters of a method
			$parameters = $reflector->getMethod($method)->getParameters();
			$requiredParameters = [];
			foreach ($parameters as $parameter) {
				if (!$parameter->isOptional()) {
					$requiredParameters[] = $parameter;
				}
			}
			if (count($requiredParameters) == count($uriParameters)) {
				header("content-type:text/json;charset:utf-8");
				echo json_encode(call_user_func_array('Foomo\\Site\\Service\\Adapter::' . $method, $uriParameters));
			} else {
				sendHeaderStatus('400 Bad Request');
			}
		} else {
			sendHeaderStatus('405 Method Not Allowed');
		}
	}
} else {
	sendHeaderStatus('503 Service Temporarily Unavailable');
}
