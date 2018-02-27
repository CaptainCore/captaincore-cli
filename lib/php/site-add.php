<?php

// Converts arguments --staging --all --plugin=woocommerce --theme=anchorhost into $staging $all
parse_str( implode( '&', $args ) );

// Decodes passwords
$password         = base64_decode( urldecode( $password ) );
$password_staging = base64_decode( urldecode( $password_staging ) );

// Check if site
$found_site = get_post( $id );

if ( $found_site ) {

	$my_post = array(

		'ID'          => $id,
		'post_title'  => $domain,
		'post_type'   => 'captcore_website',
		'post_status' => 'publish',
		'post_author' => '1',
		'meta_input'  => array(
			'install'                   => $install,
			'address'                   => $address,
			'username'                  => $username,
			'password'                  => $password,
			'protocol'                  => $protocol,
			'port'                      => $port,
			'homedir'                   => $homedir,
			'database_username'         => $database_username,
			'database_password'         => $database_password,
			'install_staging'           => $install_staging,
			'address_staging'           => $address_staging,
			'username_staging'          => $username_staging,
			'password_staging'          => $password_staging,
			'protocol_staging'          => $protocol_staging,
			'port_staging'              => $port_staging,
			'homedir_staging'           => $homedir_staging,
			'preloadusers'              => $preloadusers,
			'database_username_staging' => $database_username_staging,
			'database_password_staging' => $database_password_staging,
			's3accesskey '              => $s3accesskey,
			's3secretkey '              => $s3secretkey,
			's3bucket'                  => $s3bucket,
			's3path '                   => $s3path,

		),
	);

	echo "Site updated\n";

	wp_update_post( $my_post );

} else {

	$my_post = array(

		'import_id'   => intval( $id ),
		'post_title'  => $domain,
		'post_type'   => 'captcore_website',
		'post_status' => 'publish',
		'post_author' => '1',
		'meta_input'  => array(
			'install'                   => $install,
			'address'                   => $address,
			'username'                  => $username,
			'password'                  => $password,
			'protocol'                  => $protocol,
			'port'                      => $port,
			'homedir'                   => $homedir,
			'database_username'         => $database_username,
			'database_password'         => $database_password,
			'install_staging'           => $install_staging,
			'address_staging'           => $address_staging,
			'username_staging'          => $username_staging,
			'password_staging'          => $password_staging,
			'protocol_staging'          => $protocol_staging,
			'port_staging'              => $port_staging,
			'homedir_staging'           => $homedir_staging,
			'database_username_staging' => $database_username_staging,
			'database_password_staging' => $database_password_staging,
			'preloadusers'              => $preloadusers,
			's3accesskey '              => $s3accesskey,
			's3secretkey '              => $s3secretkey,
			's3bucket'                  => $s3bucket,
			's3path '                   => $s3path,

		),
	);


	wp_insert_post( $my_post, true );
	echo "Site added\n";
	if ( is_wp_error( $result ) ) {
		$error_string = $result->get_error_message();
		echo $error_string;
	}
}

// Rclone Import
$output = shell_exec( "captaincore generate rclone $install" );

// run initial backup, setups up token, install plugins
// and load custom configs into wp-config.php and .htaccess
// in a background process. Sent email when completed.
$output = shell_exec( 'captaincore site prep ' . $install . ' > /dev/null 2>/dev/null &' );