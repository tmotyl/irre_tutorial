<?php
t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnasym_hotel");

$TCA["tx_irretutorial_mnasym_hotel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_mnasym_hotel',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."Configuration/Tca/tca.mnasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."Resources/Public/Icons/icon_tx_irretutorial_hotel.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, offers",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnasym_hotel_offer_rel");

$TCA["tx_irretutorial_mnasym_hotel_offer_rel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_mnasym_hotel_offer_rel',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."Configuration/Tca/tca.mnasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."Resources/Public/Icons/icon_tx_irretutorial_hotel_offer_rel.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		// @see http://forge.typo3.org/issues/29278 which solves it implicitly in the Core
		// 'shadowColumnsForNewPlaceholders' => 'hotelid,offerid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, hotelid, offerid, prices, hotelsort, offersort",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnasym_offer");

$TCA["tx_irretutorial_mnasym_offer"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_mnasym_offer',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."Configuration/Tca/tca.mnasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."Resources/Public/Icons/icon_tx_irretutorial_offer.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, hotels",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnasym_price");

$TCA["tx_irretutorial_mnasym_price"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_mnasym_price',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."Configuration/Tca/tca.mnasym.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."Resources/Public/Icons/icon_tx_irretutorial_price.gif",
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		// @see http://forge.typo3.org/issues/29278 which solves it implicitly in the Core
		// 'shadowColumnsForNewPlaceholders' => 'parentid',
		'dividers2tabs' => TRUE,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, parentid, price",
	)
);
?>