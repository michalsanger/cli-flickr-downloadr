<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Tool\FilesizeFormater;

$formater = new FilesizeFormater();

Assert::equal('5B', $formater->format(5));
Assert::equal('5kB', $formater->format(5*1024));
Assert::equal('5MB', $formater->format(5*1024*1024));
Assert::equal('1.95kB', $formater->format(2000));
Assert::equal('2kB', $formater->format(2000, 1));
Assert::equal('2.9kB', $formater->format(3000, 1));
