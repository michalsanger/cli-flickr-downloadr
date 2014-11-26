<?php

namespace FlickrDownloadr\Photo;

/**
 * @method string getId()
 * @media string getSecret()
 * @media string getServer()
 * @media int getFarm()
 * @media string getIsprimary()
 * @media int getIsPublic()
 * @media int getIsFriend()
 * @media int getIsFamily()
 * @media string getOriginalsecret()
 * @media string getMedia()
 * @media string getMediaStatus()
 * @media string getUrlO()
 * @media string getHeightO()
 * @media string getWidthO()
 */
class Photo
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
    
    /**
     * @return string
     */
    public function getOriginalFormat()
    {
        if (!array_key_exists('originalformat', $this->data)) {
            return;
        }
        return $this->data['originalformat'];
    }
}