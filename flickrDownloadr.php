#!/usr/bin/env php
<?php
require_once './vendor/autoload.php';
require_once './Command/PhotosetDownload.php';
require_once './Command/PhotosetList.php';

use FlickrDownloadr\Command\PhotosetDownload;
use FlickrDownloadr\Command\PhotosetList;
use Symfony\Component\Console\Application;

$application = new Application('Flickr Downloadr');
$application->add(new PhotosetDownload());
$application->add(new PhotosetList());
$application->run();