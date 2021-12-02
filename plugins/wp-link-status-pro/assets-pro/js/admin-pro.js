;jQuery(document).ready(function($) {



	var busy = {}, nonce = $('#wplnst-results').attr('data-nonce'), nonce_advanced_display = $('#wplnst-results').attr('data-nonce-advanced-display');



	$(document).on('click', '.wplnst-results-action', function(e) {
		
		var action = $(this).attr('data-action');
		var loc_id = $(this).attr('data-loc-id');
		if ('undefined' == typeof action || '' == action || 'undefined' == typeof loc_id || '' == loc_id)
			return false;
		
		var key = action + '_' + loc_id;
		if (key in busy && false !== busy[key])
			return false;
		
		busy[key] = {}
		busy[key]['div-actions'] = $(this).closest('.row-actions');
		busy[key]['action'] = action;
		busy[key]['loc_id'] = loc_id;
		busy[key]['desc'] = $(this).text();
		
		$('.wplnst-row-actions-' + loc_id).hide();
		results_bulkactions_disable();
		
		var output = $($('#wplnst-results-output').html());
		output.addClass('wplnst-results-action-' + key);
		output.attr('data-action-key', key);
		output.insertAfter(busy[key]['div-actions']);
		
		if ('url_edit' == action) {
			output.html($('#wplnst-results-edit-url').html().replace('%s', $('#wplnst-results-url-loc-' + loc_id).text().esc_html()));
			output.find('.wplnst-results-update-box-edit input').eq(0).focus();
			
		} else if ('url_unlink' == action) {
			output.html($('#wplnst-results-unlink-confirm').html());
			
		} else if ('url_ignore' == action) {
			output.html($('#wplnst-results-ignore-confirm').html());
			
		} else if ('url_unignore' == action) {
			output.html($('#wplnst-results-unignore-confirm').html());
			
		} else if ('url_redir' == action) {
			output.html($('#wplnst-results-redir-confirm').html());
			
		} else if ('url_nofollow' == action) {
			output.html($('#wplnst-results-nofollow-confirm').html());
			
		} else if ('url_dofollow' == action) {
			output.html($('#wplnst-results-dofollow-confirm').html());
			
		} else if ('url_status' == action) {
			submit(key, action, loc_id, '');
			return false;
			
		} else if ('url_headers' == action) {
			submit(key, action, loc_id, '');
			return false;
			
		} else if ('anchor_edit' == action) {
			output.html($('#wplnst-results-edit-anchor').html().replace('%s', $('#wplnst-results-anchor-loc-' + loc_id).text().esc_html()));
			output.find('.wplnst-results-update-box-edit input').eq(0).focus();
		}
		
		output.show();
		return false;
	});



	$(document).on('click', '.wplnst-results-output-update', function(e) {
		
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length) {
			
			var key = output.attr('data-action-key');
			if ('undefined' != typeof key && key.length) {
				
				if ('bulk_url' == key || 'bulk_anchor' == key) {
					submit_bulk(output);
				
				} else if (key in busy && false !== busy[key]) {
					
					var value = '';
					if ('url_edit' == busy[key]['action'] || 'anchor_edit' == busy[key]['action']) {
						
						var value = false;
						var field = output.find('.wplnst-results-update-box-edit input').eq(0);
						if ('undefined' != typeof field)
							value = $(field).val();
						
						if ('undefined' == typeof value || false === value) {
							alert('Entered value error');
							return;
						}
						
						busy[key]['value'] = value;
					}
					
					submit(key, busy[key]['action'], busy[key]['loc_id'], value);
				}
			}
		}
		
		return false;
	});



	$(document).on('keydown', '.wplnst-results-update-box-edit input', function(e) {
		if (e.keyCode == 13) {
			$(this).closest('.wplnst-results-output-container').find('.wplnst-results-output-update').eq(0).click();
			return false;
		} else if (e.keyCode == 27) {
			$(this).closest('.wplnst-results-output-container').find('.wplnst-results-output-cancel').eq(0).click();
			return false;
		}
	});
	
	$(document).on('keydown', '.wplnst-results-bulkactions-area .wplnst-results-update-box-edit input', function(e) {
		if (e.keyCode == 27) {
			$(this).closest('.wplnst-results-output-container').find('.wplnst-results-output-cancel-bulk').eq(0).click();
			return false;
		}
	});



	$(document).on('click', '.wplnst-results-output-confirm', function(e) {
		
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length) {
			
			var key = output.attr('data-action-key');
			if ('undefined' != typeof key && key.length) {
				
				if (key in busy && false !== busy[key])
					submit(key, busy[key]['action'], busy[key]['loc_id'], '');
			}
		}
		
		return false;
	});



	$(document).on('click', '.wplnst-results-output-cancel', function(e) {
		
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length) {
			
			var key = output.attr('data-action-key');
			if ('undefined' != typeof key && key.length) {
				output.remove();
				results_bulkactions_enable();
				rollback_actions(key);
			}
		}
		
		return false;
	});



	function submit(key, action, loc_id, value) {
		
		var output = $('.wplnst-results-action-' + key);
		output.html($('#wplnst-results-processing').html().replace('%s', busy[key]['desc'])).show();
		
		$.post(ajaxurl, { 'action' : 'wplnst_results_update', 'op' : action, 'nonce' : nonce, 'loc_id' : loc_id, 'value' : value }, function(e) {
			
			if ('undefined' == typeof e.status) {
				alert( data_label('unknown-error') );
				output.remove();
				results_bulkactions_enable();
				rollback_actions(key);
			
			} else if ('ok' == e.status) {
				
				if ('url_edit' == action) {
					$('#wplnst-results-url-loc-' + loc_id).attr('href', e.data.request_url);
					$('#wplnst-results-url-loc-' + loc_id).attr('title', e.data.request_url);
					$('#wplnst-results-url-loc-' + loc_id).html(e.data.value);
					$('#wplnst-results-url-marks-' + loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-modified').eq(0).removeClass('wplnst-display-none');
					update_request_marks(loc_id, e.data.marks);
					update_request_status(loc_id, e.data.request_status);
				
				} else if ('anchor_edit' == action) {
					$('#wplnst-results-anchor-mod-'+ loc_id).removeClass('wplnst-display-none');
					$('#wplnst-results-anchor-loc-' + loc_id).html(e.data.value);
				
				} else if ('url_status' == action) {
					$('#wplnst-url-status-recheck-mark-' + loc_id).removeClass('wplnst-display-none');
					update_request_marks(loc_id, e.data.marks);
					update_request_status(loc_id, e.data.request_status);
					
				} else if ('url_redir' == action) {
					$('#wplnst-results-url-loc-' + loc_id).attr('href', e.data.request_url);
					$('#wplnst-results-url-loc-' + loc_id).attr('title', e.data.request_url);
					$('#wplnst-results-url-loc-' + loc_id).html(e.data.value);
					update_request_status(loc_id, e.data.request_status);
					$('#wplnst-results-url-marks-' + loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-modified').eq(0).removeClass('wplnst-display-none');
					update_request_marks(loc_id, e.data.marks);
					
				} else if ('url_unignore' == action) {
					$('#wplnst-action-url-unignore-' + loc_id).hide();
					$('#wplnst-action-url-ignore-' + loc_id).removeClass('wplnst-display-none');
					$('#wplnst-results-url-marks-' + loc_id).find('.wplnst-results-mark-ignored').eq(0).addClass('wplnst-display-none');
					
				} else if ('url_nofollow' == action) {
					$('#wplnst-action-url-nofollow-' + loc_id).addClass('wplnst-display-none');
					$('#wplnst-action-url-dofollow-' + loc_id).removeClass('wplnst-display-none');
					$('#wplnst-results-url-marks-' + loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-nofollow').eq(0).removeClass('wplnst-display-none');
					
				} else if ('url_dofollow' == action) {
					$('#wplnst-action-url-nofollow-' + loc_id).removeClass('wplnst-display-none');
					$('#wplnst-action-url-dofollow-' + loc_id).addClass('wplnst-display-none');
					$('#wplnst-results-url-marks-' + loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-nofollow').eq(0).addClass('wplnst-display-none');
				}
				
				if ('url_ignore' == action) {
					busy[key]['div-actions'].closest('tr').fadeOut(600, function() {
						$(this).remove();
						results_bulkactions_enable();
						rollback_actions(key);
					});
				
				} else if ('url_headers' == action) {
					headers_window(e.data);
					results_bulkactions_enable();
					rollback_actions(key);
					output.remove();
					
				} else {
					output.find('.wplnst-results-processing-action').removeClass('wplnst-results-processing-action-run').addClass('wplnst-results-processing-action-ok').delay(1000).fadeOut(600, function() {
						if ('url_unlink' == action) {
							busy[key]['div-actions'].closest('tr').find('.wplnst-row-actions-' + loc_id).remove();
							$('#wplnst-results-url-unlinked-' + loc_id).show();
						} else {
							rollback_actions(key);
						}
						results_bulkactions_enable();
						output.remove();
					});
				}
				
			} else if ('error' == e.status) {
				results_bulkactions_enable();
				output.html($('#wplnst-results-error').html().replace('%s', e.reason));
			}
		
		}).fail(function() {
			alert( data_label('server-comm-error') );
			output.remove();
			results_bulkactions_enable();
			rollback_actions(key);
		});
	}



	function update_request_status(loc_id, status) {
		
		if ('undefined' == typeof status || !status)
			return;
		
		$('#wplnst-url-status-code-loc-' + loc_id).hide();
		$('#wplnst-url-status-code-0-loc-' + loc_id).hide();
		$('#wplnst-url-status-code-redir-' + loc_id).hide();
		$('#wplnst-url-status-code-redir-error-' + loc_id).hide();
		$('#wplnst-url-status-code-redir-status-' + loc_id).hide();
		
		$('#wplnst-results-url-error-' + loc_id).hide();
		$('#wplnst-results-url-error-redir-' + loc_id).hide();
		$('#wplnst-results-url-redir-' + loc_id).hide();
		
		if (status.code > 0) {
			
			$('#wplnst-url-status-code-loc-' + loc_id).text(status.code + status.code_desc);
			$('#wplnst-url-status-code-loc-' + loc_id).attr('class', 'wplnst-url-status-code-' + status.level);
			$('#wplnst-url-status-code-loc-' + loc_id).show();
			
			if (status.redirect_url_id > 0) {
				
				$('#wplnst-results-url-redir-href-' + loc_id).attr('href', status.redirect_url);
				$('#wplnst-results-url-redir-href-' + loc_id).html(status.redirect_url.esc_html());
				$('#wplnst-results-url-redir-' + loc_id).show();
				
				if (status.redirect_url_status > 0) {
					
					$('#wplnst-url-status-code-redir-status-' + loc_id).text(status.redirect_url_status + status.redirect_url_status_desc);
					$('#wplnst-url-status-code-redir-status-' + loc_id).attr('class', 'wplnst-url-status-code-' + status.redirect_url_level);
					$('#wplnst-url-status-code-redir-status-' + loc_id).show();
					$('#wplnst-url-status-code-redir-' + loc_id).show();
					
				} else if (status.redirect_curl_errno > 0) {
					
					$('#wplnst-results-url-error-redir-title-' + loc_id).text(status.redirect_curl_err_title);
					$('#wplnst-results-url-error-redir-code-'  + loc_id).text(data_label('error-code') + ' ' + status.redirect_curl_errno);
					$('#wplnst-results-url-error-redir-desc-' + loc_id).text(status.redirect_curl_err_desc);
					$('#wplnst-results-url-error-redir-' + loc_id).show();
					
					$('#wplnst-url-status-code-redir-error-' + loc_id).show();
					$('#wplnst-url-status-code-redir-' + loc_id).show();
				}
			}
			
		} else if (status.curl_errno > 0) {
			
			$('#wplnst-url-status-code-0-loc-' + loc_id).show();
			
			$('#wplnst-results-url-error-title-' + loc_id).text(status.curl_err_title);
			$('#wplnst-results-url-error-code-'  + loc_id).text(data_label('error-code') + ' ' + status.curl_errno);
			$('#wplnst-results-url-error-desc-' + loc_id).text(status.curl_err_desc);
			$('#wplnst-results-url-error-' + loc_id).show();
		}
		
		$('#wplnst-url-status-info-time-' + loc_id).text(status.total_time + ' s');
		
		if (status.total_bytes > 0) {
			$('#wplnst-url-status-info-split-' + loc_id).show();
			$('#wplnst-url-status-info-size-'  + loc_id).text(status.total_size);
			$('#wplnst-url-status-info-size-'  + loc_id).show();
		} else {
			$('#wplnst-url-status-info-split-' + loc_id).hide();
			$('#wplnst-url-status-info-size-'  + loc_id).hide();
		}
	}



	function update_request_marks(loc_id, marks) {
		
		var key, action, mark, row = $('#wplnst-results-url-marks-' + loc_id);
		
		for (key in marks) {
			
			if ('url' == key) {
				$('#wplnst-results-url-full-' + loc_id).html(marks[key]);
				(marks['relative'] || marks['absolute'])? $('#wplnst-results-url-full-' + loc_id).removeClass('wplnst-display-none') : $('#wplnst-results-url-full-' + loc_id).addClass('wplnst-display-none');
			
			} else {
				
				mark = row.find('.wplnst-results-mark-' + key).eq(0);
				marks[key]? mark.removeClass('wplnst-display-none') : mark.addClass('wplnst-display-none');
				
				if ('redirs' == key) {
					
					if (marks[key])
						mark.html(marks['redirs_count']);
					
					action = $('.wplnst-row-actions-' + loc_id).find('span.wplnst-action-url-redir');
					if (1 == action.length) {
						marks[key]? action.eq(0).removeClass('wplnst-display-none') : action.eq(0).addClass('wplnst-display-none');
					} else if (marks[key]) {
						action = $('.wplnst-row-actions-' + loc_id).find('span.wplnst-action-url-edit');
						if (1 == action.length)
							action.after('<span class="wplnst-action-url-redir"><a href="#" id="wplnst-action-url-redir-' + loc_id + '" data-loc-id="' + loc_id + '" data-action="url_redir" class="wplnst-results-action">' + data_label('action-url-redir')  + '</a> | </span>');
					}
				}
			}
		}
	}



	function rollback_actions(key) {
		if (key in busy && false !== busy[key]) {
			$('.wplnst-row-actions-' + busy[key]['loc_id']).removeClass('visible').show();
			busy[key] = false;
		}
	}



	function headers_window(data) {
		
		var t = $($('#wplnst-results-headers-template').html());
		
		if (data.url != '') {
			var url = '<a href="' + data.url + '" target="_blank">' + data.url + '</a>';
			if (data.total_time != '' || data.total_bytes != '' || data.request_at != '') {
				url += '<br />';
				if (data.total_time != '')
					url += data.total_time + ' s';
				if (data.total_bytes != '')
					url += ((data.total_time != '')? ' <span>|</span> ' : '') + data.total_bytes;
				if (data.request_at != '')
					url += ((data.total_time != '' || data.total_bytes != '')? ' <span>|</span> ' : '') + data.request_at;
			}
			t.find('.wplnst-results-headers-url').eq(0).html(url);
		}
		
		var responses = '';
		for (var k in data.headers)
			responses += (('status' == k)? data.headers[k] : (k + ': ' + data.headers[k])) + '<br />';
		t.find('.wplnst-results-headers-response').eq(0).html(responses);
		
		var requests = '';
		for (var k in data.headers_request)
			requests += (('GET' == k)? ('GET ' + data.headers_request[k]) : (k + ': ' + data.headers_request[k])) + '<br />';
		t.find('.wplnst-results-headers-request').eq(0).html(requests);
		
		var w = $($('#wplnst-window-template').html().replace('%s', $('#wplnst-results-headers').attr('data-caption')));
		w.find('.wplnst-results-headers-content').eq(0).html(t.html());
		
		$('#wplnst-results-headers').html(w.html()).wplnst_lightboxed({
			centered : true,
			lightboxSpeed : 0,
			overlaySpeed : 0,
			overlayCSS : {
				background: '#000',
				opacity: .5
			}
		});
	}



	$('#bulk-action-selector-top').change(function() {
		results_bulkactions('top');
		return false;
	});
	
	$('#bulk-action-selector-bottom').change(function() {
		results_bulkactions('bottom');
		return false;
	});

	$('.wplnst-results-bulkactions-top .button.action').click(function() {
		results_bulkactions('top');
		return false;
	});
	
	$('.wplnst-results-bulkactions-bottom .button.action').click(function() {
		results_bulkactions('bottom');
		return false;
	});
	
	function results_bulkactions(which) {
		
		var action = $('#bulk-action-selector-' + which).val();
		if ('bulk_unlink' == action || 'bulk_ignore' == action || 'bulk_unignore' == action || 'bulk_anchor' == action || 'bulk_url' == action || 'bulk_status' == action || 'bulk_redir' == action || 'bulk_nofollow' == action || 'bulk_dofollow' == action) {
			
			$('.wplnst-results-update-box-error').closest('.wplnst-results-output-container').each(function() {
				var key = $(this).attr('data-action-key');
				if ('undefined' != typeof key && key.length)
					rollback_actions(key);
				$(this).remove();
			});
			
			$('.wplnst-row-actions').hide();
			results_bulkactions_disable();
			
			var output = $($('#wplnst-results-output').html());
			output.attr('data-action-key', action);
			
			if ('bulk_url' == action) {
				output.html($('#wplnst-results-edit-url').html().replace('%s', ''));
				output.find('.wplnst-results-output-cancel').eq(0).removeClass('wplnst-results-output-cancel').addClass('wplnst-results-output-cancel-bulk');
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				output.find('.wplnst-results-update-box-edit input').eq(0).focus();
				
			} else if ('bulk_unlink' == action) {
				output.html($('#wplnst-results-unlink-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_ignore' == action) {
				output.html($('#wplnst-results-ignore-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_unignore' == action) {
				output.html($('#wplnst-results-unignore-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_anchor' == action) {
				output.html($('#wplnst-results-edit-anchor').html().replace('%s', ''));
				output.find('.wplnst-results-output-cancel').eq(0).removeClass('wplnst-results-output-cancel').addClass('wplnst-results-output-cancel-bulk');
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				output.find('.wplnst-results-update-box-edit input').eq(0).focus();
				
			} else if ('bulk_status' == action) {
				output.html($('#wplnst-results-recheck-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_redir' == action) {
				output.html($('#wplnst-results-redir-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_nofollow' == action) {
				output.html($('#wplnst-results-nofollow-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_dofollow' == action) {
				output.html($('#wplnst-results-dofollow-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
			}
		}
	}
	
	$(document).on('click', '.wplnst-results-output-confirm-bulk', function(e) {
		submit_bulk($(this).closest('.wplnst-results-output-container'));
		return false;
	});
	
	function submit_bulk(output) {
		
		var checked = [];
		$('input[type=checkbox].wplnst-ck-loc-id').each(function() {
			if ($(this).is(':checked'))
				checked.push($(this).val());
		});
		
		if (!checked.length) {
			alert( data_label('select-any') );
			return false;
		}
		
		var action = output.attr('data-action-key');
		
		var value  = '';
		if ('bulk_url' == action || 'bulk_anchor' == action) {
			
			value = false;
			var field = output.find('.wplnst-results-update-box-edit input').eq(0);
			if ('undefined' != typeof field)
				value = $(field).val();
			
			if ('undefined' == typeof value || false === value) {
				alert('Entered value error');
				return false;
			}
		}
		
		output.html($('#wplnst-results-processing').html().replace('%s', data_label_bulk(action.replace('_', '-')) )).show();
		
		$.post(ajaxurl, { 'action' : 'wplnst_results_update', 'op' : action, 'nonce' : nonce, 'loc_id' : checked.join('-'), 'value' : value }, function(e) {
			
			if ('undefined' == typeof e.status) {
				alert( data_label('unknown-error') );
				output.remove();
				results_bulkactions_restore();
				results_bulkactions_enable();
				
			} else if ('ok' == e.status) {
				
				var location, loc_id;
				
				if ('bulk_ignore' == action) {
					
					for (var i in checked) {
						$('.wplnst-row-actions-' + checked[i]).closest('tr').fadeOut(300, function() {
							$(this).remove();
						});
					}
					
					output.remove();
					results_bulkactions_enable();
					results_bulkactions_restore();
					
				} else {
					
					if ('bulk_url' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									location = e.data.locations[i];
									loc_id = location.loc_id;
									$('#wplnst-results-url-loc-' + loc_id).attr('href',  location.request_url);
									$('#wplnst-results-url-loc-' + loc_id).attr('title', location.request_url);
									$('#wplnst-results-url-loc-' + loc_id).html(e.data.value);
									update_request_status(loc_id, location.request_status);
									$('#wplnst-results-url-marks-' + loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-modified').eq(0).removeClass('wplnst-display-none');
									update_request_marks(loc_id, location.marks);
								}
							}
						}
						
					} else if ('bulk_unlink' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									$('.wplnst-row-actions-' + e.data.locations[i].loc_id).remove();
									$('#wplnst-results-url-unlinked-' + e.data.locations[i].loc_id).show();
								}
							}
						}
						
					} else if ('bulk_unignore' == action) {
						for (var i in checked) {
							$('#wplnst-action-url-unignore-' + checked[i]).hide();
							$('#wplnst-action-url-ignore-' + checked[i]).removeClass('wplnst-display-none');
							$('#wplnst-results-url-marks-' + checked[i]).find('.wplnst-results-mark-ignored').eq(0).addClass('wplnst-display-none');
						}
						
					} else if ('bulk_anchor' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									$('#wplnst-results-anchor-mod-' + e.data.locations[i].loc_id).removeClass('wplnst-display-none');
									$('#wplnst-results-anchor-loc-' + e.data.locations[i].loc_id).html(e.data.value);
								}
							}
						}
						
					} else if ('bulk_status' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									$('#wplnst-url-status-recheck-mark-' + e.data.locations[i].loc_id).removeClass('wplnst-display-none');
									update_request_status(e.data.locations[i].loc_id, e.data.locations[i].request_status);
									update_request_marks(e.data.locations[i].loc_id, e.data.locations[i].marks);
								}
							}
						}
						
					} else if ('bulk_redir' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									location = e.data.locations[i];
									loc_id = location.loc_id;
									$('#wplnst-results-url-loc-' + loc_id).attr('href',  location.request_url);
									$('#wplnst-results-url-loc-' + loc_id).attr('title', location.request_url);
									$('#wplnst-results-url-loc-' + loc_id).html(location.value);
									update_request_status(loc_id, location.request_status);
									$('#wplnst-results-url-marks-' + loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-modified').eq(0).removeClass('wplnst-display-none');
									update_request_marks(loc_id, location.marks);
								}
							}
						}
						
					} else if ('bulk_nofollow' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									$('#wplnst-action-url-nofollow-' + e.data.locations[i].loc_id).addClass('wplnst-display-none');
									$('#wplnst-action-url-dofollow-' + e.data.locations[i].loc_id).removeClass('wplnst-display-none');
									$('#wplnst-results-url-marks-' + e.data.locations[i].loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-nofollow').eq(0).removeClass('wplnst-display-none');
								}
							}
						}
						
					} else if ('bulk_dofollow' == action) {
						if (e.data.locations.length) {
							for (var i in e.data.locations) {
								if ('ok' == e.data.locations[i].status) {
									$('#wplnst-action-url-nofollow-' + e.data.locations[i].loc_id).removeClass('wplnst-display-none');
									$('#wplnst-action-url-dofollow-' + e.data.locations[i].loc_id).addClass('wplnst-display-none');
									$('#wplnst-results-url-marks-' + e.data.locations[i].loc_id).removeClass('wplnst-display-none').find('.wplnst-results-mark-nofollow').eq(0).addClass('wplnst-display-none');
								}
							}
						}
					}
					
					output.find('.wplnst-results-processing-action').removeClass('wplnst-results-processing-action-run').addClass('wplnst-results-processing-action-ok').delay(1000).fadeOut(600, function() {
						output.remove();
						results_bulkactions_enable();
						results_bulkactions_restore();
					});
				}
				
			} else if ('error' == e.status) {
				output.html($('#wplnst-results-error-bulk').html().replace('%s', e.reason.join(' ')));
			}
		
		}).fail(function() {
			alert( data_label('server-comm-error') );
			output.remove();
			results_bulkactions_enable();
			results_bulkactions_restore();
		});
		
		return false;
	}
	
	$(document).on('click', '.wplnst-results-output-cancel-bulk', function(e) {
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length)
			output.remove();
		results_bulkactions_enable();
		results_bulkactions_restore();
		return false;
	});
	
	function results_bulkactions_restore() {
		$('.wplnst-results-bulkactions-area').hide();
		$('.wplnst-row-actions').removeClass('visible').show();
	}
	
	function results_bulkactions_disable() {
		$('#bulk-action-selector-top').attr('disabled', 'disabled');
		$('#bulk-action-selector-bottom').attr('disabled', 'disabled');
		$('.wplnst-results-bulkactions-top .button.action').attr('disabled', 'disabled');
		$('.wplnst-results-bulkactions-bottom .button.action').attr('disabled', 'disabled');
	}
	
	function results_bulkactions_enable() {
		$('#bulk-action-selector-top').removeAttr('disabled');
		$('#bulk-action-selector-bottom').removeAttr('disabled');
		$('.wplnst-results-bulkactions-top .button.action').removeAttr('disabled');
		$('.wplnst-results-bulkactions-bottom .button.action').removeAttr('disabled');
	}



	$('#wplnst-results-filters-toggle').click(function() {
		$(this).addClass('wplnst-display-none');
		$('#wplnst-results-filters').addClass('wplnst-display-none');
		$('#wplnst-results-filters-advanced').removeClass('wplnst-display-none');
		$.post(ajaxurl, { 'action' : 'wplnst_results_advanced_display', 'nonce' : nonce_advanced_display, 'display' : 'on' }, function(e) {});
		return false;
	});
	
	$('#wplnst-results-filters-advanced-close').click(function() {
		$('#wplnst-results-filters').removeClass('wplnst-display-none');
		$('#wplnst-results-filters-advanced').addClass('wplnst-display-none');
		$('#wplnst-results-filters-toggle').removeClass('wplnst-display-none');
		$.post(ajaxurl, { 'action' : 'wplnst_results_advanced_display', 'nonce' : nonce_advanced_display, 'display' : 'off' }, function(e) {});
		return false;
	});
	
	$('#wplnst-results-filters-advanced-reset').click(function() {
		if (confirm($(this).attr('data-confirm'))) {
			$('#wplnst-results-filters-advanced select').each(function() {
				this.selectedIndex = 0;
			});
			$('#wplnst-results-filters-advanced input:text').each(function() {
				$(this).val('');
			});
		}
		$(this).blur();
		return false;
	});
	
	$('.wplnst-filter-advanced-text').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#wplnst-filter-advanced-button').click();
			return false;
		}
	});
	
	$('#wplnst-filter-advanced-button').click(function() {
		
		var args = '', value;
		var fields = $(this).attr('data-fields').split(',');
		for (var i in fields) {
			value = $('#wplnst-filter-advanced-' + fields[i]).val();
			if (typeof value != 'undefined' && '' !== value)
				args += '&' + fields[i] + '=' + value;
		}
		args += '&adv=on';
		
		var surl = $('#wplnst-filter-advanced-url').val().trim();
		if (typeof surl != 'undefined' && '' !== surl)
			args += '&surl=' + encodeURIComponent(surl) + '&surlt=' + $('#wplnst-filter-advanced-url-options').val();
		
		var sanc = $('#wplnst-filter-advanced-anchor').val().trim();
		if (typeof sanc != 'undefined' && '' !== sanc)
			args += '&sanc=' + encodeURIComponent(sanc) + '&sanct=' + $('#wplnst-filter-advanced-anchor-options').val();
		
		window.location.href = $(this).attr('data-href') + args;
		return false;
	});



	function data_label(name) {
		return $('#wplnst-results').attr('data-label-' + name);
	}

	function data_label_bulk(name) {
		return $('#wplnst-results-bulkactions-area-top').attr('data-label-' + name);
	}



});