<?php

namespace FlickrDownloadr\Photo;

class Mapper
{
	/** @var \FlickrDownloadr\Photo\SizeHelper */
	private $photoSizeHelper;

	function __construct(\FlickrDownloadr\Photo\SizeHelper $photoSizeHelper)
	{
		$this->photoSizeHelper = $photoSizeHelper;
	}

	/**
	 * @param array $data
	 * @return \FlickrDownloadr\Photo\Photo
	 */
	public function fromPlainToEntity(array $data, $photoSizeName)
	{
		$code = $this->photoSizeHelper->getCode($photoSizeName);
		$url = $data['url_' . $code];
		$width = $data['width_' . $code];
		$height = $data['height_' . $code];
		$date = \Nette\Utils\DateTime::from($data['datetaken']);
		return new Photo($data, $url, $width, $height, $date);
	}
}