<?php

class PhpInfo extends Dashboard_Plugin {
	private $qs_val = 'qs-dashboard-phpinfo';
	private $default_phpinfo_output = 'all';
	private $phpinfo_types = array(
		'general'		=> INFO_GENERAL,
		'configuration' => INFO_CONFIGURATION,
		'modules'		=> INFO_MODULES,
		'environment'	=> INFO_ENVIRONMENT,
		'variables'		=> INFO_VARIABLES,
		'license'		=> INFO_LICENSE,
		'all'			=> INFO_ALL,
	);
	
    public function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'quickstart_dashboard_admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }
	
	function enqueue_scripts() {
		if ( is_admin() && isset( $_REQUEST['page'] ) && 'quickstart-phpinfo' == $_REQUEST['page'] ) {
			wp_enqueue_script( 'phpinfo-js', plugins_url( 'js/phpinfo.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		}
	}
	
	function admin_init() {
		if ( is_admin() && isset( $_GET[$this->qs_val] ) && current_user_can( 'manage_options' ) ) {
			check_admin_referer( $this->qs_val );

			if ( isset( $_GET['type'] ) && isset( $this->phpinfo_types[$_GET['type']] ) ) {
				phpinfo( $this->phpinfo_types[$_GET['type']] );
			} else {
				phpinfo();
			}

			die();
        }
	}

    public function name() {
        return __( 'PHP Info', 'quickstart-dashboard' );
    }

    function admin_menu() {
        add_submenu_page( 'vip-dashboard', $this->name(), $this->name(), 'manage-options', 'quickstart-phpinfo', array( $this, 'show' ) );
    }
    
    function show() {
		$phpinfo_url = sprintf( '%s/wp-admin/?%s=true&_wpnonce=%s', get_bloginfo('wpurl'), $this->qs_val, wp_create_nonce( $this->qs_val ) );

        ?>
        <div class="wrap">
            <h2><?php echo $this->name(); ?></h2>
			<input id="phpinfo-baseurl" type="hidden" value="<?php echo $phpinfo_url; ?>" />
			<p>
				<?php foreach ( $this->phpinfo_types as $slug => $type ): ?>
				<input class="phpinfo_selector button <?php echo ($slug == $this->default_phpinfo_output) ? 'button-primary' : '' ?>" type="button" value="<?php echo $slug; ?>" />
				<?php endforeach; ?>
				<span class="spinner" style="display: inline;"></span>
			</p>
			<iframe id="phpinfo-window" src="<?php echo $phpinfo_url; ?>" style="width: 100%; min-height: 300px;"></iframe>
			<div class="clear"></div>
        </div>
        <?php
    }
}
