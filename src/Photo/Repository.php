<?php

namespace FlickrDownloadr\Photo;

use FlickrDownloadr\Photo\SizeHelper;

class Repository
{
    /** @var \FlickrDownloadr\FlickrApi\Client */
    private $flickrApi;
	
	/** @var \FlickrDownloadr\Photo\SizeHelper */
	private $sizeHelper;
	
	/** @var \FlickrDownloadr\Photo\Mapper */
	private $mapper;
    
    function __construct(
		\FlickrDownloadr\FlickrApi\Client $flickrApi, 
		\FlickrDownloadr\Photo\SizeHelper $sizeHelper,
		\FlickrDownloadr\Photo\Mapper $mapper
	)
	{
        $this->flickrApi = $flickrApi;
		$this->sizeHelper = $sizeHelper;
		$this->mapper = $mapper;
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
            'extras' => $this->getExtras($sizeName),
        ];
        $response = $this->flickrApi->call('flickr.photosets.getPhotos', $params);
        $photosData = $response['photoset']['photo'];
        $photos = array();
        foreach ($photosData as $photoData) {
            $photos[] = $this->mapper->fromPlainToEntity($photoData, $sizeName);
        }
        return $photos;
    }
	
	private function getExtras($sizeName)
	{
		$extras = array('media', 'original_format');
		$sizeCode = $this->sizeHelper->getCode($sizeName);
		if (is_string($sizeCode)) {
			$extras[] = 'url_' . $sizeCode;
		}
		return implode(',', $extras);
	}
		
}
