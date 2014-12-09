<?php

require_once __DIR__ . '/Command/PhotosetDownload.php';
require_once __DIR__ . '/Command/PhotosetList.php';
require_once __DIR__ . '/Command/Authorize.php';
require_once __DIR__ . '/FlickrApi/Client.php';
require_once __DIR__ . '/FlickrApi/Exception.php';
require_once __DIR__ . '/FlickrApi/GuzzleJsonAdapter.php';
require_once __DIR__ . '/Photoset/Photoset.php';
require_once __DIR__ . '/Photoset/Repository.php';
require_once __DIR__ . '/Photo/Photo.php';
require_once __DIR__ . '/Photo/Repository.php';
require_once __DIR__ . '/Photo/Mapper.php';
require_once __DIR__ . '/Photo/FilenameCreator.php';
require_once __DIR__ . '/Oauth/ClientFactory.php';
require_once __DIR__ . '/Photoset/DirnameCreator.php';
require_once __DIR__ . '/Photo/SizeHelper.php';