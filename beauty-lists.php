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
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    $maxListElements = 10;
    echo '
        <div class="info-container">
            Añade el código <strong>[-- beauty_list --]</strong> en el lugar donde quieres que se pinte esta lista.
        </div>
    ';
    for ($i=0;$i<$maxListElements;$i++) {
        $metadataValues = getMetaValues($post, array("list_elem_title_".$i, "list_elem_content_".$i, "list_elem_image_".$i));
        $elemPosition = (($i+1)%2==0)?"right":"left";
        echo '
        <div class="list-elem-container '.$elemPosition.'" >
            <h2>Elemento '.($i+1).'</h2>
            <label for="list_elem_title_'.$i.'" class="list-label">Título</label>
            <input type="text" name="list_elem_title_'.$i.'" id="list_elem_title_'.$i.'" class="title-input input" value="'.$metadataValues["list_elem_title_".$i].'" />
            <label for="list_elem_content_'.$i.'" class="list-label">Contenido</label>
            <textarea name="list_elem_content_'.$i.'" id="list_elem_content_'.$i.'" class="content-input input" rows="5" value="'.$metadataValues["list_elem_content_".$i].'" >'.$metadataValues["list_elem_content_".$i].'</textarea>
            <label for="list_elem_image_'.$i.'" class="list-label">URL de la imagen</label>
            <input type="text" name="list_elem_image_'.$i.'" id="list_elem_image_'.$i.'" class="image-intput input" value="'.$metadataValues["list_elem_image_".$i].'" />
        </div>';
    }
}

function getMetaValues($post, $metadataNames)
{
    $metadatas = array();
    foreach ($metadataNames as $metadataName) {
        $metadatas[$metadataName] = get_post_meta($post->ID, $metadataName, true);
    }
    return $metadatas;
}

function save_beauty_list_data($post_id, $post, $update)
{
    reset_log_in_file();
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__))) {
        return $post_id;
    }

    if(!current_user_can("edit_post", $post_id)) {
        return $post_id;
    }

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return $post_id;
    }

    $slug = "post";
    if($slug != $post->post_type) {
        return $post_id;
    }

    $i=0;
    while (isset($_POST['list_elem_title_'.$i]) || isset($_POST['list_elem_content_'.$i]) || isset($_POST['list_elem_image_'.$i])) {
        $meta_box_title_value = "";
        $meta_box_content_value = "";
        $meta_box_image_value = "";

        if (isset($_POST['list_elem_title_'.$i])) {
            $meta_box_title_value = $_POST['list_elem_title_'.$i];
        }

        if (isset($_POST['list_elem_content_'.$i])) {
            $meta_box_content_value = $_POST['list_elem_content_'.$i];
        }

        if (isset($_POST['list_elem_image_'.$i])) {
            $meta_box_image_value = $_POST['list_elem_image_'.$i];
        }

        if (strlen($meta_box_title_value) > 0) {
            update_post_meta($post_id, "list_elem_title_".$i, $meta_box_title_value);
        } elseif (!empty(get_post_meta($post_id, "list_elem_title_".$i, true))) {
            delete_post_meta($post_id, "list_elem_title_".$i);
        }
        if (strlen($meta_box_content_value) > 0) {
            update_post_meta($post_id, "list_elem_content_".$i, $meta_box_content_value);
        } elseif (!empty(get_post_meta($post_id, "list_elem_content_".$i, true))) {
            delete_post_meta($post_id, "list_elem_content_".$i);
        }
        if (strlen($meta_box_image_value) > 0) {
            update_post_meta($post_id, "list_elem_image_".$i, $meta_box_image_value);
        } elseif (!empty(get_post_meta($post_id, "list_elem_image_".$i, true))) {
            delete_post_meta($post_id, "list_elem_image_".$i);
        }

        $i++;
    }
}

function insert_beauty_list($content)
{
    if (strpos($content, "[&#8211; beauty_list &#8211;]") !== false) {
        $data = [];
        $i = 0;

        $elemTitle = get_post_meta($GLOBALS['post']->ID, "list_elem_title_".$i, true);
        $elemContent = get_post_meta($GLOBALS['post']->ID, "list_elem_content_".$i, true);
        $elemImage = get_post_meta($GLOBALS['post']->ID, "list_elem_image_".$i, true);
        while (
            strlen($elemTitle) > 0
            || strlen($elemContent) > 0
            || strlen($elemImage) > 0
        ) {
            if (strlen($elemTitle) > 0) {
                $data[$i]['title'] = $elemTitle;
            }
            if (strlen($elemContent) > 0) {
                $data[$i]['content'] = $elemContent;
            }
            if (strlen($elemImage) > 0) {
                $data[$i]['image'] = $elemImage;
            }
            $i++;
            $elemTitle = get_post_meta($GLOBALS['post']->ID, "list_elem_title_".$i, true);
            $elemContent = get_post_meta($GLOBALS['post']->ID, "list_elem_content_".$i, true);
            $elemImage = get_post_meta($GLOBALS['post']->ID, "list_elem_image_".$i, true);
        }

        return str_replace("[&#8211; beauty_list &#8211;]", generateBeautyList($data), $content);
    }
    return $content;
}

function generateBeautyList($list)
{
    $listHTML .= '<div id="beauty-list">';
    $i=0;
    foreach ($list as $elem) {
        $listHTML .= '
            <div class="vc_row wpb_row vc_row-fluid custom-beauty-list-row">
        ';
        $imageSide = ($i%2==0) ? "left" : "right";
        $textSide = ($i%2==0) ? "right" : "left"; 
        $listHTML .= '
                <div class="list-elem-part list-counter '.$textSide.'">
                    <span class="list-number">'.($i+1).'</span>
        ';
        if (isset($elem['title'])) {
            $listHTML .= '
                    <h3 class="list-title">'.$elem['title'].'</h3>';
        }
        $listHTML .= '
                </div>
                <img class="list-image list-elem-part '.$imageSide.'" src="'.$elem['image'].'"/>
        ';
        if (isset($elem['content'])) {
            $listHTML .= '
                <div class="list-content list-elem-part '.$textSide.'"><p>'.implode("</p><p>", explode("\n", $elem['content'])).'</p></div>';
        }
        $listHTML .= '
            </div>';
        $i++;
    }
    $listHTML .= '</div>';

    return $listHTML;
}

function log_in_file($key, $element)
{
    file_put_contents("/tmp/beauty_list_log.php", $key.": ", FILE_APPEND);
    file_put_contents("/tmp/beauty_list_log.php", print_r($element, true)."\n", FILE_APPEND);
}

function reset_log_in_file()
{
    file_put_contents("/tmp/beauty_list_log.php", "");
}

function js_composer_front_load() {
    if(is_single()) {
        wp_enqueue_style('js_composer_front');
        wp_enqueue_style('beauty_list', '/wp-content/plugins/beauty-lists/style.css');
    }
}

add_action('wp_enqueue_scripts', 'js_composer_front_load');
add_action("add_meta_boxes", "add_beauty_list");
add_action("save_post", "save_beauty_list_data", 10, 3);
add_action( 'wp_print_styles', 'enqueue_my_styles' );

add_filter("the_content", "insert_beauty_list");
