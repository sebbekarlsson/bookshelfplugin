<?php

/**
 * Fetch all existing genres.
 *
 * @return Array<Object>
 */
function get_all_book_genres() {
    global $wpdb;
    $genres = [];    

    $rows = $wpdb->get_results('SELECT `id` FROM `wp_book_genres`');

    foreach ($rows as $row) {
        array_push($genres, new BookGenre($row->id));
    }

    return $genres;
}

/**
 * Fetch genres by title
 *
 * @param String $title
 *
 * @return Array<Object>
 */
function get_book_genres_by_title($title) {
    global $wpdb;
    $genres = [];    

    $sql = $wpdb->prepare(
        'SELECT `id` FROM `wp_book_genres` WHERE `title`=%s',
        $title
    );
    $rows = $wpdb->get_results($sql);

    foreach ($rows as $row) {
        array_push($genres, new BookGenre($row->id));
    }

    return $genres;
}

/**
 * Fetch books by genre_id
 *
 * @param Int $genre_id
 *
 * @return Object
 */
function get_books_by_genre_id($genre_id) {
    global $wpdb;
    $books = [];    

    $sql = $wpdb->prepare(
        '
        SELECT `book_id` FROM `wp_books_genres_relations`
        WHERE `genre_id`=%d
        ',
        $genre_id
    );
    $rows = $wpdb->get_results($sql);

    foreach ($rows as $row) {
        array_push($books, new Book($row->book_id));
    }

    return $books;
}

/**
 * Fetch genre by id
 *
 * @param $genre_id
 *
 * @return Object
 */
function get_book_genre($genre_id) {
    $genre = new BookGenre($genre_id);
    if (empty($genre->id)) { return null; }

    return $genre;
}

/**
 * Create new genre
 *
 * @param String $title
 *
 * @return Int
 */
function create_book_genre($title) {
    $gen = new BookGenre();
    $gen->title = $title;
    $gen->update();

    return $gen->id; 
}

/**
 * Create new relation between a book and a genre
 *
 * @param Int $book_id
 * @param Int $genre_id
 *
 * @return Int - id of relation
 */
function create_book_genre_relation($book_id, $genre_id) {
    global $wpdb;

    $sql = $wpdb->prepare("
    INSERT INTO ".$wpdb->prefix."books_genres_relations
    (`book_id`, `genre_id`)
    VALUES(%d, %d)
    ", $book_id, $genre_id);
    $wpdb->query($sql);

    return $wpdb->insert_id;
}

/**
 * Remove all genres from a book
 *
 * @param Int $book_id
 */
function remove_genres_from_book($book_id) {
    global $wpdb;

    if (empty($book_id)) { return false; }

    $sql = $wpdb->prepare('
        DELETE FROM `'.$wpdb->prefix.'books_genres_relations`
        WHERE `book_id`=%d
    ', $book_id);

    return $wpdb->query($sql);
}

/**
 * Create new book.
 * Updates existing book if $book_id is set.
 *
 * @param String $title
 * @param String $description
 * @param Int $genre_id
 * @param Int $attachment_id
 * @param Int $book_id
 *
 * @return Int - id of book
 */
function create_book($title, $description, $genre_id=0, $attachment_id=0, $book_id=null) {
    $book = new Book($book_id);
    $book->title = $title;
    $book->description = $description;
    $book->attachment_id = $attachment_id;
    $id = $book->update();

    create_book_genre_relation($id, $genre_id);

    return $id;
}
