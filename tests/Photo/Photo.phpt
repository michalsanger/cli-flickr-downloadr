<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Photo\Photo;

$data = array (
	"id" => "15898991521",
	"secret" => "7b48ff004e",
	"server" => "7476",
	"farm" => 8,
	"title" => "Authorization",
	"isprimary" => "0",
	"ispublic" => 1,
	"isfriend" => 0,
	"isfamily" => 0,
	"datetaken" => "2014-11-28 23:32:31",
	"originalsecret" => "a4a22cd1dc",
	"originalformat" => "png",
	"media" => "photo",
	"media_status" => "ready",
	"url_sq" => "https://farm8.staticflickr.com/7476/15898991521_7b48ff004e_s.jpg",
	"height_sq" => 75,
	"width_sq" => 75,
);
$url = 'photo URL';
$width = 800;
$height = 600;
$date = \Nette\Utils\DateTime::from($data['datetaken']);

$photo = new Photo($data, $url, $width, $height, $date);

Assert::equal($url, $photo->getUrl());
Assert::equal($width, $photo->getWidth());
Assert::equal($height, $photo->getHeight());
Assert::equal($data['title'], $photo->getTitle());
Assert::equal($data['originalformat'], $photo->getOriginalFormat());
