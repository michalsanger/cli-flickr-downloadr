#!/usr/bin/env php
<?php
require_once './vendor/autoload.php';
require_once './Command/PhotosetDownload.php';

use FlickrDownloadr\Command\PhotosetDownload;
use Symfony\Component\Console\Application;

$application = new Application('Flickr Downloadr');
$application->add(new PhotosetDownload());
$application->run();