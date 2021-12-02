<?php

namespace MavenAlgolia\Core;

class Indexer  {

	private $apiKey = "";
	private $appId = "";
	

	/**
	 * 
	 * @param string $indexName
	 * @param string $apiKey
	 * @param string $appId
	 * @throws \Exception
	 */
	public function __construct ( $appId, $apiKey ) {

	/**
	 * Help documentation
	 * https://github.com/algolia/algoliasearch-client-php#indexing-parameters
	 */
//	self::$index = array(
//						'my_index' => array(
//											'attributesToIndex' => array(),
//											'attributesForFaceting' => array(),
//											'attributeForDistinct' => '',
//											'ranking' => array(),
//											'customRanking' => array(),
//											'queryType' => 'prefixLast',
//											'slaves' => array(),
//											'master_index' => ''
//											)
//						);	
		if ( !$apiKey || !$appId ) {
			throw new \Exception( 'Missing or Invalid credentials' );
		}
		
		$this->apiKey = $apiKey;
		$this->appId = $appId;
		
	}
	
	public function getApiKey () {
		return $this->apiKey;
	}

	public function getAppId () {
		return $this->appId;
	}

	
	/**
     * Move an existing index.
     * @param tmpIndexName the name of index to copy.
     * @param indexName the new index name that will contains a copy of srcIndexName (destination will be overriten if it already exist).
	 * @return boolean
	 * @throws \MavenAlgolia\Core\Exception
	 */
	public function moveIndex( $tmpIndexName, $indexName ) {
		if( !empty( $tmpIndexName ) && !empty( $indexName ) ){
			// initialize API Client & Index
			$client = \Algolia\AlgoliaSearch\SearchClient::create( $this->getAppId(), $this->getApiKey() );
			try {
				$client->moveIndex( $tmpIndexName, $indexName );
				return true;
			} catch ( \Exception $exc ) {
				throw $exc;
			}
		}
		return false;
	}
	
	/**
	 * Index a single object
	 * @param string $indexName
	 * @param array $object
	 * @return boolean
	 * @throws \MavenAlgolia\Core\Exception
	 */
	public function indexObject( $indexName, $object ) {
		
		if( !isset( $object['objectID'] ) ){
			return false;
		}
		
		// initialize API Client & Index
		$client = \Algolia\AlgoliaSearch\SearchClient::create( $this->getAppId(), $this->getApiKey() );
		$index = $client->initIndex( $indexName );
		try {
			// object contains the object to save
			// the object must contains an objectID attribute
			$index->saveObject( $object );
			return true;
		} catch ( \Exception $exc ) {
			throw $exc;
		}
		return false;
	}
	
	/**
	 * Index multiples objects
	 * @param string $indexName
	 * @param array $objects
	 * @return boolean
	 * @throws \MavenAlgolia\Core\Exception
	 */
	public function indexObjects( $indexName, $objects ) {
		
		// initialize API Client & Index
		$client = \Algolia\AlgoliaSearch\SearchClient::create( $this->getAppId(), $this->getApiKey() );
		$index = $client->initIndex( $indexName );
		try {
			// object contains the object to save
			// the object must contains an objectID attribute
			$index->saveObjects( $objects );
			return true;
		} catch ( \Exception $exc ) {
			throw $exc;
		}
		return false;
	}
	
	/**
	 * Remove a single object from the index
	 * @param string $indexName
	 * @param integer $objectId
	 * @return boolean
	 * @throws \MavenAlgolia\Core\Exception
	 */
	public function deleteObject( $indexName, $objectId ) {

		global $blog_id;
		if ($objectId < 1000000000) {
			$objectId = $objectId+(1000000000*$blog_id);
		}

		// initialize API Client & Index
		$client = \Algolia\AlgoliaSearch\SearchClient::create( $this->getAppId(), $this->getApiKey() );
		$index = $client->initIndex( $indexName );
		try {
			// Remove objects
			$index->deleteObject( $objectId );
			return true;
		} catch ( \Exception $exc ) {
			throw $exc;
		}
		return false;
	}
	
