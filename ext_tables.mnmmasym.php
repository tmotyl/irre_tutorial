<?php
t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnmmasym_hotel");

$TCA["tx_irretutorial_mnmmasym_hotel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnmmasym_hotel',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnmmasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_hotel.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, offers",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnmmasym_offer");

$TCA["tx_irretutorial_mnmmasym_offer"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnmmasym_offer',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnmmasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_offer.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, hotels, prices",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnmmasym_price");

$TCA["tx_irretutorial_mnmmasym_price"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnmmasym_price',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnmmasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_price.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, price, offers",
	)
);
?>