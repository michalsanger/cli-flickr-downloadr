<?php

namespace FlickrDownloadr\Tool;

class FilesizeFormater
{
	/**
	 * @param int $bytes
	 * @param int $precision
	 * @return string
	 */
	public function format($bytes, $precision = 2)
	{
		$bytes = round($bytes);
		$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
		foreach ($units as $unit) {
			if (abs($bytes) < 1024 || $unit === end($units)) {
				break;
			}
			$bytes = $bytes / 1024;
		}
		return round($bytes, $precision) . $unit;
	}
}
