<?php

WP_CLI::add_command( 'dashboard', 'Quickstart_Dashboard_CLI' );

class Quickstart_Dashboard_CLI extends WP_CLI_Command {
	/**
	 * @var string Type of command triggered so we can keep track of killswitch cleanup.
	 */
	private $command = '';

	/**
	 * @var bool Flag whether or not execution should be stopped.
	 */
	private $halt = false;

	/**
	 * @subcommand load_plugins
	 */
	function load_plugins( $args, $assoc_args ) {
		WP_CLI::line( 'Loading plugins...' );
		$instance = Quickstart_Dashboard::get_instance();

		$plugins = $instance->load_plugins();

		WP_CLI::line( 'Plugins loaded: ' );
		foreach ( $plugins as $name => $plugin ) {
			WP_CLI::line( "	$name: {$plugin->name()}" );
		}
	}

	/**
	 * @subcommand scan_repos
	 */
	function scan_repos( $args, $assoc_args ) {
		WP_CLI::line( 'Scanning Known Repositories...' );
		
		$repo_monitor = $this->load_repo_monitor();

		if ( ! $repo_monitor ) {
			return;
		}

		foreach ( $repo_monitor->get_repos() as $repo ) {
			// Run the command to determine if it needs an update
			WP_CLI::log( " Scanning {$repo['repo_type']} repo {$repo['repo_friendly_name']}...\n" );
			if ( 'svn' == $repo['repo_type'] ) {
				$results = $repo_monitor->scan_svn_repo( $repo['repo_path'] );
			} elseif ( 'git' == $repo['repo_type'] ) {
				$results = $repo_monitor->scan_git_repo( $repo['repo_path'] );
			}
		}

		WP_CLI::line( 'Scan complete' );
	}

    /**
     * Adds a repository to the Repo Monitor.
     *
     * ## OPTIONS
     *
     * <path>
     * : The path to the repository to add
	 * <name>
	 * : The friendly name of the repository
     *
     * ## EXAMPLES
     *
     *     wp dashboard add_repo --name=Quickstart /srv
	 *     wp dashboard add_repo --svn --name=WordPress /srv/www/wp
     *
     * @synopsis <path> [--warn] [--svn] [--name=<name>]
	 */
	function add_repo( $args, $assoc_args ) {
		$type = 'git';
		if ( $assoc_args['svn'] ) {
			$type = 'svn';
		}

		WP_CLI::line( "Adding $type repository {$assoc_args['name']}..." );
		WP_CLI::line( "Repo path: {$args[0]}" );

		$repo_monitor = $this->load_repo_monitor();
		
		if ( ! $repo_monitor ) {
			return;
		}

		$result = $repo_monitor->add_repo( array(
			'repo_type'			 => $type,
			'repo_path'			 => $args[0],
			'repo_friendly_name' => $assoc_args['name'],
			'warn_out_of_date'   => ! $assoc_args['warn'],
		) );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( "error: ");
			WP_CLI::error( $result->get_error_message() );
			return;
		}

		WP_CLI::success( "Repo added with id $result!" );
	}

	/**
	 *
	 * @return RepoMonitor|bool The RepoMonitor plugin or false on failure
	 */
	private function load_repo_monitor() {
		$instance = Quickstart_Dashboard::get_instance();
		$plugins = $instance->load_plugins();
		
		if ( ! isset( $plugins['RepoMonitor'] ) ) {
			WP_CLI::error( 'Could not find RepoMonitor plugin' );
			return false;
		}

		return $plugins['RepoMonitor'];
	}
}