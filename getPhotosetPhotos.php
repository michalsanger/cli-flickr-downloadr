<?php

require_once './vendor/autoload.php';

$neonDecoder = new \Nette\Neon\Decoder();
$config = $neonDecoder->decode(file_get_contents('.flickrDownloadr'));

$metadata = new Rezzza\Flickr\Metadata($config['oauth']['key'], $config['oauth']['secret']);
$metadata->setOauthAccess($config['oauth']['token'], $config['oauth']['tokenSecret']);

$factory  = new Rezzza\Flickr\ApiFactory($metadata, new Rezzza\Flickr\Http\GuzzleAdapter());

$params = [
    'photoset_id' => "72157646966473272", 
    'extras' => 'url_o,media,original_format'
];
$xml = $factory->call('flickr.photosets.getPhotos', $params);
$photos = $xml->photoset->photo;
/* @var $photos SimpleXMLElement[] */
foreach ($photos as $photo) {
    //var_dump($photo);
    echo $photo->attributes()->id . '; ' . $photo->attributes()->title . PHP_EOL;
    echo $photo->attributes()->url_o . PHP_EOL . PHP_EOL;
}