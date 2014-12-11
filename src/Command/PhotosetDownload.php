<?php

namespace FlickrDownloadr\Command;

use FlickrDownloadr\Photo\Photo;
use FlickrDownloadr\Photo\SizeHelper;
use FlickrDownloadr\Photoset\Photoset;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PhotosetDownload extends Command
{
    /** @var \FlickrDownloadr\Photoset\Repository */
    private $photosetRepository;

    /** @var \FlickrDownloadr\Photo\Repository */
    private $photoRepository;
	
	/** @var \FlickrDownloadr\Photoset\DirnameCreator */
	private $dirnameCreator;
	
	/** @var \FlickrDownloadr\Photo\SizeHelper */
	private $photoSizeHelper;

	/** @var \FlickrDownloadr\Photo\FilenameCreator */
	private $photoFilenameCreator;

	/** @var \Symfony\Component\Console\Output\Output */
	private $output;
	
	public function __construct(
		\FlickrDownloadr\Photoset\Repository $photosetRepository, 
		\FlickrDownloadr\Photo\Repository $photoRepository,
		\FlickrDownloadr\Photoset\DirnameCreator $dirnameCreator,
		\FlickrDownloadr\Photo\SizeHelper $photoSizeHelper,
		\FlickrDownloadr\Photo\FilenameCreator $photoFilenameCreator
	)
    {
        $this->photosetRepository = $photosetRepository;
        $this->photoRepository = $photoRepository;
		$this->dirnameCreator = $dirnameCreator;
		$this->photoSizeHelper = $photoSizeHelper;
		$this->photoFilenameCreator = $photoFilenameCreator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('photoset:download')
            ->setDescription('Download photoset')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the photoset')
            ->addOption('no-slug', null, InputOption::VALUE_NONE, 'Do not convert filename to safe string')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not realy download')
            ->addOption('dir', null, InputOption::VALUE_OPTIONAL, 'Photoset directory. See the docs for supported placeholders', '%title%-%id%')
			->addOption('photo-size', 's', InputOption::VALUE_OPTIONAL, 'Name of photo size (original, large, medium, small...)', SizeHelper::NAME_ORIGINAL)
			->addOption('clean-dir', null, InputOption::VALUE_NONE, 'Erase directory if exists')
			->addOption('photo-name', null, InputOption::VALUE_OPTIONAL, 'Photo filename template. See the docs for supported placeholders', '%order%-%title%-%id%');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$this->output = $output;
        $id = $input->getArgument('id');
        $noSlug = $input->getOption('no-slug');
        $dryRun = $input->getOption('dry-run');
        $dir = $input->getOption('dir');
		$photoSize = $this->photoSizeHelper->validate($input->getOption('photo-size'));
		$cleanDir = $input->getOption('clean-dir');
		$photoFilename = $input->getOption('photo-name');

        $photoset = $this->photosetRepository->findOne($id);
        $dirName = $this->managePhotosetDir($photoset, $noSlug, $dryRun, $dir, $cleanDir);
        
        $photos = $this->photoRepository->findAllByPhotosetId($id, $photoSize);
        $output->writeln('<info>Number of photos in set: ' . count($photos) . '</info>');
        $i = 1;
        foreach ($photos as $photo) {
            $filename = $this->photoFilenameCreator->create($photo, $i, count($photos), $photoFilename, $photoSize, $noSlug);
            $output->write($filename . ' ');
            $size = $this->downloadPhoto($photo, $filename, $dirName, $dryRun);
            $result = $this->getDownloadResult($size);
            $output->writeln($result);
            $i++;
        }
    }
    
    /**
     * @param Photoset $photoset
     * @param boolean $noSlug
     * @param boolean $dryRun
     * @param string $dir
     * @param boolean $cleanDir
     * @return string
     */
    private function managePhotosetDir(Photoset $photoset, $noSlug, $dryRun, $dir, $cleanDir)
    {
		$dirName = $this->dirnameCreator->create($photoset, $dir, $noSlug);
		if ($cleanDir && is_dir($dirName) && !$dryRun) {
			\Nette\Utils\FileSystem::delete($dirName);
		}
		if (!is_dir($dirName) && !$dryRun) {
			\Nette\Utils\FileSystem::createDir($dirName);
        }
        return $dirName;
    }

    /**
     * @param Photo $photo
     * @param string $filename
     * @param string $dirName
     * @param boolean $dryRun
     * @return int Number of bytes that were written to the file, or FALSE on failure
     */
    private function downloadPhoto(Photo $photo, $filename, $dirName, $dryRun)
    {
		// TODO: refactor into Photo\Downloader
        $url = $photo->getUrl();
		if ($dryRun) {
			return 0;
		}
		\Nette\Utils\FileSystem::createDir($dirName . '/' . dirname($filename));
		$output = $this->output;
		$progress = null;
		$streamNotificationCallback = function($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) use (&$progress, $output)
		{
			switch($notification_code) {

				case STREAM_NOTIFY_FILE_SIZE_IS:
					$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, $bytes_max);
					$progress->start();
					break;

				case STREAM_NOTIFY_PROGRESS:
					$progress->setCurrent($bytes_transferred);
					break;
			}
		};
		$ctx = stream_context_create();
		stream_context_set_params($ctx, array("notification" => $streamNotificationCallback));

        $bytes = file_put_contents($dirName . '/' . $filename, fopen($url, 'r', FALSE, $ctx));
		$progress->clear();
		$progress->finish();
        return $bytes;
    }
    
    /**
     * Converts to human readable file size.
     * @param  int
     * @param  int
     * @return string
     */
    private function formatFilesize($bytes, $precision = 2)
    {
        $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
        foreach ($units as $unit) {
            if (abs($bytes) < 1024 || $unit === end($units)) {
                break;
            }
            $bytes = $bytes / 1024;
        }
        return round($bytes, $precision) . ' ' . $unit;
    }
    
    /**
     * @param int $downloadedSize
     * @return string
     */
    private function getDownloadResult($downloadedSize)
    {
        if ($downloadedSize === FALSE) {
            $result = '<error>Error!</error>';
        } else {
            $result = '<comment>(' . $this->formatFilesize($downloadedSize) . ')</comment>';
        }
        return $result;
    }
}
