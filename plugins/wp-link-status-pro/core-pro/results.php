<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/module.php');

/**
 * WP Link Status Pro Core Results class
 *
 * @package WP Link Status Pro
 * @subpackage WP Link Status Pro Core
 */
class WPLNST_Core_Pro_Results extends WPLNST_Core_Module {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Requested operation
	 */
	private $op;



	/**
	 * Location object
	 */
	private $location;
	private $locations;
	private $locations_ids;



	/**
	 * Scan object
	 */
	private $scan;



	/**
	 * Nonce seed for validation and creating new one
	 */
	private $nonce_key;



	/**
	 * User value
	 */
	private $value;



	/**
	 * URL info array
	 */
	private $urlinfo;



	/**
	 * Response array
	 */
	private $response;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	/**
	 * Custom constructor
	 */
	protected function on_construct($args = null) {
		
		// Validate input
		$this->validation();
		
		// Default response
		$this->response = self::default_ajax_response($this->nonce_key);
		
		// Call internal method
		$this->{$this->op}();
		
		// Output and end
		self::output_ajax_response($this->response);
	}



	/**
	 * Retrieve data and first validation
	 */
	private function validation() {
		
		
		/* Operation */
		
		// Check operation
		if (empty($_POST['op']) || !in_array($_POST['op'], array('url_edit', 'url_unlink', 'url_ignore', 'url_unignore', 'url_status', 'url_headers', 'anchor_edit', 'url_redir', 'url_nofollow', 'url_dofollow', 'bulk_ignore', 'bulk_unignore', 'bulk_unlink', 'bulk_anchor', 'bulk_url', 'bulk_status', 'bulk_redir', 'bulk_nofollow', 'bulk_dofollow')))
			self::error_ajax_response(__('Missing or invalid action parameter', 'wplnst'));
		
		// Copy operation
		$this->op = $_POST['op'];
		
		
		/* Location single or multiple */
		
		// Check loc_id
		$loc_id = empty($_POST['loc_id'])? false : array_map('intval', explode('-', $_POST['loc_id']));
		if (empty($loc_id))
			self::error_ajax_response(__('Missing or invalid result identifier parameter', 'wplnst'));
		
		// Load scans library
		$this->load_scans_object();
		
		// Check bulk action
		if (0 === strpos($this->op, 'bulk_')) {
			
			// Retrieve multiple locations
			if (false === ($locations = $this->scans->get_scan_locations_by_ids($loc_id)))
				self::error_ajax_response(__('Resource not found in database', 'wplnst'));
			
			// Copy single location
			$this->location = $locations[0];
			
			// Set properties
			$this->locations = $locations;
			$this->locations_ids = array();
			foreach ($this->locations as $location)
				$this->locations_ids[] = $location->loc_id;
		
		// Retrieve single location
		} elseif (false === ($this->location = $this->scans->get_scan_location_by_id($loc_id[0]))) {
			self::error_ajax_response(__('Resource not found in database', 'wplnst'));
		}
		
		
		/* Scan */
		
		// Retrieve scan
		if (false === ($this->scan = $this->scans->get_scan_by_id($this->location->scan_id)))
			self::error_ajax_response(__('Unable to retrieve the resource associated scan', 'wplnst'));
		
		
		/* Nonce */
		
		// Check nonce key
		$this->nonce_key = 'wplnst-results-'.$this->scan->hash;
		if (!self::check_ajax_submit($response, 'manage_options', $this->nonce_key))
			self::output_ajax_response($response);
		
		
		/* Entered value */
		
		// Check value
		$this->value = isset($_POST['value'])? stripslashes($_POST['value']) : false;
	}



	// Response to actions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set a result as ignored
	 */
	private function url_ignore() {
		$this->scans->update_scan_url_location($this->location->loc_id, array('ignored' => 1));
		$this->link_edit_status_total();
	}



	/**
	 * Undo an ignored result
	 */
	private function url_unignore() {
		$this->scans->update_scan_url_location($this->location->loc_id, array('ignored' => 0));
		$this->link_edit_status_total();
	}



	/**
	 * Location bulk ignored
	 */
	private function bulk_ignore() {
		$this->scans->update_scan_url_locations($this->locations_ids, array('ignored' => 1));
		$this->link_edit_status_total();
	}



	/**
	 * Bulk undo ignored
	 */
	private function bulk_unignore() {
		$this->scans->update_scan_url_locations($this->locations_ids, array('ignored' => 0));
		$this->link_edit_status_total();
	}



	/**
	 * Recheck status
	 */
	private function url_status() {
		$this->link_edit_status_recheck($this->location);
	}



	/**
	 * Bulk URL status
	 */
	private function bulk_status() {
		
		// Initialize
		$error = false;
		$success = false;
		$reasons = array();
		
		// Prepare response array of locations
		$this->response['data']['locations'] = array();
		
		// Update each location
		foreach ($this->locations as $location) {
			
			// Skip unlinked
			if ($location->unlinked)
				continue;
			
			// Remove temp data
			$this->response['data']['marks'] = false;
			$this->response['data']['request_status'] = false;
			$this->response['data']['request_url'] = false;
			
			// Edit anchor
			$this->link_edit_status_recheck($location);
			
			// Initialize
			$status = 'ok';
			$reason = '';
			
			// Check response error
			if ('error' == $this->response['status']) {
				
				// Mark error
				$error = true;
				
				// Copy values
				$status = 'error';
				$reason = $this->response['reason'];
				
				// Copy reason
				if (!empty($reason) && !in_array($reason, $reasons))
					$reasons[] = $reason;
				
				// Reset status
				$this->response['status'] = 'ok';
				$this->response['reason'] = '';
			
			// Ok
			} else {
				
				// At least one Ok
				$success = true;
			}
			
			// Add location
			$this->response['data']['locations'][] = array(
				'loc_id' 		 => $location->loc_id,
				'status' 		 => $status,
				'reason' 		 => $reason,
				'marks'			 => $this->response['data']['marks'],
				'request_status' => $this->response['data']['request_status'],
				'request_url' 	 => $this->response['data']['request_url'],
			);
		}
		
		// Check Error
		if ($error) {
			$this->response['status'] = $success? 'ok' : 'error';
			$this->response['reason'] = $reasons;
		}
	}



