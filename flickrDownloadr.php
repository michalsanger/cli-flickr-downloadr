#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory(sys_get_temp_dir());

$robotLoader = $configurator->createRobotLoader();
$robotLoader->autoRebuild = TRUE;
$dirs = array('Command', 'FlickrApi', 'Photoset', 'Photo');
foreach ($dirs as $dir) {
	$robotLoader->addDirectory(__DIR__ . '/' . $dir);
}
$robotLoader->register();

$configurator->addConfig(__DIR__ . '/config.neon');
$userConfig = $_SERVER['HOME'] . '/.flickrDownloadr.neon';
if (is_readable($userConfig)) {
    $configurator->addConfig($userConfig);
}
$container = $configurator->createContainer();

$application = new Symfony\Component\Console\Application('Flickr Downloadr');
$commands = $container->findByType('Symfony\Component\Console\Command\Command');
foreach ($commands as $commandName) {
    $application->add($container->getService($commandName));
}
$application->setVersion($container->getParameters()['version']);
$application->run();