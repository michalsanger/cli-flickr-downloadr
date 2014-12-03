<?php

require '../bootstrap.php';

use Tester\Assert;
use FlickrDownloadr\Photo\Mapper;
use FlickrDownloadr\Photo\SizeHelper;

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
	"originalsecret" => "a4a22cd1dc",
	"originalformat" => "png",
	"media" => "photo",
	"media_status" => "ready",
	"url_sq" => "https://farm8.staticflickr.com/7476/15898991521_7b48ff004e_s.jpg",
	"height_sq" => 75,
	"width_sq" => 75,
);

$mapper = new Mapper(new SizeHelper());
$photo = $mapper->fromPlainToEntity($data, SizeHelper::NAME_SQUARE);

Assert::equal($data['url_sq'], $photo->getUrl());
Assert::equal($data['width_sq'], $photo->getWidth());
Assert::equal($data['height_sq'], $photo->getHeight());
Assert::equal($data['title'], $photo->getTitle());
Assert::equal($data['originalformat'], $photo->getOriginalFormat());
