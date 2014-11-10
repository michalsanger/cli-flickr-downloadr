<?php

namespace FlickrDownloadr\Command;

use \FlickrDownloadr\Photoset;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotosetList extends Command
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
            ->setName('photoset:list')
            ->setDescription('List of photosets')
            ->addOption('lines', 'l', InputOption::VALUE_OPTIONAL, 'Number of photosets returned', 20)
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'If set, all photosets will be returned')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sets = $this->getPhotosets($input);
        $output->writeln('<info>Number of photosets: ' . count($sets) . '</info>');

        $table = new Table($output);
        $table->setHeaders(array('ID', 'Title', 'Photos'));
        foreach ($sets as $set) {
            $table->addRow(array($set->getId(), $set->getTitle(), $set->getPhotos()));
        }
        $table->render();
    }

    /**
     * @param InputInterface $input
     * @return \FlickrDownloadr\Photoset\Photoset[];
     */
    private function getPhotosets(InputInterface $input)
    {
        // TODO: refactor into facade
        $lines = $input->getOption('lines');
        if (!is_numeric($lines)) {
            $lines = 20;
        }
        $params = array(
            'page' => 1,
            'per_page' => (int)$lines,
        );
        if ($input->getOption('all')) {
            unset($params['per_page']);
        }

        $response = $this->flickrApi->call('flickr.photosets.getList', $params);
        $setsData = $response['photosets']['photoset'];
        $photosets = array();
        foreach ($setsData as $setData) {
            $photosets[] = new Photoset\Photoset($setData);
        }
        return $photosets;
    }
}
