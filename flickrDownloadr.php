#!/usr/bin/env php
<?php
require_once './vendor/autoload.php';
require_once './Command/PhotosetDownload.php';
require_once './Command/PhotosetList.php';
require_once './Command/Authorize.php';
require_once './FlickrApi/Client.php';
require_once './FlickrApi/Exception.php';
require_once './FlickrApi/GuzzleJsonAdapter.php';
require_once './Photoset/Photoset.php';
require_once './Photoset/Repository.php';
require_once './Photo/Photo.php';
require_once './Photo/Repository.php';

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