	/**
	 * Retrieve headers
	 */
	private function url_headers() {
		
		// Retrieve headers data
		$result = $this->scans->get_scan_result_headers($this->location->scan_id, $this->location->url_id);
		
		// No results
		if (empty($result)) {
			
			// No headers info
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Headers information not found', 'wplnst');
			
		// Done
		} else {
			
			/* Time and size data */
			
			// Request at
			$this->response['data']['request_at'] = '';
			if (!empty($result->request_at)) {
				$timestamp = strtotime($result->request_at);
				if (!empty($timestamp))
					$this->response['data']['request_at'] = gmdate('Y-m-d H:i:s', $timestamp + (get_option('gmt_offset') * HOUR_IN_SECONDS));
			}
			
			// Total time
			$this->response['data']['total_time'] = '';
			if ($result->total_time > 0)
				$this->response['data']['total_time'] = number_format_i18n($result->total_time, 3);
			
			// Total size
			$this->response['data']['total_bytes'] = '';
			if ($result->total_bytes > 0) {
				wplnst_require('core', 'util-math');
				$this->response['data']['total_bytes'] = wplnst_format_bytes($result->total_bytes);
			}
			
			
			/* Headers data */
			
			// Prepare response headers
			$result_headers = @json_decode($result->headers, true);
			if (empty($result_headers) || !is_array($result_headers))
				$result_headers = array();
			
			// Sanitize response headers
			$headers = array();
			foreach ($result_headers as $key => $value)
				$headers[esc_html($key)] = esc_html($value);
			
			// Prepare request headers
			$result_requests = @json_decode($result->headers_request, true);
			if (empty($result_requests) || !is_array($result_requests))
				$result_requests = array();
			
			// Sanitize request headers
			$requests = array();
			foreach ($result_requests as $key => $value)
				$requests[esc_html($key)] = esc_html($value);
			
			// Copy json data
			$this->response['data']['headers'] = $headers;
			$this->response['data']['headers_request'] = $requests;
			
			
			/* URL data */
			
			// Retrieve full URL data
			$this->response['data']['url'] = '';
			$result_url = $this->scans->get_scan_url(array('id' => $this->location->url_id));
			if (!empty($result_url) && is_object($result_url))
				$this->response['data']['url'] = esc_html($result_url->url);
		}
	}



	/**
	 * Remove element img, or unlink URL and leave anchor text
	 */
	private function url_unlink() {
		$this->link_edit($this->location, 'unlink');
	}



	/**
	 * Bulk unlink elements
	 */
	private function bulk_unlink() {
		
		// Initialize
		$error = false;
		$success = false;
		$reasons = array();
		
		// Prepare response array of locations
		$this->response['data']['locations'] = array();
		
		// Update each location		
		foreach ($this->locations as $location) {
			
			// Skip unlinkables
			if (!$this->is_unlinkable($location))
				continue;
			
			// Unlink item
			$this->link_edit($location, 'unlink');
			
			// Initialize
			$status = 'ok';
			$reason = '';
			
			// Check response error
			if ('error' == $this->response['status']) {
				
				// Mark error
				$error = true;
				
				// Copy values
				$status = 'error';
				$reason = $this->response['reason'];
				
				// Copy reason
				if (!empty($reason) && !in_array($reason, $reasons))
					$reasons[] = $reason;
				
				// Reset status
				$this->response['status'] = 'ok';
				$this->response['reason'] = '';
			
			// Ok
			} else {
				
				// At least one Ok
				$success = true;
			}
			
			// Add location
			$this->response['data']['locations'][] = array(
				'loc_id' => $location->loc_id,
				'status' => $status,
				'reason' => $reason,
			);
		}
		
		// Check Error
		if ($error) {
			$this->response['status'] = $success? 'ok' : 'error';
			$this->response['reason'] = $reasons;
		}
	}



	/**
	 * Edit URL
	 */
	private function url_edit() {
		$this->link_edit($this->location, 'edit_url');
	}



	/**
	 * Bulk URL edit
	 */
	private function bulk_url() {

		// Initialize
		$error = false;
		$success = false;
		$reasons = array();
		
		// Prepare response array of locations
		$this->response['data']['locations'] = array();
		
		// Update each location
		foreach ($this->locations as $location) {
			
			// Skip unlinked
			if ($location->unlinked)
				continue;
			
			// Remove temp data
			$this->response['data']['marks'] = false;
			$this->response['data']['request_status'] = false;
			$this->response['data']['request_url'] = false;
			
			// Edit anchor
			$this->link_edit($location, 'edit_url');
			
			// Initialize
			$status = 'ok';
			$reason = '';
			
			// Check response error
			if ('error' == $this->response['status']) {
				
				// Mark error
				$error = true;
				
				// Copy values
				$status = 'error';
				$reason = $this->response['reason'];
				
				// Copy reason
				if (!empty($reason) && !in_array($reason, $reasons))
					$reasons[] = $reason;
				
				// Reset status
				$this->response['status'] = 'ok';
				$this->response['reason'] = '';
			
			// Ok
			} else {
				
				// At least one Ok
				$success = true;
			}
			
			// Add location
			$this->response['data']['locations'][] = array(
				'loc_id' 		 => $location->loc_id,
				'status' 		 => $status,
				'reason' 		 => $reason,
				'marks'			 => $this->response['data']['marks'],
				'request_status' => $this->response['data']['request_status'],
				'request_url' 	 => $this->response['data']['request_url'],
			);
		}
		
		// Check Error
		if ($error) {
			$this->response['status'] = $success? 'ok' : 'error';
			$this->response['reason'] = $reasons;
		}
	}



	/**
	 * Edit anchor
	 */
	private function anchor_edit() {
		$this->link_edit($this->location, 'edit_anchor');
	}



	/**
	 * Bulk anchor edit
	 */
	private function bulk_anchor() {

		// Initialize
		$error = false;
		$success = false;
		$reasons = array();
		
		// Prepare response array of locations
		$this->response['data']['locations'] = array();
		
		// Update each location
		foreach ($this->locations as $location) {
			
			// Skip unlinkables
			if (!$this->is_unlinkable($location))
				continue;
			
			// Edit anchor
			$this->link_edit($location, 'edit_anchor');
			
			// Initialize
			$status = 'ok';
			$reason = '';
			
			// Check response error
			if ('error' == $this->response['status']) {
				
				// Mark error
				$error = true;
				
				// Copy values
				$status = 'error';
				$reason = $this->response['reason'];
				
				// Copy reason
				if (!empty($reason) && !in_array($reason, $reasons))
					$reasons[] = $reason;
				
				// Reset status
				$this->response['status'] = 'ok';
				$this->response['reason'] = '';
			
			// Ok
			} else {
				
				// At least one Ok
				$success = true;
			}
			
			// Add location
			$this->response['data']['locations'][] = array(
				'loc_id' => $location->loc_id,
				'status' => $status,
				'reason' => $reason,
			);
		}
		
		// Check Error
		if ($error) {
			$this->response['status'] = $success? 'ok' : 'error';
			$this->response['reason'] = $reasons;
		}
	}



	/**
	 * Recheck status
	 */
	private function url_redir() {
		$this->link_edit_status_redir($this->location);
	}



