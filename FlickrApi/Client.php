<?php

namespace FlickrDownloadr\FlickrApi;

class Client
{
    /**
     * @var \Rezzza\Flickr\ApiFactory
     */
    private $httpClient;
    
    function __construct(\Rezzza\Flickr\ApiFactory $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $service
     * @param array $params
     * @return array
     */
    public function call($service, array $params = array())
    {
        $params['format'] = 'json';
        $params['nojsoncallback'] = 1;
        $response = $this->httpClient->call($service, $params);
        $this->dieOnErrorResponse($response);
        return $response;
    }

    /**
     * @param array $response
     * @return null
     * @throws Exception
     */
    private function dieOnErrorResponse(array $response)
    {
        if (!array_key_exists('stat', $response)) {
            throw new Exception('Invalid response, missing status property');
        }
        if ($response['stat'] !== 'fail') {
            return;
        }
        $msg = $response['message'];
        $code = $response['code'];
        throw new Exception($msg, $code);
    }
}