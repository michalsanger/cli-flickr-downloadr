<?php

namespace FlickrDownloadr\Photoset;

class DirnameCreator
{
	/**
	 * @param \FlickrDownloadr\Photoset\Photoset $photoset
	 * @param string $mask
	 * @return string
	 */
	public function create(Photoset $photoset, $mask)
	{
		return strtr($mask, array(
			'%title%' => strtolower($photoset->getTitle()),
			'%year%' => date('Y', $photoset->getDateCreate()),
			'%month%' => date('m', $photoset->getDateCreate()),
			'%day%' => date('d', $photoset->getDateCreate()),
		));
	}
}