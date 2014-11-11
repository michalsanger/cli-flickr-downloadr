#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Command/PhotosetDownload.php';
require_once __DIR__ . '/Command/PhotosetList.php';
require_once __DIR__ . '/Command/Authorize.php';
require_once __DIR__ . '/FlickrApi/Client.php';
require_once __DIR__ . '/FlickrApi/Exception.php';
require_once __DIR__ . '/FlickrApi/GuzzleJsonAdapter.php';
require_once __DIR__ . '/Photoset/Photoset.php';
require_once __DIR__ . '/Photoset/Repository.php';
require_once __DIR__ . '/Photo/Photo.php';
require_once __DIR__ . '/Photo/Repository.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory('/tmp');
$configurator->addConfig(__DIR__ . '/config.neon');
$userConfig = $_SERVER['HOME'] . '/.flickrDownloadr.neon';
if (is_readable($userConfig)) {
    $configurator->addConfig($userConfig);
}
$container = $configurator->createContainer();

//$robotLoader = $configurator->createRobotLoader();
//$robotLoader->autoRebuild = TRUE;
//$robotLoader->addDirectory('.');
//$robotLoader->register();

$application = new Symfony\Component\Console\Application('Flickr Downloadr');
$commands = $container->findByTag('command');
foreach ($commands as $commandName => $foo) {
    $application->add($container->getService($commandName));
}
$application->run();