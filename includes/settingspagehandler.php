<?php


if (isset($_POST['publish_book'])) {
    $book_id = null;
    
    if (isset($_POST['book_id'])) {
        $book_id = (int)$_POST['book_id'];
        remove_genres_from_book($book_id);
    }

    $book_id = create_book(
        $_POST['book_title'],
        $_POST['book_description'],
        $_POST['book_genre'],
        0,
        $book_id
    );

    header('Location: /wp-admin/admin.php?page=New+Book&id='.$book_id);
}

if (isset($_POST['books_update'])) {
    $selected_genre = get_book_genre((int)$_POST['book_genre']);
}

if (isset($_POST['delete_book']) && isset($_POST['book_id'])) {
    $book = new Book((int)$_POST['book_id']);
    $book->delete();
}
