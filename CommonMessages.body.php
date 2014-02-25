<?php

class CommonMessages {

	/** @var array $keys */
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
			wfSuppressWarnings();
			$file = file_get_contents( __DIR__ . '/export/en.json' );
			wfRestoreWarnings();
			if ( !$file ) {
				$this->keys = array();
				return false;
			}
			$json = FormatJson::decode( $file, true );
			if ( !$json ) {
				$this->keys = array();
				return false;
			}
			$this->keys = array_keys( $json );
		}
		return in_array( $this->transformKey( $key ), $this->keys );
	}
}