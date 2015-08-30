<?php
/**
 * @package Class Registration
 * @versin 0.1
Plugin Name: Class Registration
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Vermont Chinese School class and activity registration, fee payment, and registration report
Author: Songshu
Version: 0.1
Author URI: http://www.sunnyinvitation.com
 * 
 */
 

register_activation_hook( __FILE__, 'clsreg_activation' );
//add_filter('show_admin_bar', '__return_false'); // Hide the admin bar in wordpress
/*
add_filter( 'register_url', 'my_register_url' );
function my_register_url( $url ) {
	return 'http://localhost/vermontchineseschool/registration';
}*/
/*
// Redirect Registration Page
function my_registration_page_redirect()
{
	global $pagenow;

	if ( ( strtolower($pagenow) == 'wp-login.php') && ( strtolower( $_GET['action']) == 'register' ) ) {
		wp_redirect( home_url('/registration-url'));
	}
}

add_filter( 'init', 'my_registration_page_redirect' );*/

function clsreg_activation() {
    // Get access to global database access class
    global $wpdb;
    // Create table on main blog in network mode or single blog
    clsreg_create_table( $wpdb->get_blog_prefix() );
}

function clsreg_create_table( $prefix ) {
    // Prepare SQL query to create database table
    // using function parameter
    /*
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        text text NOT NULL,
        url varchar(55) DEFAULT '' NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    */
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $creation_query =
        'CREATE TABLE ' . $prefix . "cls_course_data (
        cls_id int(20) NOT NULL AUTO_INCREMENT,
        cls_title varchar(50) DEFAULT NULL,
        cls_teacher varchar(50) DEFAULT NULL,
        cls_room varchar(10) DEFAULT NULL,
        cls_level int(3) NOT NULL DEFAULT 0,
        cls_language int(3) NOT NULL DEFAULT 0,
        cls_active tinyint(3) DEFAULT NULL,
        cls_create_date DATE DEFAULT NULL,
        cls_description text,
        cls_additionals text,
        PRIMARY KEY  (cls_id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $creation_query );

    $creation_query =
        'CREATE TABLE ' . $prefix . "cls_event_data (
        event_id int(8) NOT NULL AUTO_INCREMENT,
        event_date DATE DEFAULT NULL,
        event_type varchar(50) DEFAULT NULL,
        event_start_time TIME DEFAULT NULL,
        event_end_time TIME DEFAULT NULL,
        event_additionals text,
        PRIMARY KEY (event_id)
    )$charset_collate;";
    dbDelta( $creation_query );
    
    $creation_query =
        'CREATE TABLE ' . $prefix . "cls_student_data (
        sid int(8) NOT NULL AUTO_INCREMENT,
        last_name varchar(30) DEFAULT NULL,
        first_name varchar(30) DEFAULT NULL,
        gender tinyint(3) DEFAULT NULL,,
        birth_date DATE DEFAULT NULL,
        phone varchar(20) default null,
        address varchar(50) default null,
        PRIMARY KEY  (sid)
    )$charset_collate;";
    dbDelta( $creation_query );
    
    $creation_query =
        'CREATE TABLE ' . $prefix . "cls_relation_data (
        rid int(8) NOT NULL AUTO_INCREMENT,
        uid int(8) DEFAULT NULL,
        last_name varchar(30) DEFAULT NULL,
        first_name varchar(30) DEFAULT NULL,
        phone varchar(50) DEFAULT NULL,
        email TIME DEFAULT NULL,
        PRIMARY KEY  (rid)
    )$charset_collate;";
    dbDelta( $creation_query );
    
    $creation_query =
        'CREATE TABLE ' . $prefix . "cls_stu_rid (
        sid int DEFAULT NULL,
        rid int DEFAULT NULL,
        relation varchar(20) default null,
        PRIMARY KEY  (sid, rid),
        FOREIGN KEY (sid) REFERENCES ". $prefix . "cls_student_data(sid),
        FOREIGN KEY (rid) REFERENCES ". $prefix . "cls_relation_data(rid),
    )$charset_collate;";
    dbDelta( $creation_query );
    
    $creation_query =
        'CREATE TABLE ' . $prefix . "cls_stu_course (
        sid int DEFAULT NULL,
        cls_id int DEFAULT NULL,
        semester varchar(20) DEFAULT NULL,
        PRIMARY KEY  (sid, semester),
        FOREIGN KEY (sid) REFERENCES ". $prefix . "cls_student_data(sid),
        FOREIGN KEY (cls_id) REFERENCES ". $prefix . "cls_course_data(cls_id)
    )$charset_collate;";
    dbDelta( $creation_query );
}

require("class-general-settings.php");
require("class-reg-settings.php");
require("class-reg-form.php");
require("my-profile-form.php");
require("class-calendar-setup.php");

add_action( 'admin_menu', 'clsreg_admin_menu' );

function clsreg_admin_menu() {
    // Create top-level menu item
    add_menu_page( 'Class Registration Plugin Configuration Page',
        'ClassReg', 'manage_options',
        'clsreg-main-menu', 'clsreg_general_setting_page', plugins_url( 'myplugin.png', __FILE__ ) );
//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
    // Create a sub-menu under the top-level menu

    add_submenu_page( 'clsreg-main-menu',
        'Class Registration Settings', 'Class Setup',
        'manage_options', 'class-registration', 'clsreg_class_setup_page' );
 
    add_submenu_page( 'clsreg-main-menu',
        'Class Calendar Settings', 'Calendar Setup',
        'manage_options', 'calendar-setup', 'clsreg_calendar_setup_page' );

    add_submenu_page( 'clsreg-main-menu',
        'Registration & Calendar Reports', 'Reports',
        'manage_options', 'class-reports', 'clsreg_report_page' );

    add_action( 'admin_init', 'register_clsreg_general_settings' );
}
/*
add_action( 'admin_menu', 'clsreg_settings_menu' );

function clsreg_settings_menu() {
    add_options_page( 'Class Registration Data Management',
        'Class Registration', 'manage_options',
        'class-registration',
        'clsreg_config_page' );*/