<?php
namespace Foomo\Site;


use Foomo\Site\Jobs\UpdateContentServerJob;

class JobList implements \Foomo\Jobs\JobListInterface
{
	public static function getJobs()
	{
		$jobs = [
			UpdateContentServerJob::create()
		];
		return $jobs;
	}
}

