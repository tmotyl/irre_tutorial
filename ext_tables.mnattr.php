<?php
t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnattr_hotel");

$TCA["tx_irretutorial_mnattr_hotel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnattr_hotel',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnattr.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_hotel.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, offers",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnattr_hotel_offer_rel");

$TCA["tx_irretutorial_mnattr_hotel_offer_rel"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnattr_hotel_offer_rel',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnattr.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_hotel_offer_rel.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, hotelid, offerid, quality, allincl",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_irretutorial_mnattr_offer");

$TCA["tx_irretutorial_mnattr_offer"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_mnattr_offer',
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.mnattr.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_irretutorial_offer.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, hotels",
	)
);
?>