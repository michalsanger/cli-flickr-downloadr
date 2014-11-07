#!/usr/bin/env php
<?php
require_once './vendor/autoload.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory('/tmp');
$configurator->addConfig(__DIR__ . '/config.neon');
$userConfig = $_SERVER['HOME'] . '/.flickrDownloadr.neon';
if (is_readable($userConfig)) {
    $configurator->addConfig($userConfig);
}
$container = $configurator->createContainer();

$robotLoader = $configurator->createRobotLoader();
$robotLoader->autoRebuild = TRUE;
$robotLoader->addDirectory('.');
$robotLoader->register();

$application = new Symfony\Component\Console\Application('Flickr Downloadr');
$application->add($container->getService('command.photoset.list'));
$application->add($container->getService('command.photoset.download'));
$application->add($container->getService('command.photoset.authorize'));
$application->run();