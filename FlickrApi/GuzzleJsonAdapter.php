<?php

namespace FlickrDownloadr\FlickrApi;

class GuzzleJsonAdapter implements \Rezzza\Flickr\Http\AdapterInterface
{
    private $client;
    
    public function __construct()
    {
        $this->client  = new \Guzzle\Http\Client('', array('redirect.disable' => true));
    }
    
    public function multiPost(array $requests)
    {
        throw new \Nette\NotImplementedException(__METHOD__);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function post($url, array $data = array(), array $headers = array())
    {
        $request = $this->client->post($url, $headers, $data);
        // flickr does not supports this header and return a 417 http code during upload
        $request->removeHeader('Expect');

        return $request->send()->json();
    }
}