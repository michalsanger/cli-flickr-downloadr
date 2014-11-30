<?php

namespace FlickrDownloadr\Photoset;

class DirnameCreator
{
	/**
	 * @param \FlickrDownloadr\Photoset\Photoset $photoset
	 * @param string $mask
	 * @param boolean $noTitleSlug
	 * @return string
	 */
	public function create(Photoset $photoset, $mask, $noTitleSlug = FALSE)
	{
		$title = $photoset->getTitle();
		if (!$noTitleSlug) {
			$title = \Nette\Utils\Strings::webalize($title);
		}
		return strtr($mask, array(
			'%id%' => $photoset->getId(),
			'%title%' => $title,
			'%year%' => date('Y', $photoset->getDateCreate()),
			'%month%' => date('m', $photoset->getDateCreate()),
			'%day%' => date('d', $photoset->getDateCreate()),
		));
	}
}