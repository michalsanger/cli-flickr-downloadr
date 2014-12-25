<?php

namespace FlickrDownloadr\Photo;

use FlickrDownloadr\Photo\SizeHelper;

class Repository
{
	/**
	 * Number of photos to return per page. The maximum allowed value is 500.
	 * @var int
	 */
	private $photosPerPage = 100;

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
            'per_page' => $this->photosPerPage,
        ];
		$currentPage = 1;
		$firstPage = $this->getPage($currentPage, $params);
		$pages = $firstPage['photoset']['pages'];
		$photosData = $firstPage['photoset']['photo'];
		while($currentPage < $pages) {
			$currentPage++;
			$response = $this->getPage($currentPage, $params);
			$photosData = array_merge($photosData, $response['photoset']['photo']);
		}

        foreach ($photosData as $photoData) {
            $photos[] = $this->mapper->fromPlainToEntity($photoData, $sizeName);
        }
        return $photos;
    }
	
	private function getExtras($sizeName)
	{
		$extras = array('media', 'original_format', 'date_taken', 'views');
		$sizeCode = $this->sizeHelper->getCode($sizeName);
		if (is_string($sizeCode)) {
			$extras[] = 'url_' . $sizeCode;
		}
		return implode(',', $extras);
	}

	/**
	 * @param type $pageNumber
	 * @param array $params
	 * @return array API response with all metadata
	 */
	private function getPage($pageNumber, array $params)
	{
		$params['page'] = $pageNumber;
		$response = $this->flickrApi->call('flickr.photosets.getPhotos', $params);
		return $response;
	}
}
