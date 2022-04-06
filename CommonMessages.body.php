<?php
/**
 * Copyright (C) 2014 Kunal Mehta <legoktm@debian.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

use Wikimedia\AtEase\AtEase;

class CommonMessages {

	/** @var array */
	protected $keys;

	public static function singleton() {
		static $self = null;
		if ( !$self ) {
			$self = new self;
		}
		return $self;
	}

	public function transformKey( $key ) {
		global $wgCommonMessagesPrefix;
		return $wgCommonMessagesPrefix . '-' . $key;
	}

	/**
	 * @param string $key non-transformed key
	 * @return bool
	 */
	public function isKeyRegistered( $key ) {
		if ( $this->keys === null ) {
			// Load the JSON file.
			AtEase::suppressWarnings();
			$file = file_get_contents( __DIR__ . '/export/en.json' );
			AtEase::restoreWarnings();
			if ( !$file ) {
				$this->keys = [];
				return false;
			}
			$json = FormatJson::decode( $file, true );
			if ( !$json ) {
				$this->keys = [];
				return false;
			}
			$this->keys = array_keys( $json );
		}
		return in_array( $this->transformKey( $key ), $this->keys );
	}
}
