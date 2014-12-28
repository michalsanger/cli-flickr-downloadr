<?php

namespace FlickrDownloadr\Console;

class Application extends \Symfony\Component\Console\Application
{
	/** @var string */
	private $build;

	/**
	 * @param string $name
	 * @param string $version
	 * @param string $build
	 */
	public function __construct($name, $version, $build)
	{
		parent::__construct($name, $version);
		$this->build = $build;
	}

	/**
	 * @override
	 */
	public function getLongVersion()
	{
		$version = parent::getLongVersion();
		if (strlen($this->getBuild()) > 0) {
			$build = sprintf('build <comment>%s</comment>', $this->getBuild());
			$version .= ' ' . $build;
		}
		return $version;
	}
	
	/**
	 * @return string
	 */
	public function getBuild()
	{
		return $this->build;
	}
}