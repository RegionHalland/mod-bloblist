<?php
/*
 * Plugin Name: Mod-Bloblist
 * Plugin URI: -
 * Description: List files from azure blob storage by tag
 * Version: 1.0.0
 * Author: David Öhlin
 */

define('BLOBLIST_MODULE_PATH', plugin_dir_path(__FILE__));

/**
 * Registers the module
 */
add_action('Modularity', function() {
	modularity_register_module(
		BLOBLIST_MODULE_PATH,
		'Bloblist'
	);

	// Export and import ACF Fields
	$acfExportManager = new \AcfExportManager\AcfExportManager();
	$acfExportManager->setExportFolder(BLOBLIST_MODULE_PATH . 'acf/');
	$acfExportManager->import();
});