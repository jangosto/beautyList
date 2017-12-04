<?php
/**
 * @package WPSEO\Main
 */

/**
 * Plugin Name: Beauty List
 * Version: 1.0
 * Plugin URI: 
 * Description: List form to print beauty lists
 * Author: Juan Angosto Herrmann
 * Author URI: 
 * Text Domain: 
 * Domain Path: 
 * License: GPL v3
 */

function add_beauty_list()
{
    add_meta_box("beauty_list", "Lista", "add_beauty_list_in_form", "post", "normal", "high", null);
}

function add_beauty_list_in_form($post, $metabox)
{
    $maxListElements = 10;
    for ($i=0;$i<$maxListElements;$i++) {
        echo '
        <div class="list-elem-container">
            <input type="text" name="list_elem_title_'.$i.'" id="list_elem_title_'.$i.'" />
            <input type="textarea" name="list_elem_content_'.$i.'" id="list_elem_content_'.$i.'" />
            <input type="text" name="list_elem_image_'.$i.'" id="list_elem_title_'.$i.'"/>
        </div>';
    }
}

/*function buildListInterface($post, $metabox)
{
    echo '
    <div id="list-prototype" hidden>
        <div id="beauty_list_element_[--list_counter--]>" class="postbox">
            <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Alternar panel: [--list_title--]</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>[--list_title--]</span></h2>
            <div class="inside">
                <button class="add-list-elem" id="remote_list_elem_[--list_counter--]">Añadir Elemento</button>
            </div>
            <button class="remove-list" id="remove_list_[--list_counter--]">Eliminar Lista</button>
        </div>
    </div>
    <div id="list-elem-prototype" hidden>
        <div>
            <input type="text" name="list_elem_title_[--list_counter--]_[--list_elem_counter--]" />
            <input type="textarea" name="list_elem_content_[--list_counter--]_[--list_elem_counter--]" />
            <input type="text" name="list_elem_image_[--list_counter--]_[--list_elem_counter--]" />
            <button class="remove-list-elem" id="remove_list_elem_[--list_counter--]_[--list_elem_counter--]">Eliminar Elemento</button>
        </div>
    </div>
    <div id="beauty_list_element_0" class="postbox ">
        <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Alternar panel: Lista 1</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle"><span>Lista 1</span></h2>
        <div class="inside">
            <div>
                <input type="text" name="list_elem_title_0_0" />
                <input type="textarea" name="list_elem_content_0_0" />
                <input type="text" name="list_elem_image_0_0" />
            </div>
            <button class="add-list-elem" id="remote_list_elem_[--list_counter--]">Añadir Elemento</button>
        </div>
        <button class="remove-list" id="remove_list_0">Eliminar Lista</button>
    </div>
';
}*/

add_action("add_meta_boxes", "add_beauty_list");
