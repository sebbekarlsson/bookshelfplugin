<?php
global $bookshelf_db_version;
$bookshelf_db_version = '1.6';

function bookshelf_install() {
    global $wpdb;
    global $bookshelf_db_version;

    $charset = $wpdb->get_charset_collate();
    $sql = 
        "
        CREATE TABLE ".$wpdb->prefix.'bookshelfs'." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        updated TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        genre_id mediumint(9),
        UNIQUE KEY id (id)
        ) $charset;

        CREATE TABLE ".$wpdb->prefix.'books'." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        updated TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        title VARCHAR(256),
        description LONGTEXT,
        attachment_id mediumint(9),
        UNIQUE KEY id (id)
        ) $charset;

        CREATE TABLE ".$wpdb->prefix.'books_meta'." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        updated TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        name VARCHAR(256) NOT NULL,
        value LONGTEXT NOT NULL,
        book_id mediumint(9),
        UNIQUE KEY id (id)
        ) $charset;

        CREATE TABLE ".$wpdb->prefix.'book_genres'." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        updated TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        title VARCHAR(256),
        UNIQUE KEY id (id)
        ) $charset;

        CREATE TABLE ".$wpdb->prefix.'books_genres_relations'." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        updated TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        book_id mediumint(9),
        genre_id mediumint(9),
        UNIQUE KEY id (id)
        ) $charset;
    ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'bookshelf_db_version', $bookshelf_db_version );
}
register_activation_hook( __FILE__, 'bookshelf_install' );

function bookshelf_install_data() {
    $standard_genres = [
        "action",
        "adventure",
        "fantasy",
        "drama",
        "history"
    ];

    foreach ($standard_genres as $g) {
        create_book_genre($g);
    }
}

register_activation_hook( __FILE__, 'bookshelf_install_data' );

function bookshelf_update_db_check() {
    global $bookshelf_db_version;
    if ( get_site_option( 'bookshelf_db_version' ) != $bookshelf_db_version ) {
        bookshelf_install();
        bookshelf_install_data();
        //update_option( "bookshelf_db_version", $bookshelf_db_version );
    }
}
add_action( 'plugins_loaded', 'bookshelf_update_db_check' );
