<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
 */


namespace Aimeos\MW\Translation;


class GettextTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->translationSources = array(
			'testDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case1' ),
		);

		$this->object = new \Aimeos\MW\Translation\Gettext( $this->translationSources, 'de_DE' );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testDt()
	{
		$this->assertEquals( 'Datei', $this->object->dt( 'testDomain', 'File' ) );
	}


	public function testDn()
	{
		/*
		 * plural for RU: 3 pl forms
		 * 0, if $n == 1, 21, 31, 41, ...
		 * 1, if $n == 2..4, 22..24, 32..34, ...
		 * 2, if $n == 5..20, 25..30, 35..40, .
		 */

		$object = new \Aimeos\MW\Translation\Gettext( $this->translationSources, 'ru' );

		$this->assertEquals( 'plural 2', $object->dn( 'testDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'singular', $object->dn( 'testDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'plural 1', $object->dn( 'testDomain', 'File', 'Files', 2 ) );
		$this->assertEquals( 'plural 2', $object->dn( 'testDomain', 'File', 'Files', 5 ) );

		$this->assertEquals( 'plural 1', $object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'plural 2', $object->dn( 'testDomain', 'File', 'Files', 25 ) );
		$this->assertEquals( 'singular', $object->dn( 'testDomain', 'File', 'Files', 31 ) );
	}


	public function testDnOverwriteSingular()
	{
		$ds = DIRECTORY_SEPARATOR;

		$translationSources = array(
			'testDomain' => array(
				__DIR__ . $ds . 'testfiles' . $ds . 'case1',
				__DIR__ . $ds . 'testfiles' . $ds . 'case2',
			),
		);

		$object = new \Aimeos\MW\Translation\Gettext( $translationSources, 'de_DE' );
		$this->assertEquals( 'Neue Version', $object->dt( 'testDomain', 'Update' ) );
	}


	public function testDnOverwritePlural()
	{
		$ds = DIRECTORY_SEPARATOR;

		$translationSources = array(
			'testDomain' => array(
				__DIR__ . $ds . 'testfiles' . $ds . 'case1',
				__DIR__ . $ds . 'testfiles' . $ds . 'case2',
			),
		);

		$object = new \Aimeos\MW\Translation\Gettext( $translationSources, 'de_DE' );
		$this->assertEquals( 'KFZs', $object->dn( 'testDomain', 'Car', 'Cars', 25 ) );
	}


	public function testGetAll()
	{
		$ds = DIRECTORY_SEPARATOR;

		$translationSources = array(
			'testDomain' => array(
				__DIR__ . $ds . 'testfiles' . $ds . 'case1',
				__DIR__ . $ds . 'testfiles' . $ds . 'case2',
			),
		);

		$object = new \Aimeos\MW\Translation\Gettext( $translationSources, 'de_DE' );
		$result = $object->all( 'testDomain' );

		$this->assertArrayHasKey( 'Car', $result );
		$this->assertEquals( 'KFZ', $result['Car'][0] );
		$this->assertEquals( 'KFZs', $result['Car'][1] );
		$this->assertArrayHasKey( 'File', $result );
		$this->assertEquals( 'Datei mehr', $result['File'][0] );
		$this->assertEquals( 'Dateien mehr', $result['File'][1] );
		$this->assertArrayHasKey( 'Update', $result );
		$this->assertEquals( 'Neue Version', $result['Update'] );
	}

}
