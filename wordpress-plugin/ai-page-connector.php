<?php

/**
 * Plugin Name: AI Page Connector
 * Description: Secure REST endpoints for the SaaS app to create ACF-based draft pages (ACF + WPML aware). Read-only except for creating NEW draft pages.
 * Version: 1.0.0
 * Author: SaaS API Monitor
 *
 * INSTALL: copy this file to wp-content/mu-plugins/ai-page-connector.php
 * (create the mu-plugins folder if it does not exist).
 *
 * AUTH: standard WordPress Application Passwords (Users → Profile → Application
 * Passwords). The connecting account must have the `edit_pages` capability.
 *
 * SAFETY: this plugin ONLY creates new pages with status=draft. It never updates
 * or deletes existing content. On any error during creation it rolls back the
 * draft it just created.
 */
if (! defined('ABSPATH')) {
    exit;
}

define('AI_PAGE_CONNECTOR_VERSION', '1.0.0');

add_action('rest_api_init', function () {
    $namespace = 'ai-page/v1';

    register_rest_route($namespace, '/ping', [
        'methods' => 'GET',
        'callback' => 'ai_page_connector_ping',
        'permission_callback' => 'ai_page_connector_can',
    ]);

    register_rest_route($namespace, '/meta', [
        'methods' => 'GET',
        'callback' => 'ai_page_connector_meta',
        'permission_callback' => 'ai_page_connector_can',
    ]);

    register_rest_route($namespace, '/pages', [
        'methods' => 'POST',
        'callback' => 'ai_page_connector_create_page',
        'permission_callback' => 'ai_page_connector_can',
    ]);
});

/**
 * Only users who can create/edit pages may use the connector.
 */
function ai_page_connector_can()
{
    if (! current_user_can('edit_pages') || ! current_user_can('publish_pages')) {
        return new WP_Error('forbidden', 'У пользователя нет прав на создание страниц (edit_pages).', ['status' => 403]);
    }

    return true;
}

function ai_page_connector_ping()
{
    return [
        'ok' => true,
        'connector_version' => AI_PAGE_CONNECTOR_VERSION,
        'wp_version' => get_bloginfo('version'),
        'acf_active' => function_exists('update_field'),
        'wpml_active' => defined('ICL_SITEPRESS_VERSION'),
        'user' => wp_get_current_user()->user_login,
    ];
}

/**
 * Lists data the UI needs for its dropdowns: pages (for parent), categories,
 * tags, authors and WPML languages.
 */
function ai_page_connector_meta()
{
    $pages = get_pages(['sort_column' => 'post_title', 'number' => 500]);
    $page_list = array_map(function ($p) {
        return ['id' => (int) $p->ID, 'title' => $p->post_title];
    }, $pages ?: []);

    $categories = array_map(function ($c) {
        return ['id' => (int) $c->term_id, 'name' => $c->name];
    }, get_categories(['hide_empty' => false]) ?: []);

    $authors = array_map(function ($u) {
        return ['id' => (int) $u->ID, 'name' => $u->display_name];
    }, get_users(['who' => 'authors', 'fields' => ['ID', 'display_name']]) ?: []);

    $languages = [];
    if (defined('ICL_SITEPRESS_VERSION')) {
        $wpml = apply_filters('wpml_active_languages', null, []);
        if (is_array($wpml)) {
            foreach ($wpml as $code => $lang) {
                $languages[] = ['code' => $code, 'name' => $lang['native_name'] ?? $code];
            }
        }
    }

    return [
        'pages' => $page_list,
        'categories' => $categories,
        'authors' => $authors,
        'languages' => $languages,
    ];
}

/**
 * Creates a NEW draft page, fills ACF fields, sets WPML language and taxonomies.
 */
function ai_page_connector_create_page(WP_REST_Request $request)
{
    $title = sanitize_text_field((string) $request->get_param('title'));
    if ($title === '') {
        return new WP_Error('invalid', 'title обязателен.', ['status' => 422]);
    }

    $postarr = [
        'post_type' => 'page',
        'post_status' => 'draft', // always a draft — never published automatically.
        'post_title' => $title,
    ];

    if ($slug = $request->get_param('slug')) {
        $postarr['post_name'] = sanitize_title((string) $slug);
    }
    if (($parent = $request->get_param('parent')) !== null && $parent !== '') {
        $postarr['post_parent'] = (int) $parent;
    }
    if (($author = $request->get_param('author')) !== null && $author !== '') {
        $postarr['post_author'] = (int) $author;
    }
    if (($menu_order = $request->get_param('menu_order')) !== null && $menu_order !== '') {
        $postarr['menu_order'] = (int) $menu_order;
    }

    $post_id = wp_insert_post($postarr, true);

    if (is_wp_error($post_id)) {
        return new WP_Error('insert_failed', $post_id->get_error_message(), ['status' => 500]);
    }

    try {
        // ACF fields — written by field KEY for reliability.
        $acf = $request->get_param('acf');
        if (is_array($acf)) {
            if (! function_exists('update_field')) {
                throw new Exception('ACF не активен на сайте — невозможно записать поля.');
            }
            foreach ($acf as $field_key => $value) {
                update_field((string) $field_key, $value, $post_id);
            }
        }

        // WPML language.
        $language = $request->get_param('language');
        if ($language && defined('ICL_SITEPRESS_VERSION')) {
            do_action('wpml_set_element_language_details', [
                'element_id' => $post_id,
                'element_type' => 'post_page',
                'trid' => false,
                'language_code' => sanitize_text_field((string) $language),
            ]);
        }

        // Taxonomies.
        $categories = $request->get_param('categories');
        if (is_array($categories) && ! empty($categories)) {
            wp_set_post_terms($post_id, array_map('intval', $categories), 'category');
        }
        $tags = $request->get_param('tags');
        if (is_array($tags) && ! empty($tags)) {
            wp_set_post_terms($post_id, array_map('sanitize_text_field', $tags), 'post_tag');
        }
    } catch (Exception $e) {
        // Roll back the draft we created so nothing is left half-written.
        wp_delete_post($post_id, true);

        return new WP_Error('fill_failed', $e->getMessage(), ['status' => 500]);
    }

    return [
        'ok' => true,
        'id' => (int) $post_id,
        'status' => 'draft',
        'edit_link' => admin_url('post.php?post='.$post_id.'&action=edit'),
        'preview_link' => get_preview_post_link($post_id) ?: get_permalink($post_id),
    ];
}
