<?php

namespace FlickrDownloadr\Photo;

class Downloader
{
	/** @var \Symfony\Component\Console\Output\Output */
	private $output;
	
	/** @var boolean */
	private $dryRun;
	
	public function __construct(\Symfony\Component\Console\Output\Output $output, $dryRun)
	{
		$this->output = $output;
		$this->dryRun = $dryRun;
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
		$output = $this->output;
		$progress = null;
		$streamNotificationCallback = function($notification_code, $severity, $message, $messageCode, $bytesTransferred, $bytesMax) use (&$progress, $output, $filename)
		{
			switch($notification_code) {

				case STREAM_NOTIFY_FILE_SIZE_IS:
					$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, $bytesMax);
					$progress->setFormat('%message% %final_report%' . "\n" . '%percent:3s%% of %photo_size% [%bar%] %downloaded_bytes% eta %estimated:6s%');
					$progress->setMessage($filename);
					$progress->setMessage('', 'final_report');
					$progress->start();

					break;

				case STREAM_NOTIFY_PROGRESS:
					$progress->setCurrent($bytesTransferred);
					break;
			}
		};
		$ctx = stream_context_create();
		stream_context_set_params($ctx, array("notification" => $streamNotificationCallback));

        $bytes = file_put_contents($dirname . '/' . $filename, fopen($url, 'r', FALSE, $ctx));
		if ($bytes === FALSE) {
			$progress->setMessage('<error>Error!</error>', 'final_report');
		} else {
			list($time, $size, $speed) = $this->getFinalStats($progress->getMaxSteps(), $progress->getStartTime());
			$progress->setMessage('<comment>[' . $size . ' in ' . $time . ' (' . $speed . ')]</comment>', 'final_report');
			$progress->setFormat('%message% %final_report%' . "\n");
		}
		$progress->finish();
		$this->output->writeln('');
        return $bytes;
	}
	
	private function setupProgressBar()
	{
		\Symfony\Component\Console\Helper\ProgressBar::setPlaceholderFormatterDefinition(
			'photo_size',
			function (\Symfony\Component\Console\Helper\ProgressBar $bar) {
				return \Latte\Runtime\Filters::bytes($bar->getMaxSteps());
			}
		);
		\Symfony\Component\Console\Helper\ProgressBar::setPlaceholderFormatterDefinition(
			'downloaded_bytes',
			function (\Symfony\Component\Console\Helper\ProgressBar $bar) {
				return \Latte\Runtime\Filters::bytes($bar->getStep());
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
		$size = \Latte\Runtime\Filters::bytes($photoSize);
		$seconds = max(array(time() - $startTime, 1));
		$minutes = min(array(59, floor($seconds/60)));
		$hours = floor($seconds/3600);
		
		$times = array();
		if ($hours > 0) {
			$times[] = $hours . 'h';
		}
		if ($minutes > 0) {
			$times[] = $minutes . 'm';
		}
		if (($seconds%60) > 0) {
			$times[] = $seconds%60 . 's';
		}
		$time = implode(' ', $times);

		$speed = \Latte\Runtime\Filters::bytes($photoSize / $seconds) . '/s';
		return array($time, $size, $speed);
	}
		
}