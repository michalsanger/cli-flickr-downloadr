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
		$seconds = $time%60;
		$minutes = min(array(59, floor($time/60)));
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
