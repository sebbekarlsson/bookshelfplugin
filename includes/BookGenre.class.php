<?php

class BookGenre {
    var $id;
    var $title;
    var $updated;

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
