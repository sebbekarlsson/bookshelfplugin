<?php
add_action( 'admin_menu', 'bsp_add_admin_menu' );

function bsp_add_admin_menu() {
    $slug = 'New Book';
    add_menu_page(
        $slug,
        $slug,
        'manage_options',
        $slug,
        'bsp_publish_page',
        plugin_dir_url(__DIR__) . '/../book.gif',
        3
    );

    $slug = 'Books';
    add_menu_page(
        $slug,
        $slug,
        'manage_options',
        $slug,
        'bsp_books_page',
        plugin_dir_url(__DIR__) . '/../books_stack.png',
        3
    );
}

function bsp_publish_page() {
    $book_id = null;
    $book = null;

    if (isset($_GET['id'])) {
        $book_id = (int)$_GET['id'];
        $book = new Book($book_id);
    }

    $genres = get_all_book_genres(); 
?>
    <style type='text/css'><?php echo file_get_contents(plugin_dir_path(__FILE__) . 'settings.css'); ?></style>
    <form method='post'>
        <h2>Book Publisher</h2>
        <section>
            <label for='book_title'>Book Title</label>
            <input type='text' id='book_title' name='book_title' placeholder='Book title'
                <?php if (!empty($book_id)) { ?>
                    value='<?php echo $book->title; ?>'
                <?php } ?>
            >

            <label for='book_description'>Book Description</label>
            <textarea name='book_description' id='book_description'><?php if (!empty($book_id)) { echo $book->description; } ?></textarea>

            <label for='book_genre'>Book Genre</label>
            <select name='book_genre' id='book_genre'>
                <?php foreach ($genres as $g) { ?>
                    <option
<?php
    if (!empty($book_id)) {
        if (isset($book->genres[0])) {
            if ($g->id == $book->genres[0]->id) {
                ?>selected<?php
            }
        }
    }
                    ?>
                    value='<?php echo $g->id; ?>'><?php echo $g->title; ?></option>
                <?php } ?>
            </select>
        </section>
        <section>
            <?php if (!empty($book_id)) { ?>
                <input type='hidden' name='book_id' value='<?php echo $book_id; ?>'>
            <?php } ?>
            <input class='button' type='submit' name='publish_book'
                <?php if(!empty($book_id)) { ?>
                    value='Update'
                <?php } else { ?>
                    value='Publish'
                <?php } ?>
            >
        </section>
    </form>

<?php
}

function bsp_books_page() {
    global $selected_genre;

    $genres = get_all_book_genres();
    $books = [];
    
    if (!isset($selected_genre)) {
        $gen = null;
        foreach($genres as $gen) {
            if ($gen->books_count > 0) {
                $selected_genre = $gen;
            }
        }

        if (empty($gen) && isset($genres[0])) { $selected_genre = $genres[0]; }
    }
    
    if (!empty($selected_genre))
    $books = get_books_by_genre_id($selected_genre->id);

    ?>
    <style type='text/css'><?php echo file_get_contents(plugin_dir_path(__FILE__) . 'settings.css'); ?></style>
    <form method='post'>
        <h2>Books</h2>
        <?php if(sizeof($books) == 0) { ?>
            <p>You have no books...</p>
        <?php } else { ?>
        <section>
            <label for='book_genre'>Book Genre</label>
            <select name='book_genre' id='book_genre'>
                <?php foreach ($genres as $g) { ?>
                    <option
                    value='<?php echo $g->id; ?>'
                    <?php if (!empty($selected_genre)) { if ($g->id == $selected_genre->id) { ?> selected <?php } ?>>
                        <?php echo $g->title; ?> (<?php echo $g->books_count; ?>)
                    </option>
                    <?php }} ?>
            </select>
            <input class='button' type='submit' id='books_update' name='books_update' value='Update'>
        </section>
</form>
        <section class='table-section'>
            <table class='wp-list-table widefat fixed striped posts'>
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-author">Title</th>
                        <th scope="col" class="manage-column column-author">Edit</th>
                        <th scope="col" class="manage-column column-author">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book) { ?>
                        <tr>
                            <td><?php echo $book->title; ?></td>
                            <td><a class='button' href='/wp-admin/admin.php?page=New+Book&id=<?php echo $book->id; ?>'>Edit</a></td>
                            <td>
                                <form method='post'>
                                    <input type='hidden' name='book_id' value='<?php echo $book->id; ?>'>
                                    <input type='submit' class='button' name='delete_book' value='Delete'>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
<?php } ?>
    
<?php
}
