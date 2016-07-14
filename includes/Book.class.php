<?php

/**
 * wp_books model
 */
class Book {
    var $id;
    var $updated;
    var $title;
    var $description;
    var $attachment_id = 0;
    var $meta = [];
    var $genres = [];

    function __construct($id=null) {
        $this->id = $id;

        if ($this->id != null) {
            $this->build();
        }
    }

    /**
     * Fetches all data about this book and applies it to the model.
     *
     * @return Void
     */
    function build() {
        global $wpdb;

        $row = $wpdb->get_row(
            "SELECT * FROM ".$wpdb->prefix."books WHERE id=".$this->id
        );

        $this->updated = $row->updated;
        $this->title = $row->title;
        $this->description = $row->description;
        $this->attachment_id = $row->attachment_id;

        $meta_rows = $wpdb->get_results(
            "SELECT * FROM ".$wpdb->prefix."books_meta WHERE book_id=".$this->id
        );
        foreach ($meta_rows as $m) {
            $this->meta[$m->name] = $m->value;
        }

        $rows = $wpdb->get_results($wpdb->prepare(
            'SELECT `genre_id` FROM `'.
            $wpdb->prefix.'books_genres_relations` WHERE `book_id`=%d',
            $this->id
        ));

        foreach ($rows as $row) {
            $genre = get_book_genre($row->genre_id);
            array_push($this->genres, $genre);
        }
    }

    /**
     * Updates/inserts book.
     *
     * @return Int - id of book
     */
    function update() {
        global $wpdb;

        if ($this->id == null) {
            $sql = $wpdb->prepare(
                "
                INSERT INTO ".$wpdb->prefix.'books'."
                (`title`, `description`, `attachment_id`)
                VALUES
                (%s, %s, %d)
                ",
            $this->title,
                $this->description,
                $this->attachment_id
            );
            $wpdb->query($sql);
            return $wpdb->insert_id;
        } else {
            $sql = $wpdb->prepare(
                "
                UPDATE ".$wpdb->prefix.'books'."
                SET
                `title`=%s,
                `description`=%s,
                `attachment_id`=%d
                WHERE `id`=%d
                ",
            $this->title,
                $this->description,
                $this->attachment_id,
                $this->id
            );
            $wpdb->query($sql);
            return $this->id;
        }
    }

    /**
     * Deletes the book with all of its connections from the database.
     *
     * @return Void
     */
    function delete() {
        global $wpdb;

        $wpdb->query($wpdb->prepare('
            DELETE FROM `'.$wpdb->prefix.'books_genres_relations`
            WHERE `book_id`=%d; ', $this->id));

        $wpdb->query($wpdb->prepare('
            DELETE FROM `'.$wpdb->prefix.'books_meta`
            WHERE `book_id`=%d;', $this->id));
        
        $wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.'books`
            WHERE `id`=%d;
        ', $this->id));
    }
}
