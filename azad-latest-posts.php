<?php
/* 
Plugin Name: Azad Latest Posts
Description: This plugin will create an widget and the widget will show the latest posts in any widget area that could be selected.
Plugin URi: gittechs.com/plugin/azad-latest-posts
Author: Md. Abul Kalam Azad
Author URI: gittechs.com/author
Author Email: webdevazad@gmail.com
Version: 1.0.0.
Text Domain: azad-latest-posts
*/

// DENY IF DIRECTLY ACCESSED
defined('ABSPATH') || exit;

if(! function_exists('azad_latest_posts_cat_list')){
    function azad_latest_posts_cat_list(){
        $cat_lists = get_categories();
        $all_cat_list = array(
            ''=>'All Category'
        );
        foreach($cat_lists as $cat_list){
            $all_cat_list[$cat_list->cat_name]= $cat_list->slug;
        }
        return $all_cat_list;
    }
}
function azad_setup_theme(){
    add_image_size('azad-thumb',104,80,true);
}
add_action('after_setup_theme','azad_setup_theme');

if(! class_exists('Azad_Latest_Posts')){
    class Azad_Latest_Posts extends WP_Widget{
        public function __construct(){
            parent::__construct(
                'azad_latest_posts',
                esc_html__('Azad Latest Posts','azad-latest-posts'),
                array(
                    //'classname'=>'azad-widget',
                    'description'=>esc_html__('To display latest posts.','azad-latest-posts')
                )
            );
            add_action('wp_enqueue_scripts',array($this,'azad_post_scripts'));
        }
        public function azad_post_scripts(){
            wp_register_style( 'azad-latest-posts-style', plugin_dir_url(__FILE__). 'assets/css/style.css' ,null,null,'all' );
            wp_enqueue_style('azad-latest-posts-style' );
        }
        public function widget($args,$instance){
            extract($args);

            $title = apply_filters('widget_title',$instance['title']);
            $count = $instance['count'];
            $category = $instance['category'];
            
            echo $before_widget;

            if($title){
                echo $before_title . $title . $after_title;
            }            
            global $post;
            $args = array(
                'category_name'=> $category ,
                'posts_per_page'=> $count
            );
            $posts = get_posts($args);
            if(count($posts)>0){
                $output .= '<div class="azad-posts-content">';
                foreach($posts as $post ) : setup_postdata($post);
                    $output .= '<div class="azad-media">';
                        if(has_post_thumbnail()){
                            $output .= '<div class="azad-media-left">';
                            $output .= '<a class="" href="' . get_permalink() . '">' . get_the_post_thumbnail($post->ID,'azad-thumb',array('class'=>'azad_responsive')) . '</a>';
                            $output .= '</div>';
                        }
                        
                        $output .= '<div class="azad-media-body">';
                        $output .= '<h3><a href="' . get_permalink() . '">'.get_the_title().'</a></h3>';
                        $output .= '</div>';
                    $output .= '</div>';
                endforeach;            
                wp_reset_query();
                $output .= '</div>';
            }
            
            echo $output;
            
            echo $after_widget;
        }
        public function form($instance){ 
            $defaults = array(
                'title' => 'Latest Posts',
                'count' => 5,
                'category' => 'uncategorized'
            );
            $instance = wp_parse_args((array)$instance,$defaults);
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">Widget Title</label>
                <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category','azad-latest-posts')?></label>
                <select id="<?php echo $this->get_field_id('category'); ?>" id="<?php echo $this->get_field_name('category'); ?>">
                    <?php
                    $options = azad_latest_posts_cat_list();
                    
                    if(isset($instance['category'])){
                        $category = $instance['category'];
                    }
                    
                    $op = '<option value="%s" %s>%s</option>';
                    foreach($options as $key => $value){
                        if($category == $key){
                            printf($op, $key, ' selected="selected"', $value);
                        }else{
                            printf($op, $key, '', $value);
                        }
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('count'); ?>">Count</label>
                <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>"/>
            </p>
        <?php 
        }
        public function update($new_instance,$old_instance){
            $instance = array();
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['category'] = strip_tags($new_instance['caategory']);
            $instance['count'] = strip_tags($new_instance['count']);
            return $instance;
        }
    }
}

if(! function_exists('azad_latest_posts')){
    function azad_latest_posts(){
        register_widget('Azad_Latest_Posts');
    }
}
add_action('widgets_init','azad_latest_posts');

