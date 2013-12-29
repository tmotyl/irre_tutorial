<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_irretutorial_1ncsv_hotel"] = Array (
	"ctrl" => $TCA["tx_irretutorial_1ncsv_hotel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,offers"
	),
	"feInterface" => $TCA["tx_irretutorial_1ncsv_hotel"]["feInterface"],
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
				'foreign_table'       => 'tx_irretutorial_1ncsv_hotel',
				'foreign_table_where' => 'AND tx_irretutorial_1ncsv_hotel.pid=###CURRENT_PID### AND tx_irretutorial_1ncsv_hotel.sys_language_uid IN (-1,0)',
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
		"offers" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_hotel.offers",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_irretutorial_1ncsv_offer",
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
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.general, title;;;;2-2-2, offers;;;;3-3-3, ' .
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.visibility, sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1'
		)
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_irretutorial_1ncsv_offer"] = Array (
	"ctrl" => $TCA["tx_irretutorial_1ncsv_offer"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,prices"
	),
	"feInterface" => $TCA["tx_irretutorial_1ncsv_offer"]["feInterface"],
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
				'foreign_table'       => 'tx_irretutorial_1ncsv_offer',
				'foreign_table_where' => 'AND tx_irretutorial_1ncsv_offer.pid=###CURRENT_PID### AND tx_irretutorial_1ncsv_offer.sys_language_uid IN (-1,0)',
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
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_offer.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"prices" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_offer.prices",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_irretutorial_1ncsv_price",
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
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.general, title;;;;2-2-2, prices;;;;3-3-3,' .
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.visibility, sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1'
		)
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_irretutorial_1ncsv_price"] = Array (
	"ctrl" => $TCA["tx_irretutorial_1ncsv_price"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,title,price"
	),
	"feInterface" => $TCA["tx_irretutorial_1ncsv_price"]["feInterface"],
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
				'foreign_table'       => 'tx_irretutorial_1ncsv_price',
				'foreign_table_where' => 'AND tx_irretutorial_1ncsv_price.pid=###CURRENT_PID### AND tx_irretutorial_1ncsv_price.sys_language_uid IN (-1,0)',
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
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_price.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"price" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tx_irretutorial_price.price",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "double2",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" =>
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.general, title;;;;2-2-2, price;;;;3-3-3,' .
			'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tabs.visibility, sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, '
		)
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>