	/**
	 * Remove multiple objects from the index
	 * @param string $indexName
	 * @param integer $objectIds
	 * @return boolean
	 * @throws \MavenAlgolia\Core\Exception
	 */
	public function deleteObjects( $indexName, $objectIds ) {

		global $blog_id;
		for ($x=0;$x<count($objectIds);$x=$x+1) {
			if ($objectIds[$x] < 1000000000) {
				$objectIds[$x] = $objectIds[$x]+(1000000000*$blog_id);
			}
		}

		// initialize API Client & Index
		$client = \Algolia\AlgoliaSearch\SearchClient::create( $this->getAppId(), $this->getApiKey() );
		$index = $client->initIndex( $indexName );
		try {
			// Remove objects
			$index->deleteObjects( $objectIds );
			return true;
		} catch ( \Exception $exc ) {
			throw $exc;
		}
		return false;
	}
	
	/*
	 * ------------------------------------------------------------
	 * POSTS SECTION 
	 * ------------------------------------------------------------
	 */
	
		
	/**
	 * Convert WP post object to Algolia format
	 * @global \MavenAlgolia\Core\type $wpdb
	 * @param \WP_Post $post
	 * @param Domain\PostType|string $type
	 * @return array
	 */
	public function postToAlgoliaObject( $post, $type = null ) {
		global $wpdb;
		
		if( empty( $type ) && !empty( $post->post_type ) ){
			$type = $post->post_type;
		}
		if(  is_string( $type ) ){
			// TODO: Implement a better way to do this, maybe setting the post objects as a class attribute
			$postObjects = FieldsHelper::getPostTypesObject();
			if( isset( $postObjects[$type] ) ){
				$type = $postObjects[$type];
			}else{
				// If the post type object doesn't exist return an empty array
				return array();
			}
		}
		
		// select the identifier of this row
		$row = array();

		// Index WP Post table fields
		$fields = $type->getFields();
		if( is_array( $fields ) && !empty( $fields ) ){
			foreach( $fields as $field ){
				if( isset( $post->{$field->getId()} ) ){
					$row[ $field->getLabel() ] = FieldsHelper::formatFieldValue( $post->{$field->getId()}, $field->getType() );
					
					// SOCIAL DRIVER -- DYLAN  -- OVERRIDE INDEX FOR BODY CONTENT
					if ($field->getLabel() == "content") {

						$pattern = "#\[.*?\]#";
						$row[ $field->getLabel() ] = preg_replace($pattern, '', $row[ $field->getLabel() ]);
						$row[ $field->getLabel() ] = preg_replace("/\r\n|\r|\n/", ' ', $row[ $field->getLabel() ]);
						$row[ $field->getLabel() ] = strip_tags($row[ $field->getLabel() ]);
						$row[ $field->getLabel() ] = preg_replace('~\b\S{50,}\b~', '', $row[ $field->getLabel() ]);
						$row[ $field->getLabel() ] = str_replace('");', '', $row[ $field->getLabel() ]);
						$row[ $field->getLabel() ] = str_replace('= ', '', $row[ $field->getLabel() ]);
						$row[ $field->getLabel() ] = preg_replace('!\s+!', ' ', $row[ $field->getLabel() ]);

						$row[ $field->getLabel() ] = substr( $row[ $field->getLabel() ], 0, 8000);

					} else if ($field->getLabel() == "excerpt" && function_exists("sf_excerpt")) {
            
            			$limit = 20;
			            $excerpt = "";
			            $custom_excerpt = sf_get_post_meta( $post->ID, 'sf_custom_excerpt', true );
			            
			            if ( $custom_excerpt != "" ) {
			                $excerpt = $custom_excerpt;
			                $excerpt = preg_replace( '`\[[^\]]*\]`', '', $excerpt );
			                $excerpt = wp_strip_all_tags($excerpt);
			                $pattern = "#\[.*?\]#";
			                $excerpt = preg_replace($pattern, '', $excerpt);
			                $excerpt = preg_replace("/\r\n|\r|\n/", ' ', $excerpt);
			                $excerpt = wp_strip_all_tags($excerpt);
			                $excerpt = preg_replace('~\b\S{50,}\b~', '', $excerpt);
			                $excerpt = str_replace('");', '', $excerpt);
			                $excerpt = str_replace('= ', '', $excerpt);
			                $excerpt = preg_replace('!\s+!', ' ', $excerpt);  
			                $excerpt = explode( ' ', $excerpt );
			            } else {
			                $excerpt = get_the_excerpt($post->ID);
			                if ( $excerpt == "" ) {
			                    $excerpt = get_post($post->ID)->post_excerpt;
			                }
			                $excerpt = preg_replace( '`\[[^\]]*\]`', '', $excerpt );
			                $excerpt = wp_strip_all_tags($excerpt);
			                $pattern = "#\[.*?\]#";
			                $excerpt = preg_replace($pattern, '', $excerpt);
			                $excerpt = preg_replace("/\r\n|\r|\n/", ' ', $excerpt);
			                $excerpt = wp_strip_all_tags($excerpt);
			                $excerpt = preg_replace('~\b\S{50,}\b~', '', $excerpt);
			                $excerpt = str_replace('");', '', $excerpt);
			                $excerpt = str_replace('= ', '', $excerpt);
			                $excerpt = preg_replace('!\s+!', ' ', $excerpt);  
			                $excerpt = explode( ' ', $excerpt );
			            } 

			            if ( $excerpt == "" ) {
			                $excerpt = strip_tags( trim( preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $post->post_content ) ) );
			                if ( function_exists( 'mb_strimwidth' ) ) {
			                    $excerpt = mb_strimwidth( $excerpt, 0, 160, '…' );
			                }
			                $excerpt = explode(" ", $excerpt);
			            }

			            for($i=0;$i<count($excerpt);$i++) {
			                $excerpt[$i] = trim($excerpt[$i]);
			                $word = $excerpt[$i];
			                if ( strlen($word) > 100 || 
			                    strpos($word, 'http://') !== false ||
			                    strpos($word, 'https://') !== false ) {
			                    unset($excerpt[$i]);
			                }
			            }
			                
			            $excerpt = explode( ' ', implode(" ", $excerpt), $limit+1 );

			            if ( count( $excerpt ) >= $limit ) {
			                array_pop( $excerpt );
			                $excerpt = implode( " ", $excerpt ) . '…';
			            } else {
			                $excerpt = implode( " ", $excerpt ) . '';
			            }
			            $excerpt = str_replace( '[…]', '…', $excerpt );

						$row[ $field->getLabel() ] = $excerpt;

					}
				}
			}
			unset( $field );
		}
		unset( $fields );

