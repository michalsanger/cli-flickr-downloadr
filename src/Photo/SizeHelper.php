<?php

namespace FlickrDownloadr\Photo;

class SizeHelper
{
	const NAME_SQUARE = 'square';
	const NAME_SQUARE_LARGE = 'square_large';
	const NAME_THUMBNAIL = 'thumbnail';
	const NAME_SMALL = 'small';
	const NAME_SMALL_320 = 'small_320';
	const NAME_MEDIUM = 'medium';
	const NAME_MEDIUM_640 = 'medium_640';
	const NAME_MEDIUM_800 = 'medium_800';
	const NAME_LARGE = 'large';
	const NAME_LARGE_1600 = 'large_1600';
	const NAME_LARGE_2048 = 'large_2048';
	const NAME_ORIGINAL = 'original';
	
	private $codes = array(
		self::NAME_SQUARE => "sq",
		self::NAME_SQUARE_LARGE => "q",
		self::NAME_THUMBNAIL => "t",
		self::NAME_SMALL => "s",
		self::NAME_SMALL_320 => "n",
		self::NAME_MEDIUM => "m",
		self::NAME_MEDIUM_640 => "z",
		self::NAME_MEDIUM_800 => "c",
		self::NAME_LARGE => "l",
		self::NAME_LARGE_1600 => "h",
		self::NAME_LARGE_2048 => "k",
		self::NAME_ORIGINAL => "o",
	);
	
	/**
	 * @param string $sizeName
	 * @param string $default
	 * @return string
	 */
	public function validate($sizeName, $default = self::NAME_ORIGINAL)
	{
		if(array_key_exists($sizeName, $this->codes)) {
			return $sizeName;
		} else {
			return $default;
		}
	}
	
	/**
	 * @param string $sizeName
	 * @return string
	 */
	public function getCode($sizeName)
	{
		if(!array_key_exists($sizeName, $this->codes)) {
			return;
		}
		return $this->codes[$sizeName];
	}
}