<?php

$captain_id = getenv('CAPTAIN_ID');

// Replaces dashes in keys with underscores
foreach($args as $index => $arg) {
	$split = strpos($arg, "=");
	if ( $split ) {
		$key = str_replace('-', '_', substr( $arg , 0, $split ) );
		$value = substr( $arg , $split, strlen( $arg ) );

		// Removes unnecessary bash quotes
		$value = trim( $value,'"' ); 				// Remove last quote 
		$value = str_replace( '="', '=', $value );  // Remove quote right after equals

		$args[$index] = $key.$value;
	} else {
		$args[$index] = str_replace('-', '_', $arg);
	}
}

// Converts --arguments into $arguments
parse_str( implode( '&', $args ) );

$lookup = ( new CaptainCore\Sites )->where( [ "site" => $site ] );

// Error if site not found
if ( count( $lookup ) == 0 ) {
	echo "Error: Site '$site' not found.";
	return;
}

$site           = ( new CaptainCore\Sites )->get( $lookup[0]->site_id );
$environment_id = ( new CaptainCore\Site( $site->site_id ) )->fetch_environment_id( $environment );
$environment    = ( new CaptainCore\Environments )->get( $environment_id );

$json        = "{$_SERVER['HOME']}/.captaincore/config.json";
$config_data = json_decode ( file_get_contents( $json ) );
$system      = $config_data[0]->system;

foreach($config_data as $config) {
	if ( isset( $config->captain_id ) and $config->captain_id == $captain_id ) {
		$configuration = $config;
		break;
	}
}

$details  = json_decode( $site->details );
$details->connection_errors = "SSH failed";
$site_update = [
    "site_id" => $site->site_id,
    "details" => json_encode( $details )
];
( new CaptainCore\Sites )->update( $site_update, [ "site_id" => $site->site_id ] );

// Prepare request to API
$request = [
    'method'  => 'POST',
    'headers' => [ 'Content-Type' => 'application/json' ],
    'body'    => json_encode( [ 
        "command" => "update-site",
        "site_id" => $site->site_id,
        "token"   => $configuration->keys->token,
        "data"    => $site_update,
    ] ),
];

if ( $system->captaincore_dev ) {
    $request['sslverify'] = false;
}

$response = wp_remote_post( $configuration->vars->captaincore_api, $request );
echo $response['body'];
