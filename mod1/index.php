<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Oliver Hader <oh@inpublica.de>
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


$LANG->includeLLFile('EXT:irre_tutorial/mod1/locallang.xml');
$BE_USER->modAccess($MCONF, TRUE);


/**
 * Module 'IRRE Tutorial' for the 'irre_tutorial' extension.
 *
 * @author		Oliver Hader <oh@inpublica.de>
 * @package		TYPO3
 * @subpackage	tx_irretutorial
 */
class tx_irretutorial_module1 extends t3lib_SCbase {
	protected $pageinfo;
	protected $pageAlias = 'irre_tutorial_data';
	protected $irreKeys = array('1ncsv', '1nff', 'mnasym', 'mnsym', 'mnattr');
	protected $tablePre = 'tx_irretutorial_';

	/**
	 * @static
	 * @return tx_irretutorial_module1
	 */
	public static function getInstace() {
		return t3lib_div::makeInstance('tx_irretutorial_module1');
	}

	/**
	 * Initializes the Module
	 * @return	void
	 */
	public function init() {
		parent::init();
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	public function menuConfig() {
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $GLOBALS['LANG']->getLL('install'),
				'2' => $GLOBALS['LANG']->getLL('uninstall'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	void
	 */
	public function main() {
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($GLOBALS['BE_USER']->user['admin'] && !$this->id)) {

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL) {
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], 50);

			$this->content.=$this->doc->startPage($GLOBALS['LANG']->getLL('title'));
			$this->content.=$this->doc->header($GLOBALS['LANG']->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];

			$this->content.=$this->doc->startPage($GLOBALS['LANG']->getLL('title'));
			$this->content.=$this->doc->header($GLOBALS['LANG']->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	public function printContent() {

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	protected function moduleContent() {
		switch((string)$this->MOD_SETTINGS['function']) {
			case 1:
				$cmd = t3lib_div::_GP('CMD');
				if (!$cmd['install']) {
					$content = '<div>Please click the following button if you would like to install IRRE sample data on a new page at the root page (pid=0).</div>';
					$content .= $this->doc->spacer(10);
					$content .= '<div><input type="submit" value="Install sample data" name="CMD[install]" /></div>';
				} else {
					$importResponse = $this->installData();
					if (t3lib_div::testInt($importResponse)) {
						$content = '<div>IRRE sample data was installed on a new page (pid='.$importResponse.'). You are redirected to the Web&gt>List module in 5 seconds.</div>';
							// Expand new page branch in list moduel:
						$jsCode = '
							top.fsMod.recentIds["web"]='.$importResponse.';
							top.fsMod.navFrameHighlightedID["web"]="pages'.$importResponse.'_0";
						';
						$content .= t3lib_div::wrapJS($jsCode);
							// Reload page tree:
						$content .= t3lib_BEfunc::setUpdateSignal('updatePageTree');
							// Load the list module:
						$jsCode = '
							window.setTimeout(\'top.goToModule("web_list");\', 5000);
						';
						$content .= t3lib_div::wrapJS($jsCode);

					} else {
						$content = '<div>IRRE sample data cannot be installed twice!</div>';
						$content .= '<div>' . $importResponse . '</div>';
					}
				}
				$this->content.=$this->doc->section($GLOBALS['LANG']->getLL('install').':',$content,0,1);
			break;
			case 2:
				$cmd = t3lib_div::_GP('CMD');
				if (!$cmd['uninstall']) {
					$content = '<div>Please click the following button if you would like to uninstall the IRRE sample data.</div>';
					$content .= $this->doc->spacer(10);
					$content .= '<div><input type="submit" value="UNINSTALL sample data" name="CMD[uninstall]" /></div>';
				} else {
					$result = $this->uninstallData();
					if ($result) {
						$content = '<div>IRRE sample was successfully removed from your TYPO3 installation.</div>';
						$content .= t3lib_BEfunc::setUpdateSignal('updatePageTree');
					} else {
						$content = '<div>IRRE sample data was not found and so could not be uninstalled!</div>';
					}
				}
				$this->content.=$this->doc->section($GLOBALS['LANG']->getLL('uninstall').':',$content,0,1);
			break;
		}
	}
	
	/**
	 * Installs the IRRE sample data. A new page is created where all data is placed on.
	 * This page gets an alias name 'irre_tutorial_data' ($this->pageAlias) for later removal.
	 *
	 * @return	integer		The new page Id, that was created for IRRE sample data, or null or an error message
	 */
	protected function installData() {
			// Check if IRRE sample data was already installed:
		$rows = t3lib_BEfunc::getRecordsByField('pages', 'alias', $this->pageAlias);

		if (!count($rows)) {
				// Define path to T3D import file:
			include_once t3lib_extMgm::extPath('impexp') . 'class.tx_impexp.php';
			$importFile = t3lib_extMgm::extPath('irre_tutorial').'Resources/Private/SampleData/T3D__IRRE.t3d';

			/** @var $import tx_impexp */
			$import = t3lib_div::makeInstance('tx_impexp');
			$import->init(0,'import');
			
			if ($importFile && @is_file($importFile)) {
				if ($import->loadFile($importFile, 1)) {
						// Import to root page:
					$import->importData(0);
						// Get id of container page:
					$newPages = $import->import_mapId['pages'];
					reset($newPages);
					$importResponse = current($newPages);
				}
			}

				// Check for errors during the import process:
			if (empty($importResponse) && $errors = $import->printErrorLog()) {
				$importResponse = $errors;
				// No errors were found, so $importResponse is the uid of the
				// root node of the imported structure:
			} else {
				$tce = $this->getTCEmainInstance();
				$data = array(
					'pages' => array(
						$importResponse => array('alias' => $this->pageAlias)
					)
				);
				$tce->start($data, array());
				$tce->process_datamap();
			}
		} else {
			$importResponse = 'See page id ' . $rows[0]['uid'];
		}
		
		return $importResponse;
	}
	
	/**
	 * Uninstalls the IRRE sample data.
	 * A page with alias name 'irre_tutorial_data' ($this->pageAlias) is searchen in the pages table.
	 * If this could be found, it's removed with all other elements on that branch.
	 * 
	 * @return	boolean		true if uninstall was successful, or false if e.g. the page couldn'n be found
	 */
	protected function uninstallData() {
		$cmd = array('pages' => array());
		$roots = t3lib_BEfunc::getRecordsByField('pages', 'alias', $this->pageAlias);
		if (is_array($roots)) {
			foreach($roots as $rootNode) {
				$pages = t3lib_befunc::getRecordsByField('pages', 'pid', $rootNode['uid']);
				if (count($pages)) {
					foreach ($pages as $subNode) {
						$cmd['pages'][$subNode['uid']]['delete'] = 1;
					}
				}
				$cmd['pages'][$rootNode['uid']]['delete'] = 1;
			}
		}
			// Pages to be remove were found, so do the uninstall:
		if (count($cmd['pages'])) {
			$tce =& $this->getTCEmainInstance();
			$tce->start(array(), $cmd);
			$tce->process_cmdmap();
			return true;
			// No pages to be removed were found:
		} else {
			return false;
		}
	}
	
	/**
	 * Returns a new instance to TCEmain.
	 *
	 * @return 	t3lib_TCEmain New instance to TCEmain
	 */
	protected function getTCEmainInstance() {
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values = 0;
		return $tce;
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/irre_tutorial/mod1/index.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/irre_tutorial/mod1/index.php']);
}

// Make instance:
$SOBE = tx_irretutorial_module1::getInstace();
$SOBE->init();
$SOBE->main();
$SOBE->printContent();

?>