<?php

namespace FlickrDownloadr\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PhotosetDownload extends Command
{
    /**
     * @var \FlickrDownloadr\FlickrApi\Client
     */
    private $flickrApi;
    
    function __construct(\FlickrDownloadr\FlickrApi\Client $flickrApi)
    {
        $this->flickrApi = $flickrApi;
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setName('photoset:download')
            ->setDescription('Download photoset')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'ID of the photoset'
            )
            ->addOption(
                'no-slug',
                null,
                InputOption::VALUE_NONE,
                'Do not convert filename to safe string'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $noSlug = $input->getOption('no-slug');
        
        $photosetInfo = $this->getPhotosetInfo($id);
        $dirName = $this->getDirName($photosetInfo, $noSlug);
        if (!is_dir($dirName)) {
            \Nette\Utils\FileSystem::createDir($dirName);
        }
        
        $photos = $this->getPhotoList($id);
        $output->writeln('<info>Number of photos in set: ' . count($photos) . '</info>');
        $i = 1;
        foreach ($photos as $photo) {
            $filename = $this->getPhotoFilename($photo, $i, $noSlug);
            $output->write($filename . ' ');
            $size = $this->downloadPhoto($photo, $filename, $dirName);
            if ($size === FALSE) {
                $output->writeln('<error>Error!</error>');
            } else {
                $output->writeln('<comment>(' . $this->formatFilesize($size) . ')</comment>');
            }
            $i++;
        }
    }

    /**
     * @param string $photosetId
     * @return array
     */
    private function getPhotoList($photosetId)
    {
        $params = [
            'photoset_id' => $photosetId, 
            'extras' => 'url_o,media,original_format',
        ];
        $response = $this->flickrApi->call('flickr.photosets.getPhotos', $params);
        $photos = $response['photoset']['photo'];
        return $photos;
    }

    /**
     * @param string $photosetId
     * @return array
     */
    private function getPhotosetInfo($photosetId)
    {
        $params = [
            'photoset_id' => $photosetId,
        ];
        $response = $this->flickrApi->call('flickr.photosets.getInfo', $params);
        return $response['photoset'];
    }
    
    /**
     * @param array $photo
     * @param intiger $listOrder
     * @param boolean $noSlug
     * @return string
     */
    private function getPhotoFilename(array $photo, $listOrder, $noSlug)
    {
        $pos = str_pad($listOrder, 3, '0', STR_PAD_LEFT);
        $title = $photo['title'];
        $id = $photo['id'];
        $extension = $photo['originalformat'];
        
        $filename = $pos . '-' . $title . '-' . $id;
        if (!$noSlug) {
            $filename = Strings::webalize($filename);
        }
        return $filename . '.' . $extension;
    }
    
    private function getDirName($photosetInfo, $noSlug)
    {
        $dirName = $photosetInfo['title']['_content'];
        if (!$noSlug) {
            $dirName = Strings::webalize($dirName);
        }
        return $dirName;
    }


    /**
     * @param array $photo
     * @param string $filename
     * @param string $dirName
     * @return int Number of bytes that were written to the file, or FALSE on failure
     */
    private function downloadPhoto(array $photo, $filename, $dirName)
    {
        $urlOriginal = $photo['url_o'];
        return file_put_contents($dirName . '/' . $filename, fopen($urlOriginal, 'r'));
    }
    
    /**
     * Converts to human readable file size.
     * @param  int
     * @param  int
     * @return string
     */
    private function formatFilesize($bytes, $precision = 2)
    {
        $bytes = round($bytes);
        $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
        foreach ($units as $unit) {
            if (abs($bytes) < 1024 || $unit === end($units)) {
                break;
            }
            $bytes = $bytes / 1024;
        }
        return round($bytes, $precision) . ' ' . $unit;
    }
}
