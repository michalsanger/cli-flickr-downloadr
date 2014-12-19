<?php

namespace FlickrDownloadr\Photo;

use Symfony\Component\Console\Output\OutputInterface;

class DownloaderFactory
{
	/** @var \FlickrDownloadr\Tool\TimeFormater */
	private $timeFormater;

	/** @var \FlickrDownloadr\Tool\SpeedFormater */
	private $speedFormater;

	/** @var \FlickrDownloadr\Tool\FilesizeFormater */
	private $filesizeFormater;

	function __construct(
		\FlickrDownloadr\Tool\TimeFormater $timeFormater,
		\FlickrDownloadr\Tool\SpeedFormater $speedFormater,
		\FlickrDownloadr\Tool\FilesizeFormater $filesizeFormater
	)
	{
		$this->timeFormater = $timeFormater;
		$this->speedFormater = $speedFormater;
		$this->filesizeFormater = $filesizeFormater;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param boolean $dryRun
	 * @return \FlickrDownloadr\Photo\Downloader
	 */
	public function create(OutputInterface $output, $dryRun)
	{
		return new Downloader(
			$output, 
			$this->timeFormater, 
			$this->speedFormater, 
			$this->filesizeFormater, 
			$dryRun);
	}
}