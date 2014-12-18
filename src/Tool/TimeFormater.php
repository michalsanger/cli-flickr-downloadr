<?php

namespace FlickrDownloadr\Tool;

class TimeFormater
{
	/**
	 * @param int $time Seconds
	 * @return string
	 */
	public function format($time)
	{
		if ($time === 0) {
			return '0s';
		}
		$seconds = $time%60;
		$minutes = floor(($time%3600)/60);
		$hours = floor($time/3600);
		
		$times = array();
		if ($hours > 0) {
			$times[] = $hours . 'h';
		}
		if ($minutes > 0) {
			$times[] = $minutes . 'm';
		}
		if ($seconds > 0) {
			$times[] = $seconds . 's';
		}
		return implode(' ', $times);
	}
}
