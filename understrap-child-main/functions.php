<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts()
{
	wp_dequeue_style('understrap-styles');
	wp_deregister_style('understrap-styles');

	wp_dequeue_script('understrap-scripts');
	wp_deregister_script('understrap-scripts');
}
add_action('wp_enqueue_scripts', 'understrap_remove_scripts', 20);



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles()
{

	// Get the theme data.
	$the_theme = wp_get_theme();
	$theme_version = $the_theme->get('Version');

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";

	$css_version = $theme_version . '.' . filemtime(get_stylesheet_directory() . $theme_styles);

	wp_enqueue_style('child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version);
	wp_enqueue_script('jquery');

	$js_version = $theme_version . '.' . filemtime(get_stylesheet_directory() . $theme_scripts);

	wp_enqueue_script('child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true);
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain()
{
	load_child_theme_textdomain('understrap-child', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'add_child_theme_textdomain');



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version()
{
	return 'bootstrap5';
}
add_filter('theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20);



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js()
{
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array('customize-preview'),
		'20130508',
		true
	);
}
add_action('customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js');


function quitar_editor_en_plato()
{
	remove_post_type_support('plato', 'editor');
}

add_action('init', 'quitar_editor_en_plato');

// Add Shortcode
function obtener_platos_con_campos_personalizados()
{
	global $wpdb;

	// Nombre de las tablas
	$tabla_cpt = $wpdb->prefix . 'posts';
	$tabla_postmeta = $wpdb->prefix . 'postmeta';

	// Consulta SQL para obtener platos del CPT "plato" con campos personalizados de ACF
	$consulta = "
        SELECT p.ID, p.post_title, pm.meta_key, pm.meta_value
        FROM $tabla_cpt AS p
        LEFT JOIN $tabla_postmeta AS pm ON p.ID = pm.post_id
        WHERE p.post_type = 'plato'
        AND p.post_status = 'publish'
    ";

	// Ejecutar la consulta
	$resultados = $wpdb->get_results($consulta);

	// Organizar los resultados por ID del plato
	$platos_con_campos = array();
	foreach ($resultados as $resultado) {
		$platos_con_campos[$resultado->ID]['post_title'] = $resultado->post_title;
		$platos_con_campos[$resultado->ID]['campos'][$resultado->meta_key] = $resultado->meta_value;
	}

	// Devolver los resultados

	return var_dump($platos_con_campos);
}