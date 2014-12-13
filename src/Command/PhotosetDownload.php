<?php

namespace FlickrDownloadr\Command;

use FlickrDownloadr\Photo\Photo;
use FlickrDownloadr\Photo\SizeHelper;
use FlickrDownloadr\Photoset\Photoset;
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

	/** @var \FlickrDownloadr\Photo\DownloaderFactory */
	private $downloaderFactory;
	
	/** @var \Symfony\Component\Console\Output\Output */
	private $output;
	
	public function __construct(
		\FlickrDownloadr\Photoset\Repository $photosetRepository, 
		\FlickrDownloadr\Photo\Repository $photoRepository,
		\FlickrDownloadr\Photoset\DirnameCreator $dirnameCreator,
		\FlickrDownloadr\Photo\SizeHelper $photoSizeHelper,
		\FlickrDownloadr\Photo\FilenameCreator $photoFilenameCreator,
		\FlickrDownloadr\Photo\DownloaderFactory $downloaderFactory
	)
    {
        $this->photosetRepository = $photosetRepository;
        $this->photoRepository = $photoRepository;
		$this->dirnameCreator = $dirnameCreator;
		$this->photoSizeHelper = $photoSizeHelper;
		$this->photoFilenameCreator = $photoFilenameCreator;
		$this->downloaderFactory = $downloaderFactory;
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
		list($id, $noSlug, $dryRun, $dir, $photoSize, $cleanDir, $photoFilename) = $this->getInputParams($input);

        $photoset = $this->photosetRepository->findOne($id);
        $dirName = $this->managePhotosetDir($photoset, $noSlug, $dryRun, $dir, $cleanDir);
        
        $photos = $this->photoRepository->findAllByPhotosetId($id, $photoSize);
        $output->writeln('<info>Number of photos in set: ' . count($photos) . '</info>' . PHP_EOL);
        $i = 1;
		$downloader = $this->downloaderFactory->create($output, $dryRun);
        foreach ($photos as $photo) {
            $filename = $this->photoFilenameCreator->create($photo, $i, count($photos), $photoFilename, $photoSize, $noSlug);
			$downloader->download($photo, $filename, $dirName);
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
	 * @param InputInterface $input
	 * @return array
	 */
	private function getInputParams(InputInterface $input)
	{
        $id = $input->getArgument('id');
        $noSlug = $input->getOption('no-slug');
        $dryRun = $input->getOption('dry-run');
        $dir = $input->getOption('dir');
		$photoSize = $this->photoSizeHelper->validate($input->getOption('photo-size'));
		$cleanDir = $input->getOption('clean-dir');
		$photoFilename = $input->getOption('photo-name');
		return array($id, $noSlug, $dryRun, $dir, $photoSize, $cleanDir, $photoFilename);
	}
}
