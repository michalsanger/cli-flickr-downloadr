<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Tool\SpeedFormater;

$filesizeFormater = new \FlickrDownloadr\Tool\FilesizeFormater();
$formater = new SpeedFormater($filesizeFormater);

Assert::equal('10kB/s', $formater->format(10*1024, 1));
Assert::equal('7.69kB/s', $formater->format(10*1024, 1.3));