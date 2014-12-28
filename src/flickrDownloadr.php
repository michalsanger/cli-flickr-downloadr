<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory(sys_get_temp_dir());

$configurator->addConfig(__DIR__ . '/config.neon');
$userConfig = $_SERVER['HOME'] . '/' . FlickrDownloadr\Command\Authorize::USER_CONFIG_FILENAME;
if (is_readable($userConfig)) {
    $configurator->addConfig($userConfig);
}
$container = $configurator->createContainer();

$configParams = $container->getParameters();
$version = $configParams['version'];
$build = $configParams['build'];
$application = new FlickrDownloadr\Console\Application('Flickr Downloadr', $version, $build);
$commands = $container->findByType('Symfony\Component\Console\Command\Command');
foreach ($commands as $commandName) {
    $application->add($container->getService($commandName));
}

$application->run();