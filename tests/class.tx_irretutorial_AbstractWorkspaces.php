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
abstract class tx_irretutorial_AbstractWorkspaces extends tx_irretutorial_Abstract {
	const VALUE_TimeStamp = 1250000000;
	const VALUE_WorkspaceId = 9;

	const COMMAND_Version = 'version';
	const COMMAND_Version_New = 'new';
	const COMMAND_Version_Swap = 'swap';
	const COMMAND_Version_Flush = 'flush';
	const COMMAND_Version_Clear = 'clearWSID';

	/**
	 * @var integer
	 */
	private $modifiedTimeStamp;

	/**
	 * @var t3lib_TCEmain
	 */
	protected $tceMainMock;

	/**
	 * @var tx_version_tcemain
	 */
	protected $versionTceMainHookMock;

	/**
	 * @var t3lib_TCEmain_CommandMap
	 */
	protected $tceMainCommandMap;

	/**
	 * @var tx_version_tcemain_CommandMap
	 */
	protected $versionTceMainCommandMap;

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();

		$this->getBackendUser()->workspace = self::VALUE_WorkspaceId;
		$this->setWorkspacesConsiderReferences(FALSE);
	}

	/**
	 * Tears down this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		parent::tearDown();

		unset($this->tceMainMock);
		unset($this->tceMainCommandMap);

		unset($this->versionTceMainCommandMap);
		unset($this->versionTceMainHookMock);
	}

	/**
	 * Determines whether workspaces for IRRE are supported in this TYPO3 version.
	 * Most parts have been fixed in TYPO3 4.5.0 on in an inofficial side-branch
	 * (https://svn.typo3.org/TYPO3v4/Extensions/irre_tutorial/branches/TYPO3_4-4_workspaces)
	 *
	 * @return boolean
	 */
	protected function areWorkspacesSupported() {
		return (t3lib_div::int_from_ver(TYPO3_version) > 4004999 || class_exists('t3lib_TCEmain_CommandMap', TRUE));
	}

	/**
	 * Skips a test case if workspaces and IRRE are not fully supported by the current TYPO3 version.
	 *
	 * @return void
	 */
	protected function skipUnsupportedTest() {
		if ($this->areWorkspacesSupported() === FALSE) {
			$this->markTestSkipped(
				'The current TYPO3 version does not fully support Workspaces and IRRE. Either use TYPO3 4.5.0 or ' .
				'an INOFFICIAL branch from https://svn.typo3.org/TYPO3v4/Extensions/irre_tutorial/branches/TYPO3_4-4_workspaces/'
			);
		}
	}

	/**
	 * Gets a modified timestamp to ensure that a record is changed.
	 *
	 * @return integer
	 */
	protected function getModifiedTimeStamp() {
		if (!isset($this->modifiedTimeStamp)) {
			$this->modifiedTimeStamp = self::VALUE_TimeStamp + 100;
		}

		return $this->modifiedTimeStamp;
	}

	/**
	 * Initializes a test database.
	 *
	 * @return resource
	 */
	protected function initializeDatabase() {
		$hasDatabase = parent::initializeDatabase();

		if ($hasDatabase) {
			$this->importExtensions(array('version'));

			if ($this->areWorkspacesSupported()) {
				$this->importExtensions(array('workspaces'));
			}

			$this->importDataSet($this->getPath() . 'fixtures/data_sys_workspace.xml');
		}
	}

	/**
	 * Gets an element structure of tables and ids used to simulate editing with TCEmain.
	 *
	 * @param array $tables Table names with list of ids to be edited
	 * @return array
	 */
	protected function getElementStructureForEditing(array $tables) {
		$editStructure = array();

		foreach ($tables as $tableName => $idList) {
			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$editStructure[$tableName][$id] = array(
					'tstamp' => $this->getModifiedTimeStamp(),
				);
			}
		}

		return $editStructure;
	}

	/**
	 * @param  array $tables Table names with list of ids to be edited
	 * @return t3lib_TCEmain
	 */
	protected function simulateEditing(array $tables) {
		return $this->simulateEditingByStructure($this->getElementStructureForEditing($tables));
	}

	/**
	 * Simulates editing by using t3lib_TCEmain.
	 *
	 * @param  array $elements The datamap to be delivered to t3lib_TCEmain
	 * @return t3lib_TCEmain
	 */
	protected function simulateEditingByStructure(array $elements) {
		$tceMain = $this->getTceMain();
		$tceMain->start($elements, array());
		$tceMain->process_datamap();

		return $tceMain;
	}

	/**
	 * @param string $command
	 * @param array $tables
	 * @return t3lib_TCEmain
	 */
	protected function simulateVersionCommand(array $commands, array $tables) {
		return $this->simulateCommand(
			self::COMMAND_Version,
			$commands,
			$tables
		);
	}

	/**
	 * Simulates editing and command by structure.
	 *
	 * @param array $editingElements
	 * @param array $commandElements
	 * @return t3lib_TCEmain
	 */
	protected function simulateByStructure(array $editingElements, array $commandElements) {
		$tceMain = $this->getTceMain();
		$tceMain->start($editingElements, $commandElements);
		$tceMain->process_datamap();
		$tceMain->process_cmdmap();

		return $tceMain;
	}

	/**
	 * Asserts that accordant workspace version exist for live versions.
	 *
	 * @param  array $tables Table names with list of ids to be edited
	 * @param  integer $workspaceId Workspace to be used
	 * @return void
	 */
	protected function assertWorkspaceVersions(array $tables, $workspaceId = self::VALUE_WorkspaceId, $expected = TRUE) {
		foreach ($tables as $tableName => $idList) {
			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$workspaceVersion = t3lib_BEfunc::getWorkspaceVersionOfRecord($workspaceId, $tableName, $id);
				$this->assertTrue(
					($expected ? $workspaceVersion !== FALSE : $workspaceVersion === FALSE),
					'Workspace version for ' . $tableName . ':' . $id . ($expected ? ' not' : '') . ' availabe'
				);
			}
		}
	}

	/**
	 * Gets a t3lib_TCEmain mock.
	 *
	 * @param boolean $override Whether to override the instance in the getTceMain() method
	 * @param integer $expectsGetCommandMap (optional) Expects number of invokations to getCommandMap method
	 * @return void
	 * @see getTceMain
	 * @see getTceMainCommandMapCallback
	 */
	protected function getTceMainMock($override = FALSE, $expectsGetCommandMap = NULL) {
		$this->tceMainMock = $this->getMock('t3lib_TCEmain', array('getCommandMap'));

		if ($override) {
			$this->setTceMainOverride($this->tceMainMock);
		}

		if (is_integer($expectsGetCommandMap) && $expectsGetCommandMap >= 0) {
			$this->tceMainMock->expects($this->exactly($expectsGetCommandMap))->method('getCommandMap')
				->will($this->returnCallback(array($this, 'getTceMainCommandMapCallback')));
		} elseif (!is_null($expectsGetCommandMap)) {
			$this->fail('Expected invokation of getCommandMap must be integer >= 0.');
		}
	}

	/**
	 * Gets a tx_version_tcemain mock.
	 *
	 * @param integer $expectsGetCommandMap (optional) Expects number of invokations to getCommandMap method
	 * @return tx_version_tcemain
	 */
	protected function getVersionTceMainHookMock($expectsGetCommandMap = NULL) {
		$this->versionTceMainHookMock = $this->getMock('tx_version_tcemain', array('getCommandMap'));

		if (is_integer($expectsGetCommandMap) && $expectsGetCommandMap >= 0) {
			$this->versionTceMainHookMock->expects($this->exactly($expectsGetCommandMap))->method('getCommandMap')
				->will($this->returnCallback(array($this, 'getVersionTceMainCommandMapCallback')));
		} elseif (!is_null($expectsGetCommandMap)) {
			$this->fail('Expected invokation of getCommandMap must be integer >= 0.');
		}

		return $this->versionTceMainHookMock;
	}

	/**
	 * Gets access to the command map.
	 *
	 * @param integer $expectsGetCommandMap Expects number of invokations to getCommandMap method
	 * @return void
	 */
	protected function getCommandMapAccess($expectsGetCommandMap) {
		if (t3lib_div::int_from_ver(TYPO3_version) <= 4004999) {
			$this->getTceMainMock(TRUE, $expectsGetCommandMap);
		} else {
			$hookReferenceString = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['version'];
			$GLOBALS['T3_VAR']['getUserObj'][$hookReferenceString] = $this->getVersionTceMainHookMock($expectsGetCommandMap);
		}
	}

	/**
	 * @param string $tableName
	 * @param integer $id
	 * @param integer $workspaceId
	 * @param boolean $directLookup
	 * @return boolean
	 */
	protected function getWorkpaceVersionId($tableName, $id, $workspaceId = self::VALUE_WorkspaceId, $directLookup = FALSE) {
		if ($directLookup) {
			$records = $this->getAllRecords($tableName);
			foreach ($records as $record) {
				if ($record['t3ver_wsid'] == $workspaceId && $record['t3ver_oid'] == $id) {
					return $record['uid'];
				}
			}
		} else {
			$workspaceVersion = t3lib_BEfunc::getWorkspaceVersionOfRecord($workspaceId, $tableName, $id);
			if ($workspaceVersion !== FALSE) {
				return $workspaceVersion['uid'];
			}
		}

		return FALSE;
	}

	/**
	 * Asserts the existence of a delete placeholder record.
	 *
	 * @param array $tables
	 * @return void
	 */
	protected function assertHasDeletePlaceholder(array $tables) {
		foreach ($tables as $tableName => $idList) {
			$records = $this->getAllRecords($tableName);

			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$failureMessage = 'Delete placeholder of "' . $tableName . ':' . $id . '"';
				$versionizedId = $this->getWorkpaceVersionId($tableName, $id);
				$this->assertTrue(isset($records[$versionizedId]), $failureMessage . ' does not exist');
				$this->assertEquals($id, $records[$versionizedId]['t3_origuid'], $failureMessage . ' has wrong relation to live workspace');
				$this->assertEquals($id, $records[$versionizedId]['t3ver_oid'], $failureMessage . ' has wrong relation to live workspace');
				$this->assertEquals(2, $records[$versionizedId]['t3ver_state'], $failureMessage . ' is not marked as DELETED');
				$this->assertEquals('DELETED!', $records[$versionizedId]['t3ver_label'], $failureMessage . ' is not marked as DELETED');
			}
		}
	}

	/**
	 * @param array $tables
	 * @return void
	 */
	protected function assertIsDeleted(array $tables) {
		foreach ($tables as $tableName => $idList) {
			$records = $this->getAllRecords($tableName);

			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$failureMessage = 'Workspaace version "' . $tableName . ':' . $id . '"';
				$this->assertTrue(isset($records[$id]), $failureMessage . ' does not exist');
				$this->assertEquals(0, $records[$id]['t3ver_state']);
				$this->assertEquals(1, $records[$id]['deleted']);
			}
		}
	}

	/**
	 * @param array $tables
	 * @return void
	 */
	protected function assertIsCleared(array $tables) {
		foreach ($tables as $tableName => $idList) {
			$records = $this->getAllRecords($tableName);

			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$failureMessage = 'Workspaace version "' . $tableName . ':' . $id . '"';
				$this->assertTrue(isset($records[$id]), $failureMessage . ' does not exist');
				$this->assertEquals(0, $records[$id]['t3ver_state'], $failureMessage . ' has wrong state value');
				$this->assertEquals(0, $records[$id]['t3ver_wsid'],  $failureMessage . ' is still in offline workspace');
				$this->assertEquals(-1, $records[$id]['pid'],  $failureMessage . ' has wrong pid value');
			}
		}
	}

	/**
	 * Sets the User TSconfig property options.workspaces.considerReferences.
	 *
	 * @param boolean $workspacesConsiderReferences
	 * @return void
	 */
	protected function setWorkspacesConsiderReferences($workspacesConsiderReferences = TRUE) {
		$this->getBackendUser()->userTS['options.']['workspaces.']['considerReferences'] = ($workspacesConsiderReferences ? 1 : 0);
	}

	/**
	 * Creates a t3lib_TCEmain_CommandMap to be accessed in this test case.
	 * This method is accessed as callback during the unit tests.
	 *
	 * @param array $commandMap
	 * @return t3lib_TCEmain_CommandMap
	 */
	public function getTceMainCommandMapCallback(array $commandMap) {
		$this->tceMainCommandMap = t3lib_div::makeInstance('t3lib_TCEmain_CommandMap', $this->tceMainMock, $commandMap);
		return $this->tceMainCommandMap;
	}

	public function getVersionTceMainCommandMapCallback(t3lib_TCEmain $tceMain, array $commandMap) {
		$this->versionTceMainCommandMap = t3lib_div::makeInstance('tx_version_tcemain_CommandMap', $this->versionTceMainHookMock, $tceMain, $commandMap);
		return $this->versionTceMainCommandMap;
	}

	/**
	 * @return t3lib_TCEmain_CommandMap|tx_version_tcemain_CommandMap
	 */
	protected function getCommandMap() {
		if (t3lib_div::int_from_ver(TYPO3_version) <= 4004999) {
			return $this->tceMainCommandMap;
		} else {
			return $this->versionTceMainCommandMap;
		}
	}
}
