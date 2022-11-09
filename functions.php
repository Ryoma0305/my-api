<?php

register_post_type(
    'blog',
    [
    'labels' => [
      'name' => __('ブログ'),
      'singular_name' => __('blog'),
    ],
    'public' => true,
    'has_archive' => true,
          'show_ui' => true,
          'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail'),
    ]
);

add_theme_support('post-thumbnails');

// -----------------------------------------------------------------
// END POINT
// -----------------------------------------------------------------
// blog list
function add_rest_endpoint_all_posts_from_blog()
{
  register_rest_route(
    'wp/api',
    '/blog',
    array(
      'methods' => 'GET',
      'callback' => 'get_all_posts_from_blog'
    )
  );
}
function get_all_posts_from_blog()
{
    $result = array();
    $args = array(
      'posts_per_page' => -1,
      'post_type' => 'blog',
      'post_status' => 'publish'
    );
    $all_posts = get_posts($args);
    foreach ($all_posts as $post) {
      $data = array(
        'ID' => $post->ID,
        'thumbnail' => get_the_post_thumbnail_url($post->ID, 'full'),
        'slug' => $post->post_name,
        'date' => $post->post_date,
        'modified' => $post->post_modified,
        'title' => $post->post_title,
        'excerpt' => $post->post_excerpt,
        'content' => $post->post_content
      );
      array_push($result, $data);
    };
    return $result;
}
add_action('rest_api_init', 'add_rest_endpoint_all_posts_from_blog');

// blog detail
function add_rest_endpoint_single_posts() {
  register_rest_route(
    'wp/api',
    '/blog/(?P<slug>\S+)',
    array(
      'methods' => 'GET',
      'callback' => 'get_single_posts',
      'permission_callback' => function() { return true; }
    )
  );
}
function get_single_posts($parameter) {

    $result = array();
    $args_single = array(
      'posts_per_page' => 1,
      'post_type' => 'post',
      'post_status' => 'publish',
      'name' => $parameter['slug']
      // 'include' => $parameter[id]
    );
    $single_post = get_posts($args_single);
    foreach($single_post as $post) {
      $data = array(
        'ID' => $post->ID,
        'thumbnail' => get_the_post_thumbnail_url($post->ID, 'full'),
        'slug' => $post->post_name,
        'date' => $post->post_date,
        'modified' => $post->post_modified,
        'title' => $post->post_title,
        'excerpt' => $post->post_excerpt,
        'content' => $post->post_content,
        'post_author' => $post->post_author
        // 'category' => get_the_terms($post->ID, 'blog_category')[0]->name,
      );
      array_push($result, $data);
    };
    return $result;

}
add_action('rest_api_init', 'add_rest_endpoint_single_posts');