	/**
	 * Bulk URL status
	 */
	private function bulk_redir() {

		// Initialize
		$error = false;
		$success = false;
		$reasons = array();
		
		// Prepare response array of locations
		$this->response['data']['locations'] = array();
		
		// Update each location
		foreach ($this->locations as $location) {
			
			// Skip unlinked
			if ($location->unlinked)
				continue;
			
			// Remove temp data
			$this->response['data']['marks'] = false;
			$this->response['data']['value'] = null;
			$this->response['data']['request_status'] = false;
			$this->response['data']['request_url'] = false;
			
			// Edit anchor
			$this->link_edit_status_redir($location);
			
			// Initialize
			$status = 'ok';
			$reason = '';
			
			// Check response error
			if ('error' == $this->response['status']) {
				
				// Mark error
				$error = true;
				
				// Copy values
				$status = 'error';
				$reason = $this->response['reason'];
				
				// Copy reason
				if (!empty($reason) && !in_array($reason, $reasons))
					$reasons[] = $reason;
				
				// Reset status
				$this->response['status'] = 'ok';
				$this->response['reason'] = '';
			
			// Ok
			} else {
				
				// At least one Ok
				$success = true;
			}
			
			// Add location
			$this->response['data']['locations'][] = array(
				'loc_id' 		 => $location->loc_id,
				'status' 		 => $status,
				'reason' 		 => $reason,
				'marks'			 => $this->response['data']['marks'],
				'value'			 => $this->response['data']['value'],
				'request_status' => $this->response['data']['request_status'],
				'request_url' 	 => $this->response['data']['request_url'],
			);
		}
		
		// Check Error
		if ($error) {
			$this->response['status'] = $success? 'ok' : 'error';
			$this->response['reason'] = $reasons;
		}
	}



	/**
	 * Add nofollow
	 */
	private function url_nofollow() {
		$this->link_edit($this->location, 'nofollow');
	}



	/**
	 * Bulk Add nofollow
	 */
	private function bulk_nofollow($action = 'nofollow') {
		
		// Initialize
		$error = false;
		$success = false;
		$reasons = array();
		
		// Prepare response array of locations
		$this->response['data']['locations'] = array();
		
		// Update each location
		foreach ($this->locations as $location) {
			
			// Skip unlinkables
			if (!$this->is_unlinkable($location) || 'links' != $location->link_type)
				continue;
			
			// Edit anchor
			$this->link_edit($location, $action);
			
			// Initialize
			$status = 'ok';
			$reason = '';
			
			// Check response error
			if ('error' == $this->response['status']) {
				
				// Mark error
				$error = true;
				
				// Copy values
				$status = 'error';
				$reason = $this->response['reason'];
				
				// Copy reason
				if (!empty($reason) && !in_array($reason, $reasons))
					$reasons[] = $reason;
				
				// Reset status
				$this->response['status'] = 'ok';
				$this->response['reason'] = '';
			
			// Ok
			} else {
				
				// At least one Ok
				$success = true;
			}
			
			// Add location
			$this->response['data']['locations'][] = array(
				'loc_id' => $location->loc_id,
				'status' => $status,
				'reason' => $reason,
			);
		}
		
		// Check Error
		if ($error) {
			$this->response['status'] = $success? 'ok' : 'error';
			$this->response['reason'] = $reasons;
		}
	}



	/**
	 * Remove nofollow
	 */
	private function url_dofollow() {
		$this->link_edit($this->location, 'dofollow');
	}



	/**
	 * Bulk Remove nofollow
	 */
	private function bulk_dofollow() {
		$this->bulk_nofollow('dofollow');
	}



	// Update process methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Common link anchor
	 */
	private function link_edit($location, $action) {
		
		
		/* Input formatting */
		
		// Format URL and output
		if ('edit_url' == $action) {
			
			// Check previous formatting
			if (!isset($this->response['data']['value'])) {
			
				// Clean reserved chars
				$this->value = str_replace(array('"', "'", '<', '>'), '', $this->value);
				
				// Copy to output
				$this->response['data']['value'] = $this->value;
			}
		
		// Format anchor output
		} elseif ('edit_anchor' == $action) {
			
			// Check previous formatting
			if (!isset($this->response['data']['value'])) {
			
				// Clean link open tags
				$this->value = preg_replace('/<a>/isUu', '&lt;a&gt;', $this->value);
				$this->value = preg_replace('/<a(\s+)>/isUu', '&lt;a'.'$1'.'&gt;', $this->value);
				$this->value = preg_replace('/<a(\s+)/isUu', '&lt;a'.'$1', $this->value);
				
				// Clean link close tags
				$this->value = preg_replace('/<\/a>/isUu', '&lt;/a&gt;', $this->value);
				$this->value = preg_replace('/<\/a(\s+)>/isUu', '&lt;/a'.'$1'.'&gt;', $this->value);
				$this->value = preg_replace('/<\/a(\s+)/isUu', '&lt;/a'.'$1', $this->value);
				
				// Copy to output
				$this->response['data']['value'] = esc_html($this->value);
			}
		}
		
		
		/* Validation */
		
		// Check unlinked
		if ($location->unlinked) {
			
			// Link type error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Link already unlinked', 'wplnst');
		
		// Check bad context
		} elseif (!in_array($location->link_type, array('links', 'images'))) {
			
			// Link type error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Link type unknown', 'wplnst');

		// Check value input
		} elseif (false === $this->value && ('edit_anchor' == $action || 'edit_url' == $action)) {
			
			// Input error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Input value missing', 'wplnst');
		
		// Check action exception for images
		} elseif (in_array($action, array('edit_anchor', 'nofollow', 'dofollow')) && 'links' != $location->link_type) {
			
			// Operation not allowed
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Operation not allowed', 'wplnst');
		
		// Retrieve associated object
		} elseif (false === ($object = $this->get_location_object($location))) {
			
			// Object not found
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Object not found', 'wplnst');
			
		// Check bad URL modification
		} elseif ('edit_url' == $action && !$this->parse_url($location, $this->value, $object)) {
			
			// Not valid URL
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Not valid URL', 'wplnst');
			
		// Correct
		} else {
			
			// Check type of link edit
			if (in_array($location->object_field, array('post_content', 'comment_content')) || 0 === strpos($location->object_field, 'custom_field_html_')) {
				
				// Check editable content
				if (false === ($object_content = $this->get_object_content($location, $object))) {
					
					// No content available
					$this->response['status'] = 'error';
					$this->response['reason'] = __('Cannot retrieve element content', 'wplnst');
				
				// Exists	
				} else {
					
					// Edit content
					$this->link_edit_content($location, $action, $object, $object_content);
				}
			
			// Not editable link chunks
			} elseif (in_array($action, array('nofollow', 'dofollow'))) {
				
				// Not valid context
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Not valid context', 'wplnst');
			
			// Inline
			} else {
				
				// No content fields
				$this->link_edit_field($location, $action, $object);
			}
		}
	}



