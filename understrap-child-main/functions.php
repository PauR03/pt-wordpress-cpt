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

// Añadir el shortcode de entrantes
function return_platos()
{
	// $platos = get_posts(
	// 	array(
	// 		'post_type' => 'plato',
	// 		'posts_per_page' => -1,  // -1 para obtener todos los platos, puedes ajustar esto según tus necesidades.
	// 	)
	// );


	// foreach ($platos as $plato) {
	// 	$plato_id = $plato->ID;
	// 	$nombre_plato = $plato->post_title;
	// 	$precio_plato = get_field('price', $plato_id);

	// 	echo 'Nombre del plato: ' . $nombre_plato . '<br>';
	// 	echo 'Precio del plato: ' . $precio_plato . '<br>';
	// 	echo '<hr>';
	// }

	$PLATOS_ESTRELLA = [
		19,
		18,
		39,
		41
	];
	$MONEDA = "$";

	$platos = get_posts(
		[
			'post_type' => 'plato',
			'numberposts' => -1,
		]
	);
	$stringFinal = "<section class='menu entrantes'>\n";
	$stringFinal .= "<h1>Entrantes</h1>\n";
	$stringFinal .= "<ul class='plates'>\n";


	foreach ($platos as $plato) {
		$ID = $plato->ID;
		$platTitle = $plato->post_title;

		// Comprovante para ver si existe titulo del post, en caso que no, cogemos el titulo del como componente
		$platTitle = ($platTitle ? $platTitle : get_field('title', $ID));

		$platoPrice = get_field('price', $ID);
		$platoDescription = get_field('description', $ID);

		$stringFinal .= "<li class='plate'>\n";
		$stringFinal .= "	<div class='text'>\n";
		$stringFinal .= "		<div class='top'>\n";
		$stringFinal .= "			<span class='name'>" . trim($platTitle) . "</span>\n";
		$stringFinal .= "			<div class='line'>\n";
		if (in_array($ID, $PLATOS_ESTRELLA)) {
			$stringFinal .= "			<img src='http://localhost/wp-content/uploads/2024/02/smallBlackStar.png'>\n";
		}
		$stringFinal .= "			</div>\n";
		$stringFinal .= "		</div>\n";
		$stringFinal .= "		<div class='bottom'>\n";
		$stringFinal .= "			<span class='description'>" . trim($platoDescription) . "</span>\n";
		$stringFinal .= "		</div>\n";
		$stringFinal .= "	</div>\n";
		$stringFinal .= "	<div class='price'>\n";
		$stringFinal .= "		<span class='currency'>$MONEDA</span>\n";
		$stringFinal .= "		<span class='number'>" . trim($platoPrice) . "</span>\n";
		$stringFinal .= "	</div>\n";
		$stringFinal .= "</li>\n";

	}
	$stringFinal .= "</ul>\n";
	$stringFinal .= "</section>\n";
	return $stringFinal;
}
add_shortcode('entrantes', 'return_platos');

// Enlaza el archivo style.css
function understrap_child_enqueue_styles()
{
	wp_enqueue_style('understrap-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style('understrap-child-style', get_stylesheet_directory_uri() . '/style.css', array('understrap-style'));
}
add_action('wp_enqueue_scripts', 'understrap_child_enqueue_styles');