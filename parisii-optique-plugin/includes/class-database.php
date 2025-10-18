<?php
/**
 * Database management class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Database {
    
    /**
     * Create plugin tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Table: po_brand
        $table_brand = $wpdb->prefix . 'po_brand';
        $sql_brand = "CREATE TABLE IF NOT EXISTS $table_brand (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            logo varchar(500) DEFAULT NULL,
            visible tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY name (name)
        ) $charset_collate;";
        
        // Table: po_brand_category
        $table_brand_category = $wpdb->prefix . 'po_brand_category';
        $sql_brand_category = "CREATE TABLE IF NOT EXISTS $table_brand_category (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY name (name)
        ) $charset_collate;";
        
        // Table: po_brand_categories (junction table)
        $table_brand_categories = $wpdb->prefix . 'po_brand_categories';
        $sql_brand_categories = "CREATE TABLE IF NOT EXISTS $table_brand_categories (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_brand bigint(20) UNSIGNED NOT NULL,
            id_brand_category bigint(20) UNSIGNED NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY brand_category_unique (id_brand, id_brand_category),
            KEY id_brand (id_brand),
            KEY id_brand_category (id_brand_category)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_brand);
        dbDelta($sql_brand_category);
        dbDelta($sql_brand_categories);
    }
    
    /**
     * Drop plugin tables
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'po_brand_categories',
            $wpdb->prefix . 'po_brand',
            $wpdb->prefix . 'po_brand_category',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
}

