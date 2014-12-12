<?php

namespace FlickrDownloadr\Photo;

class DownloaderFactory
{
	/**
	 * @param \Symfony\Component\Console\Output\Output $output
	 * @param boolean $dryRun
	 * @return \FlickrDownloadr\Photo\Downloader
	 */
	public function create(\Symfony\Component\Console\Output\Output $output, $dryRun)
	{
		return new Downloader($output, $dryRun);
	}
}