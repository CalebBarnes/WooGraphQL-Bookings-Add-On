<?php

/**
 * Plugin Name: WooGraphQL Bookings Add-On
 * Plugin URI: https://github.com/CalebBarnes/
 * Description: Add support for Bookable product type
 * Version: 0.1
 * Author: Caleb Barnes
 * Author URI: https://github.com/CalebBarnes/
 */

namespace App\GraphQL;
use WPGraphQL\Data\DataSource;



// register bookableResources root field with acf fields
add_filter( 'register_post_type_args', function( $args, $post_type ) {
	if ($post_type == 'bookable_resource') {
		 $args['show_in_graphql'] = true;
		 $args['graphql_single_name'] = 'bookableResource';
		 $args['graphql_plural_name'] = 'bookableResources';
	}

	return $args;

}, 10, 2 );

// add_action( 'admin_init', function (){
//     $product = wc_get_product(497);

// 	var_dump($product->get_resources());
// 	die();
// });


const TYPE_BOOKING_PRODUCT = 'BookingProduct';
const TYPE_BOOKING_RESOURCE = 'BookingResource';

/**
 * Register BookingProduct Type
 */
add_action( 'graphql_register_types', function () {

   /**
	 * Register our 'booking resource' object type
	 */
	register_graphql_object_type( TYPE_BOOKING_RESOURCE, [
			'description' => 'A product booking resource object',
			'interfaces'  => [ 'Node' ], // Following same pattern that other product types declare
			'fields'      =>
				[
					'databaseId' => [
						'type' => 'Int',
						'resolve' => function ( $source ) {
							return $source->get_id();
						}
					],
					'name' => [
						'type'    => 'String',
						'resolve' => function ( $source ) {
							return $source->get_name();
						},
					],
					// 'image'       => [
					// 	'type'        => 'MediaItem',
					// 	'description' => 'Booking Resource image',
					// 	'resolve'     => function( $source, array $args, AppContext $context ) {

					// 		$image = get_field('image', $source->get_id());

					// 		// return DataSource::resolve_post_object( $image["id"], $context );
					// 		return $image;

					// 	},
					// ],
					'description' => [
						'type' 			=> 'String',
						'description' 	=> 'Booking Resource description',
						'resolve' 		=> function ( $source ) {
							return get_field('description', $source->get_id());
						}
					],
				],
		]
    );

	/**
	 * Register our 'booking' object type
	 */
	register_graphql_object_type( TYPE_BOOKING_PRODUCT, [
			'description' => 'A product booking object',
			'interfaces'  => [ 'Node', 'Product' ], // Following same pattern that other product types declare
			'fields'      =>
				[
					'price' => [
						'type'    => 'String',
						'resolve' => function ( $source ) {
							return $source->get_price();
						},
					],
					'salePrice' => [
						'type'    => 'String',
						'resolve' => function ( $source ) {
							return $source->get_sale_price();
						},
					],
					'regularPrice' => [
						'type'    => 'String',
						'resolve' => function ( $source ) {
							return $source->get_regular_price();
						},
                    ],
					'resources' => [
						'type' => ['list_of' => TYPE_BOOKING_RESOURCE],
						'resolve' => function( $source ) {
							return $source->get_resources();
						}
					]
				],
		]
    );


 
    
	/**
	 * Register root query for bundle
   	 *
   	 * @todo - still need to provide args to pass in filter values such as ID
	 */
	register_graphql_field(
		'RootQuery',
		'bookingProduct',
		[
			'type' => TYPE_BOOKING_PRODUCT,
		]
	);
    
	/**
	 * Register root query for bundle
   	 *
   	 * @todo - still need to provide args to pass in filter values such as ID
	 */
	register_graphql_field(
		'RootQuery',
		'bookingResource',
		[
			'type' => TYPE_BOOKING_RESOURCE,
		]
	);


} );

// add the booking product type to GraphQL product types
add_filter( 'graphql_woocommerce_product_types', function ( $product_types ) {
	$product_types['booking'] = TYPE_BOOKING_PRODUCT;

	return $product_types;
} );

/**
 * Add the bundle to the product input->where to allow for filtering by bundles
 */

add_filter( 'graphql_product_types_enum_values', function ( $values ) {
	$values['BOOKING'] = [
		'value'       => 'booking',
		'description' => 'A booking product',
	];

	return $values;
} );