		// Index WP Compound fields
		$compoundFields = $type->getCompoundFields();
		if( is_array( $compoundFields ) && !empty( $compoundFields ) ){
			foreach( $compoundFields as $compoundField ){					
				$row[ $compoundField->getLabel() ] = FieldsHelper::getCompoundFieldValue( $post, $compoundField->getId() );
			}
			unset( $compoundField );
		}
		unset( $compoundFields );

		// Index WP Post meta fields
		$metaFields = $type->getMetaFields();
		if( is_array( $metaFields ) && !empty( $metaFields ) ){
			foreach( $metaFields as $metaField ){
				$metaValue = get_post_meta( $post->ID, $metaField->getId(), $metaField->isSingle() );
				if( $metaValue !== FALSE ){
					if( !is_array( $metaValue ) ){
						$metaValue = FieldsHelper::formatFieldValue( $metaValue, $metaField->getType() );
					}
					$row[ $metaField->getLabel() ] = $metaValue;
				}
			}
			unset( $metaValue );					
			unset( $metaField );					
		}
		unset( $metaFields );

		// Index WP Taxonomies
		$taxonomies = $type->getTaxonomies();
		if( is_array( $taxonomies ) && !empty( $taxonomies ) ){
			$termNames = array();

			foreach( $taxonomies as $taxonomy_slug => $taxonomy ){

				$termNames = wp_get_post_terms( $post->ID, $taxonomy_slug );
				if( is_array( $termNames ) && !empty( $termNames ) && count($termNames) > 0 ){
					// $row[ "_".$taxonomy->getLabel() ] = array();
					$row[ $taxonomy->getLabel() ] = array();
					for ($i = 0; $i <= count($termNames); $i++) {
						if ( isset($termNames[$i]) && !empty($termNames[$i]) && $termNames[$i]->name != null ) {
							$row[ ($taxonomy->getLabel()) ][] = str_replace("'", "", $termNames[$i]->name);
							// $row[ "_".$taxonomy->getLabel() ][] = $termNames[$i];
						}
					}
				}
				// if there is no terms create the field with empty values
				else{
					$row[ $taxonomy->getLabel() ] = array();
				}

			}
			unset( $termNames );
			unset( $taxonomy );
			unset( $tags );
		}
		unset( $taxonomies );

