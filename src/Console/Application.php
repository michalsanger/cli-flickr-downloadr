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
		$banner = $this->getBanner();
		if (strlen($this->getBuild()) > 0) {
			$build = sprintf('build <comment>%s</comment>', $this->getBuild());
			$banner .= "\n" . $build;
		}
		return $banner;
	}

	/**
	 * @return string
	 */
	public function getBuild()
	{
		return $this->build;
	}

	private function getBanner()
	{
		$bannerTpl = <<<str
 Flickr                 __             __   
  / _ \___ _    _____  / /__  ___ ____/ /___
 / // / _ \ |/|/ / _ \/ / _ \/ _ `/ _  / __/
/____/\___/__,__/_//_/_/\___/\_,_/\_,_/_/ <comment>{version}</comment>
str;
		$banner = strtr($bannerTpl, array(
			'Flickr' => '<info>Flickr</info>',
			'{version}' => $this->getVersion(),
		));
		return $banner;
	}
}