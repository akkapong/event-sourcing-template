<?php
declare(strict_types=1);

use Script\Generate;

include dirname(__DIR__) . '/vendor/autoload.php';

//create
$generator = new Generate($argv[1]);

//create directory
$generator->createDirectory();


//generate collection
$generator->getGenClass('collection')->createFile();
$generator->getGenClass('infrastructure')->createFile();
$generator->getGenClass('command')->createFile();
$generator->getGenClass('command_handle')->createFile();
$generator->getGenClass('event')->createFile();
$generator->getGenClass('module')->createFile();
$generator->getGenClass('module_repo')->createFile();
$generator->getGenClass('projection')->createFile();
$generator->getGenClass('controller')->createFile();
$generator->getGenClass('worker')->createFile();
$generator->getGenClass('command_route')->createFile();
$generator->getGenClass('event_route')->createFile();