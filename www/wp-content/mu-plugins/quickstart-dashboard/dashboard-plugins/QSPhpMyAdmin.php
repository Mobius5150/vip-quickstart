<?php

class QSPhpMyAdmin extends Dashboard_Plugin {
	public function init() {
		add_action( 'quickstart_dashboard_admin_menu', array( $this, 'admin_menu' ) );
	}

	public function name() {
		return __( 'phpMyAdmin', 'quickstart-dashboard' );
	}

	function admin_menu() {
		add_submenu_page( 'vip-dashboard', $this->name(), $this->name(), 'manage-options', 'qs-phpmyadmin', array( $this, 'show' ) );
	}
	
	function show() {
		?>
		<iframe src="<?php bloginfo( 'wpurl' ); ?>/phpmyadmin"></iframe>
		<?php
	}
}
