<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// ext_tables.php is split to each single part of application
require(t3lib_extMgm::extPath($_EXTKEY)).'ext_tables.1ncsv.php';
require(t3lib_extMgm::extPath($_EXTKEY)).'ext_tables.1nff.php';
require(t3lib_extMgm::extPath($_EXTKEY)).'ext_tables.mnasym.php';
require(t3lib_extMgm::extPath($_EXTKEY)).'ext_tables.mnmmasym.php';
require(t3lib_extMgm::extPath($_EXTKEY)).'ext_tables.mnsym.php';
require(t3lib_extMgm::extPath($_EXTKEY)).'ext_tables.mnattr.php';

if (TYPO3_MODE=="BE") {
	t3lib_extMgm::addModule("web","txirretutorialM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}
?>