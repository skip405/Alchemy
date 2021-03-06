<?php

namespace Alchemy\Includes;

use Alchemy\Options;

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( class_exists( __NAMESPACE__ . '\Metabox' ) ) {
    return;
}

class Metabox {
    private $metabox;

    function __construct( $metabox ) {
        $this->metabox = $metabox;
    }

    function add_metabox() {
        foreach ( $this->metabox['post-types'] as $postType ) {
            if( post_type_exists( $postType ) ) {
                add_action( 'add_meta_boxes_' . $postType, array( $this, 'create_meta_box' ) );
            }
        }
    }

    function create_meta_box() {
        $metaBoxId = isset( $this->metabox['id'] ) ? $this->metabox['id'] : '';

        if( empty( $metaBoxId ) ) {
            return;
        }

        add_meta_box(
            $this->metabox['id'],
            $this->metabox['title'],
            array( $this, 'meta_box_html' ),
            $this->metabox['post-types']
        );
    }

    function meta_box_html( $post ) {
        $html = '<div class="alchemy__metabox metabox jsAlchemyMetaBox">';

        $html .= sprintf( '<div class="metabox__fields">%s</div>',
            Options::get_meta_html( $post->ID, $this->metabox['meta']['options'] )
        );

        $html .= '</div>';

        echo $html;
    }
}