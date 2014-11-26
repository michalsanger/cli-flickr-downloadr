<?php

namespace FlickrDownloadr\Photoset;

/**
 * @method string getId()
 * @method string getOwner()
 * @method string getUsername()
 * @method string getPrimary()
 * @method string getSecret()
 * @method string getServer()
 * @method int getFarm()
 * @method int getPhotos() Photos count
 * @method string getCountViews()
 * @method string getCountComments()
 * @method string getCountPhotos()
 * @method int getCountVideos()
 * @method int getCanComment()
 * @method string getDateCreate()
 * @method string getDateUpdate()
 * @method string getCoverphotoServer()
 * @method int getCoverphotoFarm()
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
        return $this->getInnerContent('title');
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getInnerContent('description');
    }
    
    /**
     * @param string $propName
     * @return string
     */
    private function getInnerContent($propName)
    {
        if (!array_key_exists($propName, $this->data)) {
            return;
        }
        if (!is_array($this->data[$propName])) {
            return $this->data[$propName];
        }
        if (array_key_exists('_content', $this->data[$propName])) {
            return $this->data[$propName]['_content'];
        }
    }        
}