<?php
t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnsym_hotel");

$TCA["tx_irretutorial_mnsym_hotel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnsym_hotel',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		"sortby" => "sorting",
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnsym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_hotel.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, branches",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnsym_hotel_rel");

$TCA["tx_irretutorial_mnsym_hotel_rel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnsym_hotel_rel',		
		'label' => 'uid',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnsym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_hotel_rel.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, hotelid, branchid",
	)
);
?>