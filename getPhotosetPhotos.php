<?php

if (count($_SERVER['argv']) < 2) {
    die('Photoset ID required!' . PHP_EOL);
}

$photosetId = $_SERVER['argv'][1];

require_once './vendor/autoload.php';

$neonDecoder = new \Nette\Neon\Decoder();
$config = $neonDecoder->decode(file_get_contents('.flickrDownloadr'));

$metadata = new Rezzza\Flickr\Metadata($config['oauth']['key'], $config['oauth']['secret']);
$metadata->setOauthAccess($config['oauth']['token'], $config['oauth']['tokenSecret']);

$factory  = new Rezzza\Flickr\ApiFactory($metadata, new Rezzza\Flickr\Http\GuzzleAdapter());

$params = [
    'photoset_id' => $photosetId, 
    'extras' => 'url_o,media,original_format'
];
$xml = $factory->call('flickr.photosets.getPhotos', $params);
$photos = $xml->photoset->photo;
/* @var $photos SimpleXMLElement[] */
$i = 1;
foreach ($photos as $photo) {
    //var_dump($photo);
    $title = $photo->attributes()->title;
    $id = $photo->attributes()->id;
    $urlOriginal = $photo->attributes()->url_o;
    echo $id . '; ' . $title . PHP_EOL;
    $pos = str_pad($i, 3, '0', STR_PAD_LEFT);
    $filename = $pos . '-' . $title . '-' . $id . '.' . $photo->attributes()->originalformat;
    file_put_contents($filename, fopen($urlOriginal, 'r'));
    $i++;
}