<?php

/*
 * A function from WooCommerce
 */

function wc_get_product($the_product = false, $deprecated = array()) {
    
}

function shipping_info() {
    
}

function create_product_attribute($product, $attributeName, $attributeValue,
        $is_visible, $is_variation, $is_taxonomy) {
    // Get existing attributes
    $attributes = $product->get_attributes();

    $attribute_object = new WC_Product_Attribute();
    $attribute_object->set_name($attributeName);
    $attribute_object->set_options(array($attributeValue));

    $attribute_object->set_position(1);
    $attribute_object->set_visible($is_visible);
    $attribute_object->set_variation($is_variation);
    $attributes[] = $attribute_object;
    $product->set_attributes($attributes);

    $product->save();
}

function is_type(string $txt) {
    
}

function get_attribute(string $txt) {
    
}

function set_stock_quantity(int $nb) {
    
}

function set_stock_status(string $txt) {
    
}

function save() {
    
}

function update_post_meta(int $post_id, string $meta_key, mixed $meta_value, mixed $prev_value = ''): int|bool {
    
}
