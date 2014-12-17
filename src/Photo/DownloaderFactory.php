<?php

namespace FlickrDownloadr\Photo;

use Symfony\Component\Console\Output\OutputInterface;

class DownloaderFactory
{
	/** @var \FlickrDownloadr\Tool\TimeFormater */
	private $timeFormater;

	function __construct(\FlickrDownloadr\Tool\TimeFormater $timeFormater)
	{
		$this->timeFormater = $timeFormater;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param boolean $dryRun
	 * @return \FlickrDownloadr\Photo\Downloader
	 */
	public function create(OutputInterface $output, $dryRun)
	{
		return new Downloader($output, $this->timeFormater, $dryRun);
	}
}