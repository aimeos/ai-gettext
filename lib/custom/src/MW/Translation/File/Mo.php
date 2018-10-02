<?php


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation\File;


/**
 * Class for reading Gettext MO files
 *
 * @package MW
 * @subpackage Translation
 */
class Mo
{
	const MAGIC1 = -1794895138;
	const MAGIC2 = -569244523;
	const MAGIC3 = 2500072158;


	private $str;
	private $strlen;
	private $pos = 0;
	private $messages = [];


	/**
	 * Initializes the .mo file reader
	 *
	 * @param string $filepath Absolute path to the Gettext .mo file
	 */
	public function __construct( $filepath )
	{
		if( ( $str = file_get_contents( $filepath ) ) === false ) {
			throw new \Aimeos\MW\Translation\Exception( sprintf( 'Unable to read from file "%1$s"', $filepath ) );
		}

		$this->str = $str;
		$this->strlen = strlen( $str );
		$this->messages = $this->extract();
	}


	/**
	 * Returns all translations
	 *
	 * @return array List of translations with original as key and translations as values
	 */
	public function all()
	{
		return $this->messages;
	}


	/**
	 * Returns the translations for the given original string
	 *
	 * @param string $original Untranslated string
	 * @return array|boolean List of translations or false if none is available
	 */
	public function get( $original )
	{
		$original = (string) $original;

		if( isset( $this->messages[$original] ) ) {
			return $this->messages[$original];
		}

		return false;
	}


	/**
	 * Extracts the messages and translations from the MO file
	 *
	 * @throws \Aimeos\MW\Translation\Exception If file content is invalid
	 * @return array Associative list of original singular as keys and one or more translations as values
	 */
	protected function extract()
	{
		$magic = $this->readInt( 'V' );

		if( ( $magic === self::MAGIC1 ) || ( $magic === self::MAGIC3 ) ) { //to make sure it works for 64-bit platforms
			$byteOrder = 'V'; //low endian
		} elseif( $magic === ( self::MAGIC2 & 0xFFFFFFFF ) ) {
			$byteOrder = 'N'; //big endian
		} else {
			throw new \Aimeos\MW\Translation\Exception( 'Invalid MO file' );
		}

		$this->readInt( $byteOrder );
		$total = $this->readInt( $byteOrder ); //total string count
		$originals = $this->readInt( $byteOrder ); //offset of original table
		$trans = $this->readInt( $byteOrder ); //offset of translation table

		$this->seekto( $originals );
		$originalTable = $this->readIntArray( $byteOrder, $total * 2 );
		$this->seekto( $trans );
		$translationTable = $this->readIntArray( $byteOrder, $total * 2 );

		return $this->extractTable( $originalTable, $translationTable, $total );
	}


	/**
	 * Extracts the messages and their translations
	 *
	 * @param array $originalTable MO table for original strings
	 * @param array $translationTable MO table for translated strings
	 * @param integer $total Total number of translations
	 * @return array Associative list of original singular as keys and one or more translations as values
	 */
	protected function extractTable( $originalTable, $translationTable, $total )
	{
		$messages = [];

		for( $i = 0; $i < $total; ++$i )
		{
			$plural = null;
			$next = $i * 2;

			$this->seekto( $originalTable[$next + 2] );
			$original = $this->read( $originalTable[$next + 1] );
			$this->seekto( $translationTable[$next + 2] );
			$translated = $this->read( $translationTable[$next + 1] );

			if( $original === '' || $translated === '' ) { // Headers
				continue;
			}

			if( strpos( $original, "\x04" ) !== false ) {
				list( $context, $original ) = explode( "\x04", $original, 2 );
			}

			if( strpos( $original, "\000" ) !== false ) {
				list( $original, $plural ) = explode( "\000", $original );
			}

			if( $plural === null )
			{
				$messages[$original] = $translated;
				continue;
			}

			$messages[$original] = [];

			foreach( explode( "\x00", $translated ) as $idx => $value ) {
				$messages[$original][$idx] = $value;
			}
		}

		return $messages;
	}


	/**
	 * Returns a single integer starting from the current position
	 *
	 * @param string $byteOrder Format code for unpack()
	 * @return integer Read integer
	 */
	protected function readInt( $byteOrder )
	{
		if( ( $content = $this->read( 4 )) === false ) {
			return false;
		}

		$content = unpack( $byteOrder, $content );
		return array_shift( $content );
	}


	/**
	 * Returns the list of integers starting from the current position
	 *
	 * @param string $byteOrder Format code for unpack()
	 * @param integer $count Number of four byte integers to read
	 * @return array List of integers
	 */
	protected function readIntArray( $byteOrder, $count )
	{
		return unpack( $byteOrder . $count, $this->read( 4 * $count ) );
	}


	/**
	 * Returns a part of the file
	 *
	 * @param integer $bytes Number of bytes to read
	 * @return string|boolean Read bytes or false on failure
	 */
	protected function read($bytes)
	{
		$data = substr( $this->str, $this->pos, $bytes );
		$this->seekto( $this->pos + $bytes );

		return $data;
	}


	/**
	 * Move the cursor to the position in the file
	 *
	 * @param integer $pos Number of bytes to move
	 * @return integer New file position in bytes
	 */
	protected function seekto($pos)
	{
		$this->pos = ( $this->strlen < $pos ? $this->strlen : $pos );
		return $this->pos;
	}
}