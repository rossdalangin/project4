<?php
/**
 * Comprehensive Test Runner for Dynamic Schema JSON-LD Loader
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

class WP_User {
	public $ID;
	public $display_name;
	public $user_login;
	public $user_url;
	public $description;

	public function __construct( $id = 1, $display_name = 'Jane Doe <script>alert(1)</script>', $login = 'janedoe' ) {
		$this->ID           = $id;
		$this->display_name = $display_name;
		$this->user_login   = $login;
		$this->description  = 'Experienced SEO Specialist & Technical Writer. </script><script>alert("XSS")</script>';
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

	public function __construct( $id = 101, $title = 'Understanding Technical SEO </script>' ) {
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

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value, ...$args ) {
		return $value;
	}
}

if ( ! function_exists( 'plugin_basename' ) ) {
	function plugin_basename( $file ) {
		return basename( $file );
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return dirname( $file ) . '/';
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		$url = trim( $url );
		if ( preg_match( '#^(javascript|data|vbscript):#i', $url ) ) {
			return '';
		}
		return filter_var( $url, FILTER_SANITIZE_URL );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return trim( strip_tags( $str ) );
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
		return 'Understanding Technical SEO </script>';
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
			return 'Experienced SEO Specialist & Technical Writer. </script><script>alert("XSS")</script>';
		}
		if ( 'job_title' === $field ) {
			return 'Senior SEO Architect <b onmouseover="alert(1)">test</b>';
		}
		if ( 'twitter' === $field ) {
			return '@janedoe_seo';
		}
		if ( 'user_url' === $field ) {
			return 'javascript:alert(1)'; // Malicious protocol
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

echo "--- RUNNING DYNAMIC SCHEMA PRO SUITE ---\n";

$plugin = Dynamic_Schema_JSON_LD::get_instance();

// TEST 1: Public API function dynamic_schema_get_payload()
echo "\n[Test 1] Public API Function Test:\n";
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

$payload = dynamic_schema_get_payload();
if ( empty( $payload['@graph'] ) || count( $payload['@graph'] ) < 2 ) {
	echo "FAIL: Public API dynamic_schema_get_payload() failed to return graph\n";
	exit(1);
}
echo "PASS: Public API dynamic_schema_get_payload() verified.\n";

// TEST 2: Security & Escaping Check on Single Post
echo "\n[Test 2] Security & Escaping Check on Single Post:\n";
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

$inner_json = preg_replace( '#^<script type="application/ld\+json">\n?|\n?</script>\n?$#', '', trim( $output1 ) );
if ( strpos( $inner_json, '</script>' ) !== false ) {
	echo "FAIL: Unescaped </script> found inside JSON-LD payload!\n";
	exit(1);
}

if ( strpos( $output1, 'javascript:' ) !== false ) {
	echo "FAIL: Unsanitized javascript: URL found in payload!\n";
	exit(1);
}

if ( strpos( $output1, '<script>' ) !== false || strpos( $output1, '<b onmouseover' ) !== false ) {
	echo "FAIL: Unstripped HTML tags found in text fields!\n";
	exit(1);
}

echo "PASS: Security & Escaping verified.\n";

echo "\n--- ALL PRO TESTS PASSED SUCCESSFULLY! ---\n";
