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
abstract class tx_irretutorial_abstract extends tx_phpunit_database_testcase {
	const TABLE_Pages = 'pages';

	const VALUE_TimeStamp = 1250000000;
	const VALUE_WorkspaceId = 9;

	const COMMAND_Version = 'version';
	const COMMAND_Version_New = 'new';
	const COMMAND_Version_Swap = 'swap';
	const COMMAND_Version_Flush = 'flush';
	const COMMAND_Version_Clear = 'clearWSID';
	const COMMAND_Localize = 'localize';
	const COMMAND_Delete = 'delete';

	/**
	 * @var boolean
	 */
	private $hasDatabase = FALSE;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var integer
	 */
	private $modifiedTimeStamp;

	/**
	 * @var t3lib_beUserAuth
	 */
	private $originalBackendUser;

	/**
	 * @var array
	 */
	private $originalConvVars;

	/**
	 * @var integer
	 */
	private $expectedLogEntries = 0;

	/**
	 * @var t3lib_beUserAuth
	 */
	private $backendUser;

	/**
	 * @var t3lib_TCEmain
	 */
	private $tceMainOverride;

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
		$this->expectedLogEntries = 0;

		$this->originalConvVars = $GLOBALS['TYPO3_CONF_VARS'];
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['sqlDebug'] = 1;