		// EXTRA FIELDS -- SOCIAL DRIVER -- DYLAN

        $exclude_posts = array();
        if ( is_plugin_active("search-exclude/search-exclude.php") ) {
            $exclude_posts = array_merge($exclude_posts, get_option('sep_exclude'));
        }
        if ( count($exclude_posts) > 0 && in_array($post->ID, $exclude_posts) ) {
			$row["public"] = false;
        } else {
			$row["public"] = true;
        }

		if ( isset($row["title"]) && !empty($row["title"]) && $row["title"] != "" && count(str_split($row["title"])) > 0 && preg_match("/^[a-zA-Z0-9]+$/", str_split($row["title"])[0]) == 1 ) {
			$row["alpha"] = strtoupper(str_split($row["title"])[0]);
		}

        $row["byline"] = dqc_get_post_details_cards( $post->ID );
		
		// Index featured image if it was configured so
		$postThumbId = 0;
		if( $type->indexFeaturedImage() ){
			if( has_post_thumbnail( $post->ID ) ){
				$postThumbId = get_post_thumbnail_id( $post->ID );
				$row['featuredImage'] = sf_post_thumbnail( "", "no" );
			} else {
				unset( $row['featuredImage'] );
			}
		}

		if ( $row["type"] == "point" ) {
			$geolocation = get_field("geolocation", $post->ID);
			if ( isset($geolocation["lat"]) && !empty($geolocation["lat"]) && $geolocation["lat"] != "" &&
			     isset($geolocation["lng"]) && !empty($geolocation["lng"]) && $geolocation["lng"] != "" ) {
				$geoloc = array("lat" => floatval($geolocation["lat"]), "lng" => floatval($geolocation["lng"]));
				$geoloc = json_decode(json_encode($geoloc), FALSE);
				$row["_geoloc"] = $geoloc;
			}
			if ( FALSE === get_post_status(get_post_meta($post->ID, "region", true)[0]) ) { } else {
				$row["region"] = get_post(get_post_meta($post->ID, "region", true)[0])->post_title;
			}
		} else if ( $post->post_type == "region" ) {
			$row["map_projection"] = get_field("map_projection", $post->ID);
			$geolocation = get_field("geolocation", $post->ID);
			if ( isset($geolocation["lat"]) && !empty($geolocation["lat"]) && $geolocation["lat"] != "" &&
			     isset($geolocation["lng"]) && !empty($geolocation["lng"]) && $geolocation["lng"] != "" ) {
				$geoloc = array("lat" => floatval($geolocation["lat"]), "lng" => floatval($geolocation["lng"]));
				$geoloc = json_decode(json_encode($geoloc), FALSE);
				$row["_geoloc"] = $geoloc;
			}
		}

