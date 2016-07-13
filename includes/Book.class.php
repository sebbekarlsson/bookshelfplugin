<?php

class Book {
    var $id;
    var $updated;
    var $title;
    var $description;
    var $attachment_id = 0;
    var $meta = [];

    function __construct($id=null) {
        $this->id = $id;

        if ($this->id != null) {
            $this->build();
        }
    }

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
    }

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
}
