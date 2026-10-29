<?php
/**
 * Test Runner / Stubs for Dynamic Schema JSON-LD Loader
 */

// Define mock functions and classes if WordPress is not loaded

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

class WP_User {
	public $ID;
	public $display_name;
	public $user_login;
	public $user_url;
	public $description;

	public function __construct( $id = 1, $display_name = 'Jane Doe', $login = 'janedoe' ) {
		$this->ID           = $id;
		$this->display_name = $display_name;
		$this->user_login   = $login;
		$this->description  = 'Experienced SEO Specialist & Technical Writer.';
	}
}

class WP_Post {
	public $ID;
	public $post_title;
	public $post_author;
	public $post_date;
	public $post_modified;
	public $post_excerpt;
	public $post_content;
	public $post_type;

	public function __construct( $id = 101, $title = 'Understanding Technical SEO' ) {
		$this->ID            = $id;
		$this->post_title    = $title;
		$this->post_author   = 1;
		$this->post_date     = '2026-01-15 10:00:00';
		$this->post_modified = '2026-02-01 12:00:00';
		$this->post_excerpt  = 'A deep dive into Schema.org structured data and JSON-LD implementation.';
		$this->post_content  = 'Full post content here...';
		$this->post_type     = 'post';
	}
}

class WP_Term {
	public $term_id;
	public $name;
	public $slug;
	public $taxonomy;

	public function __construct( $id = 5, $name = 'SEO Best Practices', $slug = 'seo-best-practices', $taxonomy = 'category' ) {
		$this->term_id  = $id;
		$this->name     = $name;
		$this->slug     = $slug;
		$this->taxonomy = $taxonomy;
	}
}

class WP_Error {
	public function __construct() {}
}

// Global context simulation state
$GLOBALS['test_context'] = array(
	'is_front_page' => false,
	'is_home'       => false,
	'is_single'     => false,
	'is_singular'   => false,
	'is_author'     => false,
	'is_archive'    => false,
	'is_category'   => false,
	'is_tag'        => false,
	'is_tax'        => false,
	'queried_object' => null,
);

// Stub WordPress functions
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value, ...$args ) {
		return $value;
	}
}

if ( ! function_exists( 'is_front_page' ) ) {
	function is_front_page() {
		return $GLOBALS['test_context']['is_front_page'];
	}
}

if ( ! function_exists( 'is_home' ) ) {
	function is_home() {
		return $GLOBALS['test_context']['is_home'];
	}
}

if ( ! function_exists( 'is_single' ) ) {
	function is_single() {
		return $GLOBALS['test_context']['is_single'];
	}
}

if ( ! function_exists( 'is_singular' ) ) {
	function is_singular( $post_type = '' ) {
		return $GLOBALS['test_context']['is_singular'];
	}
}

if ( ! function_exists( 'is_author' ) ) {
	function is_author() {
		return $GLOBALS['test_context']['is_author'];
	}
}

if ( ! function_exists( 'is_archive' ) ) {
	function is_archive() {
		return $GLOBALS['test_context']['is_archive'];
	}
}

if ( ! function_exists( 'is_category' ) ) {
	function is_category() {
		return $GLOBALS['test_context']['is_category'];
	}
}

if ( ! function_exists( 'is_tag' ) ) {
	function is_tag() {
		return $GLOBALS['test_context']['is_tag'];
	}
}

if ( ! function_exists( 'is_tax' ) ) {
	function is_tax() {
		return $GLOBALS['test_context']['is_tax'];
	}
}

if ( ! function_exists( 'is_page' ) ) {
	function is_page() {
		return false;
	}
}

if ( ! function_exists( 'get_queried_object' ) ) {
	function get_queried_object() {
		return $GLOBALS['test_context']['queried_object'];
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $show = '' ) {
		if ( 'name' === $show ) {
			return 'Tech SEO Blog';
		}
		if ( 'description' === $show ) {
			return 'Insights and Guides on Technical SEO';
		}
		return '';
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '/' ) {
		return 'https://example.com' . $path;
	}
}

if ( ! function_exists( 'wp_get_canonical_url' ) ) {
	function wp_get_canonical_url() {
		return false;
	}
}

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post = 0 ) {
		return 'https://example.com/understanding-technical-seo/';
	}
}

if ( ! function_exists( 'get_author_posts_url' ) ) {
	function get_author_posts_url( $author_id ) {
		return 'https://example.com/author/janedoe/';
	}
}

if ( ! function_exists( 'get_term_link' ) ) {
	function get_term_link( $term, $taxonomy = '' ) {
		return 'https://example.com/category/seo-best-practices/';
	}
}

if ( ! function_exists( 'get_post_type_archive_link' ) ) {
	function get_post_type_archive_link( $post_type ) {
		return 'https://example.com/blog/';
	}
}

if ( ! function_exists( 'get_post_type' ) ) {
	function get_post_type( $post = null ) {
		return 'post';
	}
}

if ( ! function_exists( 'single_post_title' ) ) {
	function single_post_title( $prefix = '', $display = true ) {
		return 'Understanding Technical SEO';
	}
}

if ( ! function_exists( 'get_the_archive_title' ) ) {
	function get_the_archive_title() {
		return 'Archives: SEO Best Practices';
	}
}

if ( ! function_exists( 'get_the_archive_description' ) ) {
	function get_the_archive_description() {
		return 'Articles on SEO strategies and best practices.';
	}
}

if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post = null ) {
		if ( $post instanceof WP_Post ) {
			return $post;
		}
		return new WP_Post();
	}
}

