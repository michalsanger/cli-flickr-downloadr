#!/usr/bin/env php
<?php
require_once './vendor/autoload.php';
require_once './Command/PhotosetDownload.php';
require_once './Command/PhotosetList.php';
require_once './Command/Authorize.php';
require_once './Http/GuzzleJsonAdapter.php';

use FlickrDownloadr\Command\PhotosetDownload;
use FlickrDownloadr\Command\PhotosetList;
use FlickrDownloadr\Command;
use Symfony\Component\Console\Application;

$application = new Application('Flickr Downloadr');
$application->add(new PhotosetDownload());
$application->add(new PhotosetList());
$application->add(new Command\Authorize());
$application->run();