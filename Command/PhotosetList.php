<?php

namespace FlickrDownloadr\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotosetList extends Command
{
    /**
     * @var \Rezzza\Flickr\ApiFactory
     */
    private $flickrApi;

    protected function configure()
    {
        $this
            ->setName('photoset:list')
            ->setDescription('List of photosets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->flickrApi = $this->getFlickrApi();
        $sets = $this->getPhotosets();
        $output->writeln('<info>Number of photosets: ' . count($sets) . '</info>');
        foreach ($sets as $set) {
            $output->writeln($set->attributes()->id . '; ' . $set->title);
        }
    }
    
    /**
     * @return \Rezzza\Flickr\ApiFactory
     */
    private function getFlickrApi()
    {
        // TODO: refactor into service
        $configFilename = dirname(__DIR__) . '/.flickrDownloadr';
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
     * @return \SimpleXMLElement[]
     */
    private function getPhotosets()
    {
        $xml = $this->flickrApi->call('flickr.photosets.getList');
        $sets = $xml->photosets->photoset;
        return $sets;
    }
}