if ( ! function_exists( 'get_userdata' ) ) {
	function get_userdata( $user_id ) {
		return new WP_User();
	}
}

if ( ! function_exists( 'get_the_author_meta' ) ) {
	function get_the_author_meta( $field, $user_id ) {
		if ( 'description' === $field ) {
			return 'Experienced SEO Specialist & Technical Writer.';
		}
		if ( 'job_title' === $field ) {
			return 'Senior SEO Architect';
		}
		if ( 'twitter' === $field ) {
			return '@janedoe_seo';
		}
		if ( 'linkedin' === $field ) {
			return 'janedoe-seo';
		}
		return '';
	}
}

if ( ! function_exists( 'get_the_category' ) ) {
	function get_the_category( $post_id ) {
		return array( new WP_Term() );
	}
}

if ( ! function_exists( 'get_ancestors' ) ) {
	function get_ancestors( $object_id, $object_type = '' ) {
		return array();
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
	}
}

if ( ! function_exists( 'has_post_thumbnail' ) ) {
	function has_post_thumbnail( $post = null ) {
		return true;
	}
}

if ( ! function_exists( 'get_post_thumbnail_id' ) ) {
	function get_post_thumbnail_id( $post = null ) {
		return 99;
	}
}

if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {
		return 'https://example.com/wp-content/uploads/2026/01/seo-guide.jpg';
	}
}

if ( ! function_exists( 'get_the_date' ) ) {
	function get_the_date( $format = '', $post = null ) {
		return '2026-01-15T10:00:00+00:00';
	}
}

if ( ! function_exists( 'get_the_modified_date' ) ) {
	function get_the_modified_date( $format = '', $post = null ) {
		return '2026-02-01T12:00:00+00:00';
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $string ) {
		return strip_tags( $string );
	}
}

if ( ! function_exists( 'wp_trim_words' ) ) {
	function wp_trim_words( $text, $num_words = 55 ) {
		return $text;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

// Load plugin code
require_once __DIR__ . '/dynamic-schema-ld-json.php';

echo "--- RUNNING DYNAMIC SCHEMA TESTS ---\n";

$plugin = Dynamic_Schema_JSON_LD::get_instance();

// TEST 1: Single Post Context
echo "\n[Test 1] Single Post Context:\n";
$GLOBALS['test_context'] = array(
	'is_front_page' => false,
	'is_home'       => false,
	'is_single'     => true,
	'is_singular'   => true,
	'is_author'     => false,
	'is_archive'    => false,
	'is_category'   => false,
	'is_tag'        => false,
	'is_tax'        => false,
	'queried_object' => new WP_Post(),
);

ob_start();
$plugin->output_schema();
$output1 = ob_get_clean();
echo $output1;

// Validate output
if ( strpos( $output1, '<script type="application/ld+json">' ) === false ) {
	echo "FAIL: Missing script tag\n";
	exit(1);
}
if ( strpos( $output1, '"BlogPosting"' ) === false ) {
	echo "FAIL: Missing BlogPosting schema\n";
	exit(1);
}
if ( strpos( $output1, '"Person"' ) === false ) {
	echo "FAIL: Missing Person schema\n";
	exit(1);
}
if ( strpos( $output1, '"Senior SEO Architect"' ) === false ) {
	echo "FAIL: Missing jobTitle in Person schema\n";
	exit(1);
}
echo "PASS: Single Post Context verified.\n";

// TEST 2: Author Context
echo "\n[Test 2] Author Context:\n";
$GLOBALS['test_context'] = array(
	'is_front_page' => false,
	'is_home'       => false,
	'is_single'     => false,
	'is_singular'   => false,
	'is_author'     => true,
	'is_archive'    => true,
	'is_category'   => false,
	'is_tag'        => false,
	'is_tax'        => false,
	'queried_object' => new WP_User(),
);

ob_start();
$plugin->output_schema();
$output2 = ob_get_clean();
echo $output2;

if ( strpos( $output2, '"ProfilePage"' ) === false ) {
	echo "FAIL: Missing ProfilePage schema for author context\n";
	exit(1);
}
if ( strpos( $output2, '"sameAs"' ) === false ) {
	echo "FAIL: Missing sameAs in author Person schema\n";
	exit(1);
}
echo "PASS: Author Context verified.\n";

// TEST 3: Front Page Context
echo "\n[Test 3] Front Page Context:\n";
$GLOBALS['test_context'] = array(
	'is_front_page' => true,
	'is_home'       => true,
	'is_single'     => false,
	'is_singular'   => false,
	'is_author'     => false,
	'is_archive'    => false,
	'is_category'   => false,
	'is_tag'        => false,
	'is_tax'        => false,
	'queried_object' => null,
);

ob_start();
$plugin->output_schema();
$output3 = ob_get_clean();
echo $output3;

if ( strpos( $output3, '"WebPage"' ) === false ) {
	echo "FAIL: Missing WebPage schema on front page\n";
	exit(1);
}
echo "PASS: Front Page Context verified.\n";

// TEST 4: Conflict Prevention (Yoast SEO defined)
echo "\n[Test 4] Conflict Prevention (Yoast SEO defined):\n";
define( 'WPSEO_VERSION', '20.0' );

ob_start();
$plugin->output_schema();
$output4 = ob_get_clean();

if ( ! empty( $output4 ) ) {
	echo "FAIL: Schema output was not suppressed when WPSEO_VERSION is defined\n";
	exit(1);
}
echo "PASS: Conflict Prevention verified.\n";

echo "\n--- ALL TESTS PASSED SUCCESSFULLY! ---\n";