		/* START PDF INDEXER */
		if ( $row["type"] == "resource" ) {

			$resource_file = get_field("sf_file", $post->ID);

			if ( $resource_file ) {

				if ( $resource_file["mime_type"] == "application/pdf" ) {

					$resource_file = $row["resourceLink"] = wp_get_attachment_url( $resource_file["ID"] );
					if ( file_get_contents($resource_file) ) {
						$parser = new \Smalot\PdfParser\Parser();
						$pdf    = $parser->parseFile($resource_file);
						$text = $pdf->getText();
						$text = str_replace("......................................................", "…", $text);
						$text = str_replace(".....................................................", "…", $text);
						$text = str_replace("....................................................", "…", $text);
						$text = str_replace("...................................................", "…", $text);
						$text = str_replace("..................................................", "…", $text);
						$text = str_replace(".................................................", "…", $text);
						$text = str_replace("................................................", "…", $text);
						$text = str_replace("...............................................", "…", $text);
						$text = str_replace("..............................................", "…", $text);
						$text = str_replace(".............................................", "…", $text);
						$text = str_replace("............................................", "…", $text);
						$text = str_replace("...........................................", "…", $text);
						$text = str_replace("..........................................", "…", $text);
						$text = str_replace(".........................................", "…", $text);
						$text = str_replace("........................................", "…", $text);
						$text = str_replace(".......................................", "…", $text);
						$text = str_replace("......................................", "…", $text);
						$text = str_replace(".....................................", "…", $text);
						$text = str_replace("....................................", "…", $text);
						$text = str_replace("...................................", "…", $text);
						$text = str_replace("..................................", "…", $text);
						$text = str_replace(".................................", "…", $text);
						$text = str_replace("................................", "…", $text);
						$text = str_replace("...............................", "…", $text);
						$text = str_replace("..............................", "…", $text);
						$text = str_replace(".............................", "…", $text);
						$text = str_replace("............................", "…", $text);
						$text = str_replace("...........................", "…", $text);
						$text = str_replace("..........................", "…", $text);
						$text = str_replace(".........................", "…", $text);
						$text = str_replace("........................", "…", $text);
						$text = str_replace(".......................", "…", $text);
						$text = str_replace("......................", "…", $text);
						$text = str_replace(".....................", "…", $text);
						$text = str_replace("....................", "…", $text);
						$text = str_replace("...................", "…", $text);
						$text = str_replace("..................", "…", $text);
						$text = str_replace(".................", "…", $text);
						$text = str_replace("................", "…", $text);
						$text = str_replace("...............", "…", $text);
						$text = str_replace("..............", "…", $text);
						$text = str_replace(".............", "…", $text);
						$text = str_replace("............", "…", $text);
						$text = str_replace("...........", "…", $text);
						$text = str_replace("..........", "…", $text);
						$text = str_replace(".........", "…", $text);
						$text = str_replace("........", "…", $text);
						$text = str_replace(".......", "…", $text);
						$text = str_replace("......", "…", $text);
						$text = str_replace(".....", "…", $text);
						$text = str_replace("....", "…", $text);
						$text = str_replace("...", "…", $text);
						$text = str_replace("……", "…", $text);
						$row["resourceContent"] = substr( $text, 0, 6000);
					}

				} else if ( $resource_file["mime_type"] == "text/plain" ) {

					$resource_file = $row["resourceLink"] = wp_get_attachment_url( $resource_file["ID"] );
					if ( file_get_contents($resource_file) ) {
						$text = file_get_contents($resource_file);
						$row["resourceContent"] = substr( $text, 0, 6000);
					}

				}

			}

		}

		/* $attached_media = get_attached_media("application/pdf",$post->ID);
		if ( count($attached_media) > 0 ) {
			$row["attachmentLink"] = array();
			$row["attachmentContent"] = array();
			foreach($attached_media as $attachment) {
				if ( file_get_contents($attachment->guid) ) {
					$row["attachmentLink"][] = $attachment->guid;
					$parser = new \Smalot\PdfParser\Parser();
					$pdf    = $parser->parseFile($attachment->guid);
					$text = $pdf->getText();
					$row["attachmentContent"][] = substr( $text, 0, 2000);
				}
			}
		} */
		/* END PDF INDEXER */

		/* START EXTERNAL LINK */
	    $redirect_link = get_post_meta($post->ID, 'external_link');
	    if ( empty($redirect_link) || count($redirect_link) <= 0 || $redirect_link[0] == "" ) {
	        $redirect_link = get_post_meta($post->ID, 'link');
	    }
	    if (!empty($redirect_link) && count($redirect_link) > 0) {
	        $redirect_link = $redirect_link[0];
	        if ( !empty($redirect_link) && $redirect_link != "" ) {
	            if ( strpos($redirect_link, 'http://') <= 0 && strpos($redirect_link, 'https://') <= 0 ) {
	                $redirect_link = 'http://' . $redirect_link;
	            }
	            $redirect_link = str_replace("http://http://", "http://", $redirect_link);
	            $redirect_link = str_replace("http://https://", "https://", $redirect_link);
	            $redirect_link = esc_url($redirect_link);
				$row["permalink"] = $redirect_link;
	        }
	    }
		/* END EXTERNAL LINK */

