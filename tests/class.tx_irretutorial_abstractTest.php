<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Oliver Hader <oliver@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Generic test helpers.
 *
 * @author Oliver Hader <oliver@typo3.org>
 */
abstract class tx_irretutorial_abstractTest extends tx_phpunit_database_testcase {
	const VALUE_TimeStamp = 1250000000;

	/**
	 * @var boolean
	 */
	private $hasDatabase = FALSE;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * Gets the path to the test directory.
	 *
	 * @return string
	 */
	protected function getPath() {
		if (!isset($this->path)) {
			$this->path = t3lib_extMgm::extPath('irre_tutorial') . 'tests/';
		}

		return $this->path;
	}

	/**
	 * Initializes a test database.
	 *
	 * @return resource
	 */
	protected function initializeDatabase() {
		$this->hasDatabase = $this->createDatabase();

		if ($this->hasDatabase === TRUE) {
			$database = $this->useTestDatabase();

			$this->importStdDB();
			$this->importExtensions(array('cms', 'version', 'irre_tutorial'));

			return $database;
		} else {
			$this->fail('No test database available');
		}
	}

	/**
	 * Purges the test database.
	 *
	 * @return void
	 */
	protected function purgeDatabase() {
		if ($this->hasDatabase === TRUE) {
			$this->dropDatabase();
		}
	}
}
