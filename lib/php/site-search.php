<?php

// Converts arguments --staging --all --plugin=woocommerce --plugin_status=active --theme=anchorhost into $staging $all
parse_str( str_replace( '-', '_', implode( '&', $args ) ) );

$arguments = array(
	'post_type'      => 'captcore_website',
	'posts_per_page' => '-1',
	'fields'         => 'ids',
	'meta_or_title'  => $search,
	'meta_query'     => array(
		'relation' => 'and',
		array(
			'key'     => 'status', // name of custom field
			'value'   => 'active', // matches exaclty "123", not just 123. This prevents a match for "1234"
			'compare' => '=',
		),
		array(
			'key'     => 'site', // name of custom field
			'value'   => '',
			'compare' => '!=',
		),
		array(
      'relation' => 'OR',
			array(
				'key'     => 'address', // name of custom field
				'value'   => $search,
				'compare' => 'like',
			),
			array(
				'key'     => 'site', // name of custom field
				'value'   => $search,
				'compare' => 'like',
			),
		),
	),
);



$websites = get_posts( $arguments );

$results = array();

foreach ( $websites as $website_id ) {

	if ($field && $field == "domain") {
		$site = get_the_title( $website_id );
	} elseif ($field) {
		$site = get_post_meta( $website_id, $field, true );
	} else {
		$site = get_post_meta( $website_id, 'site', true );
	}
	$results[] = $site;

}

echo implode( ' ', $results );