	/**
	 * Edit link in a content environment
	 */
	private function link_edit_content($location, $action, $object, $object_content) {
		
		
		/* Change link URL, anchor, or unlink and leave anchor */
		
		// Check links link type
		if ('links' == $location->link_type) {
			
			// Explode chunk
			if (false === ($matches = $this->deconstruct_link($location->chunk))) {
				
				// Unable to analyze link
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Unable to parse the current link', 'wplnst');
			
			// Check same stored URL
			} elseif ($location->raw_url != $matches[2]) {
				
				// Link URL mismatch
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Saved URL does not match the link URL', 'wplnst');
			
			// Check same stored anchor
			} elseif ($location->anchor != $matches[4]) {
				
				// Link anchor mismatch
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Saved anchor does not match the anchor link', 'wplnst');
			
			// Unlink action
			} elseif ('unlink' == $action) {
				
				// Replace entire chunk with anchor
				$this->link_edit_content_unlink($location, $object, $object_content, $matches[4]);
			
			// Edit URL action
			} elseif ('edit_url' == $action) {
				
				// Replace with new chunk
				$this->link_edit_content_url($location, $object, $object_content, $matches[1].$this->value.$matches[3].$matches[4].$matches[5]);
			
			// Edit anchor action
			} elseif ('edit_anchor' == $action) {
				
				// Need a different anchor
				if ($this->value == $location->anchor) {
					
					// Same anchor value
					$this->response['status'] = 'error';
					$this->response['reason'] = __('No anchor text changes detected.', 'wplnst');
				
				// Continue
				} else {
				
					// Compose new chunk
					$chunk = $matches[1].$matches[2].$matches[3].$this->value.$matches[5];
					if (false === ($content = $this->link_edit_content_replace($location, $object_content, $chunk))) {
						
						// Anchor not replaced
						$this->response['status'] = 'error';
						$this->response['reason'] = __('Cannot update, stored content does not match.', 'wplnst');
						
					// Changed
					} else {
						
						// Save location info
						$this->scans->update_scan_url_location($location->loc_id, array(
							'chunk' 	=> $chunk,
							'anchor' 	=> $this->value,
							'anchored' 	=> 1,
						));
						
						// Save content
						$this->update_object_content($location, $object, $content);
					}
				}
				
			// Add or remove nofollow
			} elseif (in_array($action, array('nofollow', 'dofollow'))) {

				// Decompose chunk
				if (preg_match('/((<a[^>]+href=["|\'])(.+)(["|\'][^>]*>))(.*)(<\/a[^>]*>)/isUu', $location->chunk, $parts)) {
					
					// Add nofollow
					if ('nofollow' == $action) {
						
						// Initialize
						$link = $link_new = $parts[1];
						
						// Add new rel attribute to a
						if (false === stripos($link, 'rel=')) {
							$link_new = preg_replace('/(?=>)/', ' rel="nofollow"', $link_new);
						
						// Existing rel without nofollow
						} elseif (!preg_match('/(rel=["|\'].*)\s*\bnofollow\b\s*(.*["|\'])/iU', $link_new)) {
							$link_new = preg_replace('/(?<=rel=.)/i', 'nofollow ', $link_new);
						}
						
						// Check changes
						if ($link != $link_new) {
							
							// New link
							$chunk = $link_new.$parts[5].$parts[6];
							if (false === ($content = $this->link_edit_content_replace($location, $object_content, $chunk))) {
								
								// Anchor not replaced
								$this->response['status'] = 'error';
								$this->response['reason'] = __('Cannot update, stored content does not match.', 'wplnst');
								
							// Changed
							} else {
								
								// Save location info
								$this->scans->update_scan_url_location($location->loc_id, array(
									'chunk' 	=> $chunk,
									'nofollow' 	=> 1,
								));
								
								// Save content
								$this->update_object_content($location, $object, $content);
							}
						}
					
					// Remove nofollow
					} else {
						
						// Initialize
						$link = $link_new = $parts[1];
						
						// Check existing nofollow
						if (false !== stripos($link, 'nofollow')) {
							
							// Remove only nofollow value
							$link_new = preg_replace('/(rel=["|\'].*)\s*\bnofollow\b\s*(.*["|\'])/iU', '$1$2', $link_new);
							
							// Remove attribute if empty
							$link_new = preg_replace('/(\s*rel=["|\']\s*["|\']\s*)/iU', '', $link_new);	
						}
						
						// Check changes
						if ($link != $link_new) {
							
							// Compose new chunk
							$chunk = $link_new.$parts[5].$parts[6];
							if (false === ($content = $this->link_edit_content_replace($location, $object_content, $chunk))) {
								
								// Anchor not replaced
								$this->response['status'] = 'error';
								$this->response['reason'] = __('Cannot update, stored content does not match.', 'wplnst');
								
							// Changed
							} else {
								
								// Save location info
								$this->scans->update_scan_url_location($location->loc_id, array(
									'chunk' 	=> $chunk,
									'nofollow' 	=> 0,
								));
								
								// Save content
								$this->update_object_content($location, $object, $content);
							}
						}
					}
				}
			}
		
		
		/* Change image URL, or remove image element */
		
		} elseif ('images' == $location->link_type) {
			
			// Explode chunk
			if (false === ($matches = $this->deconstruct_image($location->chunk))) {
				
				// Unable to analyze image
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Unable to parse the current image', 'wplnst');
			
			// Check same URL
			} elseif ($location->raw_url != $matches[2]) {
				
				// Image URL mismatch
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Saved URL does not match the image URL', 'wplnst');

			// Unkink action
			} elseif ('unlink' == $action) {
			
				// Replace entire chunk with anchor
				$this->link_edit_content_unlink($location, $object, $object_content, '');
			
			// Edit URL action
			} elseif ('edit_url' == $action) {
				
				// Replace with new chunk
				$this->link_edit_content_url($location, $object, $object_content, $matches[1].$this->value.$matches[3]);
			}
		}
	}



	/**
	 * Common function for URL replacement
	 */
	private function link_edit_content_url($location, $object, $object_content, $chunk) {

		// Need a different URL
		if ($this->value == $location->raw_url) {
			
			// URL not changed
			$this->response['status'] = 'error';
			$this->response['reason'] = __('No URL changes detected.', 'wplnst');
		
		// Check new different chunk
		} elseif (false === ($content = $this->link_edit_content_replace($location, $object_content, $chunk))) {
			
			// URL not replaced
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Cannot update, stored content does not match.', 'wplnst');
		
		// Ok, check new URL status
		} elseif (!$this->link_edit_status($location, $this->link_edit_data_url(array('chunk' => $chunk, 'modified' => '1')))) {
			
			// Something wrong checking new URL
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Failed to check the new link.', 'wplnst');
			
		// Done
		} else {
			
			// Save content
			$this->update_object_content($location, $object, $content);
			
			// Update marks data
			$this->response['data']['marks'] = $this->scans->get_scan_location_marks($location->scan_id, $location->loc_id);
		}
	}



