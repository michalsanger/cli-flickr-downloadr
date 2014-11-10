<?php

namespace FlickrDownloadr\Photoset;

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
     * @param int $limit
     * @return \FlickrDownloadr\Photoset\Photoset[]
     */
    public function findAll($limit = NULL)
    {
        $params = array(
            'page' => 1,
            'per_page' => (int)$limit,
        );
        if ($limit === NULL) {
            unset($params['per_page']);
        }

        $response = $this->flickrApi->call('flickr.photosets.getList', $params);
        $setsData = $response['photosets']['photoset'];
        $photosets = array();
        foreach ($setsData as $setData) {
            $photosets[] = new Photoset($setData);
        }
        return $photosets;
    }

}
