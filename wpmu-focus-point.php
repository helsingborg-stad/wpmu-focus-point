<?php

/*
Plugin Name:    WPMU Focus Point
Description:    Sets focus point in media library.
Version:        1.0.0
Author:         Niclas Norin
*/

namespace WPMUFocusPoint;


if (! defined('WPINC')) {
    die;
}


define('WPMUFOCUSPOINT_PATH', plugin_dir_path(__FILE__));
define('WPMUFOCUSPOINT_URL', plugins_url('', __FILE__));

require_once WPMUFOCUSPOINT_PATH . 'CacheBust.php';

class WPMUFocusPoint
{
    private $cacheBust;
    public function __construct()
    {
        $this->cacheBust = new CacheBust();
        add_filter('attachment_fields_to_edit', array($this, 'addFocusPointFields'), 10, 2);
        add_filter('attachment_fields_to_save', array($this, 'saveFocusPointFields'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
    }

    public function enqueueAdminScripts()
    {
        wp_register_script(
            'js-init',
            WPMUFOCUSPOINT_URL . '/dist/' .
            $this->cacheBust->name('js/init.js')
        ); 

        wp_register_style(
            'css-main',
            WPMUFOCUSPOINT_URL . '/dist/' .
            $this->cacheBust->name('css/main.css')
        );

        wp_enqueue_style('css-main');
        wp_enqueue_script('js-init');
    }
    public function addFocusPointFields($fields, $post) 
    {
        $focusPoint = json_decode(get_post_meta($post->ID, '_focus_point', true), true);

        // Default values if not set
        $focusX = $focusPoint['left'] ?? 50;
        $focusY = $focusPoint['top'] ?? 50;

        // Hidden input fields with HTML rendering
        $fields['focusX'] = [
            'input' => 'html',
            'html'  => sprintf(
                '<input type="hidden" 
                        id="focus-point-x-%d" 
                        class="focus-point-input" 
                        name="attachments[%d][focusX]" 
                        value="%s" 
                        data-js-focus-axis="x" 
                        data-attachment-id="%d" />',
                $post->ID,
                $post->ID,
                esc_attr($focusX),
                $post->ID
            )
        ];
        
        $fields['focusY'] = [
            'input' => 'html',
            'html'  => sprintf(
                '<input type="hidden" 
                        id="focus-point-y-%d" 
                        class="focus-point-input" 
                        name="attachments[%d][focusY]" 
                        value="%s" 
                        data-js-focus-axis="y" 
                        data-attachment-id="%d" />',
                $post->ID,
                $post->ID,
                esc_attr($focusY),
                $post->ID
            )
        ];

        return $fields;
    }

    public function saveFocusPointFields($post, $attachment)
    {
        if (isset($attachment['focusX']) && isset($attachment['focusY'])) {
            $focusX = max(0, min(100, (int) $attachment['focusX']));
            $focusY = max(0, min(100, (int) $attachment['focusY']));
            
            $focusPoint = [
                'left' => $focusX,
                'top' => $focusY
            ];

            update_post_meta($post['ID'], '_focus_point', wp_json_encode($focusPoint));
        }

        return $post;
    }
}

new \WPMUFocusPoint\WPMUFocusPoint();
