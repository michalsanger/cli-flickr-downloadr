<?php

namespace FlickrDownloadr\Photoset;

/**
 * @method string getId()
 * @method int getPhotos() Count of photos
 */
class Photoset
{
    /** @var array */
    private $data;
    
    /**
     * @param array $data API response data, underscores keys
     */
    function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $key = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $name)), '_');
        if (strpos($key, 'get_') === 0) {
            $key = substr($key, 4);
        }
        if (!array_key_exists($key, $this->data)) {
            return;
        }
        return $this->data[$key];
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        if (!array_key_exists('title', $this->data)) {
            return;
        }
        if (!is_array($this->data['title'])) {
            return $this->data['title'];
        }
        if (array_key_exists('_content', $this->data['title'])) {
            return $this->data['title']['_content'];
        }
    }
}