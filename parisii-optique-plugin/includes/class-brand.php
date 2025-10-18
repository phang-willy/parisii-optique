<?php
/**
 * Brand class - CRUD operations
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Brand {
    
    /**
     * Get all brands
     */
    public static function get_all($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand';
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'visible_only' => false,
            'search' => '',
            'offset' => 0,
            'limit' => 999999,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array();
        
        if ($args['visible_only']) {
            $where[] = 'visible = 1';
        }
        
        if (!empty($args['search'])) {
            $search = esc_sql($wpdb->esc_like($args['search']));
            $where[] = "name LIKE '%{$search}%'";
        }
        
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $orderby = esc_sql($args['orderby']);
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        $offset = absint($args['offset']);
        $limit = absint($args['limit']);
        
        $query = "SELECT * FROM $table $where_clause ORDER BY $orderby $order LIMIT $offset, $limit";
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get brand by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Create a new brand
     */
    public static function create($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand';
        
        $defaults = array(
            'name' => '',
            'logo' => '',
            'visible' => 1,
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $result = $wpdb->insert(
            $table,
            array(
                'name' => sanitize_text_field($data['name']),
                'logo' => esc_url_raw($data['logo']),
                'visible' => absint($data['visible']),
            ),
            array('%s', '%s', '%d')
        );
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update a brand
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand';
        
        $update_data = array();
        $format = array();
        
        if (isset($data['name'])) {
            $update_data['name'] = sanitize_text_field($data['name']);
            $format[] = '%s';
        }
        
        if (isset($data['logo'])) {
            $update_data['logo'] = esc_url_raw($data['logo']);
            $format[] = '%s';
        }
        
        if (isset($data['visible'])) {
            $update_data['visible'] = absint($data['visible']);
            $format[] = '%d';
        }
        
        if (empty($update_data)) {
            return false;
        }
        
        return $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            $format,
            array('%d')
        );
    }
    
    /**
     * Delete a brand
     */
    public static function delete($id) {
        global $wpdb;
        
        // Delete brand categories associations first
        $wpdb->delete(
            $wpdb->prefix . 'po_brand_categories',
            array('id_brand' => $id),
            array('%d')
        );
        
        // Delete brand
        return $wpdb->delete(
            $wpdb->prefix . 'po_brand',
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Get brand categories
     */
    public static function get_categories($brand_id) {
        global $wpdb;
        
        $query = $wpdb->prepare(
            "SELECT bc.* FROM {$wpdb->prefix}po_brand_category bc
            INNER JOIN {$wpdb->prefix}po_brand_categories bcs ON bc.id = bcs.id_brand_category
            WHERE bcs.id_brand = %d
            ORDER BY bc.name ASC",
            $brand_id
        );
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Set brand categories
     */
    public static function set_categories($brand_id, $category_ids) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand_categories';
        
        // Delete existing associations
        $wpdb->delete($table, array('id_brand' => $brand_id), array('%d'));
        
        // Insert new associations
        if (!empty($category_ids)) {
            foreach ($category_ids as $category_id) {
                $wpdb->insert(
                    $table,
                    array(
                        'id_brand' => $brand_id,
                        'id_brand_category' => $category_id,
                    ),
                    array('%d', '%d')
                );
            }
        }
        
        return true;
    }
    
    /**
     * Count brands
     */
    public static function count($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_brand';
        
        $where = array();
        
        if (!empty($args['visible_only'])) {
            $where[] = 'visible = 1';
        }
        
        if (!empty($args['search'])) {
            $search = esc_sql($wpdb->esc_like($args['search']));
            $where[] = "name LIKE '%{$search}%'";
        }
        
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table $where_clause");
    }
    
    /**
     * Get brands by category
     */
    public static function get_by_category($category_id, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'visible_only' => true,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array();
        $where[] = $wpdb->prepare('bcs.id_brand_category = %d', $category_id);
        
        if ($args['visible_only']) {
            $where[] = 'b.visible = 1';
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where);
        
        $orderby = esc_sql($args['orderby']);
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        
        $query = "SELECT DISTINCT b.* FROM {$wpdb->prefix}po_brand b
            INNER JOIN {$wpdb->prefix}po_brand_categories bcs ON b.id = bcs.id_brand
            $where_clause
            ORDER BY b.$orderby $order";
        
        return $wpdb->get_results($query);
    }
}

