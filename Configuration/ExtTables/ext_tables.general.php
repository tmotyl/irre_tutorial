<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns(
	'pages',
	 array (
		'tx_irretutorial_hotels' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:pages.tx_irretutorial_hotels',
			'config' => Array (
				'type' => 'inline',
				'foreign_table' => 'tx_irretutorial_1nff_hotel',
				'foreign_field' => 'parentid',
				'foreign_table_field' => 'parenttable',
				'maxitems' => 10,
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
	1
);
t3lib_extMgm::addToAllTCAtypes(
	'pages',
	'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:pages.doktype.div.irre, tx_irretutorial_hotels;;;;1-1-1'
);


t3lib_div::loadTCA('pages_language_overlay');
t3lib_extMgm::addTCAcolumns(
	'pages_language_overlay',
	 array (
		'tx_irretutorial_hotels' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:pages.tx_irretutorial_hotels',
			'config' => Array (
				'type' => 'inline',
				'foreign_table' => 'tx_irretutorial_1nff_hotel',
				'foreign_field' => 'parentid',
				'foreign_table_field' => 'parenttable',
				'maxitems' => 10,
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
	1
);
t3lib_extMgm::addToAllTCAtypes(
	'pages_language_overlay',
	'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:pages.doktype.div.irre, tx_irretutorial_hotels;;;;1-1-1'
);


t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns(
	'tt_content',
	 array (
		'tx_irretutorial_flexform' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tt_content.tx_irretutorial_flexform',
			'config' => Array (
				'type' => 'flex',
				'ds' => array(
					'default' => 'FILE:EXT:irre_tutorial/Configuration/FlexForms/tt_content_flexform.xml',
				),
			)
		),
	),
	1
);
t3lib_extMgm::addToAllTCAtypes(
	'tt_content',
	'--div--;LLL:EXT:irre_tutorial/Resources/Private/Language/locallang_db.xml:tt_content.div.irre, tx_irretutorial_flexform;;;;1-1-1'
);
?>