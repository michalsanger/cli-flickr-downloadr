<?php

namespace FlickrDownloadr\Photo;

class FilenameCreator
{
	/**
	 * @param \FlickrDownloadr\Photo\Photo $photo
	 * @param int $listOrder
	 * @param int $photosCount
	 * @param string $template
	 * @param string $sizeName
	 * @param boolean $noTitleSlug
	 * @return string
	 */
	public function create(Photo $photo, $listOrder, $photosCount, $template, $sizeName, $noTitleSlug = FALSE)
	{
		$title = $this->getTitle($photo, $noTitleSlug);
		$order = $this->getOrder($listOrder, $photosCount);
		$ext = $this->getExtension($photo);
		$size = $photo->getWidth() . 'x' . $photo->getHeight();
		$template .= '.' . $ext;

		return strtr($template, array(
			'%id%' => $photo->getId(),
			'%order%' => $order,
			'%title%' => $title,
			'%year%' => $photo->getDate()->format('Y'),
			'%month%' => $photo->getDate()->format('m'),
			'%day%' => $photo->getDate()->format('d'),
			'%date%' => $photo->getDate()->format('Y-m-d-H.i.s'),
			'%size%' => $size,
			'%sizeName%' => $sizeName,
			'%width%' => $photo->getWidth(),
			'%height%' => $photo->getHeight(),
		));
	}

	/**
	 * @param \FlickrDownloadr\Photo\Photo $photo
	 * @param boolean $noTitleSlug
	 * @return string
	 */
	private function getTitle(Photo $photo, $noTitleSlug)
	{
		$title = $photo->getTitle();
		if (!$noTitleSlug) {
			$title = \Nette\Utils\Strings::webalize($title);
		}
		return $title;
	}

	/**
	 * @param int $listOrder
	 * @param int $photosCount
	 * @return string
	 */
	private function getOrder($listOrder, $photosCount)
	{
		$padLength = strlen((string)$photosCount);
        $order = str_pad($listOrder, $padLength, '0', STR_PAD_LEFT);
		return $order;
	}
	
	/**
	 * @param \FlickrDownloadr\Photo\Photo $photo
	 * @return string
	 */
	private function getExtension(Photo $photo)
	{
		$url = $photo->getUrl();
		return pathinfo($url, PATHINFO_EXTENSION);
	}
}