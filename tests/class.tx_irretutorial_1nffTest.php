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
 * Testcase for 1:n ff relations.
 *
 * @author Oliver Hader <oliver@typo3.org>
 */
class tx_irretutorial_1nffTest extends tx_irretutorial_abstractTest {
	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->initializeDatabase();
		$this->importDataSet($this->getPath() . 'fixtures/data_1nff.xml');
	}

	/**
	 * Tears down this test case.
	 *
	 * @return void
	 */
	public function tearDown() {
		$this->dropDatabase();
	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenVersonizedWithParent() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function areExistingChildVersionsUsedOnParentVersioning() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildPublishedSeparately() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenPublishedWithParent() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildSwappedSeparately() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenSwappedWithParent() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildDoubleSwappingSeparately() {

	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenDoubleSwapping() {

	}
}