		// Index WP media
		$mediaTypes = $type->getMediaTypes();
		if( is_array( $mediaTypes ) && !empty( $mediaTypes ) ){
			$tags = array();
			//TODO: implement different methods or ways to index audio, videos and other files
			foreach( $mediaTypes as $mediaType ){

				// For now we just support images
				if( $mediaType !== 'image' ){
					continue;
				}

				// Index WP media
				$whereExclude = '';
				// Exclude featured image if it is indexed separately
				if( !empty( $postThumbId ) ){
					$whereExclude = " AND ID != {$postThumbId} ";
				}
				$query = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_parent = %d AND post_mime_type LIKE %s {$whereExclude} ORDER BY menu_order ASC", $post->ID, $mediaType . '%' );
				$children = $wpdb->get_col( $query );

				if( is_array( $children ) && !empty( $children ) ){
					$mediaFields = array();
					foreach ( $children as $childId ) {
						$mediaFields[] = $this->getImage( $childId );
					}
					$row[ $mediaType ] = $mediaFields;

					unset($childId);
				}else{
					$row[ $mediaType ] = array();
				}
			}
			unset($mediaFields);
			unset( $children );
			unset( $query );
			unset( $mediaType );
		}
		unset( $mediaTypes );

		global $blog_id;
		if ($row["objectID"] < 1000000000) {
			$row["objectID"] = $row["objectID"]+(1000000000*$blog_id);
		}
		$row["siteID"] = $blog_id;

		unset($row["parent"]);
		unset($row["authorId"]);
		unset($row["menu_order"]);

