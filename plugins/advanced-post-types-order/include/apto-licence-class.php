<?php   
        
               
        class APTO_licence
        {
         
            function __construct()
                {
                    $this->licence_deactivation_check();   
                }
                
            function __destruct()
                {
                    
                }
                
            function licence_key_verify()
                {
                    $license_data = get_site_option('apto_license');
                    if(!is_array($license_data))
                        $license_data   =   array();
                    
                    if($this->is_local_instance())
                        return TRUE;
                             
                    if(!isset($license_data['kye']) || $license_data['kye'] == '')
                        return FALSE;
                        
                    return TRUE;
                }
                
            function is_local_instance()
                {
                        
                    if( defined('APTO_REQUIRE_KEY') &&  APTO_REQUIRE_KEY    === TRUE    )
                        return FALSE;
                    
                    $instance   =   trailingslashit(APTO_INSTANCE);
                    if(
                            stripos($instance, base64_decode('bG9jYWxob3N0Lw==')) !== FALSE
                        ||  stripos($instance, base64_decode('MTI3LjAuMC4xLw==')) !== FALSE
                        ||  stripos($instance, base64_decode('LmRldg==')) !== FALSE
                        ||  stripos($instance, base64_decode('c3RhZ2luZy53cGVuZ2luZS5jb20=')) !== FALSE
                        

                        
                        )
                        {
                            return TRUE;   
                        }
                        
                    return FALSE;
                    
                }
                
                
            function licence_deactivation_check()
                {
                    
                    if(!$this->licence_key_verify() ||  $this->is_local_instance()  === TRUE)
                        return;
                    
                    $license_data = get_site_option('apto_license');
                    if(!is_array($license_data))
                        $license_data   =   array();
                    
                    if(isset($license_data['last_check']))
                        {
                            if(time() < ($license_data['last_check'] + 86400))
                                {
                                    return;
                                }
                        }
                        
                    if ( !  $this->create_lock( 'APTO__API_status-check', 50 ) )
                        return;
                    
                    global $wp_version;
                    
                    //update already the last_check to avoid multiple calls
                    $license_data['last_check']   = time();    
                    update_site_option('apto_license', $license_data);
                    
                    $license_key =  isset($license_data['kye']) ?       $license_data['kye']    :   '';
                    $args = array(
                                        'woo_sl_action'         => 'status-check',
                                        'product_unique_id'     => APTO_PRODUCT_ID,
                                        'licence_key'           => $license_key,
                                        'domain'                => APTO_INSTANCE,
                                    );
                    $request_uri    = APTO_APP_API_URL . '?' . http_build_query( $args , '', '&');
                    $data           = wp_remote_get( $request_uri,  array(
                                                                            'timeout'     => 20,
                                                                            'user-agent'  => 'WordPress/' . $wp_version . '; APTO/' . APTO_VERSION .'; ' . get_bloginfo( 'url' ),
                                                                            ) );
                    
                    if(is_wp_error( $data ) || $data['response']['code'] != 200)
                        return;
                        
                    $response_block = json_decode($data['body']);

                    if(!is_array($response_block) || count($response_block) < 1)
                        return;    
                        
                    $response_block = $response_block[count($response_block) - 1];
                    if (is_object($response_block))
                        {
                            if($response_block->status_code == 'e312' || $response_block->status_code == 's203' ||  $response_block->status_code == 'e204')
                                {
                                    $license_data['kye']          = '';
                                }
                                
                            if($response_block->status == 'error')
                                {
                                    $license_data['kye']          = '';
                                }       
                        }
                          
                    update_site_option('apto_license', $license_data);
                    
                    $this->release_lock( 'APTO__API_status-check' );
                    
                }
                
                
                
                
            /**
            * Create a Lock functionality using the MySql 
            * 
            * @param mixed $lock_name
            * @param mixed $release_timeout
            * 
            * @return bool False if a lock couldn't be created or if the lock is still valid. True otherwise.
            */
            function create_lock( $lock_name, $release_timeout = null ) 
                {
                
                    global $wpdb, $blog_id;
                    
                    if ( ! $release_timeout ) {
                        $release_timeout = 10;
                    }
                    $lock_option = $lock_name . '.lock';
                    
                    if (    is_multisite()  )
                        {
                            // Try to lock.
                            $lock_result = $wpdb->query( $wpdb->prepare( "INSERT INTO `". $wpdb->sitemeta ."` (`site_id`, `meta_key`, `meta_value`) 
                                                                            SELECT %s, %s, %s FROM DUAL
                                                                            WHERE NOT EXISTS (SELECT * FROM `". $wpdb->sitemeta ."` 
                                                                                  WHERE `meta_key` = %s AND `meta_value` != '') 
                                                                            LIMIT 1", $blog_id, $lock_option, time(), $lock_option) );
                        }
                        else
                        {
                            // Try to lock.
                            $lock_result = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `". $wpdb->options ."` (`option_name`, `option_value`, `autoload`) 
                                                                            VALUES (%s, %s, 'no') /* LOCK */", $lock_option, time() ));   
                        }
                    
                                        
                    if ( ! $lock_result ) 
                        {
                            $lock_result    =   $this->get_lock( $lock_option );

                            // If a lock couldn't be created, and there isn't a lock, bail.
                            if ( ! $lock_result ) {
                                return false;
                            }

                            // Check to see if the lock is still valid. If it is, bail.
                            if ( $lock_result > ( time() - $release_timeout ) ) {
                                return false;
                            }

                            // There must exist an expired lock, clear it and re-gain it.
                            $this->release_lock( $lock_name );

                            return $this->create_lock( $lock_name, $release_timeout );
                        }

                    // Update the lock, as by this point we've definitely got a lock, just need to fire the actions.
                    $this->update_lock( $lock_option, time() );

                    return true;
                
                }


            /**
            * Retrieve a lock value
            * 
            * @param mixed $lock_name
            * @param mixed $return_full_row
            */
            private function get_lock( $lock_name, $return_full_row =   FALSE )
                {
                
                    global $wpdb;
                    
                    if (    is_multisite()  )
                        {
                            $mysq_query =   $wpdb->get_row( $wpdb->prepare("SELECT `site_id`, `meta_key`, `meta_value` FROM  `". $wpdb->sitemeta ."`
                                                                            WHERE `meta_key`    =   %s", $lock_name ) );

                            if ( $return_full_row   === TRUE )
                                return $mysq_query;
                                
                            if ( is_object($mysq_query) && isset ( $mysq_query->meta_value ) )
                                return $mysq_query->meta_value;
                        }
                        else
                        {
                            $mysq_query =   $wpdb->get_row( $wpdb->prepare("SELECT `option_name`, `option_value` FROM  `". $wpdb->options ."`
                                                                            WHERE `option_name`    =   %s", $lock_name ) );

                            if ( $return_full_row   === TRUE )
                                return $mysq_query;
                                
                            if ( is_object($mysq_query) && isset ( $mysq_query->option_value ) )
                                return $mysq_query->option_value;   
                            
                        }
                        
                    return FALSE;
                
                }


            /**
            * Update lock value
            *     
            * @param mixed $lock_name
            * @param mixed $lock_value
            */
            private function update_lock( $lock_name, $lock_value )
                {
                
                    global $wpdb;
                    
                    if (    is_multisite()  )
                        {
                            $mysq_query =   $wpdb->query( $wpdb->prepare("UPDATE `". $wpdb->sitemeta ."` 
                                                                            SET meta_value = %s
                                                                            WHERE meta_key = %s", $lock_value, $lock_name) );
                        }
                        else
                        {
                            $mysq_query =   $wpdb->query( $wpdb->prepare("UPDATE `". $wpdb->options ."` 
                                                                            SET option_value = %s
                                                                            WHERE option_name = %s", $lock_value, $lock_name) );
                        }
                    
                    
                    return $mysq_query;
                    
                }


            /**
            * Releases an upgrader lock.
            *
            * @param string $lock_name The name of this unique lock.
            * @return bool True if the lock was successfully released. False on failure.
            */
            function release_lock( $lock_name ) 
                {
                
                    global $wpdb;
                    
                    $lock_option = $lock_name . '.lock';
                    
                    if (    is_multisite()  )
                        {
                            $mysq_query =   $wpdb->query( $wpdb->prepare( "DELETE FROM `". $wpdb->sitemeta ."` 
                                                                            WHERE meta_key = %s", $lock_option ) );
                        }
                        else
                        {
                            $mysq_query =   $wpdb->query( $wpdb->prepare( "DELETE FROM `". $wpdb->options ."` 
                                                                            WHERE option_name = %s", $lock_option ) );
                        }
                    
                    return $mysq_query;
                    
                }
            
            
        }
            

        
    
?>