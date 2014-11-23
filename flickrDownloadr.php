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
require_once __DIR__ . '/Oauth/ClientFactory.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory(sys_get_temp_dir());

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