	/**
	 * Common function for unlink element
	 */
	private function link_edit_content_unlink($location, $object, $object_content, $chunk) {
		
		// Leave only anchor
		if (false === ($content = $this->link_edit_content_replace($location, $object_content, $chunk))) {
			
			// URL not replaced
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Cannot update, stored content does not match.', 'wplnst');
		
		// Changed
		} else {
			
			// Save location info
			$this->scans->update_scan_url_location($location->loc_id, array('unlinked' => 1));
			
			// Save content
			$this->update_object_content($location, $object, $content);
		}
	}



	/**
	 * Replaces content with new chunk
	 */
	private function link_edit_content_replace($location, $object_content, $chunk) {
		
		// Replace in content
		if (!is_array($object_content)) {

			// Find original chunk
			$pos = strpos($object_content, $location->chunk);
			if (false !== $pos) {
				$content = substr_replace($object_content, $chunk, $pos, strlen($location->chunk));
				if ($content == $object_content)
					return false;
				
				// Changed
				return $content;
			}
			
			// Not found
			return false;
		
		// Custom fields
		} else {
			
			// Find original chunk
			$pos = strpos($object_content['content'], $location->chunk);
			if (false !== $pos) {
				$content = substr_replace($object_content['content'], $chunk, $pos, strlen($location->chunk));
				if ($content == $object_content['content'])
					return false;
				
				// Copy new changed content
				$object_content['content'] = $content;
				return $object_content;
			}
			
			// Not found
			return false;
		}
	}



	/**
	 * Editable fields
	 */
	private function link_edit_field($location, $action, $object) {
		
		
		/* Custom fields */
		
		// Custom fields with a full URL
		if (0 === strpos($location->object_field, 'custom_field_url_')) {
			
			// Retrieve content
			if (false === ($content = $this->get_object_content_meta($location, $object, 'custom_field_url_'))) {
				
				// Something wrong checking new URL
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Failed to retrieve post meta data.', 'wplnst');
				
			// Edit meta URL
			} elseif ('edit_url' == $action) {
				
				// Need a different URL
				if ($this->value == $location->raw_url) {
					
					// Same URL
					$this->response['status'] = 'error';
					$this->response['reason'] = __('No URL changes detected.', 'wplnst');
				
				// Continue
				} else {
					
					// Check URL status
					if (!$this->link_edit_status($location, $this->link_edit_data_url(array('modified' => 1)))) {
						
						// Something wrong checking new URL
						$this->response['status'] = 'error';
						$this->response['reason'] = __('Failed to check the new link.', 'wplnst');
					
					// Continue
					} else {
						
						// Update meta
						$this->scans->update_scan_post_meta($object->ID, $content['meta_id'], $this->urlinfo['url']);
						
						// Update marks data
						$this->response['data']['marks'] = $this->scans->get_scan_location_marks($location->scan_id, $location->loc_id);
					}
				}
			}
			
		
		/* Comment author */
		
		// Comment author URL
		} elseif ('comment_author_url' == $location->object_field) {
			
			// Edit comment URL
			if ('edit_url' == $action) {
				
				// Need a different URL
				if ($this->value == $location->raw_url) {
					
					// URL not changed
					$this->response['status'] = 'error';
					$this->response['reason'] = __('No URL changes detected.', 'wplnst');
				
				// Continue
				} else {
					
					// Check URL status
					if (!$this->link_edit_status($location, $this->link_edit_data_url(array('modified' => 1)))) {
						
						// Something wrong checking new URL
						$this->response['status'] = 'error';
						$this->response['reason'] = __('Failed to check the new link.', 'wplnst');
					
					// Continue
					} else {
						
						// Update comment author URL
						$this->scans->update_scan_comment($object->comment_ID, array('comment_author_url' => $this->urlinfo['url']));
						
						// Update marks data
						$this->response['data']['marks'] = $this->scans->get_scan_location_marks($location->scan_id, $location->loc_id);
					}
				}
			
			// Edit comment anchor
			} elseif ('edit_anchor' == $action) {
				
				// Need a different anchor
				if ($this->value == $location->anchor) {
					
					// Same anchor value
					$this->response['status'] = 'error';
					$this->response['reason'] = __('No anchor text changes detected.', 'wplnst');
				
				// Continue
				} else {
					
					// Update location
					$this->scans->update_scan_url_location($location->loc_id, array('anchor' => $this->value, 'anchored' => 1));
					
					// Update comment author
					$this->scans->update_scan_comment($object->comment_ID, array('comment_author' => $this->value));
				}

			// Remove comment link
			} elseif ('unlink' == $action) {
				
				// Update location
				$this->scans->update_scan_url_location($location->loc_id, array('unlinked' => 1));
				
				// Update comment author URL
				$this->scans->update_scan_comment($object->comment_ID, array('comment_author_url' => ''));
			}
		
		
		/* Bookmark */
		
		// Edit bookmark URL
		} elseif ('link_url' == $location->object_field) {
			
			// Edit bookmark URL
			if ('edit_url' == $action) {
				
				// Need a different URL
				if ($this->value == $location->raw_url) {
					
					// URL not changed
					$this->response['status'] = 'error';
					$this->response['reason'] = __('No URL changes detected.', 'wplnst');
				
				// Continue
				} else {
					
					// Check URL status
					if (!$this->link_edit_status($location, $this->link_edit_data_url(array('modified' => 1)))) {
						
						// Something wrong checking new URL
						$this->response['status'] = 'error';
						$this->response['reason'] = __('Failed to check the new link.', 'wplnst');
					
					// Continue
					} else {
						
						// Update comment author URL
						$this->scans->update_scan_bookmark($object->link_id, array('link_url' => $this->urlinfo['url']));
						
						// Update marks data
						$this->response['data']['marks'] = $this->scans->get_scan_location_marks($location->scan_id, $location->loc_id);
					}
				}
			
			// Edit bookmark anchor
			} elseif ('edit_anchor' == $action) {
				
				// Need a different anchor
				if ($this->value == $location->anchor) {
					
					// Same anchor value
					$this->response['status'] = 'error';
					$this->response['reason'] = __('No anchor text changes detected.', 'wplnst');
				
				// Continue
				} else {

					// Update location info
					$this->scans->update_scan_url_location($location->loc_id, array('anchor' => $this->value, 'anchored' => 1));
					
					// Update comment author
					$this->scans->update_scan_bookmark($object->link_id, array('link_name' => $this->value));
				}
			}
		}
	}



