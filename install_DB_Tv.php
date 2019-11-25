<?php

/**
 * Installe toutes les base de données si elles n'existent pas
 * Créé tous les rôles que nous avons besoin : Étudiant, Enseignant, Secretaire, Technicien et Télévision
 */

global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


$charset_collate = $wpdb->get_charset_collate();
$commissions_table_name = 'alerts';

$query = "CREATE TABLE IF NOT EXISTS $commissions_table_name (
			`ID_alert` INT(20) NOT NULL AUTO_INCREMENT,
			`author` VARCHAR( 255 ) NOT NULL ,
			`text` TEXT NOT NULL ,
			`creation_date` date NOT NULL,
			`end_date` date NOT NULL,
			`codes` varchar(510) NOT NULL,
			PRIMARY KEY  (ID_alert)
			) $charset_collate;";

dbDelta($query);

$sql = "ALTER TABLE `" . $commissions_table_name . "` AUTO_INCREMENT = 2;";
$wpdb->query($sql);

$commissions_table_name = 'code_ade';

$query = "CREATE TABLE IF NOT EXISTS $commissions_table_name (
			`ID` INT(11) NOT NULL AUTO_INCREMENT,
			`type` VARCHAR( 255 ) NOT NULL ,
			`title` VARCHAR ( 255 ) NOT NULL ,
			`code` VARCHAR ( 255 ) NOT NULL,
			PRIMARY KEY  (ID)
			) $charset_collate;";

dbDelta($query);

$sql = "ALTER TABLE `" . $commissions_table_name . "` AUTO_INCREMENT = 2;";
$wpdb->query($sql);

$commissions_table_name = 'code_delete_account';

$query = "CREATE TABLE IF NOT EXISTS $commissions_table_name (
			`ID` INT(20) NOT NULL AUTO_INCREMENT,
			`ID_user` INT( 20 ) NOT NULL ,
			`Code` VARCHAR ( 255 ) NOT NULL ,
			PRIMARY KEY  (ID)
			) $charset_collate;";

dbDelta($query);

$sql = "ALTER TABLE `" . $commissions_table_name . "` AUTO_INCREMENT = 2;";
$wpdb->query($sql);

$commissions_table_name = 'informations';

$query = "CREATE TABLE IF NOT EXISTS $commissions_table_name (
			`ID_info` INT(20) NOT NULL AUTO_INCREMENT,
			`title` VARCHAR ( 255 ) NOT NULL ,
			`author` VARCHAR ( 255 ) NOT NULL ,
			`creation_date` date NOT NULL,
			`end_date` date NOT NULL,
			`content` VARCHAR( 255 ) NOT NULL ,
			`type` VARCHAR ( 255 ) NOT NULL ,
			PRIMARY KEY  (ID_info)
			) $charset_collate;";

dbDelta($query);

$sql = "ALTER TABLE `" . $commissions_table_name . "` AUTO_INCREMENT = 2;";
$wpdb->query($sql);

$table = 'wp_users';
$col_name = 'code';

$col = $wpdb->get_results("SELECT $col_name FROM $table");


if (!$col) {
    $sql = "ALTER TABLE `" . $table . "` ADD `$col_name` VARCHAR(255) NOT NULL AFTER `user_pass`;";
    $wpdb->query($sql);
}

$result = add_role(
    'secretaire',
    __('Secretaire'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'television',
    __('Television'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'etudiant',
    __('Etudiant'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'enseignant',
    __('Enseignant'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'technicien',
    __('Technicien'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'directeuretude',
    __('Directeur etude'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);