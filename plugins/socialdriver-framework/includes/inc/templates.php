<?php

    /*
    *
    *	Swift Page Builder - Templates Return Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    function spb_get_prebuilt_template_code( $template_id ) {

        $template_code = "";

        $prebuilt_templates = spb_get_prebuilt_templates();

        if ( array_key_exists( $template_id, $prebuilt_templates ) ) {
            $template_code = $prebuilt_templates[ $template_id ]['code'];
        }

        return $template_code;

    }


    function spb_get_prebuilt_templates() {

        // create array
        $prebuilt_templates = array();

//		$prebuilt_templates["agency-two-home"] = array(
//			'id' => "agency-two-home",
//			'name' => 'Home (Agency Two)',
//			'code' => '[cool]'
//		);	

//		$prebuilt_templates["agency-two-home"] = array(
//			'id' => "agency-two-home",
//			'name' => 'Home (Agency Two)',
//			'code' => '[cool]'
//		);	

        // filter array
        $prebuilt_templates = apply_filters( 'spb_prebuilt_templates', $prebuilt_templates );

        // return array
        return $prebuilt_templates;

    }

?>