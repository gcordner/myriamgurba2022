<?php

    /*
    *
    *	Swift Framework Permalinks Class
    *	------------------------------------------------
    *	Swift Framework v2.0
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    } // Exit if accessed directly

    if ( ! class_exists( 'sf_post_type_permalinks' ) ) :

        class sf_post_type_permalinks {

            public function __construct() {
                add_action( 'admin_init', array( $this, 'settings_init' ) );
                add_action( 'admin_init', array( $this, 'settings_save' ) );
            }

            public function settings_init() {

                if ( post_type_exists("news") ) {
                    // Add news section to the permalinks page
                    add_settings_section( 'sf-news-permalink', __( 'News permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'news_settings'
                        ), 'permalink' );
                }

                if ( post_type_exists("sponsor") ) {
                    // Add team section to the permalinks page
                    add_settings_section( 'sf-sponsor-permalink', __( 'Sponsor permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'sponsor_settings'
                        ), 'permalink' );
                }


                if ( post_type_exists("team") ) {
                    // Add team section to the permalinks page
                    add_settings_section( 'sf-team-permalink', __( 'Team permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'team_settings'
                        ), 'permalink' );
                }

                if ( post_type_exists("event") ) {
                    // Add event section to the permalinks page
                    add_settings_section( 'sf-event-permalink', __( 'Event permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'event_settings'
                        ), 'permalink' );
                }

                if ( post_type_exists("resource") ) {
                    // Add resource section to the permalinks page
                    add_settings_section( 'sf-resource-permalink', __( 'Resource permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'resource_settings'
                        ), 'permalink' );
                }

                if ( post_type_exists("job") ) {
                    // Add job section to the permalinks page
                    add_settings_section( 'sf-job-permalink', __( 'Job permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'job_settings'
                        ), 'permalink' );
                }

                if ( post_type_exists("timeline") ) {
                    // Add timeline section to the permalinks page
                    add_settings_section( 'sf-timeline-permalink', __( 'Timeline permalink base', 'swift-framework-plugin' ), array(
                            $this,
                            'timeline_settings'
                        ), 'permalink' );
                }
            }

            public function news_settings() {
                echo wpautop( __( 'These settings control the permalinks used for news. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_news_permalinks' );
                $news_permalink = $permalinks['news_base'];

                // Set base slug & news base
                $base_slug = __( 'news', 'swift-framework-plugin' );
                $news_base = __( 'news', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $news_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%news-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="news_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_news_tog" <?php checked( $structures[0], $news_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?news=sample-news-article</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="news_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_news_tog" <?php checked( $structures[1], $news_permalink ); ?> /> <?php _e( 'News', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $news_base; ?>/sample-news-article/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="news_permalink" id="sf_news_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_news_tog" <?php checked( in_array( $news_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="news_permalink_structure" id="sf_news_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $news_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_news_tog' ).change(
                                function() {
                                    jQuery( '#sf_news_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_news_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_news_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function sponsor_settings() {
                echo wpautop( __( 'These settings control the permalinks used for sponsors. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_sponsor_permalinks' );
                $sponsor_permalink = $permalinks['sponsor_base'];

                // Set base slug & sponsor base
                $base_slug = __( 'sponsor', 'swift-framework-plugin' );
                $sponsor_base = __( 'sponsor', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $sponsor_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%sponsor-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="sponsor_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_sponsor_tog" <?php checked( $structures[0], $sponsor_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?sponsor=sample-sponsor</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="sponsor_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_sponsor_tog" <?php checked( $structures[1], $sponsor_permalink ); ?> /> <?php _e( 'Sponsor', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $sponsor_base; ?>/sample-sponsor/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="sponsor_permalink" id="sf_sponsor_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_sponsor_tog" <?php checked( in_array( $sponsor_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="sponsor_permalink_structure" id="sf_sponsor_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $sponsor_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_sponsor_tog' ).change(
                                function() {
                                    jQuery( '#sf_sponsor_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_sponsor_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_sponsor_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function team_settings() {
                echo wpautop( __( 'These settings control the permalinks used for team members. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_team_permalinks' );
                $team_permalink = $permalinks['team_base'];

                // Set base slug & team base
                $base_slug = __( 'team', 'swift-framework-plugin' );
                $team_base = __( 'team', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $team_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%team-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="team_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_team_tog" <?php checked( $structures[0], $team_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?team=sample-team-member</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="team_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_team_tog" <?php checked( $structures[1], $team_permalink ); ?> /> <?php _e( 'Team', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $team_base; ?>/sample-team-member/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="team_permalink" id="sf_team_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_team_tog" <?php checked( in_array( $team_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="team_permalink_structure" id="sf_team_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $team_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_team_tog' ).change(
                                function() {
                                    jQuery( '#sf_team_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_team_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_team_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function event_settings() {
                echo wpautop( __( 'These settings control the permalinks used for event members. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_event_permalinks' );
                $event_permalink = $permalinks['event_base'];

                // Set base slug & event base
                $base_slug = __( 'event', 'swift-framework-plugin' );
                $event_base = __( 'event', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $event_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%event-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="event_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_event_tog" <?php checked( $structures[0], $event_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?event=sample-event-member</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="event_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_event_tog" <?php checked( $structures[1], $event_permalink ); ?> /> <?php _e( 'Event', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $event_base; ?>/sample-event-member/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="event_permalink" id="sf_event_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_event_tog" <?php checked( in_array( $event_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="event_permalink_structure" id="sf_event_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $event_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_event_tog' ).change(
                                function() {
                                    jQuery( '#sf_event_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_event_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_event_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function resource_settings() {
                echo wpautop( __( 'These settings control the permalinks used for resources. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_resource_permalinks' );
                $resource_permalink = $permalinks['resource_base'];

                // Set base slug & resource base
                $base_slug = __( 'resource', 'swift-framework-plugin' );
                $resource_base = __( 'resource', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $resource_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%resource-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="resource_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_resource_tog" <?php checked( $structures[0], $resource_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?resource=sample-resource-article</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="resource_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_resource_tog" <?php checked( $structures[1], $resource_permalink ); ?> /> <?php _e( 'Resources', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $resource_base; ?>/sample-resource-article/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="resource_permalink" id="sf_resource_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_resource_tog" <?php checked( in_array( $resource_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="resource_permalink_structure" id="sf_resource_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $resource_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_resource_tog' ).change(
                                function() {
                                    jQuery( '#sf_resource_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_resource_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_resource_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function job_settings() {
                echo wpautop( __( 'These settings control the permalinks used for job. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_job_permalinks' );
                $job_permalink = $permalinks['job_base'];

                // Set base slug & job base
                $base_slug = __( 'job', 'swift-framework-plugin' );
                $job_base = __( 'job', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $job_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%job-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="job_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_job_tog" <?php checked( $structures[0], $job_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?job=sample-job-article</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="job_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_job_tog" <?php checked( $structures[1], $job_permalink ); ?> /> <?php _e( 'Jobs', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $job_base; ?>/sample-job-article/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="job_permalink" id="sf_job_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_job_tog" <?php checked( in_array( $job_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="job_permalink_structure" id="sf_job_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $job_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_job_tog' ).change(
                                function() {
                                    jQuery( '#sf_job_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_job_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_job_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function timeline_settings() {
                echo wpautop( __( 'These settings control the permalinks used for timeline. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'swift-framework-plugin' ) );

                // Get current permalinks
                $permalinks     = get_option( 'sf_timeline_permalinks' );
                $timeline_permalink = $permalinks['timeline_base'];

                // Set base slug & timeline base
                $base_slug = __( 'timeline', 'swift-framework-plugin' );
                $timeline_base = __( 'timeline', 'swift-framework-plugin' );

                $structures = array(
                    0 => '',
                    1 => '/' . trailingslashit( $timeline_base ),
                    2 => '/' . trailingslashit( $base_slug ),
                    3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%timeline-category%' )
                );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label><input name="timeline_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                                          class="sf_timeline_tog" <?php checked( $structures[0], $timeline_permalink ); ?> /> <?php _e( 'Default', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/?timeline=sample-timeline-article</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="timeline_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                                          class="sf_timeline_tog" <?php checked( $structures[1], $timeline_permalink ); ?> /> <?php _e( 'Timeline Points', 'swift-framework-plugin' ); ?>
                            </label></th>
                        <td><code><?php echo home_url(); ?>/<?php echo $timeline_base; ?>/sample-timeline-article/</code></td>
                    </tr>
                    <tr>
                        <th><label><input name="timeline_permalink" id="sf_timeline_custom_selection" type="radio"
                                          value="custom"
                                          class="sf_timeline_tog" <?php checked( in_array( $timeline_permalink, $structures ), false ); ?> />
                                <?php _e( 'Custom Base', 'swift-framework-plugin' ); ?></label></th>
                        <td>
                            <input name="timeline_permalink_structure" id="sf_timeline_permalink_structure" type="text"
                                   value="<?php echo esc_attr( $timeline_permalink ); ?>" class="regular-text code"> <span
                                class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'swift-framework-plugin' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <script type="text/javascript">
                    jQuery(
                        function() {
                            jQuery( 'input.sf_timeline_tog' ).change(
                                function() {
                                    jQuery( '#sf_timeline_permalink_structure' ).val( jQuery( this ).val() );
                                }
                            );

                            jQuery( '#sf_timeline_permalink_structure' ).focus(
                                function() {
                                    jQuery( '#sf_timeline_custom_selection' ).click();
                                }
                            );
                        }
                    );
                </script>
            <?php
            }

            public function settings_save() {
                if ( ! is_admin() ) {
                    return;
                }

                // Save options
                if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) ) {

                    if ( post_type_exists("news") ) {
                        $news_permalinks = get_option( 'sf_news_permalinks' );
                        if ( ! $news_permalinks ) {
                            $news_permalinks = array();
                        }
                        $news_permalinks['category_base'] = untrailingslashit( $sf_news_category_slug );
                    } else {

                    }

                    if ( post_type_exists("sponsor") ) {
                        $sponsor_permalinks = get_option( 'sf_sponsor_permalinks' );
                        if ( ! $sponsor_permalinks ) {
                            $sponsor_permalinks = array();
                        }
                        $sponsor_permalinks['category_base'] = untrailingslashit( $sf_sponsor_category_slug );
                    } else {

                    }

                    if ( post_type_exists("team") ) {
                        $team_permalinks = get_option( 'sf_team_permalinks' );
                        if ( ! $team_permalinks ) {
                            $team_permalinks = array();
                        }
                        $team_permalinks['category_base'] = untrailingslashit( $sf_team_category_slug );
                    } else {

                    }

                    if ( post_type_exists("event") ) {
                        $event_permalinks = get_option( 'sf_event_permalinks' );
                        if ( ! $event_permalinks ) {
                            $event_permalinks = array();
                        }
                        $event_permalinks['category_base'] = untrailingslashit( $sf_event_category_slug );
                    } else {

                    }

                    if ( post_type_exists("resource") ) {
                        $resource_permalinks = get_option( 'sf_resource_permalinks' );
                        if ( ! $resource_permalinks ) {
                            $resource_permalinks = array();
                        }
                        $resource_permalinks['category_base'] = untrailingslashit( $sf_resource_category_slug );
                    } else {

                    }

                    if ( post_type_exists("job") ) {
                        $job_permalinks = get_option( 'sf_job_permalinks' );
                        if ( ! $job_permalinks ) {
                            $job_permalinks = array();
                        }
                        $job_permalinks['category_base'] = untrailingslashit( $sf_job_category_slug );
                    } else {

                    }

                    if ( post_type_exists("timeline") ) {
                        $timeline_permalinks = get_option( 'sf_timeline_permalinks' );
                        if ( ! $timeline_permalinks ) {
                            $timeline_permalinks = array();
                        }
                        $timeline_permalinks['category_base'] = untrailingslashit( $sf_timeline_category_slug );
                    } else {

                    }

                    // Permalink bases
                    $news_permalink         = sanitize_text_field( $_POST['news_permalink'] );
                    $sponsor_permalink      = sanitize_text_field( $_POST['sponsor_permalink'] );
                    $team_permalink         = sanitize_text_field( $_POST['team_permalink'] );
                    $event_permalink        = sanitize_text_field( $_POST['event_permalink'] );
                    $resource_permalink        = sanitize_text_field( $_POST['resource_permalink'] );
                    $job_permalink         = sanitize_text_field( $_POST['job_permalink'] );
                    $timeline_permalink         = sanitize_text_field( $_POST['timeline_permalink'] );

                    if ( $news_permalink == 'custom' ) {
                        $news_permalink = sanitize_text_field( $_POST['news_permalink_structure'] );
                    } elseif ( empty( $news_permalink ) ) {
                        $news_permalink = false;
                    }
                    if ( $sponsor_permalink == 'custom' ) {
                        $sponsor_permalink = sanitize_text_field( $_POST['sponsor_permalink_structure'] );
                    } elseif ( empty( $sponsor_permalink ) ) {
                        $sponsor_permalink = false;
                    }
                    if ( $team_permalink == 'custom' ) {
                        $team_permalink = sanitize_text_field( $_POST['team_permalink_structure'] );
                    } elseif ( empty( $team_permalink ) ) {
                        $team_permalink = false;
                    }
                    if ( $event_permalink == 'custom' ) {
                        $event_permalink = sanitize_text_field( $_POST['event_permalink_structure'] );
                    } elseif ( empty( $event_permalink ) ) {
                        $event_permalink = false;
                    }
                    if ( $resource_permalink == 'custom' ) {
                        $resource_permalink = sanitize_text_field( $_POST['resource_permalink_structure'] );
                    } elseif ( empty( $resource_permalink ) ) {
                        $resource_permalink = false;
                    }
                    if ( $job_permalink == 'custom' ) {
                        $job_permalink = sanitize_text_field( $_POST['job_permalink_structure'] );
                    } elseif ( empty( $job_permalink ) ) {
                        $job_permalink = false;
                    }
                    if ( $timeline_permalink == 'custom' ) {
                        $timeline_permalink = sanitize_text_field( $_POST['timeline_permalink_structure'] );
                    } elseif ( empty( $timeline_permalink ) ) {
                        $timeline_permalink = false;
                    }

                    // Set base for each permalinks variable
                    $news_permalinks['news_base'] = untrailingslashit( $news_permalink );
                    $sponsor_permalinks['sponsor_base'] = untrailingslashit( $sponsor_permalink );
                    $team_permalinks['team_base'] = untrailingslashit( $team_permalink );
                    $event_permalinks['event_base'] = untrailingslashit( $event_permalink );
                    $resource_permalinks['resource_base'] = untrailingslashit( $resource_permalink );
                    $job_permalinks['job_base'] = untrailingslashit( $job_permalink );
                    $timeline_permalinks['timeline_base'] = untrailingslashit( $timeline_permalink );
                    
                    // Update permalinks
                    update_option( 'sf_news_permalinks', $news_permalinks );
                    update_option( 'sf_sponsor_permalinks', $sponsor_permalinks );
                    update_option( 'sf_team_permalinks', $team_permalinks );
                    update_option( 'sf_event_permalinks', $event_permalinks );
                    update_option( 'sf_resource_permalinks', $resource_permalinks );
                    update_option( 'sf_job_permalinks', $job_permalinks );
                    update_option( 'sf_timeline_permalinks', $timeline_permalinks );
                }
            }
        }

    endif;

    return new sf_post_type_permalinks();

?>
