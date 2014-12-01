<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Photo\SizeHelper;

$sizeHelper = new SizeHelper();

Assert::equal(SizeHelper::NAME_ORIGINAL, $sizeHelper->validate('not_there'));
Assert::equal(SizeHelper::NAME_MEDIUM, $sizeHelper->validate('not_there', SizeHelper::NAME_MEDIUM));
Assert::equal(SizeHelper::NAME_SMALL, $sizeHelper->validate(SizeHelper::NAME_SMALL));

Assert::equal('sq', $sizeHelper->getCode(SizeHelper::NAME_SQUARE));
Assert::equal('m', $sizeHelper->getCode(SizeHelper::NAME_MEDIUM));
Assert::equal('o', $sizeHelper->getCode(SizeHelper::NAME_ORIGINAL));