<?php
/**
 * gridnow functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package gridnow
 */

if ( ! function_exists( 'gridnow_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function gridnow_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on gridnow, use a find and replace
	 * to change 'gridnow' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'gridnow', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'menu-1' => esc_html__( 'Primary', 'gridnow' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'gridnow_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif;
add_action( 'after_setup_theme', 'gridnow_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function gridnow_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'gridnow_content_width', 640 );
}
add_action( 'after_setup_theme', 'gridnow_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function gridnow_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'gridnow' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'gridnow' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'gridnow_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function gridnow_scripts() {
	wp_enqueue_style( 'gridnow-style', get_stylesheet_uri() );

	wp_enqueue_script( 'gridnow-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'gridnow-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gridnow_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

add_action( 'load-post.php', 'annframe_meta_boxes_setup' );
add_action( 'load-post-new.php', 'annframe_meta_boxes_setup' );
add_action( 'save_post', 'annframe_save_post_meta', 10, 2 );

function annframe_meta_boxes_setup() {
  add_action( 'add_meta_boxes', 'annframe_add_meta_box' );
}

function annframe_add_meta_box() {
  add_meta_box(
      'featured_post',                    // Unique ID
      __( 'Featured Post', 'gridnow' ),    // Title
      'annframe_display_meta_box',        // Callback function
      'post',  // Admin page (or post type)
      'side',    // Context
      'high'
    );
}

function annframe_display_meta_box( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'ann_meta_boxes_nonce' );
  ?>
   <label for="meta-box-checkbox"><?php _e( 'Mark as featured', 'gridnow'); ?></label>
   <input type="checkbox" id="meta-box-checkbox"  name="meta-box-checkbox" value="yes" <?php if ( get_post_meta( $post->ID, 'featured_post', true ) == 'yes' ) echo ' checked="checked"'; ?>>
  <?php
}

// Save meta value.
function annframe_save_post_meta( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['ann_meta_boxes_nonce'] ) || !wp_verify_nonce( $_POST['ann_meta_boxes_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  $meta_box_checkbox_value = '';
  if( isset( $_POST["meta-box-checkbox"] ) ) {
    $meta_box_checkbox_value = $_POST["meta-box-checkbox"];
  }

  update_post_meta( $post_id, "featured_post", $meta_box_checkbox_value );
}

// add class.
function annframe_featured_class( $classes ) {
global $post;
if ( get_post_meta( $post->ID, 'featured_post' ) &&  get_post_meta( $post->ID, 'featured_post', true ) == 'yes' ) {
$classes[] = 'featured-post';
}
return $classes;
}
add_filter( 'post_class', 'annframe_featured_class' ); 


add_filter( 'post_thumbnail_html', 'my_post_image_html', 10, 3 );

function my_post_image_html( $html, $post_id, $post_image_id ) {

  $html = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_post_field( 'post_title', $post_id ) ) . '">' . $html . '</a>';
  return $html;

}



function wpb_add_google_fonts() {

wp_enqueue_style( 'wpb-google-fonts', 'https://fonts.googleapis.com/css?family=Titillium+Web:300,300i,400,600|Rubik:400,400i,500,500i', false );
}

add_action( 'wp_enqueue_scripts', 'wpb_add_google_fonts' );
