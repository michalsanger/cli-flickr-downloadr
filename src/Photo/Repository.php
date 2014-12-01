<?php

namespace FlickrDownloadr\Photo;

use FlickrDownloadr\Photo\SizeHelper;

class Repository
{
    /**
     * @var \FlickrDownloadr\FlickrApi\Client
     */
    private $flickrApi;
	
	/**
	 * @var \FlickrDownloadr\Photo\SizeHelper
	 */
	private $sizeHelper;
    
    function __construct(
		\FlickrDownloadr\FlickrApi\Client $flickrApi, 
		\FlickrDownloadr\Photo\SizeHelper $sizeHelper
	)
	{
        $this->flickrApi = $flickrApi;
		$this->sizeHelper = $sizeHelper;
    }
    
    /**
     * @param string $photosetId
     * @param string $sizeName
     * @return \FlickrDownloadr\Photo\Photo[]
     */
    public function findAllByPhotosetId($photosetId, $sizeName = SizeHelper::NAME_ORIGINAL)
    {
        $params = [
            'photoset_id' => $photosetId, 
            'extras' => 'media,original_format,url_o',
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
