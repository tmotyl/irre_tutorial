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
	const TABLE_Hotel = 'tx_irretutorial_1nff_hotel';
	const TABLE_Offer = 'tx_irretutorial_1nff_offer';
	const TABLE_Price = 'tx_irretutorial_1nff_price';

	const FIELD_Hotel_Offers = 'offers';
	const FIELD_Offers_Prices = 'prices';

	const FIELD_Offers_Parent = 'parentid';
	const FIELD_Prices_Parent = 'parentid';

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();
		$this->initializeDatabase();
		$this->importDataSet($this->getPath() . 'fixtures/data_1nff.xml');
	}

	/**
	 * Tears down this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		parent::tearDown();
		$this->dropDatabase();
	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenVersonizedWithParent() {
		$liveElements = array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '1,2',
			self::TABLE_Price => '1,2,3',
		);

		$this->simulateEditing($liveElements);
		$this->assertWorkspaceVersions($liveElements);

		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);

		$this->assertWorkspaceChildren(
			self::TABLE_Hotel, $versionizedHotelId, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 1,
					't3_origuid' => 1,
					self::FIELD_Offers_Parent => $versionizedHotelId,
				),
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 2,
					't3_origuid' => 2,
					self::FIELD_Offers_Parent => $versionizedHotelId,
				)
			)
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function areExistingChildVersionsUsedOnParentVersioning() {
		$childElements = array(
			self::TABLE_Offer => '1',
		);

		$this->simulateEditing($childElements);
		$this->assertWorkspaceVersions($childElements);

		$this->assertWorkspaceChildren(
			self::TABLE_Hotel, 1, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 1,
					self::FIELD_Offers_Parent => 1,
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					self::FIELD_Offers_Parent => 1,
				)
			)
		);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 1);

		$liveElements = array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '2',
			self::TABLE_Price => '1,2,3',
		);
		$liveElementsToBeVersionized = $liveElements;
		$liveElementsToBeVersionized[self::TABLE_Offer] .= ',' . $versionizedOfferId;

		$this->simulateEditing($liveElementsToBeVersionized);
		$this->assertWorkspaceVersions($liveElements);

		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);

		$this->assertWorkspaceChildren(
			self::TABLE_Hotel, $versionizedHotelId, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 1,
					't3_origuid' => 1,
					self::FIELD_Offers_Parent => $versionizedHotelId,
				),
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 2,
					't3_origuid' => 2,
					self::FIELD_Offers_Parent => $versionizedHotelId,
				)
			)
		);
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
