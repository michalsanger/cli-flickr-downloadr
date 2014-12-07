<?php

namespace FlickrDownloadr\Photo;

/**
 * @method string getId()
 * @method string getSecret()
 * @method string getServer()
 * @method int getFarm()
 * @method string getIsprimary()
 * @method int getIsPublic()
 * @method int getIsFriend()
 * @method int getIsFamily()
 * @method string getOriginalsecret()
 * @method string getMedia()
 * @method string getMediaStatus()
 * @method string getUrl()
 * @method int getHeight()
 * @method int getWidth()
 * @method int getViews()
 * @method \Nette\Utils\DateTime getDate()
 */
class Photo
{
    /** @var array */
    private $data;
    
    /**
	 * 
	 * @param array $data API response data, underscores keys
	 * @param string $url
	 * @param int $width
	 * @param int $height
	 * @param \Nette\Utils\DateTime $date
	 */
    function __construct(array $data, $url, $width, $height, \Nette\Utils\DateTime $date)
    {
		$data['url'] = $url;
		$data['width'] = $width;
		$data['height'] = $height;
		$data['date'] = $date;
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