	/**
	 * Set URL data source before to check status
	 */
	private function link_edit_status($location, $location_data) {
		
		// Initialize
		static $statuses = array();
		
		// Check if exists URL
		if (false === ($url = $this->scans->get_scan_url(array('url' => $this->urlinfo['url'], 'no_cache' => true)))) {
			
			// New scan URL
			if (false === ($url_id = $this->scans->add_scan_url($this->urlinfo, $this->scan->id)))
				return false;
			
			// Check same URL reference
			if ($url_id == $location->url_id)
				return false;
			
			// Retrieve complete url object
			if (false === ($url = $this->scans->get_scan_url(array('id' => $url_id))))
				return false;
		}
		
		// Check if there is a previous status
		if (!empty($statuses[$url->url_id])) {
			
			// Check current location data
			$location_update = array('url_id' => $url->url_id);
			if (false !== $location_data && is_array($location_data))
				$location_update = array_merge($location_update, $location_data);
			
			// Update new URL
			$this->scans->update_scan_url_location($location->loc_id, $location_update);
			
			// Copy data for others
			$this->response['data']['request_status'] = $statuses[$url->url_id];
			$this->response['data']['request_url'] = $url->url.(isset($this->urlinfo)? $this->urlinfo['fragment'] : '');
			
			// Done
			return true;
		
		// Perform a request and update
		} elseif (false !== ($status = $this->link_edit_status_request($url))) {
			
			// Save status for this URL
			$statuses[$url->url_id] = $status;
			
			// Check current location data
			$location_update = array('url_id' => $url->url_id);
			if (false !== $location_data && is_array($location_data))
				$location_update = array_merge($location_update, $location_data);
			
			// Update new URL
			$this->scans->update_scan_url_location($location->loc_id, $location_update);
			
			// Update scan data
			$this->link_edit_status_update($url, $status);
			
			// Done
			return true;
		}
		
		// Error
		return false;
	}



	/**
	 * Prepare current location for a recheck
	 */
	private function link_edit_status_recheck($location) {
		
		// Initialize
		static $statuses = array();
		
		// Check unlinked
		if ($location->unlinked) {
			
			// Link type error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Link already unlinked', 'wplnst');
		
		// Retrieve complete url object
		} elseif (false === ($url = $this->scans->get_scan_url(array('id' => $location->url_id)))) {
			
			// Retriee error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Given URL missing.', 'wplnst');
		
		// Check if there is a previous status
		} elseif (!empty($statuses[$url->url_id])) {
			
			// Copy data for locations
			$this->response['data']['request_status'] = $statuses[$url->url_id];
			$this->response['data']['request_url'] = $url->url;
			
			// Update marks data
			$this->response['data']['marks'] = $this->scans->get_scan_location_marks($location->scan_id, $location->loc_id);
		
		// Perform and check request result
		} elseif (false !== ($status = $this->link_edit_status_request($url))) {
			
			// Save status for this URL
			$statuses[$url->url_id] = $status;
			
			// Update scan data
			$this->link_edit_status_update($url, $status, true);
			
			// Update marks data
			$this->response['data']['marks'] = $this->scans->get_scan_location_marks($location->scan_id, $location->loc_id);
		}
	}



	/**
	 * Prepare current location for a redir
	 */
	private function link_edit_status_redir($location) {
		
		// Retrieve complete url object
		if (false === ($url = $this->scans->get_scan_url(array('id' => $location->url_id)))) {
			
			// Retrieve error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('Given URL missing.', 'wplnst');
		
		// Check status info
		} elseif (false === ($url_status = $this->scans->get_scan_url_status(array('url_id' => $url->url_id, 'scan_id' => $this->scan->id)))) {

			// Retrieve error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('No URL status info available.', 'wplnst');
		
		// Check valid redirect
		} elseif ('3' != $url_status[0]->status_level || empty($url_status[0]->redirect_url) || empty($url_status[0]->redirect_url_id)) {

			// Retrieve error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('No allowed redirections.', 'wplnst');
		
		// Retrieve URL redirection
		} elseif (false === ($url_redir = $this->scans->get_scan_url(array('id' => $url_status[0]->redirect_url_id)))) {

			// Destination URL error
			$this->response['status'] = 'error';
			$this->response['reason'] = __('No redirection found.', 'wplnst');
			
		// Done
		} else {
			
			// Extract possible fragment
			$parts = @parse_url($url_status[0]->redirect_url);
			$fragment = isset($parts['fragment'])? '#'.$parts['fragment'] : '';
			
			// Set input value
			$this->value = $url_redir->url.$fragment;
			
			// Edit URL
			$this->link_edit($location, 'edit_url');
		}
	}



	/**
	 * Update status in database
	 */
	private function link_edit_status_update($url, $status, $rechecked = false) {
		
		
		/* Update location */
		
		// Remove status if exists
		$this->scans->remove_scan_url_status($url->url_id, $this->scan->id);
		
		// Recreate scan status
		$this->scans->add_scan_url_status($url->url_id, $this->scan->id, 'end');
		
		// Prepare update
		$update = array(
			'request_at'			=> $status->request_at,
			'status_level' 			=> $status->level,
			'status_code' 			=> $status->code,
			'redirect_url'			=> $status->redirect_url,
			'redirect_url_id' 		=> $status->redirect_url_id,
			'redirect_url_status' 	=> $status->redirect_url_status,
			'redirect_curl_errno'	=> $status->redirect_curl_errno,
			'redirect_steps'		=> empty($status->redirect_steps)? '' : @json_encode($status->redirect_steps),
			'headers' 				=> empty($status->headers)? '' : @json_encode($status->headers),
			'headers_request' 		=> empty($status->headers_request)? '' : @json_encode($status->headers_request),
			'total_time'			=> $status->total_time,
			'total_bytes'			=> $status->total_bytes,
			'curl_errno'			=> $status->curl_errno,
			'requests'				=> (empty($status->redirect_steps) || !is_array($status->redirect_steps))? 1 : (count($status->redirect_steps) + 1),
		);
		
		// Rechecked
		if ($rechecked)
			$update['rechecked'] = 1;
		
		// Update status data
		$this->scans->update_scan_url_status($url->url_id, $this->scan->id, $update);
		
		// Update URL data
		$this->scans->update_scan_url($url->url_id, array(
			'last_scan_id' 			=> $this->scan->id,
			'last_status_level' 	=> $status->level,
			'last_status_code'  	=> $status->code,
			'last_curl_errno'		=> $status->curl_errno,
			'last_request_at' 		=> $status->request_at,
		));
		
		
		/* Update totals */
		
		$this->link_edit_status_total();
		
		
		/* Prepare presentation */
		
		// Localize time after save it
		$status->total_time = number_format_i18n($status->total_time, 3);
		
		// Localize total size
		if ($status->total_bytes > 0) {
			wplnst_require('core', 'util-math');
			$status->total_size = wplnst_format_bytes($status->total_bytes);
		}
		
		// Response data
		$this->response['data']['request_status'] = $status;
		$this->response['data']['request_url'] = $url->url.(isset($this->urlinfo)? $this->urlinfo['fragment'] : '');
	}



