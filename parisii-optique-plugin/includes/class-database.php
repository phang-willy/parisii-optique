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
     * Ensure all plugin tables exist (creates them if missing).
     * Call this on init so tables are created even if the plugin was not reactivated.
     */
    public static function maybe_create_tables() {
        global $wpdb;
        $table_contact = $wpdb->prefix . 'po_contact';
        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_contact));
        if ($exists !== $table_contact) {
            self::create_tables();
        } else {
            self::maybe_upgrade_contact_table();
        }
    }

    /**
     * Add read_flag, read_by, read_at columns if missing (existing installs).
     */
    public static function maybe_upgrade_contact_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'po_contact';
        $col = $wpdb->get_results($wpdb->prepare("SHOW COLUMNS FROM `$table` LIKE %s", 'read_flag'));
        if (empty($col)) {
            $wpdb->query("ALTER TABLE `$table` ADD COLUMN read_flag tinyint(1) DEFAULT 0 AFTER date_created, ADD COLUMN read_by bigint(20) UNSIGNED DEFAULT NULL AFTER read_flag, ADD COLUMN read_at datetime DEFAULT NULL AFTER read_by, ADD KEY read_flag (read_flag)");
        }
    }
    
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
        
        // Table: po_contact
        $table_contact = $wpdb->prefix . 'po_contact';
        $sql_contact = "CREATE TABLE IF NOT EXISTS $table_contact (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            nom varchar(255) NOT NULL,
            prenom varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            tel varchar(50) NOT NULL,
            sujet varchar(255) NOT NULL,
            message text NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP,
            read_flag tinyint(1) DEFAULT 0,
            read_by bigint(20) UNSIGNED DEFAULT NULL,
            read_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY date_created (date_created),
            KEY read_flag (read_flag)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_brand);
        dbDelta($sql_contact);
    }
    
    /**
     * Drop plugin tables
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'po_brand',
            $wpdb->prefix . 'po_contact',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
}

