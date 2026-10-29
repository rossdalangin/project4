<?php
/**
 * Plugin Name: Dynamic Schema JSON-LD Loader
 * Plugin URI:  https://example.com/dynamic-schema-json-ld
 * Description: A lightweight, self-contained WordPress plugin that dynamically outputs Schema.org JSON-LD structured data inside <head>.
 * Version:     1.0.0
 * Author:      Technical SEO Engineer
 * Author URI:  https://example.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: dynamic-schema-json-ld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Dynamic_Schema_JSON_LD' ) ) {

	/**
	 * Main Dynamic Schema JSON-LD Plugin Class
	 */
	class Dynamic_Schema_JSON_LD {

		/**
		 * Singleton instance
		 *
		 * @var Dynamic_Schema_JSON_LD|null
		 */
		private static $instance = null;

		/**
		 * Get singleton instance
		 *
		 * @return Dynamic_Schema_JSON_LD
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'wp_head', array( $this, 'output_schema' ), 20 );
		}

		/**
		 * Check if conflicting SEO plugins are present or active.
		 *
		 * Specifically checks for Yoast SEO and Rank Math.
		 *
		 * @return bool True if output should be disabled due to conflict, false otherwise.
		 */
		public function is_conflict_detected() {
			$conflict = false;

			// Check for Yoast SEO
			if ( defined( 'WPSEO_VERSION' ) || class_exists( 'WPSEO_Options' ) || defined( 'WPSEO_FILE' ) ) {
				$conflict = true;
			}

			// Check for Rank Math
			if ( defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' ) || defined( 'RANK_MATH_FILE' ) ) {
				$conflict = true;
			}

			/**
			 * Filter to allow themes/plugins to override conflict detection.
			 *
			 * @param bool $conflict Whether a conflict was detected.
			 */
			return apply_filters( 'dynamic_schema_conflict_detected', $conflict );
		}

		/**
		 * Determine whether schema output is enabled.
		 *
		 * @return bool
		 */
		public function is_enabled() {
			$enabled = ! $this->is_conflict_detected();

			/**
			 * Filter whether dynamic schema output is enabled.
			 *
			 * @param bool $enabled True if enabled, false if disabled.
			 */
			return (bool) apply_filters( 'dynamic_schema_enable_output', $enabled );
		}

		/**
		 * Safely escape and validate URL for JSON-LD output.
		 *
		 * @param string $url
		 * @return string
		 */
		public function sanitize_url( $url ) {
			if ( empty( $url ) || ! is_string( $url ) ) {
				return '';
			}
			return esc_url_raw( trim( $url ) );
		}

		/**
		 * Safely sanitize text fields for JSON-LD output.
		 *
		 * @param string $text
		 * @return string
		 */
		public function sanitize_text( $text ) {
			if ( empty( $text ) || ! is_string( $text ) ) {
				return '';
			}
			return sanitize_text_field( wp_strip_all_tags( $text ) );
		}

		/**
		 * Get canonical URL for current context.
		 *
		 * @return string
		 */
		public function get_canonical_url() {
			$url = '';

			if ( is_singular() ) {
				$url = wp_get_canonical_url();
				if ( ! $url ) {
					$url = get_permalink();
				}
			} elseif ( is_front_page() || is_home() ) {
				$url = home_url( '/' );
			} elseif ( is_author() ) {
				$author = get_queried_object();
				if ( $author && isset( $author->ID ) ) {
					$url = get_author_posts_url( $author->ID );
				} else {
					$url = home_url( '/' );
				}
			} elseif ( is_category() || is_tag() || is_tax() ) {
				$term = get_queried_object();
				if ( $term && isset( $term->term_id ) ) {
					$link = get_term_link( $term );
					$url  = ! is_wp_error( $link ) ? $link : home_url( '/' );
				}
			} elseif ( is_archive() ) {
				$post_type = get_post_type();
				$link      = get_post_type_archive_link( $post_type );
				$url       = $link ? $link : home_url( '/' );
			} else {
				$url = home_url( '/' );
			}

			$url = $this->sanitize_url( $url );

			return apply_filters( 'dynamic_schema_canonical_url', $url );
		}

		/**
		 * Get title for current page context.
		 *
		 * @return string
		 */
		public function get_page_title() {
			$title = '';

			if ( is_front_page() || is_home() ) {
				$title = get_bloginfo( 'name' );
			} elseif ( is_singular() ) {
				$title = single_post_title( '', false );
			} elseif ( is_author() ) {
				$author = get_queried_object();
				$title  = ( $author && isset( $author->display_name ) ) ? $author->display_name : get_the_archive_title();
			} elseif ( is_archive() ) {
				$title = get_the_archive_title();
			} else {
				$title = get_bloginfo( 'name' );
			}

			return apply_filters( 'dynamic_schema_page_title', $this->sanitize_text( $title ) );
		}

		/**
		 * Get description for current page context.
		 *
		 * @return string
		 */
		public function get_page_description() {
			$description = '';

			if ( is_front_page() || is_home() ) {
				$description = get_bloginfo( 'description' );
			} elseif ( is_singular() ) {
				$post = get_post();
				if ( $post ) {
					if ( ! empty( $post->post_excerpt ) ) {
						$description = $post->post_excerpt;
					} elseif ( ! empty( $post->post_content ) ) {
						$description = wp_trim_words( $post->post_content, 30 );
					}
				}
			} elseif ( is_author() ) {
				$author = get_queried_object();
				if ( $author && ! empty( $author->description ) ) {
					$description = $author->description;
				}
			} elseif ( is_archive() ) {
				$desc = get_the_archive_description();
				if ( $desc ) {
					$description = $desc;
				}
			}

			if ( empty( $description ) ) {
				$description = get_bloginfo( 'description' );
			}

			return apply_filters( 'dynamic_schema_page_description', $this->sanitize_text( $description ) );
		}

		/**
		 * Build Person schema object.
		 *
		 * @param WP_User|int $user User object or ID.
		 * @return array
		 */
		public function build_person_schema( $user ) {
			if ( is_numeric( $user ) ) {
				$user = get_userdata( $user );
			}

			if ( ! $user || ! ( $user instanceof WP_User ) ) {
				return array();
			}

			$user_id      = $user->ID;
			$display_name = $user->display_name ? $user->display_name : $user->user_login;
			$author_url   = $this->sanitize_url( get_author_posts_url( $user_id ) );
			$bio          = get_the_author_meta( 'description', $user_id );

			// Job Title - check user meta fields
			$job_title = get_the_author_meta( 'job_title', $user_id );
			if ( empty( $job_title ) ) {
				$job_title = get_the_author_meta( 'user_job_title', $user_id );
			}
			if ( empty( $job_title ) ) {
				$job_title = get_the_author_meta( 'headline', $user_id );
			}

			// Gather sameAs URLs
			$same_as = array();

			$user_url = get_the_author_meta( 'user_url', $user_id );
			if ( ! empty( $user_url ) ) {
				$clean_url = $this->sanitize_url( $user_url );
				if ( ! empty( $clean_url ) ) {
					$same_as[] = $clean_url;
				}
			}

			$social_fields = array( 'facebook', 'twitter', 'linkedin', 'instagram', 'github', 'youtube' );
			foreach ( $social_fields as $field ) {
				$val = get_the_author_meta( $field, $user_id );
				if ( ! empty( $val ) ) {
					$constructed_url = '';
					if ( filter_var( $val, FILTER_VALIDATE_URL ) ) {
						$constructed_url = $val;
					} elseif ( 'twitter' === $field ) {
						$constructed_url = 'https://twitter.com/' . ltrim( $val, '@' );
					} elseif ( 'facebook' === $field ) {
						$constructed_url = 'https://facebook.com/' . $val;
					} elseif ( 'linkedin' === $field ) {
						$constructed_url = 'https://linkedin.com/in/' . $val;
					} elseif ( 'instagram' === $field ) {
						$constructed_url = 'https://instagram.com/' . ltrim( $val, '@' );
					}

					$clean_social = $this->sanitize_url( $constructed_url );
					if ( ! empty( $clean_social ) ) {
						$same_as[] = $clean_social;
					}
				}
			}

			$meta_same_as = get_the_author_meta( 'same_as', $user_id );
			if ( ! empty( $meta_same_as ) ) {
				if ( is_array( $meta_same_as ) ) {
					foreach ( $meta_same_as as $item ) {
						$clean_item = $this->sanitize_url( $item );
						if ( ! empty( $clean_item ) ) {
							$same_as[] = $clean_item;
						}
					}
				} else {
					$lines = explode( "\n", str_replace( "\r", "", $meta_same_as ) );
					foreach ( $lines as $line ) {
						$clean_line = $this->sanitize_url( trim( $line ) );
						if ( ! empty( $clean_line ) ) {
							$same_as[] = $clean_line;
						}
					}
				}
			}

			$same_as = array_values( array_unique( array_filter( $same_as ) ) );

			$person_schema = array(
				'@type' => 'Person',
				'@id'   => $author_url . '#person',
				'name'  => $this->sanitize_text( $display_name ),
				'url'   => $author_url,
			);

			if ( ! empty( $bio ) ) {
				$person_schema['description'] = $this->sanitize_text( $bio );
			}

			if ( ! empty( $job_title ) ) {
				$person_schema['jobTitle'] = $this->sanitize_text( $job_title );
			}

			if ( ! empty( $same_as ) ) {
				$person_schema['sameAs'] = $same_as;
			}

			return apply_filters( 'dynamic_schema_person_data', $person_schema, $user );
		}

		/**
		 * Build Article / BlogPosting schema object.
		 *
		 * @param WP_Post|int|null $post
		 * @return array
		 */
		public function build_article_schema( $post = null ) {
			$post = get_post( $post );
			if ( ! $post ) {
				return array();
			}

			$canonical_url = $this->get_canonical_url();
			$headline      = single_post_title( '', false );
			if ( empty( $headline ) ) {
				$headline = $post->post_title;
			}

			$pub_date  = get_the_date( 'c', $post );
			$mod_date  = get_the_modified_date( 'c', $post );
			$author_id = $post->post_author;
			$author    = get_userdata( $author_id );

			$author_schema = $author ? $this->build_person_schema( $author ) : array();

			$publisher_schema = array(
				'@type' => 'Organization',
				'@id'   => $this->sanitize_url( home_url( '/#organization' ) ),
				'name'  => $this->sanitize_text( get_bloginfo( 'name' ) ),
				'url'   => $this->sanitize_url( home_url( '/' ) ),
			);

			$article_type = apply_filters( 'dynamic_schema_article_type', 'BlogPosting', $post );

			$article_schema = array(
				'@type'            => $this->sanitize_text( $article_type ),
				'@id'              => $canonical_url . '#article',
				'isPartOf'         => array(
					'@id' => $canonical_url,
				),
				'headline'         => $this->sanitize_text( $headline ),
				'datePublished'    => $this->sanitize_text( $pub_date ),
				'dateModified'     => $this->sanitize_text( $mod_date ),
				'mainEntityOfPage' => array(
					'@type' => 'WebPage',
					'@id'   => $canonical_url,
				),
				'publisher'        => $publisher_schema,
			);

			if ( ! empty( $author_schema ) ) {
				$article_schema['author'] = $author_schema;
			}

			$description = $this->get_page_description();
			if ( ! empty( $description ) ) {
				$article_schema['description'] = $description;
			}

			if ( has_post_thumbnail( $post ) ) {
				$thumbnail_id  = get_post_thumbnail_id( $post );
				$image_url     = wp_get_attachment_image_url( $thumbnail_id, 'full' );
				if ( $image_url ) {
					$clean_image = $this->sanitize_url( $image_url );
					if ( ! empty( $clean_image ) ) {
						$article_schema['image'] = $clean_image;
					}
				}
			}

			return apply_filters( 'dynamic_schema_article_data', $article_schema, $post );
		}

		/**
		 * Build BreadcrumbList schema object.
		 *
		 * @return array
		 */
		public function build_breadcrumb_schema() {
			$items = array();

			// Item 1: Home
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => $this->sanitize_text( get_bloginfo( 'name' ) ),
				'item'     => $this->sanitize_url( home_url( '/' ) ),
			);

			$position = 2;

			if ( is_front_page() || is_home() ) {
				// Only Home item needed on front page
			} elseif ( is_single() ) {
				$post = get_post();
				if ( $post ) {
					$categories = get_the_category( $post->ID );
					if ( ! empty( $categories ) ) {
						// Primary category
						$cat = $categories[0];
						$parents = get_ancestors( $cat->term_id, 'category' );
						if ( ! empty( $parents ) ) {
							$parents = array_reverse( $parents );
							foreach ( $parents as $parent_id ) {
								$parent_term = get_term( $parent_id, 'category' );
								if ( $parent_term && ! is_wp_error( $parent_term ) ) {
									$items[] = array(
										'@type'    => 'ListItem',
										'position' => $position++,
										'name'     => $this->sanitize_text( $parent_term->name ),
										'item'     => $this->sanitize_url( get_term_link( $parent_term ) ),
									);
								}
							}
						}
						$items[] = array(
							'@type'    => 'ListItem',
							'position' => $position++,
							'name'     => $this->sanitize_text( $cat->name ),
							'item'     => $this->sanitize_url( get_term_link( $cat ) ),
						);
					}

					$items[] = array(
						'@type'    => 'ListItem',
						'position' => $position++,
						'name'     => $this->sanitize_text( single_post_title( '', false ) ?: $post->post_title ),
						'item'     => $this->get_canonical_url(),
					);
				}
			} elseif ( is_page() ) {
				$post = get_post();
				if ( $post ) {
					if ( $post->post_parent ) {
						$ancestors = get_post_ancestors( $post->ID );
						$ancestors = array_reverse( $ancestors );
						foreach ( $ancestors as $ancestor_id ) {
							$items[] = array(
								'@type'    => 'ListItem',
								'position' => $position++,
								'name'     => $this->sanitize_text( get_the_title( $ancestor_id ) ),
								'item'     => $this->sanitize_url( get_permalink( $ancestor_id ) ),
							);
						}
					}
					$items[] = array(
						'@type'    => 'ListItem',
						'position' => $position++,
						'name'     => $this->sanitize_text( get_the_title( $post ) ),
						'item'     => $this->get_canonical_url(),
					);
				}
			} elseif ( is_category() || is_tag() || is_tax() ) {
				$term = get_queried_object();
				if ( $term && isset( $term->term_id ) ) {
					if ( taxonomy_exists( $term->taxonomy ) && is_taxonomy_hierarchical( $term->taxonomy ) ) {
						$parents = get_ancestors( $term->term_id, $term->taxonomy );
						if ( ! empty( $parents ) ) {
							$parents = array_reverse( $parents );
							foreach ( $parents as $parent_id ) {
								$parent_term = get_term( $parent_id, $term->taxonomy );
								if ( $parent_term && ! is_wp_error( $parent_term ) ) {
									$items[] = array(
										'@type'    => 'ListItem',
										'position' => $position++,
										'name'     => $this->sanitize_text( $parent_term->name ),
										'item'     => $this->sanitize_url( get_term_link( $parent_term ) ),
									);
								}
							}
						}
					}
					$items[] = array(
						'@type'    => 'ListItem',
						'position' => $position++,
						'name'     => $this->sanitize_text( $term->name ),
						'item'     => $this->get_canonical_url(),
					);
				}
			} elseif ( is_author() ) {
				$author = get_queried_object();
				if ( $author && isset( $author->display_name ) ) {
					$items[] = array(
						'@type'    => 'ListItem',
						'position' => $position++,
						'name'     => $this->sanitize_text( $author->display_name ),
						'item'     => $this->get_canonical_url(),
					);
				}
			} elseif ( is_archive() ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => $this->sanitize_text( get_the_archive_title() ),
					'item'     => $this->get_canonical_url(),
				);
			}

			$breadcrumb_schema = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $this->get_canonical_url() . '#breadcrumb',
				'itemListElement' => $items,
			);

			return apply_filters( 'dynamic_schema_breadcrumb_data', $breadcrumb_schema );
		}

		/**
		 * Build WebPage schema object.
		 *
		 * @return array
		 */
		public function build_webpage_schema() {
			$canonical_url = $this->get_canonical_url();
			$title         = $this->get_page_title();
			$description   = $this->get_page_description();

			$page_type = 'WebPage';
			if ( is_front_page() || is_home() ) {
				$page_type = 'WebPage';
			} elseif ( is_singular() ) {
				$page_type = 'ItemPage';
			} elseif ( is_author() ) {
				$page_type = 'ProfilePage';
			} elseif ( is_archive() ) {
				$page_type = 'CollectionPage';
			}

			$webpage_schema = array(
				'@type'       => $page_type,
				'@id'         => $canonical_url,
				'url'         => $canonical_url,
				'name'        => $title,
				'description' => $description,
				'isPartOf'    => array(
					'@type' => 'WebSite',
					'@id'   => $this->sanitize_url( home_url( '/#website' ) ),
					'name'  => $this->sanitize_text( get_bloginfo( 'name' ) ),
					'url'   => $this->sanitize_url( home_url( '/' ) ),
				),
			);

			return apply_filters( 'dynamic_schema_webpage_data', $webpage_schema );
		}

		/**
		 * Route and compile schema payload based on query context.
		 *
		 * @return array Schema graph payload
		 */
		public function compile_schema() {
			$graph = array();

			// Always include WebPage and BreadcrumbList
			$webpage    = $this->build_webpage_schema();
			$breadcrumb = $this->build_breadcrumb_schema();

			if ( ! empty( $webpage ) ) {
				$graph[] = $webpage;
			}

			if ( ! empty( $breadcrumb ) ) {
				$graph[] = $breadcrumb;
			}

			// Contextual handling
			if ( is_single() || is_singular( 'post' ) ) {
				$article = $this->build_article_schema();
				if ( ! empty( $article ) ) {
					$graph[] = $article;
				}

				$post = get_post();
				if ( $post && $post->post_author ) {
					$author = get_userdata( $post->post_author );
					if ( $author ) {
						$person = $this->build_person_schema( $author );
						if ( ! empty( $person ) ) {
							$graph[] = $person;
						}
					}
				}
			} elseif ( is_author() ) {
				$author = get_queried_object();
				if ( $author && $author instanceof WP_User ) {
					$person = $this->build_person_schema( $author );
					if ( ! empty( $person ) ) {
						$graph[] = $person;
					}
				}
			}

			$schema_data = array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			);

			return apply_filters( 'dynamic_schema_compiled_payload', $schema_data );
		}

		/**
		 * Output JSON-LD schema into wp_head safely.
		 */
		public function output_schema() {
			if ( ! $this->is_enabled() ) {
				return;
			}

			$schema_data = $this->compile_schema();

			if ( empty( $schema_data['@graph'] ) ) {
				return;
			}

			$json = wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

			if ( ! $json ) {
				return;
			}

			// Prevent inline script tag closure XSS breakout attempts
			$json = str_replace( '</script', '<\/script', $json );

			echo '<script type="application/ld+json">' . "\n";
			echo $json . "\n";
			echo '</script>' . "\n";
		}
	}

	/**
	 * Initialize the plugin
	 */
	function dynamic_schema_json_ld_init() {
		return Dynamic_Schema_JSON_LD::get_instance();
	}

	add_action( 'plugins_loaded', 'dynamic_schema_json_ld_init' );
}
