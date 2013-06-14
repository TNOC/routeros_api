<?php
namespace RouterOS\Tests;

require_once './vendor/autoload.php';

use \RouterOS;

use \Monolog\Logger;

use \PHPUnit\PHPUnit_Framework_TestCase;

class LogTest extends \PHPUnit_Framework_TestCase {

    public function testLog() {
        $api = new RouterOS\Core();

        $logger = new Logger('name');
        $logger->log(Logger::INFO, "test");

        $api->setLogger($logger);

        $this->assertInstanceOf(
            'Monolog\Logger', 
            $api->getLogger()
        );
    }

}