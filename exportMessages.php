<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../..';
}

// Require base maintenance class
require_once( "$IP/maintenance/Maintenance.php" );

class ExportMessages extends Maintenance {

	public function execute() {
		global $wgCommonMessagesExportDir;
		$commons = CommonMessages::singleton();
		$dbr = wfGetDB( DB_SLAVE );
		// @todo Can we make this less memory intensive?
		$rows = $dbr->select(
			'page',
			array( 'page_namespace', 'page_title', 'page_id', 'page_latest' ),
			array( 'page_namespace' => NS_MEDIAWIKI ),
			__METHOD__
		);
		$messages = array();
		foreach( $rows as $row ) {
			$title = Title::newFromRow( $row );
			$exp = explode( '/', $title->getPrefixedText() );
			$lang = $exp[1];
			if ( !Language::isValidCode( $lang ) ) {
				wfDebugLog( 'MessageCommons', 'Found invalid page: ' . $title->getFullText() );
				continue;
			}
			$key = $commons->transformKey( strtolower( $title->getPrefixedText() ) );
			$messages[$lang][$key]
				= Revision::newFromTitle( $title )->getContent( Revision::RAW )->getNativeData();
		}
		// Now export.
		foreach ( $messages as $lang => $data ) {
			$json = FormatJson::encode( $data, true );
			file_put_contents( "$wgCommonMessagesExportDir/$lang.json", $json );
		}
	}
}

$maintClass = 'ExportMessages';
require_once( RUN_MAINTENANCE_IF_MAIN );
