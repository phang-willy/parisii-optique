<?php
/**
 * Brand Category class - CRUD operations
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Brand_Category {
    
    /**
     * Get all categories
     */
    public static function get_all($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand_category';
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $orderby = esc_sql($args['orderby']);
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        
        return $wpdb->get_results("SELECT * FROM $table ORDER BY $orderby $order");
    }
    
    /**
     * Get category by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand_category';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Create a new category
     */
    public static function create($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand_category';
        
        $result = $wpdb->insert(
            $table,
            array('name' => sanitize_text_field($data['name'])),
            array('%s')
        );
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update a category
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand_category';
        
        return $wpdb->update(
            $table,
            array('name' => sanitize_text_field($data['name'])),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Delete a category
     */
    public static function delete($id) {
        global $wpdb;
        
        // Delete brand associations first
        $wpdb->delete(
            $wpdb->prefix . 'po_brand_categories',
            array('id_brand_category' => $id),
            array('%d')
        );
        
        // Delete category
        return $wpdb->delete(
            $wpdb->prefix . 'po_brand_category',
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Count brands in category
     */
    public static function count_brands($category_id) {
        global $wpdb;
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}po_brand_categories WHERE id_brand_category = %d",
            $category_id
        ));
    }
}

