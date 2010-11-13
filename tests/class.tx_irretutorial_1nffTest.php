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
	 * Versionize all children with parent.
	 *
	 * @return array
	 */
	protected function versionizeAllChildrenWithParent() {
		$liveElements = array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '1,2',
			self::TABLE_Price => '1,2,3',
		);

		$this->simulateEditing($liveElements);

		return $liveElements;
	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenVersonizedWithParent() {
		$liveElements = $this->versionizeAllChildrenWithParent();
		$this->assertWorkspaceVersions($liveElements);

		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);

			// Workspace:
		$this->assertChildren(
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
				),
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

			// Live:
		$this->assertChildren(
			self::TABLE_Hotel, 1, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 1,
					't3ver_id' => 0,
					self::FIELD_Offers_Parent => 1,
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					't3ver_id' => 0,
					self::FIELD_Offers_Parent => 1,
				),
			)
		);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 1);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$liveElements = array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '2',
			self::TABLE_Price => '1,2',
		);
		$liveElementsToBeVersionized = $liveElements;
		$liveElementsToBeVersionized[self::TABLE_Offer] .= ',' . $versionizedOfferId;
		$liveElementsToBeVersionized[self::TABLE_Price] .= ',' . $versionizedPriceId;

		$this->simulateEditing($liveElementsToBeVersionized);
		$this->assertWorkspaceVersions($liveElements);

		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);

			// Workspace:
		$this->assertChildren(
			self::TABLE_Hotel, $versionizedHotelId, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 1,
					't3_origuid' => 1,
					't3ver_id' => 1,
					self::FIELD_Offers_Parent => $versionizedHotelId,
				),
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 2,
					't3_origuid' => 2,
					't3ver_id' => 1,
					self::FIELD_Offers_Parent => $versionizedHotelId,
				),
			)
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildPublishedSeparatelyIfParentIsNotVersionized() {
		$childElements = array(
			self::TABLE_Offer => '1',
		);
		$this->simulateEditing($childElements);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 1);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommandByStructure(array(
			self::TABLE_Price => array(
				'3' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedPriceId,
					)
				)
			),
			self::TABLE_Offer => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedOfferId,
					)
				)
			),
		));

			// Live:
		$this->assertChildren(
			self::TABLE_Hotel, 1, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 1,
					't3ver_oid' => 0,
					't3_origuid' => 1,
					't3ver_id' => 1, // it was pubslished
					't3ver_label' => 'Auto-created for WS #' . self::VALUE_WorkspaceId,
					self::FIELD_Offers_Parent => 1,
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					't3ver_oid' => 0,
					't3_origuid' => 0,
					't3ver_id' => 0,
					self::FIELD_Offers_Parent => 1,
				),
			)
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildPublishedSeparatelyIfParentIsVersionized() {
		$this->setExpectedLogEntries(1);

		$this->versionizeAllChildrenWithParent();

		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);
		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 1);

		$this->simulateVersionCommand(
			array(
				'action' => self::COMMAND_Version_Swap,
				'swapWith' => $versionizedOfferId,
			),
			array(
				self::TABLE_Offer => '1',
			)
		);

		$this->assertContains(
			'cannot be swapped or published independently, because it is related to other new or modified records',
			$this->getLastLogEntryMessage(),
			'Expected error was not reported.'
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildSwappedSeparatelyIfParentIsNotVersionized() {
		$childElements = array(
			self::TABLE_Offer => '1',
		);
		$this->simulateEditing($childElements);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 1);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommandByStructure(array(
			self::TABLE_Price => array(
				'3' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedPriceId,
						'swapIntoWS' => 1,
					)
				)
			),
			self::TABLE_Offer => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedOfferId,
						'swapIntoWS' => 1,
					)
				)
			),
		));

			// Live:
		$this->assertChildren(
			self::TABLE_Hotel, 1, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 1,
					't3ver_oid' => 0,
					't3_origuid' => 1,
					't3ver_id' => 1, // it was pubslished
					't3ver_label' => 'Auto-created for WS #' . self::VALUE_WorkspaceId,
					self::FIELD_Offers_Parent => 1,
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					't3ver_oid' => 0,
					't3_origuid' => 0,
					't3ver_id' => 0,
					self::FIELD_Offers_Parent => 1,
				),
			)
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function isChildSwappedSeparatelyIfParentIsVersionized() {
		$this->setExpectedLogEntries(2);

		$this->versionizeAllChildrenWithParent();

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 1);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommandByStructure(array(
			self::TABLE_Price => array(
				'3' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedPriceId,
						'swapIntoWS' => 1,
					)
				)
			),
			self::TABLE_Offer => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedOfferId,
						'swapIntoWS' => 1,
					)
				)
			),
		));

		$this->assertContains(
			'cannot be swapped or published independently, because it is related to other new or modified records',
			$this->getLastLogEntryMessage(),
			'Expected error was not reported.'
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenSwappedAutomaticallyIfParentIsSwapped() {
		$this->setWorkspacesConsiderReferences(TRUE);

		$this->versionizeAllChildrenWithParent();
		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);

		$this->getTceMainMock(TRUE, 1);

		// Swap to live:
		$this->simulateCommandByStructure(array(
			self::TABLE_Hotel => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedHotelId,
						'swapIntoWS' => 1,
					)
				)
			),
		));

		$commandMap = $this->tceMainCommandMap->get();

		$this->assertTrue(isset($commandMap[self::TABLE_Hotel][1]['version']), self::TABLE_Hotel . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Offer][1]['version']), self::TABLE_Offer . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Offer][2]['version']), self::TABLE_Offer . ':2 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][1]['version']), self::TABLE_Price . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][2]['version']), self::TABLE_Price . ':2 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][3]['version']), self::TABLE_Price . ':3 is not set.');
	}

	/**
	 * @return void
	 * @test
	 */
	public function areAllChildrenDoubleSwappedAutomaticallyIfParentIsSwapped() {
		$this->setWorkspacesConsiderReferences(TRUE);

		$this->versionizeAllChildrenWithParent();
		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1);

		// Swap to live:
		$this->simulateCommandByStructure(array(
			self::TABLE_Hotel => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedHotelId,
						'swapIntoWS' => 1,
					)
				)
			),
		));

		$this->getTceMainMock(TRUE, 1);

		// Swap back to workspace:
		$this->simulateCommandByStructure(array(
			self::TABLE_Hotel => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedHotelId,
						'swapIntoWS' => 1,
					)
				)
			),
		));

		$commandMap = $this->tceMainCommandMap->get();

		$this->assertTrue(isset($commandMap[self::TABLE_Hotel][1]['version']), self::TABLE_Hotel . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Offer][1]['version']), self::TABLE_Offer . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Offer][2]['version']), self::TABLE_Offer . ':2 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][1]['version']), self::TABLE_Price . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][2]['version']), self::TABLE_Price . ':2 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][3]['version']), self::TABLE_Price . ':3 is not set.');
	}
}
