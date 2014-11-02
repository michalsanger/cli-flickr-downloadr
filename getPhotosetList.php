<?php

require_once './vendor/autoload.php';

$neonDecoder = new \Nette\Neon\Decoder();
$config = $neonDecoder->decode(file_get_contents('.flickrDownloadr'));

$metadata = new Rezzza\Flickr\Metadata($config['oauth']['key'], $config['oauth']['secret']);
$metadata->setOauthAccess($config['oauth']['token'], $config['oauth']['tokenSecret']);

$factory  = new Rezzza\Flickr\ApiFactory($metadata, new Rezzza\Flickr\Http\GuzzleAdapter());

$xml = $factory->call('flickr.photosets.getList');
$sets = $xml->photosets->photoset;
/* @var $sets SimpleXMLElement[] */
foreach ($sets as $set) {
    echo (string)$set->title . PHP_EOL;
}