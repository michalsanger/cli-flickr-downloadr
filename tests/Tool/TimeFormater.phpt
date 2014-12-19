<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Tool\TimeFormater;

$formater = new TimeFormater();

Assert::equal('1s', $formater->format(1));
Assert::equal('1.3s', $formater->format(1.33));
Assert::equal('1.7s', $formater->format(1.685));
Assert::equal('0.5s', $formater->format(0.5));
Assert::equal('44s', $formater->format(44));
Assert::equal('1m', $formater->format(60));
Assert::equal('8m', $formater->format(480));
Assert::equal('1h', $formater->format(3600));
Assert::equal('1h 29m 14s', $formater->format(5354));
Assert::equal('14h 52m 28s', $formater->format(53548));
Assert::equal('0s', $formater->format(0));
Assert::equal('14m 8s', $formater->format(848));