<?php

namespace FlickrDownloadr\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotosetList extends Command
{
    /**
     * Number of photosets returned
     * @var int
     */
    const DEFAULT_LIMIT = 20;

    /**
     * @var \FlickrDownloadr\Photoset\Repository
     */
    private $photosetRepository;
    
    function __construct(\FlickrDownloadr\Photoset\Repository $photosetRepository)
    {
        $this->photosetRepository = $photosetRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('photoset:list')
            ->setDescription('List of photosets')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of photosets returned', static::DEFAULT_LIMIT)
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'If set, all photosets will be returned')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int)$input->getOption('limit');
        $all = $input->getOption('all');
        $sets = $this->getPhotosets($limit, $all);

        $output->writeln('<info>Number of photosets: ' . count($sets) . '</info>');

        $table = new Table($output);
        $table->setHeaders(array('ID', 'Title', 'Items'));
        foreach ($sets as $set) {
            $table->addRow(array($set->getId(), $set->getTitle(), $set->getPhotos() + $set->getVideos()));
        }
        $table->render();
    }

    /**
     * @param int $limit
     * @param boolean $all
     * @return \FlickrDownloadr\Photoset\Photoset[];
     */
    private function getPhotosets($limit, $all)
    {
        if ($limit <= 0) {
            $limit = static::DEFAULT_LIMIT;
        }
        if ($all === TRUE) {
            $limit = NULL;
        }
        return $this->photosetRepository->findAll($limit);
    }
}
