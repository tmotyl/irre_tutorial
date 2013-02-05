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
 * Testcase for 1:n csv relations.
 *
 * @author Oliver Hader <oliver@typo3.org>
 */
class tx_irretutorial_1ncsvWorkspacesTest extends tx_irretutorial_AbstractWorkspaces {
	const TABLE_Hotel = 'tx_irretutorial_1ncsv_hotel';
	const TABLE_Offer = 'tx_irretutorial_1ncsv_offer';
	const TABLE_Price = 'tx_irretutorial_1ncsv_price';

	const FIELD_Hotel_Offers = 'offers';
	const FIELD_Offers_Prices = 'prices';

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->importDataSet($this->getPath() . 'fixtures/data_1ncsv.xml');
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
			// prices 1,2 are children of offer 1
			// price 3 is child of offer 2
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
					self::FIELD_Offers_Prices => $this->getWorkpaceVersionId(self::TABLE_Price, 1) . ',' . $this->getWorkpaceVersionId(self::TABLE_Price, 2),
				),
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 2,
					't3_origuid' => 2,
					self::FIELD_Offers_Prices => $this->getWorkpaceVersionId(self::TABLE_Price, 3),
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
			self::TABLE_Offer => '2',
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
					self::FIELD_Offers_Prices => '1,2',
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					't3ver_id' => 0,
					self::FIELD_Offers_Prices => '3',
				),
			)
		);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$liveElements = array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '1',
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
					self::FIELD_Offers_Prices => $this->getWorkpaceVersionId(self::TABLE_Price, 1) . ',' . $this->getWorkpaceVersionId(self::TABLE_Price, 2),
				),
				array(
					'tableName' => self::TABLE_Offer,
					't3ver_oid' => 2,
					't3_origuid' => 2,
					't3ver_id' => 1,
					self::FIELD_Offers_Prices => $versionizedPriceId,
				),
			)
		);
	}

	/****************************************************************
	 * COPY Behaviour
	 ****************************************************************/

	/**
	 * @test
	 */
	public function liveRecordsAreCopied() {
		$tceMain = $this->simulateCommand(
			self::COMMAND_Copy,
			-1,
			array(
				self::TABLE_Hotel => 1
			)
		);

		$placeholderHotelId = $tceMain->copyMappingArray_merged[self::TABLE_Hotel][1];
		$versionizedHotelId = $tceMain->getAutoVersionId(self::TABLE_Hotel, $placeholderHotelId);

		$this->assertGreaterThan($placeholderHotelId, $versionizedHotelId);

		$placeholderOfferIdA = $tceMain->copyMappingArray_merged[self::TABLE_Offer][1];
		$placeholderOfferIdB = $tceMain->copyMappingArray_merged[self::TABLE_Offer][2];
		$placeholderPriceId = $tceMain->copyMappingArray_merged[self::TABLE_Price][3];

		$this->assertGreaterThan(0, $placeholderOfferIdA, 'Seems like child reference have not been considered');
		$this->assertGreaterThan(0, $placeholderOfferIdB, 'Seems like child reference have not been considered');
		$this->assertGreaterThan(0, $placeholderPriceId, 'Seems like child reference have not been considered');

		$versionizedOfferIdA = $tceMain->getAutoVersionId(self::TABLE_Offer, $placeholderOfferIdA);
		$versionizedOfferIdB = $tceMain->getAutoVersionId(self::TABLE_Offer, $placeholderOfferIdB);
		$versionizedPriceId = $tceMain->getAutoVersionId(self::TABLE_Price, $placeholderPriceId);

		/**
		 * Placeholder (Live)
		 */

		$this->assertRecords(
			array(
				self::TABLE_Hotel => array(
					$placeholderHotelId => array(
						'pid' => self::VALUE_Pid,
						't3ver_wsid' => self::VALUE_WorkspaceId,
						't3ver_state' => 1,
					),
					$versionizedHotelId => array(
						'pid' => -1,
						't3ver_wsid' => self::VALUE_WorkspaceId,
						't3ver_state' => -1,
						self::FIELD_Hotel_Offers => $versionizedOfferIdA . ',' . $versionizedOfferIdB,
					),
				),
				self::TABLE_Offer => array(
					$placeholderOfferIdA => array(
						'pid' => self::VALUE_Pid,
						't3ver_wsid' => self::VALUE_WorkspaceId,
					),
					$placeholderOfferIdB => array(
						'pid' => self::VALUE_Pid,
						't3ver_wsid' => self::VALUE_WorkspaceId,
					),
				),
				self::TABLE_Price => array(
					$placeholderPriceId => array(
						'pid' => self::VALUE_Pid,
						't3ver_wsid' => self::VALUE_WorkspaceId,
					),
				),
			)
		);

		/**
		 * Workspace (Version)
		 */

		$this->assertChildren(
			self::TABLE_Hotel, $versionizedHotelId, self::FIELD_Hotel_Offers,
			array(
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => $versionizedOfferIdA,
					'pid' => -1,
					't3ver_id' => 1,
					't3ver_oid' => $placeholderOfferIdA,
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => $versionizedOfferIdB,
					'pid' => -1,
					't3ver_id' => 1,
					't3ver_oid' => $placeholderOfferIdB,
				),
			)
		);

		$this->assertChildren(
			self::TABLE_Offer, $versionizedOfferIdB, self::FIELD_Offers_Prices,
			array(
				array(
					'tableName' => self::TABLE_Price,
					'uid' => $versionizedPriceId,
					'pid' => -1,
					't3ver_id' => 1,
					't3ver_oid' => $placeholderPriceId,
				),
			)
		);

		$this->assertReferenceIndex(
			array(
				$this->combine(self::TABLE_Hotel, $versionizedHotelId, 'offers') => array(
					$this->combine(self::TABLE_Offer, $versionizedOfferIdA),
					$this->combine(self::TABLE_Offer, $versionizedOfferIdB),
				),
				$this->combine(self::TABLE_Offer , $versionizedOfferIdB, 'prices') => array(
					$this->combine(self::TABLE_Price, $versionizedPriceId),
				),
			)
		);
	}

	/****************************************************************
	 * PUBLISH/SWAP/CLEAR Behaviour
	 ****************************************************************/

	/**
	 * @return void
	 * @test
	 */
	public function isChildPublishedSeparatelyIfParentIsNotVersionized() {
		$childElements = array(
			self::TABLE_Offer => '2',
		);
		$this->simulateEditing($childElements);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommandByStructure(array(
			self::TABLE_Price => array(
				'3' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedPriceId,
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
			self::TABLE_Offer => array(
				'2' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedOfferId,
						'notificationAlternativeRecipients' => array(),
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
					't3_origuid' => 0,
					't3ver_id' => 0,
					self::FIELD_Offers_Prices => '1,2',
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					't3ver_oid' => 0,
					't3_origuid' => 2,
					't3ver_id' => 1, // it was pubslished
					't3ver_label' => 'Auto-created for WS #' . self::VALUE_WorkspaceId,
					self::FIELD_Offers_Prices => '3',
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
			self::TABLE_Offer => '2',
		);
		$this->simulateEditing($childElements);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommandByStructure(array(
			self::TABLE_Price => array(
				'3' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedPriceId,
						'swapIntoWS' => 1,
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
			self::TABLE_Offer => array(
				'2' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedOfferId,
						'swapIntoWS' => 1,
						'notificationAlternativeRecipients' => array(),
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
					't3_origuid' => 0,
					't3ver_id' => 0,
					self::FIELD_Offers_Prices => '1,2',
				),
				array(
					'tableName' => self::TABLE_Offer,
					'uid' => 2,
					't3ver_oid' => 0,
					't3_origuid' => 2,
					't3ver_id' => 1, // it was pubslished
					't3ver_label' => 'Auto-created for WS #' . self::VALUE_WorkspaceId,
					self::FIELD_Offers_Prices => '3',
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

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommandByStructure(array(
			self::TABLE_Price => array(
				'3' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedPriceId,
						'swapIntoWS' => 1,
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
			self::TABLE_Offer => array(
				'2' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedOfferId,
						'swapIntoWS' => 1,
						'notificationAlternativeRecipients' => array(),
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

		$this->getCommandMapAccess(1);

		// Swap to live:
		$this->simulateCommandByStructure(array(
			self::TABLE_Hotel => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedHotelId,
						'swapIntoWS' => 1,
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
		));

		$commandMap = $this->getCommandMap()->get();

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
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
		));

		$this->getCommandMapAccess(1);

		// Swap back to workspace:
		$this->simulateCommandByStructure(array(
			self::TABLE_Hotel => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedHotelId,
						'swapIntoWS' => 1,
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
		));

		$commandMap = $this->getCommandMap()->get();

		$this->assertTrue(isset($commandMap[self::TABLE_Hotel][1]['version']), self::TABLE_Hotel . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Offer][1]['version']), self::TABLE_Offer . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Offer][2]['version']), self::TABLE_Offer . ':2 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][1]['version']), self::TABLE_Price . ':1 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][2]['version']), self::TABLE_Price . ':2 is not set.');
		$this->assertTrue(isset($commandMap[self::TABLE_Price][3]['version']), self::TABLE_Price . ':3 is not set.');
	}

	/*
	 * Removing child records
	 */

	/**
	 * Live version will be versionized, but one child branch is removed.
	 *
	 * @return void
	 * @test
	 */
	public function areChildRecordsConsideredToBeRemovedOnEditingParent() {
		$this->simulateByStructure(
			$this->getElementStructureForEditing(array(
				self::TABLE_Hotel => '1',
			)),
			$this->getElementStructureForCommands(self::COMMAND_Delete, 1, array(
				self::TABLE_Offer => '2',
			))
		);

		$this->assertHasDeletePlaceholder(array(
			self::TABLE_Offer => '2',
			self::TABLE_Price => '3',
		));
	}

	/**
	 * Live version will be versionized, but one child branch is removed.
	 *
	 * @return void
	 * @test
	 */
	public function areChildRecordsConsideredToBeRemovedOnEditingParentAndChildren() {
		$this->simulateByStructure(
			$this->getElementStructureForEditing(array(
				self::TABLE_Hotel => '1',
				self::TABLE_Offer => '2',
			)),
			$this->getElementStructureForCommands(self::COMMAND_Delete, 1, array(
				self::TABLE_Offer => '2',
			))
		);

		$this->assertHasDeletePlaceholder(array(
			self::TABLE_Offer => '2',
			self::TABLE_Price => '3',
		));
	}

	/**
	 * Versionized version will be modifed and one child branch is removed.
	 *
	 * @return void
	 * @test
	 */
	public function areChildRecordsConsideredToBeRevertedOnEditing() {
		$this->versionizeAllChildrenWithParent();

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 3);

		$this->simulateCommand(self::COMMAND_Delete, 1, array(self::TABLE_Offer => $versionizedOfferId));

		$this->assertIsDeleted(array(
			self::TABLE_Offer => $versionizedOfferId,
			self::TABLE_Price => $versionizedPriceId,
		));
	}

	/**
	 * @return void
	 * @test
	 */
	public function areNestedChildRecordsConsideredToBeRemovedOnDirectRemoval() {
		$this->simulateCommand(self::COMMAND_Delete, 1, array(self::TABLE_Offer => 2));

		$this->assertHasDeletePlaceholder(array(
			self::TABLE_Offer => '2',
			self::TABLE_Price => '3',
		));
	}

	/**
	 * Test whether elements that are reverted in the workspace module
	 * also trigger the reverting of child records.
	 *
	 * @return void
	 * @test
	 */
	public function areChildRecordsRevertedOnRevertingTheRelativeRemovedParent() {
		$this->setWorkspacesConsiderReferences(TRUE);

		$this->simulateByStructure(
			$this->getElementStructureForEditing(array(
				self::TABLE_Hotel => '1',
				self::TABLE_Offer => '2',
			)),
			$this->getElementStructureForCommands(self::COMMAND_Delete, 1, array(
				self::TABLE_Offer => '2',
			))
		);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2, self::VALUE_WorkspaceId, TRUE);

		$this->simulateCommandByStructure(array(
			self::TABLE_Offer => array(
				$versionizedOfferId => array(
					'version' => array(
						'action' => self::COMMAND_Version_Clear,
					)
				)
			),
		));

		$this->assertWorkspaceVersions(array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '1',
			self::TABLE_Price => '1,2',
		));

		$this->assertFalse($this->getWorkpaceVersionId(self::TABLE_Offer, 2, self::VALUE_WorkspaceId, TRUE));
		$this->assertFalse($this->getWorkpaceVersionId(self::TABLE_Price, 3, self::VALUE_WorkspaceId, TRUE));
	}

	/**
	 * Test whether elements that are reverted in the workspace module
	 * also trigger the reverting of child records.
	 *
	 * @return void
	 * @test
	 */
	public function areChildRecordsRevertedOnRevertingMultipleElements() {
		$this->setWorkspacesConsiderReferences(TRUE);

		$this->simulateByStructure(
			$this->getElementStructureForEditing(array(
				self::TABLE_Hotel => '1',
				self::TABLE_Offer => '2',
			)),
			$this->getElementStructureForCommands(self::COMMAND_Delete, 1, array(
				self::TABLE_Offer => '2',
			))
		);

		$versionizedOfferId = $this->getWorkpaceVersionId(self::TABLE_Offer, 2, self::VALUE_WorkspaceId, TRUE);
		$versionizedPriceId = $this->getWorkpaceVersionId(self::TABLE_Price, 1);

		$this->simulateCommandByStructure(array(
			self::TABLE_Offer => array(
				$versionizedOfferId => array(
					'version' => array(
						'action' => self::COMMAND_Version_Clear,
					)
				)
			),
			self::TABLE_Price => array(
				$versionizedPriceId => array(
					'version' => array(
						'action' => self::COMMAND_Version_Clear,
					)
				)
			),
		));

		$this->assertWorkspaceVersions(array(
			self::TABLE_Hotel => '1',
			self::TABLE_Offer => '1',
			self::TABLE_Price => '2',
		));

		$this->assertFalse($this->getWorkpaceVersionId(self::TABLE_Offer, 2, self::VALUE_WorkspaceId, TRUE));
		$this->assertFalse($this->getWorkpaceVersionId(self::TABLE_Price, 3, self::VALUE_WorkspaceId, TRUE));
		$this->assertFalse($this->getWorkpaceVersionId(self::TABLE_Price, 1, self::VALUE_WorkspaceId, TRUE));
	}

	/**
	 * Tests whether records marked to be deleted in a workspace
	 * are really removed if they are published.
	 *
	 * @return void
	 * @test
	 */
	public function areParentAndChildRecordsRemovedOnPublishingDeleteAction() {
		$this->setWorkspacesConsiderReferences(TRUE);

		$this->simulateByStructure(
			array(),
			$this->getElementStructureForCommands(self::COMMAND_Delete, 1, array(
				self::TABLE_Hotel => '1',
			))
		);

		$versionizedHotelId = $this->getWorkpaceVersionId(self::TABLE_Hotel, 1, self::VALUE_WorkspaceId, TRUE);

		// Swap to live:
		$this->simulateCommandByStructure(array(
			self::TABLE_Hotel => array(
				'1' => array(
					'version' => array(
						'action' => self::COMMAND_Version_Swap,
						'swapWith' => $versionizedHotelId,
						'notificationAlternativeRecipients' => array(),
					)
				)
			),
		));

		$this->assertRecords(
			array(
				self::TABLE_Hotel => array(
					1 => array('deleted' => '1',),
				),
				self::TABLE_Offer => array(
					1 => array('deleted' => '1',),
					2 => array('deleted' => '1',),
				),
				self::TABLE_Price => array(
					1 => array('deleted' => '1',),
					2 => array('deleted' => '1',),
					3 => array('deleted' => '1',),
				),
			),
			self::VALUE_WorkspaceIdIgnore
		);
	}
}

?>