<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_irretutorial_mnsym_hotel"] = Array (
	"ctrl" => $TCA["tx_irretutorial_mnsym_hotel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,branches"
	),
	"feInterface" => $TCA["tx_irretutorial_mnsym_hotel"]["feInterface"],
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
				'foreign_table'       => 'tx_irretutorial_mnsym_hotel',
				'foreign_table_where' => 'AND tx_irretutorial_mnsym_hotel.pid=###CURRENT_PID### AND tx_irretutorial_mnsym_hotel.sys_language_uid IN (-1,0)',
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
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_hotel.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"branches" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_hotel.branches",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_irretutorial_mnsym_hotel_rel",
				"foreign_field" => "hotelid",
				"foreign_sortby" => "hotelsort",
				"foreign_label" => "branchid",
				"symmetric_field" => "branchid",
				"symmetric_sortby" => "branchsort",
				"symmetric_label" => "hotelid",
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
		"0" => Array("showitem" =>
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.general, title;;;;2-2-2, branches;;;;3-3-3,' .
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.visibility, sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1'
		)
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_irretutorial_mnsym_hotel_rel"] = Array (
	"ctrl" => $TCA["tx_irretutorial_mnsym_hotel_rel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,hotelid,offerid,hotelsort,offersort"
	),
	"feInterface" => $TCA["tx_irretutorial_mnsym_hotel_rel"]["feInterface"],
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
				'foreign_table'       => 'tx_irretutorial_mnsym_hotel_rel',
				'foreign_table_where' => 'AND tx_irretutorial_mnsym_hotel_rel.pid=###CURRENT_PID### AND tx_irretutorial_mnsym_hotel_rel.sys_language_uid IN (-1,0)',
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
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_hotel_rel.hotelid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_irretutorial_mnsym_hotel",
				"maxitems" => 1,
				'localizeReferences' => 1,
			)
		),
		"branchid" => Array (
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_hotel_rel.branchid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_irretutorial_mnsym_hotel",
				"maxitems" => 1,
				'localizeReferences' => 1,
			)
		),
		"hotelsort" => Array (
			"config" => Array (
				"type" => "passthrough",
			)
		),
		"branchsort" => Array (
			"config" => Array (
				"type" => "passthrough",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" =>
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.general, title;;;;2-2-2, hotelid;;;;3-3-3, branchid,' .
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.visibility, sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, hotelsort, branchsort'
		)
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>