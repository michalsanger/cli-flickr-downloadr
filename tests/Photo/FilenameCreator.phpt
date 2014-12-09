<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Photo;

$photoSizeHelper = new Photo\SizeHelper();
$mapper = new Photo\Mapper($photoSizeHelper);
$data = array (
	"id" => "15898991521",
	"secret" => "7b48ff004e",
	"server" => "7476",
	"farm" => 8,
	"title" => "Authorizátion",
	"isprimary" => "0",
	"ispublic" => 1,
	"isfriend" => 0,
	"isfamily" => 0,
	"datetaken" => "2014-11-28 23:32:31",
	"datetakengranularity" => 0,
	"datetakenunknown" => "1",
	"views" => "24",
	"originalsecret" => "a4a22cd1dc",
	"originalformat" => "png",
	"media" => "photo",
	"media_status" => "ready",
	"url_o" => "https://farm8.staticflickr.com/7476/15898991521_a4a22cd1dc_o.jpg",
	"height_o" => "565",
	"width_o" => "684",
);
$photo = $mapper->fromPlainToEntity($data, Photo\SizeHelper::NAME_ORIGINAL);

$creator = new Photo\FilenameCreator();

Assert::equal('09-authorization.jpg', $creator->create($photo, 9, 25, '%order%-%title%', 'original'));
Assert::equal('authorization-15898991521.jpg', $creator->create($photo, 9, 25, '%title%-%id%', 'original'));
Assert::equal('2014-authorization.jpg', $creator->create($photo, 9, 25, '%year%-%title%', 'original'));
Assert::equal('2014-11-authorization.jpg', $creator->create($photo, 9, 25, '%year%-%month%-%title%', 'original'));
Assert::equal('2014-11-28-authorization.jpg', $creator->create($photo, 9, 25, '%year%-%month%-%day%-%title%', 'original'));

Assert::equal('2014-11-28-23.32.31.jpg', $creator->create($photo, 9, 25, '%date%', 'original'));
Assert::equal('684x565-original-684-565.jpg', $creator->create($photo, 9, 25, '%size%-%sizeName%-%width%-%height%', 'original'));

// test $noTitleSlug param
Assert::equal('Authorizátion.jpg', $creator->create($photo, 9, 25, '%title%', 'original', TRUE));
