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

    /**
     * Enqueue admin scripts and styles.
     */
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

    /**
     * Add focus point fields to the media library attachment edit screen.
     *
     * @param array $fields The existing fields.
     * @param object $post The current post object.
     * @return array The modified fields.
     */
    public function addFocusPointFields($fields, $post) 
    {
        $focusPoint = json_decode(get_post_meta($post->ID, '_focus_point', true), true);

        // Default values if not set
        $focusX = $focusPoint['left'] ?? 50;
        $focusY = $focusPoint['top'] ?? 50;

        $fields['focusX'] = $this->generateFocusPointField('x', $post->ID, $focusX);
        $fields['focusY'] = $this->generateFocusPointField('y', $post->ID, $focusY);

        return $fields;
    }

    /**
     * Generates a focus point hidden input field.
     *
     * @param string $axis Either 'x' or 'y'
     * @param int $postId Attachment ID
     * @param string $value Current focus point value
     * @return array Field configuration array
     */
    function generateFocusPointField(string $axis, int $postId, string $value): array {
        return [
            'input' => 'html',
            'html'  => sprintf(
                '<input type="hidden" 
                        id="focus-point-%s-%d" 
                        class="focus-point-input" 
                        name="attachments[%d][focus%s]" 
                        value="%s" 
                        data-js-focus-axis="%s" 
                        data-attachment-id="%d" />',
                strtolower($axis),
                $postId,
                $postId,
                strtoupper($axis), // For name="attachments[%d][focusX]"
                esc_attr($value),
                strtolower($axis),
                $postId
            )
        ];
    }

    /**
     * Save the focus point fields when the attachment is saved.
     *
     * @param array $post The post data.
     * @param array $attachment The attachment data.
     * @return array The modified post data.
     */
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
