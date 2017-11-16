<?php

/*
	Plugin Name: Category Descriptions
	Plugin URI:
	Plugin Description: Allows category descriptions.
	Plugin Version: 1.0.0
	Plugin Date: 2016-07-19
	Plugin Author: 38qa.net
	Plugin Author URI: http://38qa.net/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.7
	Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// CONSTANT value
@define( 'CAT_DESC_DIR', dirname( __FILE__ ) );
@define( 'CAT_DESC_FOLDER', basename( dirname( __FILE__ ) ) );
@define( 'CAT_DESC_RELATIVE_PATH', '../qa-plugin/'.CAT_DESC_FOLDER.'/');

qa_register_plugin_module(
	'widget', // type of module
	'qa-cat-desc-widget.php', // PHP file containing module class
	'qa_cat_descriptions_widget', // module class name in that PHP file
	'Category Descriptions' // human-readable name of module
);

// qa_register_plugin_module(
// 	'page', // type of module
// 	'qa-cat-desc-edit.php', // PHP file containing module class
// 	'qa_cat_descriptions_edit_page', // name of module class
// 	'Category Description Edit Page' // human-readable name of module
// );

qa_register_plugin_overrides('qa-cat-desc-overrides.php');

qa_register_plugin_layer(
	'qa-cat-desc-layer.php', // PHP file containing layer
	'Category Description Plugin Layer' // human-readable name of layer
);

qa_register_plugin_phrases(
	'qa-cat-desc-lang-*.php', // pattern for language files
	'plugin_cat_desc' // prefix to retrieve phrases
);
