<?php
add_action( 'admin_menu', 'bsp_add_admin_menu' );

function bsp_add_admin_menu() { 
    add_menu_page( 'bookshelfplugin', 'bookshelfplugin', 'manage_options', 'bookshelfplugin', 'bsp_options_page', null, 3);
}

function bsp_options_page() {
   $genres = get_all_book_genres(); 
    ?>
    <style type='text/css'><?php echo file_get_contents(plugin_dir_path(__FILE__) . 'settings.css'); ?></style>
    <form method='post'>
        <h2>Book Publisher</h2>
        <section>
            <label for='book_title'>Book Title</label>
            <input type='text' id='book_title' name='book_title' placeholder='Book title'>
            
            <label for='book_description'>Book Description</label>
            <textarea name='book_description' id='book_description'></textarea>

            <label for='book_genre'>Book Genre</label>
            <select name='book_genre' id='book_genre'>
                <?php foreach ($genres as $g) { ?>
                    <option value='<?php echo $g->id; ?>'><?php echo $g->title; ?></option>
                <?php } ?>
            </select>
        </section>
        <section>
            <input class='button' type='submit' name='publish_book' value='Publish'>
        </section>
    </form>

<?php
}
