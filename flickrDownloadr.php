#!/usr/bin/env php
<?php
require_once './vendor/autoload.php';
require_once './Command/PhotosetDownload.php';
require_once './Command/PhotosetList.php';
require_once './Command/Authorize.php';
require_once './Http/GuzzleJsonAdapter.php';

use Symfony\Component\Console\Application;

$configurator = new Nette\Configurator();
$configurator->setTempDirectory('/tmp');
$configurator->addConfig(__DIR__ . '/config.neon');
$container = $configurator->createContainer();

$application = new Application('Flickr Downloadr');
$application->add($container->getService('command.photoset.list'));
$application->add($container->getService('command.photoset.download'));
$application->add($container->getService('command.photoset.authorize'));
$application->run();