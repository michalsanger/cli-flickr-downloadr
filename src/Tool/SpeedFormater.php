<?php

namespace FlickrDownloadr\Tool;

class SpeedFormater
{
	/** @var \FlickrDownloadr\Tool\FilesizeFormater */
	private $filesizeFormater;

	function __construct(\FlickrDownloadr\Tool\FilesizeFormater $filesizeFormater)
	{
		$this->filesizeFormater = $filesizeFormater;
	}

	/**
	 * @param int $bytes
	 * @param float $duration Seconds with miliseconds
	 * @return string
	 */
	public function format($bytes, $duration)
	{
		if ($duration == 0) {
			return '';
		}
		$bytesPerSec = $bytes / $duration;
		return $this->filesizeFormater->format($bytesPerSec) . '/s';
	}
}