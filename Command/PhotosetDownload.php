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
     * @var \Rezzza\Flickr\ApiFactory
     */
    private $flickrApi;

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
        $this->flickrApi = $this->getFlickrApi();
        $photos = $this->getPhotoList($id);
        $output->writeln('<info>Number of photos in set: ' . count($photos) . '</info>');
        $i = 1;
        foreach ($photos as $photo) {
            $filename = $this->getPhotoFilename($photo, $i, $noSlug);
            $output->write($filename . ' ');
            $size = $this->downloadPhoto($photo, $filename);
            if ($size === FALSE) {
                $output->writeln('<error>Error!</error>');
            } else {
                $output->writeln('<comment>(' . $this->formatFilesize($size) . ')</comment>');
            }
            $i++;
        }
    }
    
    /**
     * @return \Rezzza\Flickr\ApiFactory
     */
    private function getFlickrApi()
    {
        $configFilename = $_SERVER['HOME'] . '/.flickrDownloadr';
        if (!is_readable($configFilename)) {
            throw new \RuntimeException('Config file missing or not readable!');
        }
        $neonDecoder = new \Nette\Neon\Decoder();
        $config = $neonDecoder->decode(file_get_contents($configFilename));

        $metadata = new \Rezzza\Flickr\Metadata($config['oauth']['key'], $config['oauth']['secret']);
        $metadata->setOauthAccess($config['oauth']['token'], $config['oauth']['tokenSecret']);

        $flickrApi = new \Rezzza\Flickr\ApiFactory($metadata, new \Rezzza\Flickr\Http\GuzzleAdapter());
        return $flickrApi;
    }
    
    /**
     * @param string $photosetId
     * @return \SimpleXMLElement[]
     */
    private function getPhotoList($photosetId)
    {
        $params = [
            'photoset_id' => $photosetId, 
            'extras' => 'url_o,media,original_format'
        ];
        $xml = $this->flickrApi->call('flickr.photosets.getPhotos', $params);
        $this->dieOnErrorResponse($xml);
        $photos = $xml->photoset->photo;
        return $photos;
    }
    
    /**
     * @param \SimpleXMLElement $photo
     * @param intiger $listOrder
     * @param boolean $noSlug
     * @return string
     */
    private function getPhotoFilename(\SimpleXMLElement $photo, $listOrder, $noSlug)
    {
        $pos = str_pad($listOrder, 3, '0', STR_PAD_LEFT);
        $title = $photo->attributes()->title;
        $id = $photo->attributes()->id;
        $extension = $photo->attributes()->originalformat;
        
        $filename = $pos . '-' . $title . '-' . $id;
        if (!$noSlug) {
            $filename = Strings::webalize($filename);
        }
        return $filename . '.' . $extension;
    }
    
    /**
     * @param \SimpleXMLElement $photo
     * @param type $filename
     * @return int Number of bytes that were written to the file, or FALSE on failure
     */
    private function downloadPhoto(\SimpleXMLElement $photo, $filename)
    {
        $urlOriginal = $photo->attributes()->url_o;
        return file_put_contents($filename, fopen($urlOriginal, 'r'));
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
    
    /**
     * @param \SimpleXMLElement $xml
     * @throws \RuntimeException
     */
    private function dieOnErrorResponse(\SimpleXMLElement $xml)
    {
        // TODO: refactor into API wrapper
        $xml = (array)$xml;
        $status = $xml['@attributes']['stat'];
        if ($status !== 'fail') {
            return;
        }
        $err = (array)$xml['err'];
        $msg = $err['@attributes']['msg'];
        throw new \RuntimeException($msg);
    }
}