		return $row;
	}
	
	/**
	 * 
	 * @global type $wpdb
	 * @param int $attachId Attachment Post ID
	 * @return array  Image information
	 */
	public function getImage( $attachId ) {
		global $wpdb;
		if( empty($attachId) ){
			return array();
		}
		$uploadDir = wp_upload_dir();
		$uploadBaseUrl = $uploadDir['baseurl'];
		$image['ID'] = $attachId;
		//we will need to get the ALT info from Metas
		$image['alt'] = get_post_meta( $attachId, '_wp_attachment_image_alt', TRUE );
		
		$query = $wpdb->prepare( "SELECT post_title, post_content, post_excerpt, post_mime_type FROM {$wpdb->posts} WHERE ID = %d", $attachId );
		$attachment = $wpdb->get_row( $query );
		if( $attachment ){
			$image['title'] = $attachment->post_title;
			$image['description'] = $attachment->post_content;
			$image['caption'] = $attachment->post_excerpt;
			$image['mime_type'] = $attachment->post_mime_type;
		}
		unset( $query );
		unset( $attachment );
		
		$attachmentMeta = get_post_meta( $attachId, '_wp_attachment_metadata', TRUE );		
		
		if( is_array($attachmentMeta) && !empty( $attachmentMeta ) ){
			$image['width'] = $attachmentMeta['width'];
			$image['height'] =	$attachmentMeta['height'];
			$image['file'] = sprintf('%s/%s', $uploadBaseUrl, $attachmentMeta['file'] );
			$image['sizes'] = $attachmentMeta['sizes'];
			if( isset( $image['sizes'] ) && is_array( $image['sizes'] ) ){
				$sizesToIndex = apply_filters( 'ma_image_sizes_to_index', array('thumbnail', 'medium', 'large') );
				foreach ( $image['sizes'] as $size => &$sizeAttrs ) {
					if( !in_array( $size, $sizesToIndex ) ){
						unset( $image['sizes'][$size] );
						continue;
					}
					if( isset( $sizeAttrs['file'] ) && $sizeAttrs['file'] ){
						$baseFileUrl = str_replace( wp_basename($attachmentMeta['file']), '', $attachmentMeta['file']);
						
						$sizeAttrs['file'] = sprintf( '%s/%s%s', $uploadBaseUrl, $baseFileUrl, $sizeAttrs['file']);
					}
				}
			}
			unset($attachmentMeta);
		}
		return $image;
	}

	/**
	 * 
	 * @global type $wpdb
	 * @param string $indexName
	 * @param \Maven\Core\Domain\PostType[] $types
	 * @param int $postsPerPage How many posts per page
	 * @param int $offset Where to start
	 * @return void
	 * @throws \Exception
	 */
	public function removeIndexData ( $indexName, $types, $postsPerPage = -1, $offset = 0 ) {
		// WE will use $wpdb to make the calls faster
		global $wpdb, $blog_id;
		
		if ( ! $indexName  ) {
			throw new \Exception( 'Missing or Invalid Index Name' );
		}
		
		if( !is_array( $types ) ){
			$types = array( $types );
		}
		
		$postTypes = implode( "','", $types );
		
		$postStatuses = implode( "','", array_diff( get_post_stati( array( 'show_in_admin_status_list' => TRUE ) ), array( 'publish' ) ) );
		
			$limit = '';
			if( (int)$postsPerPage > 0 ){
				$limit = sprintf( "LIMIT %d, %d", $offset, $postsPerPage );
			}
			$join = apply_filters('mvnAlgRemoveIndexDataJoin', '');
			$where = apply_filters('mvnAlgRemoveIndexDataWhere', " AND ( post_status IN ('{$postStatuses}') AND post_type IN ( '{$postTypes}' ) ) ");
			$query = "SELECT DISTINCT ID FROM {$wpdb->posts} {$join} WHERE 1 = 1 {$where} {$limit}";
			$posts = $wpdb->get_results( $query );
			$totalRemoved = 0;
			if ( $posts ) {
			
				$batch = array();
				
				// iterate over results and send them by batch of 10000 elements
				foreach ( $posts as $post ) {
					// select the identifier of this row
					if ($post->ID < 1000000000) {
						$post->ID = $post->ID+(1000000000*$blog_id);
					}
					array_push( $batch, $post->ID );
					$totalRemoved++;
				}
				unset( $post );
				unset( $posts );
				try {
					// Remove objects
					$this->deleteObjects( $indexName, $batch );
				} catch ( \Exception $exc ) {
					throw $exc;
				}
				unset( $batch );
			}
	
		return $totalRemoved;
	}
	
	/**
	 * 
	 * @global type $wpdb
	 * @param string $indexName
	 * @param \Maven\Core\Domain\PostType[] $types
	 * @param int $postsPerPage How many posts per page
	 * @param int $offset Where to start
	 * @return void
	 * @throws \Exception
	 */
	public function indexData ( $indexName, $types, $postsPerPage = -1, $offset = 0 ) {
		// WE will use $wpdb to make the calls faster
		global $wpdb;

		if ( strpos($_SERVER['HTTP_HOST'], PRODSITE) !== false || strpos($_SERVER['HTTP_HOST'], STAGSITE) !== false || strpos($_SERVER['HTTP_HOST'], DEVSITE) !== false ) {
			
			if ( ! $indexName  ) {
				throw new \Exception( 'Missing or Invalid Index Name' );
			}
			
			
				$limit = '';
				if( (int)$postsPerPage > 0 ){
					$limit = sprintf( "LIMIT %d, %d", $offset, $postsPerPage );
				}
				$postFields = $types->getFieldsIdsForQuery();
				$join = apply_filters('mvnAlgIndexDataJoin', '');
				$where = apply_filters('mvnAlgIndexDataWhere', " AND ( post_status IN ('publish') AND post_type = '{$types->getType()}' )");
				$query = "SELECT {$postFields} FROM {$wpdb->posts} {$join} WHERE 1 = 1 {$where} {$limit}";
				$posts = $wpdb->get_results( $query );
				$totalIndexed = 0;
				if ( $posts ) {
				
					$batch = array();
					// iterate over results and send them by batch of 10000 elements
					foreach ( $posts as $post ) {
						// select the identifier of this row
						$row = $this->postToAlgoliaObject( $post, $types );
						array_push( $batch, $row );
						$totalIndexed++;
					}
					unset($row);
					unset( $post );
					unset( $posts );

					try {
						$this->indexObjects($indexName, $batch);
					} catch ( \Exception $exc ) {
						throw $exc;
					}

					unset( $batch );
				}
		
			unset( $postFields );
			return $totalIndexed;

		} else {

			return 0;

		}
	}
		
	/*
	 * ------------------------------------------------------------
	 * END POSTS SECTION 
	 * ------------------------------------------------------------
	 */
	
	
	
	/*
	 * ------------------------------------------------------------
	 * TAXONOMIES SECTION 
	 * ------------------------------------------------------------
	 */
	
	
	/**
	 * Convert WP post object to Algolia format
	 * @global \MavenAlgolia\Core\type $wpdb
	 * @param object $term
	 * @param Domain\Taxonomy|string $taxonomy
	 * @return array
	 */
	public function termToAlgoliaObject( $term, $taxonomy = null ) {
		
		if( empty( $taxonomy ) && !empty( $term->taxonomy ) ){
			$taxonomy = $term->taxonomy;
		}
		if(  is_string( $taxonomy ) ){
			$taxonomy = FieldsHelper::getTaxonomyObjectByType( $taxonomy );
		}
		
		// select the identifier of this row
		$row = array();

		// Index WP Tert and Taxonomy tables fields
		$fields = $taxonomy->getFields();
		if( is_array( $fields ) && !empty( $fields ) ){
			foreach( $fields as $field ){
				if( isset( $term->{$field->getId()} ) ){
					$row[ $field->getLabel() ] = FieldsHelper::formatFieldValue( $term->{$field->getId()}, $field->getType() );
				}
			}
			unset( $field );
		}
		unset( $fields );

		// Index Taxonomy Compound fields
		$compoundFields = $taxonomy->getCompoundFields();
		if( is_array( $compoundFields ) && !empty( $compoundFields ) ){
			foreach( $compoundFields as $compoundField ){					
				$row[ $compoundField->getLabel() ] = FieldsHelper::getTaxCompoundFieldValue( $term, $compoundField->getId() );
			}
			unset( $compoundField );
		}
		unset( $compoundFields );

		// Index Term meta fields
//		$metaFields = $taxonomy->getMetaFields();
//		if( is_array( $metaFields ) && !empty( $metaFields ) ){
//			foreach( $metaFields as $metaField ){
//				$metaValue = get_post_meta( $taxonomy->ID, $metaField->getId(), $metaField->isSingle() );
//				if( $metaValue !== FALSE ){
//					if( !is_array( $metaValue ) ){
//						$metaValue = FieldsHelper::formatFieldValue( $metaValue, $metaField->getType() );
//					}
//					$row[ $metaField->getLabel() ] = $metaValue;
//				}
//			}
//			unset( $metaValue );					
//			unset( $metaField );					
//		}
//		unset( $metaFields );

		return $row;
	}

	/**
	 * 
	 * @global type $wpdb
	 * @param string $indexName
	 * @param \Maven\Core\Domain\PostType[] $types
	 * @param int $postsPerPage How many posts per page
	 * @param int $offset Where to start
	 * @return void
	 * @throws \Exception
	 */
	public function indexTaxonomyData ( $indexName, $taxonomy, $postsPerPage = -1, $offset = 0 ) {
		// WE will use $wpdb to make the calls faster
		global $wpdb;
		
		if ( ! $indexName  ) {
			throw new \Exception( 'Missing or Invalid Index Name' );
		}
		
		
			$limit = '';
			if( (int)$postsPerPage > 0 ){
				$limit = sprintf( "LIMIT %d, %d", $offset, $postsPerPage );
			}
			$termFields = $taxonomy->getFieldsIdsForQuery();
			
			$showEmpty = 0;
			$query = $wpdb->prepare( "SELECT {$termFields} FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id WHERE {$wpdb->term_taxonomy}.taxonomy = %s AND {$wpdb->term_taxonomy}.count >= %d {$limit}", $taxonomy->getType(), $showEmpty );
			$terms = $wpdb->get_results( $query );
			$totalIndexed = 0;
			if ( $terms ) {
			
				$batch = array();
				// iterate over results and send them by batch of 10000 elements
				foreach ( $terms as $term ) {
					// select the identifier of this row
					$row = $this->termToAlgoliaObject( $term, $taxonomy );
					array_push( $batch, $row );
					$totalIndexed++;
				}
				unset($row);
				unset( $term );
				unset( $terms );
	//			echo json_encode( $batch );
	//				die;
	//			
				try {
					$this->indexObjects($indexName, $batch);
				} catch ( \Exception $exc ) {
					throw $exc;
				}

				unset( $batch );
			}
	
		unset( $termFields );
		return $totalIndexed;
	}

	/*
	 * ------------------------------------------------------------
	 * END TAXONOMIES SECTION 
	 * ------------------------------------------------------------
	 */
	
}