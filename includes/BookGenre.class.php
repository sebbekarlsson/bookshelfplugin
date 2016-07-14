<?php

class BookGenre {
    var $id;
    var $title;
    var $updated;
    var $books_count = 0;

    function __construct($id=null) {
        $this->id = $id;

        if ($this->id != null) {
            $this->build();
        }
    }

    function build() {
        global $wpdb;

        $row = $wpdb->get_row(
            "SELECT * FROM ".$wpdb->prefix."book_genres WHERE id=".$this->id
        );

        $this->updated = $row->updated;
        $this->title = $row->title;

        $row = $wpdb->get_row($wpdb->prepare('
            SELECT COUNT(*) as i FROM
                '.$wpdb->prefix.'books_genres_relations
            WHERE `genre_id`=%d
            ', $this->id));
        $this->books_count = $row->i;
    }

    function update() {
        global $wpdb;
        
        $existing_genres = get_book_genres_by_title($this->title);
        $existing_id = null;
        foreach ($existing_genres as $g)
            if ($g->title == $this->title) { $this->id = $g->id; }

        if ($this->id == null) {
            $sql = $wpdb->prepare(
                "
                INSERT INTO ".$wpdb->prefix.'book_genres'."
                (`title`)
                VALUES
                (%s)
                ",
                $this->title
            );
            return $wpdb->query($sql);
        } else {
            $sql = $wpdb->prepare(
                "
                UPDATE ".$wpdb->prefix.'book_genres'."
                SET
                `title`=%s
                WHERE `id`=%d
                ",
                $this->title,
                $this->id
            );
            return $wpdb->query($sql);
        }
    }
}
