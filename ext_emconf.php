<?php

########################################################################
# Extension Manager/Repository config file for ext "irre_tutorial".
#
# Auto generated 10-03-2012 21:03
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Tutorial for Inline Relational Record Editing IRRE',
	'description' => 'Shows how Inline Relational Record Editing could be used to build a structure of hotel, offers and prices differently. Use the "IRRE Tutorial" module to get a quickstart by installing some IRRE sample data.',
	'category' => 'example',
	'shy' => 0,
	'version' => '0.4.0',
	'dependencies' => 'workspaces,version',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Oliver Hader',
	'author_email' => 'oliver@typo3.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-0.0.0',
			'workspaces' => '0.0.0-',
			'version' => '0.0.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:49:{s:9:"ChangeLog";s:4:"beb5";s:10:"README.txt";s:4:"e002";s:16:"ext_autoload.php";s:4:"b016";s:12:"ext_icon.gif";s:4:"b4e6";s:20:"ext_tables.1ncsv.php";s:4:"7720";s:19:"ext_tables.1nff.php";s:4:"095d";s:22:"ext_tables.general.php";s:4:"7785";s:21:"ext_tables.mnasym.php";s:4:"ba43";s:21:"ext_tables.mnattr.php";s:4:"f80c";s:23:"ext_tables.mnmmasym.php";s:4:"bf72";s:20:"ext_tables.mnsym.php";s:4:"21c5";s:14:"ext_tables.php";s:4:"ae00";s:14:"ext_tables.sql";s:4:"5e78";s:30:"icon_tx_irretutorial_hotel.gif";s:4:"4ad7";s:40:"icon_tx_irretutorial_hotel_offer_rel.gif";s:4:"d3a6";s:34:"icon_tx_irretutorial_hotel_rel.gif";s:4:"9e5c";s:30:"icon_tx_irretutorial_offer.gif";s:4:"1e24";s:40:"icon_tx_irretutorial_offer_price_rel.gif";s:4:"4025";s:30:"icon_tx_irretutorial_price.gif";s:4:"dc05";s:16:"locallang_db.xml";s:4:"3e61";s:13:"tca.1ncsv.php";s:4:"136a";s:12:"tca.1nff.php";s:4:"5887";s:14:"tca.mnasym.php";s:4:"19d6";s:14:"tca.mnattr.php";s:4:"fcdf";s:16:"tca.mnmmasym.php";s:4:"11e7";s:13:"tca.mnsym.php";s:4:"8454";s:14:"doc/manual.sxw";s:4:"b30a";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"3f25";s:14:"mod1/index.php";s:4:"5703";s:18:"mod1/locallang.xml";s:4:"29c4";s:22:"mod1/locallang_mod.xml";s:4:"3d98";s:19:"mod1/moduleicon.gif";s:4:"b4e6";s:17:"res/T3D__IRRE.t3d";s:4:"030b";s:51:"tests/class.tx_irretutorial_1ncsvWorkspacesTest.php";s:4:"24ae";s:50:"tests/class.tx_irretutorial_1nffWorkspacesTest.php";s:4:"f45a";s:40:"tests/class.tx_irretutorial_Abstract.php";s:4:"bf1d";s:52:"tests/class.tx_irretutorial_AbstractLocalization.php";s:4:"52b5";s:50:"tests/class.tx_irretutorial_AbstractWorkspaces.php";s:4:"9473";s:60:"tests/class.tx_irretutorial_mnmmaysmLocalizationKeepTest.php";s:4:"6076";s:62:"tests/class.tx_irretutorial_mnmmaysmLocalizationSelectTest.php";s:4:"0fe6";s:29:"tests/fixtures/data_1ncsv.xml";s:4:"4e87";s:28:"tests/fixtures/data_1nff.xml";s:4:"4b5e";s:30:"tests/fixtures/data_mnasym.xml";s:4:"4f9a";s:32:"tests/fixtures/data_mnmmasym.xml";s:4:"fade";s:29:"tests/fixtures/data_mnsym.xml";s:4:"3f09";s:29:"tests/fixtures/data_pages.xml";s:4:"87ad";s:36:"tests/fixtures/data_sys_language.xml";s:4:"fb16";s:37:"tests/fixtures/data_sys_workspace.xml";s:4:"5185";}',
	'suggests' => array(
	),
);

?>