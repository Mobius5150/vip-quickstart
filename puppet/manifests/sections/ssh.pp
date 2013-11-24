class { 'ssh':
	serveroptions => {
		'PasswordAuthentication' => 'no',
		'PermitRootLogin'        => 'no',
	}
}

ssh::match { 'sftpusers':
	type    => 'group',
	options => {
		'X11Forwarding'      => 'no',
		'AllowTCPForwarding' => 'no',
		'GatewayPorts'       => 'no',
		'ForceCommand'       => 'internal-sftp',
		'ChrootDirectory'    => '/srv',
	},
}
