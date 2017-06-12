<?php
//no direct access allowed
if( ! defined( 'ALCHEMY_OPTIONS_VERSION' ) ) {
    exit;
}

if( ! class_exists( 'Alchemy_Value' ) ) {
    class Alchemy_Value {
        private $value;

        public function __construct( $rawValue, $network = false ) {
            $this->value = $rawValue;
            $this->isNetworlValue = $network;

            $this->filter_value_by_type();
        }

        public function filter_value_by_type() {
            switch ( $this->value['type'] ) {
                case 'slider' :
                    $this->value['value'] = (int) $this->value['value'];
                break;
                case 'radio' :
                case 'image-radio' :
                    $this->value['value'] = $this->value['value'][0];
                break;
                case 'upload' :
                    $this->value['value'] = $this->modify_upload_value( $this->value['value'] );
                break;
                case 'repeater' :
                    $this->value['value'] = $this->modify_repeater_value( $this->value['value'] );
                break;
                default : break;
            }
        }

        public function modify_upload_value( $value ) {
            if( $this->isNetworlValue ) {
                switch_to_blog(1);

                $valueToReturn = $this->get_attached_image( $value );

                restore_current_blog();
            } else {
                $valueToReturn = $this->get_attached_image( $value );
            }

            return $valueToReturn;
        }

        public function get_attached_image( $value ) {
            $valueToReturn = $value;
            $imageMeta = wp_get_attachment_metadata( $value );

            if( is_array( $imageMeta['sizes'] ) ) {
                $valueToReturn = array(
                    'id' => (int) $value,
                    'sizes' => array(),
                );

                foreach( $imageMeta['sizes'] as $sizeTitle => $sizeValue ) {
                    $valueToReturn['sizes'][$sizeTitle] = wp_get_attachment_image_src( $value, $sizeTitle );
                }

                $valueToReturn['sizes']['full'] = wp_get_attachment_image_src( $value, 'full' );
            }

            return $valueToReturn;
        }

        public function modify_repeater_value( $value ) {
            //remove temporarily hidden fields
            $value = array_filter($value, function( $item ){
                return $item['isVisible'] === 'true';
            });

            $value = array_map(function( $item ){
                $values = array();

                foreach ( $item['fields'] as $key => $val ) {
                    //$key can be 'null' in a field with no id
                    if( 'null' !== $key ) {
                        $valInst = new Alchemy_Value( $val, $this->isNetworlValue );

                        $values[$key] = $valInst->get_value();
                    }
                }

                return $values;
            }, $value);

            return $value;
        }

        public function get_value() {
            return $this->value['value'];
        }
    }
}