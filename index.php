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
	 * Register our 'booking resource' object type
	 */
	register_graphql_object_type( TYPE_BOOKING_RESOURCE, [
			'description' => 'A product booking resource object',
			'interfaces'  => [ 'Node' ], // Following same pattern that other product types declare
			'fields'      =>
				[
					'id' => [
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