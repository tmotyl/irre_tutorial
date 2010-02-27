<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_irretutorial_mnasym_hotel"] = Array (
	"ctrl" => $TCA["tx_irretutorial_mnasym_hotel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,offers"
	),
	"feInterface" => $TCA["tx_irretutorial_mnasym_hotel"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_irretutorial_mnasym_hotel',
				'foreign_table_where' => 'AND tx_irretutorial_mnasym_hotel.pid=###CURRENT_PID### AND tx_irretutorial_mnasym_hotel.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (
			"exclude" => 1,
			'l10n_mode' => 'prefixLangTitle',
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_hotel.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"offers" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_hotel.offers",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_irretutorial_mnasym_hotel_offer_rel",
				"foreign_field" => "hotelid",
				"foreign_sortby" => "hotelsort",
				"foreign_label" => "offerid",
				"maxitems" => 10,
				'appearance' => array(
					'showSynchronizationLink' => 1,
					'showAllLocalizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showRemovedLocalizationRecords' => 1,
				),
				'behaviour' => array(
					'localizationMode' => 'select',
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, offers;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_irretutorial_mnasym_hotel_offer_rel"] = Array (
	"ctrl" => $TCA["tx_irretutorial_mnasym_hotel_offer_rel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,hotelid,offerid,prices,hotelsort,offersort"
	),
	"feInterface" => $TCA["tx_irretutorial_mnasym_hotel_offer_rel"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_irretutorial_mnasym_hotel_offer_rel',
				'foreign_table_where' => 'AND tx_irretutorial_mnasym_hotel_offer_rel.pid=###CURRENT_PID### AND tx_irretutorial_mnasym_hotel_offer_rel.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"hotelid" => Array (
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_hotel_offer_rel.hotelid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_irretutorial_mnasym_hotel",
				"foreign_table_where" => "AND tx_irretutorial_mnasym_hotel.pid=###CURRENT_PID### AND tx_irretutorial_mnasym_hotel.sys_language_uid='###REC_FIELD_sys_language_uid###'",
				"maxitems" => 1,
				'localizeReferences' => 1,
			)
		),
		"offerid" => Array (
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_hotel_offer_rel.offerid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_irretutorial_mnasym_offer",
				"foreign_table_where" => "AND tx_irretutorial_mnasym_offer.pid=###CURRENT_PID### AND tx_irretutorial_mnasym_offer.sys_language_uid='###REC_FIELD_sys_language_uid###'",
				"maxitems" => 1,
				'localizeReferences' => 1,
			)
		),
		"prices" => Array (
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_hotel_offer_rel.prices",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_irretutorial_mnasym_price",
				"foreign_field" => "parentid",
				"maxitems" => 10,
				'appearance' => array(
					'showSynchronizationLink' => 1,
					'showAllLocalizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showRemovedLocalizationRecords' => 1,
				),
				'behaviour' => array(
					'localizationMode' => 'select',
				),
			)
		),
		"hotelsort" => Array (
			"config" => Array (
				"type" => "passthrough",
			)
		),
		"offersort" => Array (
			"config" => Array (
				"type" => "passthrough",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, hotelid;;;;3-3-3, offerid, prices, hotelsort, offersort")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_irretutorial_mnasym_offer"] = Array (
	"ctrl" => $TCA["tx_irretutorial_mnasym_offer"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,hotels"
	),
	"feInterface" => $TCA["tx_irretutorial_mnasym_offer"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_irretutorial_mnasym_offer',
				'foreign_table_where' => 'AND tx_irretutorial_mnasym_offer.pid=###CURRENT_PID### AND tx_irretutorial_mnasym_offer.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (
			"exclude" => 1,
			'l10n_mode' => 'prefixLangTitle',
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_offer.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"hotels" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_offer.hotels",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_irretutorial_mnasym_hotel_offer_rel",
				"foreign_field" => "offerid",
				"foreign_sortby" => "offersort",
				"foreign_label" => "hotelid",
				"maxitems" => 10,
				'appearance' => array(
					'showSynchronizationLink' => 1,
					'showAllLocalizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showRemovedLocalizationRecords' => 1,
				),
				'behaviour' => array(
					'localizationMode' => 'select',
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, hotels")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_irretutorial_mnasym_price"] = Array (
	"ctrl" => $TCA["tx_irretutorial_mnasym_price"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,parentid,price"
	),
	"feInterface" => $TCA["tx_irretutorial_mnasym_price"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_irretutorial_mnasym_price',
				'foreign_table_where' => 'AND tx_irretutorial_mnasym_price.pid=###CURRENT_PID### AND tx_irretutorial_mnasym_price.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"parentid" => Array (
			"config" => Array (
				"type" => "passthrough",
			)
		),
		"title" => Array (
			"exclude" => 1,
			'l10n_mode' => 'prefixLangTitle',
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_price.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"price" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/locallang_db.xml:tx_irretutorial_price.price",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "double2",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, parentid, title;;;;2-2-2, parentid, price;;;;3-3-3;")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>