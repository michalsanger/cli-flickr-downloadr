<?php

namespace FlickrDownloadr\Photo;

class Repository
{
    /**
     * @var \FlickrDownloadr\FlickrApi\Client
     */
    private $flickrApi;
    
    function __construct(\FlickrDownloadr\FlickrApi\Client $flickrApi)
    {
        $this->flickrApi = $flickrApi;
    }
    
    /**
     * @param string $photosetId
     * @return \FlickrDownloadr\Photo\Photo[]
     */
    public function findAllByPhotosetId($photosetId)
    {
        $params = [
            'photoset_id' => $photosetId, 
            'extras' => 'url_o,media,original_format',
        ];
        $response = $this->flickrApi->call('flickr.photosets.getPhotos', $params);
        $photosData = $response['photoset']['photo'];
        $photos = array();
        foreach ($photosData as $photoData) {
            $photos[] = new Photo($photoData);
        }
        return $photos;
    }
}
