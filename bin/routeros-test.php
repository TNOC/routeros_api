<?php

function prompt_silent($prompt = "Enter Password:") {
  if (preg_match('/^win/i', PHP_OS)) {
    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    file_put_contents(
      $vbscript, 'wscript.echo(InputBox("'
      . addslashes($prompt)
      . '", "", "password here"))');
    $command = "cscript //nologo " . escapeshellarg($vbscript);
    $password = rtrim(shell_exec($command));
    unlink($vbscript);
    return $password;
  } else {
    $command = "/usr/bin/env bash -c 'echo OK'";
    if (rtrim(shell_exec($command)) !== 'OK') {
      trigger_error("Can't invoke bash");
      return;
    }
    $command = "/usr/bin/env bash -c 'read -s -p \""
      . addslashes($prompt)
      . "\" mypassword && echo \$mypassword'";
    $password = rtrim(shell_exec($command));
    echo "\n";
    return $password;
  }
}

require_once dirname(__FILE__) . "/../src/RouterOS/Core.php";
require_once "vendor/autoload.php";

use \RouterOS;
use Ulrichsg\Getopt;

$getopt = new Getopt(array(
    array('h', 'host', Getopt::REQUIRED_ARGUMENT),
    array('u', 'user', Getopt::REQUIRED_ARGUMENT),
    array('p', 'port', Getopt::OPTIONAL_ARGUMENT),
    array(null, 'help', Getopt::NO_ARGUMENT)
));

try {

    $getopt->parse();

    $help = $getopt->getOption('help');

    if ($help != null) {
        $getopt->showHelp();
        exit();
    }

    $api = new RouterOS\Core();

    $host = $getopt->getOption('host');
    $port = $getopt->getOption('port');
    $user = $getopt->getOption('user');

    if ($host != null || $user != null) {

        if ($port != null) {
            $api->port = $port;
        }

        $password = prompt_silent();

        $connected = $api->connect(
            $getopt->getOption('host'),
            $getopt->getOption('user'),
            $password
        );

        if ($connected == true) {
            echo "Able to connect to the Mikrotik\n";
        } else {
            echo "Not able to connect to the Mikrotik\n";
        }

    } else {
        echo "Missing required argument, use argument --help\n";
    }
    

} catch (\UnexpectedValueException $e) {

    echo "Unexpected Value\n";

}



?>