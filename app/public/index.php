<?php
require $_ENV['APP_ROOT_DIR']. '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SocketHandler;
use Monolog\Formatter\JsonFormatter;

function createLogger() : Monolog\Logger
{
	$log = new Monolog\Logger('name');
	$logHandler = new SocketHandler('tcp://logstash:5000');
	$logHandler->setPersistent(true);

	$logFormatter = new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES);
	$logHandler->setFormatter($logFormatter);
	$log->pushHandler($logHandler);

	
	return $log;
}


function sayHelloBws2018(Monolog\Logger $logger) : void
{
	$logger->addInfo('BWS2018 Saying hello');
	echo "Hello BWS2018";

	$logger->addWarning('BWS2018 warning', [ 'backtrace' => debug_backtrace() ]);
}

$logger = createLogger();

sayHelloBws2018($logger);