	/**
	 * Update total/summary data
	 */
	private function link_edit_status_total() {
		
		// URLs status codes summary
		$this->scans->set_scan_summary_status_codes($this->scan->id, $this->scan->status_levels, $this->scan->status_codes, true);
		
		// Update posts matched
		if ($this->scan->check_posts)
			$this->scans->set_scan_summary_objects_match($this->scan->id, 'posts', true);
		
		// Update posts matched
		if ($this->scan->check_comments)
			$this->scans->set_scan_summary_objects_match($this->scan->id, 'comments', true);
		
		// Update blogroll matched
		if ($this->scan->check_blogroll)
			$this->scans->set_scan_summary_objects_match($this->scan->id, 'blogroll', true);
	}



	/**
	 * Perform a request to an URL
	 */
	private function link_edit_status_request($url, $fragment = '', $child = false) {
		
		// Initialize
		static $steps;
		if (!isset($steps)) {
			
			// First attempt
			$steps = array();
			
			// No timeout
			set_time_limit(0);
			
			// Dependencies
			wplnst_require('core',  'status');
			wplnst_require('core',  'curl');
		}
		
		// Prepare POST fields
		$postfields = array(
			'url' 				=> $url->url,
			'hash'				=> $url->hash,
			'url_id'			=> $url->url_id,
			'connect_timeout' 	=> wplnst_get_nsetting('connect_timeout', $this->scan->threads->connect_timeout),
			'request_timeout' 	=> wplnst_get_nsetting('request_timeout', $this->scan->threads->request_timeout),
			'max_download'		=> wplnst_get_nsetting('max_download') * 1024,
			'debug'				=> wplnst_is_debug()? '1' : '0',
			'user_agent'		=> wplnst_get_tsetting('user_agent'),
			'nonce' 			=> WPLNST_Core_Nonce::create_nonce($url->hash),
		);
		
		// Request crawler API
		$response = WPLNST_Core_CURL::post(array(
			'CURLOPT_URL' 				=> plugins_url('core/requests/http.php', WPLNST_FILE),
			'CURLOPT_CONNECTTIMEOUT' 	=> $postfields['connect_timeout'],
			'CURLOPT_TIMEOUT' 			=> $postfields['connect_timeout'] + (2 * $postfields['request_timeout']),
			'CURLOPT_USERAGENT' 		=> wplnst_get_tsetting('user_agent'),
		), $postfields);
		
		// Check array
		if (empty($response) || !is_array($response)) {
			
			// No redirection mode
			if (!$child) {
			}
		
		// Check wrapper error
		} elseif ($response['error']) {
			
			// No redirection mode
			if (!$child) {
			}
		
		// Check body
		} elseif (empty($response['data'])) {
			
			// No redirection mode
			if (!$child) {
			}
		
		// JSON body
		} else {
			
			// Decode JSON
			$body = @json_decode($response['data']);
			
			// Check value
			if (empty($body) || !is_object($body)) {
				
				// No redirection mode
				if (!$child) {
				}
				
			// Check status
			} elseif (empty($body->status) || 'ok' != $body->status) {
				
				// No redirection mode
				if (!$child) {
				}
				
			// Check data
			} elseif (empty($body->data) || !is_object($body->data)) {
				
				// No redirection mode
				if (!$child) {
				}
			
			// Done	
			} else {
				
				// Return status from data
				$status = new WPLNST_Core_Status((array) $body->data);
				
				// Check redirection
				if ($this->scan->redir_status && 3 == $status->level && !empty($status->redirect_url) && (count($steps) + 1) <= wplnst_get_nsetting('max_redirs')) {
					
					// Parse redirection
					$this->load_url_object();
					$urlinfo = $this->urlo->parse($status->redirect_url, $url->url);
					if ($this->urlo->is_crawleable($urlinfo)) {
						
						// Add URL if not exists
						if (false === ($url_redir = $this->scans->get_scan_url(array('url' => $urlinfo['url'], 'no_cache' => true)))) {
							$new_url_id = $this->scans->add_scan_url($urlinfo, $this->scan->id);
							$url_redir = $this->scans->get_scan_url(array('id' => $new_url_id));
						}
						
						// Check existing or added
						if (!empty($url_redir)) {
							
							// Add this URL step
							$steps[] = array('url' => $url->url, 'status' => $status->code);
							
							// Find next status
							if (false !== ($status_redirect = $this->link_edit_status_request($url_redir, $urlinfo['fragment'], true))) {
								
								// From the original
								if (!$child) {
									$status->redirect_url = $status_redirect->redirect_url;
									$status->redirect_url_id = $status_redirect->redirect_url_id;
									$status->redirect_url_level = $status_redirect->redirect_url_level;
									$status->redirect_url_status = $status_redirect->redirect_url_status;
									$status->redirect_curl_errno = $status_redirect->redirect_curl_errno;
									$status->redirect_steps = $steps;
								
								// Under redirection
								} else {
									
									// Send to original
									return $status_redirect;
								}
							}
						}
					}
				
				// No new redirection but and under redirection (yep)
				} elseif ($child) {
					
					// Copy values to the previous
					$status->redirect_url = $url->url.$fragment;
					$status->redirect_url_id = $url->url_id;
					$status->redirect_url_level = $status->level;
					$status->redirect_url_status = $status->code;
					$status->redirect_curl_errno = $status->curl_errno;
				}				
				
				// Final response, prepare data
				if (!$child) {
					
					// Set status description
					if ($status->code > 0) {
						
						// Retrieve status codes
						$status_codes = WPLNST_Core_Types::get_status_codes_raw();
						
						// Check status desc
						if (isset($status_codes[$status->code]))
							$status->code_desc = ' '.$status_codes[$status->code];
						
						// Check redirect status
						if (!empty($status->redirect_url_id)) {
							
							// Check defined status
							if ($status->redirect_url_status > 0) {
								if (isset($status_codes[$status->redirect_url_status]))
									$status->redirect_url_status_desc = ' '.$status_codes[$status->redirect_url_status];
								
							// Check cURL error
							} elseif ($status->redirect_curl_errno > 0) {
								
								// Load dependencies
								wplnst_require('core', 'types-curl');
								
								// Retrieve error type
								$curl_error = WPLNST_Core_Types_CURL::get_code_info($status->redirect_curl_errno);
								$status->redirect_curl_err_title = empty($curl_error)? __('Error code ', 'wplnst').$status->redirect_curl_errno : $curl_error['title'];
								$status->redirect_curl_err_desc  = empty($curl_error)? '' : $curl_error['desc'];
							}
						}
					
					// Set error description
					} elseif ($status->curl_errno > 0) {
						
						// Load dependencies
						wplnst_require('core', 'types-curl');
						
						// Retrieve error type
						$curl_error = WPLNST_Core_Types_CURL::get_code_info($status->curl_errno);
						$status->curl_err_title = empty($curl_error)? __('Error code ', 'wplnst').$status->curl_errno : $curl_error['title'];
						$status->curl_err_desc  = empty($curl_error)? '' : $curl_error['desc'];
					}
				}
				
				// End
				return $status;
			}
		}
		
		// Error
		return false;
	}



