/**
 * Get the part after the hash character from a URL. This is especially useful to support older versions of IE
 * when getting the href attribute of an element
 *
 * @param string url URL to get the hash part from
 * @return string Part of URL after the hash character
 */
function jwdm_get_url_hashpart(url)
{
	var hashindex = url.indexOf('#');
	
	url = url.substring(hashindex + 1);
	
	return url;
}

jQuery(document).ready(function($) {
	$('.jwdm-list-checkall').click(function() {
		var target = jwdm_get_url_hashpart($(this).attr('href'));
		
		$('#' + target + ' input[type="checkbox"]').attr('checked', true);
		
		return false;
	});
	
	$('.jwdm-list-uncheckall').click(function() {
		var target = jwdm_get_url_hashpart($(this).attr('href'));
		
		$('#' + target + ' input[type="checkbox"]').attr('checked', false);
		
		return false;
	});
	
	$('.jwdm-list-toggleall').click(function() {
		var target = jwdm_get_url_hashpart($(this).attr('href'));
		
		$('#' + target + ' input[type="checkbox"]').each(function() {
			$(this).attr('checked', $(this).attr('checked') ? false : true);
		});
		
		return false;
	});
});