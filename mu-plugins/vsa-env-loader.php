<?php
/**
 * Plugin Name: VisitSaudiArab Environment Loader
 * Description: Loads environment variables from a file outside the document root
 *              so database credentials and salts are never committed to Git.
 * Version: 1.0.0
 * Author: VisitSaudiArab
 *
 * Usage: add the following line at the TOP of wp-config.php, BEFORE any define() calls:
 *
 *     require_once __DIR__ . '/wp-content/mu-plugins/vsa-env-loader.php';
 *
 * Then in wp-config.php use getenv() for each constant, for example:
 *
 *     define( 'DB_NAME', getenv( 'DB_NAME' ) ?: 'fallback_db_name' );
 *
 * Place the real environment file outside public_html, e.g.:
 *     /home/yourcpaneluser/private/visitsaudiarab.env
 * Or set the VSA_ENV_PATH environment variable in cPanel/Apache.
 *
 * NOTE: this file intentionally does not guard on ABSPATH because it must be
 * includable from wp-config.php before WordPress defines ABSPATH.
 */

if ( ! function_exists( 'vsa_load_environment' ) ) {

	/**
	 * Locate the environment file.
	 *
	 * Priority:
	 *   1. $_SERVER['VSA_ENV_PATH'] if set and the file exists.
	 *   2. Common cPanel-safe locations outside public_html.
	 *
	 * @return string|null Absolute path to the .env file, or null if not found.
	 */
	function vsa_find_env_file() {
		if ( ! empty( $_SERVER['VSA_ENV_PATH'] ) && file_exists( $_SERVER['VSA_ENV_PATH'] ) ) {
			return $_SERVER['VSA_ENV_PATH'];
		}

		$home = null;
		if ( ! empty( $_SERVER['HOME'] ) ) {
			$home = $_SERVER['HOME'];
		} elseif ( ! empty( $_ENV['HOME'] ) ) {
			$home = $_ENV['HOME'];
		} elseif ( function_exists( 'posix_getpwuid' ) && function_exists( 'posix_getuid' ) ) {
			$user = posix_getpwuid( posix_getuid() );
			if ( ! empty( $user['dir'] ) ) {
				$home = $user['dir'];
			}
		}

		if ( empty( $home ) ) {
			return null;
		}

		$candidates = array(
			$home . '/private/visitsaudiarab.env',
			$home . '/private/.env',
			$home . '/.env',
			$home . '/visitsaudiarab.env',
		);

		foreach ( $candidates as $candidate ) {
			if ( file_exists( $candidate ) ) {
				return $candidate;
			}
		}

		return null;
	}

	/**
	 * Parse a simple KEY=VALUE environment file and expose values to PHP.
	 *
	 * Does NOT overwrite values that are already present in $_ENV/$_SERVER.
	 * Known WordPress keys are mapped to their constant names only if the
	 * constant has not already been defined.
	 *
	 * @param string $path Absolute path to the environment file.
	 */
	function vsa_parse_env_file( $path ) {
		if ( ! is_readable( $path ) ) {
			return;
		}

		$lines = file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		if ( false === $lines ) {
			return;
		}

		$wordpress_constant_map = array(
			'DB_NAME'           => 'DB_NAME',
			'DB_USER'           => 'DB_USER',
			'DB_PASSWORD'       => 'DB_PASSWORD',
			'DB_HOST'           => 'DB_HOST',
			'DB_CHARSET'        => 'DB_CHARSET',
			'DB_COLLATE'        => 'DB_COLLATE',
			'WP_HOME'           => 'WP_HOME',
			'WP_SITEURL'        => 'WP_SITEURL',
			'WP_ENV'            => 'WP_ENVIRONMENT_TYPE',
			'WP_DEBUG'          => 'WP_DEBUG',
			'WP_DEBUG_LOG'      => 'WP_DEBUG_LOG',
			'WP_DEBUG_DISPLAY'  => 'WP_DEBUG_DISPLAY',
			'AUTH_KEY'          => 'AUTH_KEY',
			'SECURE_AUTH_KEY'   => 'SECURE_AUTH_KEY',
			'LOGGED_IN_KEY'     => 'LOGGED_IN_KEY',
			'NONCE_KEY'         => 'NONCE_KEY',
			'AUTH_SALT'         => 'AUTH_SALT',
			'SECURE_AUTH_SALT'  => 'SECURE_AUTH_SALT',
			'LOGGED_IN_SALT'    => 'LOGGED_IN_SALT',
			'NONCE_SALT'        => 'NONCE_SALT',
			'COOKIE_DOMAIN'     => 'COOKIE_DOMAIN',
		);

		foreach ( $lines as $line ) {
			$line = trim( $line );

			// Skip comments and empty lines.
			if ( '' === $line || '#' === $line[0] ) {
				continue;
			}

			$equals_at = strpos( $line, '=' );
			if ( false === $equals_at ) {
				continue;
			}

			$key   = trim( substr( $line, 0, $equals_at ) );
			$value = trim( substr( $line, $equals_at + 1 ) );

			if ( '' === $key ) {
				continue;
			}

			// Remove surrounding quotes.
			$value_length = strlen( $value );
			if ( $value_length >= 2 ) {
				$first = $value[0];
				$last  = $value[ $value_length - 1 ];
				if ( ( '"' === $first && '"' === $last ) || ( "'" === $first && "'" === $last ) ) {
					$value = substr( $value, 1, -1 );
				}
			}

			// Expose to PHP environment without overwriting existing values.
			if ( false === getenv( $key ) ) {
				putenv( "{$key}={$value}" );
			}
			if ( ! isset( $_ENV[ $key ] ) ) {
				$_ENV[ $key ] = $value;
			}
			if ( ! isset( $_SERVER[ $key ] ) ) {
				$_SERVER[ $key ] = $value;
			}

			// Define WordPress constants if this key maps to one and it is not defined yet.
			if ( isset( $wordpress_constant_map[ $key ] ) && ! defined( $wordpress_constant_map[ $key ] ) ) {
				$constant_name = $wordpress_constant_map[ $key ];

				if ( in_array( $constant_name, array( 'WP_DEBUG', 'WP_DEBUG_LOG', 'WP_DEBUG_DISPLAY' ), true ) ) {
					define( $constant_name, filter_var( $value, FILTER_VALIDATE_BOOL ) );
				} else {
					define( $constant_name, $value );
				}
			}
		}
	}

	/**
	 * Main entry point: find and load the environment file once.
	 */
	function vsa_load_environment() {
		static $loaded = false;
		if ( $loaded ) {
			return;
		}
		$loaded = true;

		$env_file = vsa_find_env_file();
		if ( $env_file ) {
			vsa_parse_env_file( $env_file );
		}
	}

	vsa_load_environment();
}
