<?php

namespace FlickrDownloadr\Photo;

use Symfony\Component\Console\Output\OutputInterface;

class DownloaderFactory
{
	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param boolean $dryRun
	 * @return \FlickrDownloadr\Photo\Downloader
	 */
	public function create(OutputInterface $output, $dryRun)
	{
		return new Downloader($output, $dryRun);
	}
}