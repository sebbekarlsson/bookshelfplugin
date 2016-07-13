<?php


if (isset($_POST['publish_book'])) {
    $dot = create_book(
        $_POST['book_title'],
        $_POST['book_description'],
        $_POST['book_genre']
    );
}