	// URL utilities
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Parse URL wrapper
	 */
	private function parse_url($location, $url, $object) {
		
		// Not allow empty URLs
		if ('' === ''.trim($url))
			return false;
		
		// Check relative URL		
		if ('posts' == $location->object_type) {
			
			// Entire object
			$post = $object;
		
		// Comment object
		} elseif ('comments' == $location->object_type) {
			
			// Identifier of parent post
			$post = (int) $object->comment_post_ID;
		}
		
		// Set parent for relatives
		$parent_url = isset($post)? get_permalink($post) : false;
		
		// Parse URL
		$this->load_url_object();
		$this->urlinfo = $this->urlo->parse($this->value, $parent_url);
		
		// Check valid query
		return $this->urlo->is_crawleable($this->urlinfo);
	}



	/**
	 * Set URL args for update location
	 */
	private function link_edit_data_url($update) {
		return array_merge(array(
			'raw_url'	=> $this->value,
			'fragment'	=> $this->urlinfo['fragment'],
			'spaced' 	=> $this->urlinfo['spaced']? 	1 : 0,
			'malformed' => $this->urlinfo['malformed']? 1 : 0,
			'absolute'	=> $this->urlinfo['absolute']? 	1 : 0,
			'protorel'	=> $this->urlinfo['protorel']? 	1 : 0,
			'relative'	=> $this->urlinfo['relative']? 	1 : 0,
		), $update);
	}



	// Retrieve WP objects
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Try to retrieve the location associated post
	 */
	private function get_location_object($location, $editable = true) {
		
		// Prepare identifier
		$object_id = (int) $location->object_id;
		
		// Retrive posts
		if ('posts' == $location->object_type) {
		
			// Retrieve post
			$post = get_post($object_id);
			if (empty($post) || !is_object($post) || 'WP_Post' != get_class($post)) {
				
				// Object not found
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Post not found', 'wplnst');
				
				// Error
				return false;
			
			// Check editable
			} elseif ($editable && !current_user_can('edit_post', $post->ID)) {
				
				// Object not editable
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Current user can`t edit this post', 'wplnst');
				
				// Error
				return false;
			}
			
			// Done
			return $post;
		
		// Retrieve comments
		} elseif ('comments' == $location->object_type) {
		
			// Retrieve comment
			$comment = get_comment($object_id);
			
			// Check comment object
			if (empty($comment) || !is_object($comment)) {
				
				// Object not found
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Comment not found', 'wplnst');
				
				// Error
				return false;
			
			// Check editable
			} elseif ($editable && !current_user_can('edit_comment', $comment->comment_ID)) {
				
				// Object not editable
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Current user can`t edit this comment', 'wplnst');
				
				// Error
				return false;
			}
			
			// Done
			return $comment;
		
		// Retrieve blogroll
		} elseif ('blogroll' == $location->object_type) {
			
			// Retrieve bookmark
			$bookmark = get_bookmark($object_id);
			
			// Check bookmark object
			if (empty($bookmark) || !is_object($bookmark)) {
				
				// Object not found
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Bookmark record not found', 'wplnst');
				
				// Error
				return false;
				
			// Check editable
			} elseif($editable && !current_user_can('manage_links', $bookmark->link_id)) {
				
				// Object not editable
				$this->response['status'] = 'error';
				$this->response['reason'] = __('Current user can`t edit this bookmark', 'wplnst');
				
				// Error
				return false;
			}
			
			// Done
			return $bookmark;
		}
	}



	/**
	 * Retrieve content value associated with the object
	 */
	private function get_object_content($location, $object) {
		
		// Direct object field
		if (in_array($location->object_field, array('post_content', 'comment_content')))
			return $object->{$location->object_field};
		
		// Post custom field
		if (0 === strpos($location->object_field, 'custom_field_html_')) {
			if (false !== ($content = $this->get_object_content_meta($location, $object, 'custom_field_html_')))
				return $content;
		}
		
		// Not found
		return false;
	}



	/**
	 * Retrieve meta values from object
	 */
	private function get_object_content_meta($location, $object, $prefix) {
		
		// Initialize
		$meta_id = $meta_key = false;

		// Remove prefix and extract identifier
		$name = str_replace($prefix, '', $location->object_field);
		if (($pos = strpos($name, '_')) > 0) {
			$meta_id = (int) mb_substr($name, 0, $pos);
			$meta_key = mb_substr($name, $pos + 1);
		}
		
		// Check results
		if ($meta_id > 0 && !empty($meta_key)) {
		
			// Retrieve custom fields
			$post_metas = $this->scans->get_post_metas($object->ID);
			if (!empty($post_metas) && is_array($post_metas)) {
				
				// Check existing key
				if (isset($post_metas[$meta_key]) && is_array($post_metas[$meta_key])) {
					
					// Check existing meta id
					if (is_array($post_metas[$meta_key]) && isset($post_metas[$meta_key][$meta_id])) {
						
						// Meta info
						return array(
							'meta_id' => $meta_id,
							'content' => $post_metas[$meta_key][$meta_id],
						);
					}
				}
			}
		}
		
		// Error
		return false;
	}



	/**
	 * Update object content
	 */
	private function update_object_content($location, $object, $content) {
		
		// Update post content
		if ('post_content' == $location->object_field) {
			$this->scans->update_scan_post($object->ID, array('post_content' => $content));
		
		// Update comment content
		} elseif ('comment_content' == $location->object_field) {
			$this->scans->update_scan_comment($object->comment_ID, array('comment_content' => $content));
		
		// Update custom fields
		} elseif (0 === strpos($location->object_field, 'custom_field_html_') && !empty($content) && is_array($content)) {
			
			// Update meta
			$this->scans->update_scan_post_meta($object->ID, $content['meta_id'], $content['content']);
		}
	}



	// String data extraction
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check if a location can be unlinked
	 */
	private function is_unlinkable($location) {
		return !($location->unlinked || 'blogroll' == $location->object_type || ('comments' == $location->object_type && 'comment_author_url' == $location->object_field) || ('posts' == $location->object_type && 0 === strpos($location->object_field, 'custom_field_url_')));
	}



	/**
	 * Return pregmatched link
	 */	
	private function deconstruct_link($chunk) {
		$result = preg_match('/(<a[^>]+href=["|\'])(.+)(["|\'][^>]*>)(.*)(<\/a>)/isUu', $chunk, $matches);
		return (1 == $result && !empty($matches) && is_array($matches) && 6 == count($matches))? $matches : false;
	}



	/**
	 * Return pregmatched image
	 */	
	private function deconstruct_image($chunk) {
		$result = preg_match('/(<img[^>]+src=["|\'])(.+)(["|\'][^>]*>)/isUu', $chunk, $matches);
		return (1 == $result && !empty($matches) && is_array($matches) && 4 == count($matches))? $matches : false;
	}



}