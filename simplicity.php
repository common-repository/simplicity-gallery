<?php
/*
Plugin Name: Simplicity - WordPress gallery plugin
Plugin URI: http://saiman.thefreeart.com/simplicity-gallery
Description: Easy to use WordPress gallery plugin that allows you to create and manage image galleries just dropping the files into a folder of your choice.
Author: saiman
Version: 1.2
Author URI: http://www.saiman.thefreeart.com
*/
define('SIMPLICITY_URL', 'http://www.saiman.thefreeart.com');

require_once 'meta-boxes/meta-box.php';
require_once 'UberGallery/resources/UberGallery.php';

global $meta_boxes;
$prefix = '_simplicity_';
$meta_boxes[] = array(
    'id' => 'simplicity',
    'title' => 'Simplicity - WordPress gallery',
    'pages' => array( 'post', 'page', 'simplicity_settings'),
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name'		=> 'Gallery directory',
            'id'		=> "{$prefix}dir",
            'type'		=> 'text',
            'desc'		=> 'In which directory the images are located? <br />NOTE: The directory should contains "cache" directory inside and the web server needs write permissions for "cache" directory. '
        ),
        array(
            'name'		=> 'Thumbnail width',
            'id'		=> "{$prefix}basic_settings_thumbnail_width",
            'type'		=> 'text',
            'std'		=> '150',
            'desc'		=> 'Thumbnail width (in pixels).',
        ),
        array(
            'name'		=> 'Thumbnail height',
            'id'		=> "{$prefix}basic_settings_thumbnail_height",
            'type'		=> 'text',
            'std'		=> '170',
            'desc'		=> 'Thumbnail height (in pixels).',
        ),
    ),
);

/**
 * Fetch all the data related to the gallery for a given post id
 * 
 * @param integer $id Post ID
 * @return array
 * */
function _simplicity__get_data($id)
{
    if (empty($id) || !is_numeric($id)) {
    	throw new InvalidArgumentException(__METHOD__ . ' invalid id passed.');
    }
    
    $data = array();
    $data['dir'] = get_post_meta($id, '_simplicity_dir', true);
    $data['site_url'] = get_site_url();
    $data['config']['basic_settings']['thumbnail_width'] = get_post_meta($id, '_simplicity_basic_settings_thumbnail_width', true);
    $data['config']['basic_settings']['thumbnail_height'] = get_post_meta($id, '_simplicity_basic_settings_thumbnail_height', true);

    return $data;
}

function _simplicity__register_meta_boxes()
{
    global $meta_boxes;

    // Make sure there's no errors when the plugin is deactivated or during upgrade
    if ( class_exists( 'RW_Meta_Box' ) )
    {
        foreach ( $meta_boxes as $meta_box )
        {
            new RW_Meta_Box( $meta_box );
        }
    }
}
// Hook to 'admin_init' to make sure the meta box class is loaded before
// (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking page template, categories, etc.
add_action( 'admin_init', '_simplicity__register_meta_boxes' );

function _simplicity__header_setup()
{
    // check all the posts listed on the page
    // and if simpliciti is used in any of those posts then 
    global $wp_query;
    
    $body = '';
    foreach ($wp_query->posts as $post) {
        
        $id = $post->ID;
        $data = _simplicity__get_data($id);
        
        if (!empty($data['dir'])) {
            
            $url = $data['site_url'];
            
    $body = <<<EOF
    <link rel="stylesheet" type="text/css" href="$url/wp-content/plugins/simplicity-gallery/UberGallery/resources/UberGallery.css" />
    <link rel="stylesheet" type="text/css" href="$url/wp-content/plugins/simplicity-gallery/UberGallery/resources/colorbox/1/colorbox.css" />

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="$url/wp-content/plugins/simplicity-gallery/UberGallery/resources/colorbox/jquery.colorbox.js"></script>

    <script type="text/javascript">
    $(document).ready(function(){
        $("a[rel='colorbox']").colorbox({maxWidth: "90%", maxHeight: "90%", opacity: ".5"});
    });
    </script>
EOF;
            echo $body;
            return;
        }
    }
}
add_action('wp_head', '_simplicity__header_setup', 999999);


function _simplicity__show($content)
{
    // we should be in the loop
    // so get the post id and use it
    $id = get_the_ID();
    
    $data = _simplicity__get_data($id);
    if (!empty($data['dir'])) {
        $gallery = UberGallery::init();
        $gallery->setConfig($data['config']);
        $gallery->setSiteUrl($data['site_url']);
        $galleryContent = $gallery->createGallery('.' . DIRECTORY_SEPARATOR . $data['dir']);
    }

    return $content . $galleryContent;


}
add_filter('the_content', '_simplicity__show');
