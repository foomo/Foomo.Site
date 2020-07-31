<?php

namespace Foomo\Site\Jobs;

use Foomo\Jobs\AbstractJob;
use Foomo\Site\Module;

class UpdateContentServerJob extends AbstractJob
{
	const LOCK = 'contentServerUpdate';
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $executionRule = '* * * * *';

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return 'Update contentserver if necessary';
	}

	/**
	 *
	 */
	public function run()
	{
		ini_set('max_execution_time', 600);
		ini_set('memory_limit', '1G');
		if (\Foomo\Site\Module::getSiteConfig()->updateContentServerAfterContentAdapterUpdate && \Foomo\Site\Module::getSiteConfig()->updateContentServerAfterContentAdapterUpdate) {
			$this->updateContentServer();
			UpdateContentServerJob::setUpdateFlag(false);

		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private methods
	// --------------------------------------------------------------------------------------------

	public function updateContentServer()
	{
		$service = new \Foomo\Site\Service\ContentServer();
		$result = $service->update();
		\Foomo\Utils::appendToPhpErrorLog(__CLASS__ . ' contentserver update ' . json_encode($result));
	}


	public static function setUpdateFlag($flag)
	{
		\Foomo\Lock::lock(self::LOCK);

		if ($flag) {
			$success = false;
			if (!file_exists(self::getUpdateFlagFile())) {
				$success = \file_put_contents(self::getUpdateFlagFile(), '' . time());
			} else {
				$success = true;
			}
		} else {
			//remove
			if (file_exists(self::getUpdateFlagFile())) {
				$success = unlink(self::getUpdateFlagFile());
			} else {
				$success = true;
			}
		}
		\Foomo\Lock::release(self::LOCK);
		return $success;
	}

	private static function getUpdateFlag()
	{
		\Foomo\Lock::lock(self::LOCK);
		$ret = false;
		if (file_exists(self::getUpdateFlagFile())) {
			$ret = true;
		}
		\Foomo\Lock::release(self::LOCK);
		return $ret;
	}


	private static function getUpdateFlagFile()
	{
		return Module::getVarDir() . DIRECTORY_SEPARATOR . 'contentServerUpdate.flag';
	}

}
