<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Oliver Hader <oliver@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Testcase for 1:n ff relations.
 *
 * @author Oliver Hader <oliver@typo3.org>
 */
class tx_irretutorial_mnmmaysmLocalizationTest extends tx_irretutorial_AbstractLocalization {
	const TABLE_Hotel = 'tx_irretutorial_mnmmasym_hotel';
	const TABLE_Offer = 'tx_irretutorial_mnmmasym_offer';
	const TABLE_Price = 'tx_irretutorial_mnmmasym_price';

	const FIELD_Hotel_Offers = 'offers';
	const FIELD_Offers_Prices = 'prices';

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();
		$this->importDataSet($this->getPath() . 'fixtures/data_mnmmasym.xml');
	}

	/**
	 * @return void
	 * @test
	 */
	public function isOnlyParentLocalized() {
		$this->simulateCommand(
			self::COMMAND_Localize,
			self::VALUE_LanguageId,
			array(self::TABLE_Hotel => '1')
		);

		$this->assertLocalizations(
			array(
				self::TABLE_Hotel => '1',
			)
		);

		$this->assertLocalizations(
			array(
				self::TABLE_Offer => '1,2',
			),
			self::VALUE_LanguageId,
			FALSE
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function areChildElementsLocalized() {
		$this->simulateCommand(
			self::COMMAND_Localize,
			self::VALUE_LanguageId,
			array(self::TABLE_Hotel => '1')
		);

		$localizedHotelId = $this->getLocalizationId(self::TABLE_Hotel, 1);

		$this->simulateCommand(
			self::COMMAND_LocalizeSynchronize,
			self::FIELD_Hotel_Offers . ',' . self::COMMAND_LocalizeSynchronize_Localize,
			array(self::TABLE_Hotel => $localizedHotelId)
		);

		$this->assertLocalizations(
			array(
				self::TABLE_Hotel => '1',
				self::TABLE_Offer => '1,2',
			)
		);
	}

	/**
	 * @return void
	 * @test
	 */
	public function areChildElementsLocalizedWithParent() {
		$this->setTcaFieldConfiguration(
			self::TABLE_Hotel,
			self::FIELD_Hotel_Offers,
			self::BEHAVIOUR_LocalizeReferencesAtParentLocalization,
			TRUE
		);

		$this->simulateCommand(
			self::COMMAND_Localize,
			self::VALUE_LanguageId,
			array(self::TABLE_Hotel => '1')
		);

		$this->assertLocalizations(
			array(
				self::TABLE_Hotel => '1',
				self::TABLE_Offer => '1,2',
				self::TABLE_Price => '1,2,3',
			)
		);
	}
}
