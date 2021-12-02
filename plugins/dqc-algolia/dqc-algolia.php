<?php

/*
  Plugin Name: DQC Algolia
  Plugin URI:
  Description: Fully customise WordPress search implementing algolia API
  Version: 1.0
  Author: Social Driver
  Author URI: https://www.socialdriver.com/
 */

namespace MavenAlgolia;

// Exit if accessed directly 
if ( !defined( 'ABSPATH' ) )
	exit;

/* DEVELOPMENT NEEDS TO UPDATE THE FOLLOWING

../core/indexer.php
Update function postToAlgoliaObject using $row as your data object that is delivered to Algolia

The following definitions allow you to map specific domains to specific indexes for prod, staging, and development environments.

START CONSTANTS */

define( 'DEVSITE', "dataqualitycampaign.local" );
define( 'DEVINDEX', "DQC Search - Development" );

define( 'STAGSITE', "dataqualstag.wpengine.com" );
define( 'STAGINDEX', "DQC Search - Staging" );

define( 'PRODSITE', "dataqualitycampaign.org" );
define( 'PRODINDEX', "DQC Search" );

/* END CONSTANTS */

//These are the only require_once needed. Then you should use the Loader class
require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/core/loader.php';
Core\Loader::load( plugin_dir_path( __FILE__ ), array( 'core/registry', 'core/utils',  'core/utils-algolia', 'core/initializer', 'core/fields-helper', 'core/indexer', 'core/searcher', 'admin/controllers/settings', 'admin/controllers/indexer', 'core/domain/field', 'core/domain/meta-field', 'core/domain/post-taxonomy', 'core/domain/post-type', 'core/domain/taxonomy' ) );

$registry = Core\Registry::instance();
$registry->setPluginDir( plugin_dir_path( __FILE__ ) );
$registry->setPluginUrl( defined( 'DEV_ENV' ) && DEV_ENV ? WP_PLUGIN_URL . "/dqc-algolia/" : plugin_dir_url( __FILE__ )  );
$registry->setPluginVersion( "0.4" );
$registry->setPluginName( 'Algolia' );
$registry->setPluginShortName( 'mvnAlg' );
$registry->init();

/**
 * We need to register the namespace of the plugin. It will be used for autoload function to add the required files. 
 */
Core\Loader::registerType( "MavenAlgolia", $registry->getPluginDir() );
Core\Initializer::init();

if( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ){
	$settings = Admin\Controllers\Settings::instance();
	$adminIndexer = Admin\Controllers\Indexer::instance();
}else{
	// TODO: Check if we should do this here or if would be better to call it just in search pages
	if( $registry->isEnabled() ){
		try {
			$searcher = new Core\Searcher( $registry->getAppId() , $registry->getApiKey() );
		} catch ( Exception $exc ) {
			// TODO: save this to show to the admin
			$error = $exc->getTraceAsString();
		}
	}
}