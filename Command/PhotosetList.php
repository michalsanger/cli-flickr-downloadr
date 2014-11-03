<?php

namespace FlickrDownloadr\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('List of photosets')
            ->addOption(
                'rows',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Number of photosets returned',
                20
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'If set, all photosets will be returned'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->flickrApi = $this->getFlickrApi();
        $sets = $this->getPhotosets($input);
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
     * @param InputInterface $input
     * @return \SimpleXMLElement[]
     */
    private function getPhotosets(InputInterface $input)
    {
        $rows = $input->getOption('rows');
        if (!is_numeric($rows)) {
            $rows = 20;
        }
        $params = array(
            'page' => 1,
            'per_page' => (int)$rows,
        );
        if ($input->getOption('all')) {
            unset($params['per_page']);
        }

        $xml = $this->flickrApi->call('flickr.photosets.getList', $params);
        $sets = $xml->photosets->photoset;
        return $sets;
    }
}
