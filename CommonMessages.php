<?php

/**
 * CommonMessages extension
 *
 * Allows a wikifarm to have custom
 * message overrides easilly
 * Mainly designed for ShoutWiki's setup,
 * but can be used for any farm.
 *
 * @requires MediaWiki 1.23
 * @license WTFPL
 * @author Kunal Mehta <legoktm@gmail.com>
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit();
}

$wgExtensionCredits['other'][] = [
	'path' => __FILE__,
	'name' => 'CommonMessages',
	'author' => 'Kunal Mehta',
	'descriptionmsg' => 'commonmessages-desc',
	'version' => '0.0.1',
];

/**
 * Prefix of messages when exporting.
 * Must be configured.
 */
$wgCommonMessagesPrefix = '';

/**
 * Directory of where to export messages to
 * and where to read from
 * Must already exist
 */
$wgCommonMessagesExportDir = __DIR__ . '/export';

$wgExtensionFunctions[] = function () {
	global $wgCommonMessagesExportDir;
	if ( file_exists( $wgCommonMessagesExportDir . '/en.json' ) ) {
		// If an export has happened, load it
		$wgMessagesDirs['CommonMessagesExport'] = $wgCommonMessagesExportDir;
	}
};

$wgMessagesDirs['CommonMessages'] = __DIR__ . '/i18n';
$wgAutoloadClasses['CommonMessages'] = __DIR__ . '/CommonMessages.body.php';

$wgHooks['MessageCache::get'][] = function ( &$key ) {
	$commons = CommonMessages::singleton();
	if ( $commons->isKeyRegistered( $key ) ) {
		$key = $commons->transformKey( $key );
	}

	return true;
};