		$this->originalBackendUser = clone $GLOBALS['BE_USER'];
		$this->backendUser = $GLOBALS['BE_USER'];
		$this->backendUser->workspace = self::VALUE_WorkspaceId;
		$this->setWorkspacesConsiderReferences(FALSE);
	}

	/**
	 * Tears down this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		$this->assertNoLogEntries();

		$GLOBALS['TYPO3_CONF_VARS'] = $this->originalConvVars;

		unset($GLOBALS['T3_VAR']['getUserObj']);
		$GLOBALS['BE_USER'] = $this->originalBackendUser;

		unset($this->backendUser);
		unset($this->originalBackendUser);
		unset($this->originalConvVars);
		unset($this->t3var);

		unset($this->tceMainMock);
		unset($this->tceMainCommandMap);
		unset($this->tceMainOverride);

		unset($this->versionTceMainCommandMap);
		unset($this->versionTceMainHookMock);

		$this->expectedLogEntries = 0;
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
		$this->hasDatabase = $this->createDatabase();

		if ($this->hasDatabase === TRUE) {
			$database = $this->useTestDatabase();

			$this->importStdDB();
			$this->importExtensions(array('cms', 'version', 'irre_tutorial'));

			if ($this->areWorkspacesSupported()) {
				$this->importExtensions(array('workspaces'));
			}

			$this->importDataSet($this->getPath() . 'fixtures/data_pages.xml');
			$this->importDataSet($this->getPath() . 'fixtures/data_sys_workspace.xml');

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
	 * @param mixed $value
	 * @param array $tables Table names with list of ids to be edited
	 * @return array
	 */
	protected function getElementStructureForCommands($command, $value, array $tables) {
		$commandStructure = array();

		foreach ($tables as $tableName => $idList) {
			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$commandStructure[$tableName][$id] = array(
					$command => $value
				);
			}
		}

		return $commandStructure;
	}

	/**
	 * @param string $command
	 * @param mixed $value
	 * @param array $tables Table names with list of ids to be edited
	 * @return t3lib_TCEmain
	 */
	protected function simulateCommand($command, $value, array $tables) {
		return $this->simulateCommandByStructure(
			$this->getElementStructureForCommands($command, $value, $tables)
		);
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
	 * Simulates executing commands by using t3lib_TCEmain.
	 *
	 * @param  array $elements The cmdmap to be delivered to t3lib_TCEmain
	 * @return t3lib_TCEmain
	 */
	protected function simulateCommandByStructure(array $elements) {
		$tceMain = $this->getTceMain();
		$tceMain->start(array(), $elements);
		$tceMain->process_cmdmap();

		return $tceMain;
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
	 * @param  string $parentTableName
	 * @param  integer $parentId
	 * @param  string $parentFieldName
	 * @param  array $assertions
	 * @param string $mmTable
	 * @return void
	 */
	protected function assertChildren($parentTableName, $parentId, $parentFieldName, array $assertions, $mmTable = '') {
		$tcaFieldConfiguration = $this->getTcaFieldConfiguration($parentTableName, $parentFieldName);

		$loadDbGroup = $this->getLoadDbGroup();
		$loadDbGroup->start(
			$this->getFieldValue($parentTableName, $parentId, $parentFieldName),
			$tcaFieldConfiguration['foreign_table'],
			$mmTable,
			$parentId,
			$parentTableName,
			$tcaFieldConfiguration
		);

		$elements = $this->getElementsByItemArray($loadDbGroup->itemArray);

		foreach ($assertions as $index => $assertion) {
			$this->assertTrue(
				$this->executeAssertionOnElements($assertion, $elements),
				'Assertion #' . $index . ' failed'
			);
		}
	}

	/**
	 * @param  array $itemArray
	 * @return array
	 */
	protected function getElementsByItemArray(array $itemArray) {
		$elements = array();

		foreach ($itemArray as $item) {
			$elements[$item['table']][$item['id']] = t3lib_BEfunc::getRecord($item['table'], $item['id']);
		}

		return $elements;
	}

	/**
	 * @param  array $assertion
	 * @param  array $elements
	 * @return boolean
	 */
	protected function executeAssertionOnElements(array $assertion, array $elements) {
		$tableName = $assertion['tableName'];
		unset($assertion['tableName']);

		foreach ($elements[$tableName] as $id => $element) {
			$result = FALSE;

			foreach ($assertion as $field => $value) {
				if ($element[$field] == $value) {
					$result = TRUE;
				} else {
					$result = FALSE;
					break;
				}
			}

			if ($result === TRUE) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Gets the TCE configuration of a field.
	 *
	 * @param  $tableName
	 * @param  $fieldName
	 * @return array
	 */
	protected function getTcaFieldConfiguration($tableName, $fieldName) {
		if (!isset($GLOBALS['TCA'][$tableName]['columns'])) {
			t3lib_div::loadTCA($tableName);
		}

		if (isset($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'])) {
			return $GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'];
		}
	}

	/**
	 * Gets the field value of a record.
	 *
	 * @param  $tableName
	 * @param  $id
	 * @param  $fieldName
	 * @return string
	 */
	protected function getFieldValue($tableName, $id, $fieldName) {
		$record = t3lib_BEfunc::getRecord($tableName, $id, $fieldName);
		if (is_array($record)) {
			return $record[$fieldName];
		}
	}

	/**
	 * Gets an instance of t3lib_TCEmain.
	 *
	 * @return t3lib_TCEmain
	 */
	protected function getTceMain() {
		if (isset($this->tceMainOverride)) {
			$tceMain = $this->tceMainOverride;
		} else {
			$tceMain = t3lib_div::makeInstance('t3lib_TCEmain');
		}

		return $tceMain;
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
	 * Gets instance of t3lib_loadDBGroup.
	 *
	 * @return t3lib_loadDBGroup
	 */
	protected function getLoadDbGroup() {
		$loadDbGroup = t3lib_div::makeInstance('t3lib_loadDBGroup');

		return $loadDbGroup;
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
	 * Gets all records of a table.
	 *
	 * @param string $table Name of the table
	 * @return array
	 */
	protected function getAllRecords($table) {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, '1=1', '', '', '', 'uid');
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
	 * Assert that no sys_log entries had been written.
	 *
	 * @return void
	 */
	protected function assertNoLogEntries() {
		$logEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_log', 'error IN (1,2)');

		if (count($logEntries) > $this->expectedLogEntries) {
			var_dump(array_values($logEntries));
			$this->fail('The sys_log table contains unexpected entries.');
		} elseif (count($logEntries) < $this->expectedLogEntries) {
			$this->fail('Expected count of sys_log entries no reached.');
		}
	}

	/**
	 * Sets the number of expected log entries.
	 *
	 * @param integer $count
	 * @return void
	 */
	protected function setExpectedLogEntries($count) {
		$count = intval($count);

		if ($count > 0) {
			$this->expectedLogEntries = $count;
		}
	}

	/**
	 * Gets the last log entry.
	 *
	 * @return array
	 */
	protected function getLastLogEntryMessage() {
		$message = '';

		$logEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_log', 'error IN (1,2)', '', '', 1);

		if (is_array($logEntries) && count($logEntries)) {
			$message = $logEntries[0]['details'];
		}

		return $message;
	}

	/**
	 * Sets the User TSconfig property options.workspaces.considerReferences.
	 *
	 * @param boolean $workspacesConsiderReferences
	 * @return void
	 */
	protected function setWorkspacesConsiderReferences($workspacesConsiderReferences = TRUE) {
		$this->backendUser->userTS['options.']['workspaces.']['considerReferences'] = ($workspacesConsiderReferences ? 1 : 0);
	}

	/**
	 * Overrides the t3lib_TCEmain instance to be used (could be a mock as well).
	 *
	 * @param t3lib_TCEmain $tceMainOverride
	 * @return void
	 */
	protected function setTceMainOverride(t3lib_TCEmain $tceMainOverride = NULL) {
		$this->tceMainOverride = $tceMainOverride;
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

	/**
	 * Asserts the correct order of elements.
	 *
	 * @param string $table
	 * @param string $field
	 * @param array $expectedOrderOfIds
	 * @param string $message
	 * @return void
	 */
	protected function assertSortingOrder($table, $field, $expectedOrderOfIds, $message) {
		$expectedOrderOfIdsCount = count($expectedOrderOfIds);
		$elements = $this->getAllRecords($table);

		for ($i = 0; $i < $expectedOrderOfIdsCount-1; $i++) {
			$this->assertLessThan(
				$elements[$expectedOrderOfIds[$i+1]][$field],
				$elements[$expectedOrderOfIds[$i]][$field],
				$message
			);
		}
	}
}
