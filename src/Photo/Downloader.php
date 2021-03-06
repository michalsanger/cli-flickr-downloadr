<?php

namespace FlickrDownloadr\Photo;

use \Symfony\Component\Console\Helper\ProgressBar;
use \Symfony\Component\Console\Output\OutputInterface;
use \FlickrDownloadr\Tool\TimeFormater;
use \FlickrDownloadr\Tool\SpeedFormater;
use \FlickrDownloadr\Tool\FilesizeFormater;

class Downloader
{
	/** @var \Symfony\Component\Console\Output\OutputInterface */
	private $output;
	
	/** @var \FlickrDownloadr\Tool\TimeFormater */
	private $timeFormater;

	/** @var \FlickrDownloadr\Tool\SpeedFormater */
	private $speedFormater;

	/** @var \FlickrDownloadr\Tool\FilesizeFormater */
	private $filesizeFormater;

	/** @var boolean */
	private $dryRun;

	/** @var \Symfony\Component\Console\Helper\ProgressBar */
	private $progress;

	public function __construct(
		OutputInterface $output,
		TimeFormater $timeFormater,
		SpeedFormater $speedFormater,
		FilesizeFormater $filesizeFormater,
		$dryRun
	)
	{
		$this->output = $output;
		$this->timeFormater = $timeFormater;
		$this->speedFormater = $speedFormater;
		$this->filesizeFormater = $filesizeFormater;
		$this->dryRun = $dryRun;
		$this->progress = new ProgressBar($output);
	}

	/**
	 * @param \FlickrDownloadr\Photo\Photo $photo
	 * @param string $filename
	 * @param string $dirname
	 * @return int Number of bytes that were written to the file, or FALSE on failure
	 */
	public function download(Photo $photo, $filename, $dirname)
	{
        $url = $photo->getUrl();
		if ($this->dryRun) {
			return 0;
		}
		\Nette\Utils\FileSystem::createDir($dirname . '/' . dirname($filename));
		
		$this->setupProgressBar();
		$ctx = stream_context_create();
		stream_context_set_params($ctx, array("notification" => $this->getNotificationCallback($filename)));

        $bytes = file_put_contents($dirname . '/' . $filename, fopen($url, 'r', FALSE, $ctx));
		if ($bytes === FALSE) {
			$this->progress->setMessage('<error>Error!</error>', 'final_report');
		} else {
			list($time, $size, $speed) = $this->getFinalStats($this->progress->getMaxSteps(), $this->progress->getStartTime());
			$this->progress->setMessage('<comment>[' . $size . ' in ' . $time . ' (' . $speed . ')]</comment>', 'final_report');
			$this->progress->setFormat('%message% %final_report%' . "\n");
		}
		$this->progress->finish();
		$this->output->writeln('');
        return $bytes;
	}
	
	private function getNotificationCallback($filename)
	{
		$notificationCallback = function($notification_code, $severity, $message, $messageCode, $bytesTransferred, $bytesMax) use ($filename)
		{
			switch($notification_code) {

				case STREAM_NOTIFY_FILE_SIZE_IS:
					$this->progress = new ProgressBar($this->output, $bytesMax);
					$this->progress->setFormat('%message% %final_report%' . "\n" . '%percent:3s%% of %photo_size% [%bar%] %downloaded_bytes% eta %estimated:6s%');
					$this->progress->setMessage($filename);
					$this->progress->setMessage('', 'final_report');
					$this->progress->start();

					break;

				case STREAM_NOTIFY_PROGRESS:
					$this->progress->setCurrent($bytesTransferred);
					break;
			}
		};
		return $notificationCallback;
	}
	
	private function setupProgressBar()
	{
		ProgressBar::setPlaceholderFormatterDefinition(
			'photo_size',
			function (ProgressBar $bar) {
				return $this->filesizeFormater->format($bar->getMaxSteps());
			}
		);
		ProgressBar::setPlaceholderFormatterDefinition(
			'downloaded_bytes',
			function (ProgressBar $bar) {
				return $this->filesizeFormater->format($bar->getStep());
			}
		);
	}
		


	/**
	 * @param int $photoSize
	 * @param int $startTime
	 * @return array
	 */
	private function getFinalStats($photoSize, $startTime)
	{
		$size = $this->filesizeFormater->format($photoSize);
		$duration = max(array(microtime(TRUE) - $startTime, 0.001));
		$time = $this->timeFormater->format($duration);
		$speed = $this->speedFormater->format($photoSize, $duration);
		return array($time, $size, $speed);
	}
		
}