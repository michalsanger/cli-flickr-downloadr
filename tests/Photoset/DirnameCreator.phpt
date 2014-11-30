<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Photoset;

$creator = new Photoset\DirnameCreator();
$photosetData = array(
	'title' => 'Foo',
	'date_create' => '1220831697'
);
$photoset = new Photoset\Photoset($photosetData);

Assert::equal('foo', $creator->create($photoset, '%title%'));
Assert::equal('2008-foo', $creator->create($photoset, '%year%-%title%'));
Assert::equal('2008-09-foo', $creator->create($photoset, '%year%-%month%-%title%'));
Assert::equal('2008-09-08-foo', $creator->create($photoset, '%year%-%month%-%day%-%title%'));
