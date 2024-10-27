jQuery(document).ready(function($) {
    $('#wp-admin-bar-amistacx-page-speed-purge-button a').click(function(e) {
        e.preventDefault();
        $.post(custom_ajax.url, {action: 'amistacx_pagespeed_purge_click' }, function(response) {
			var message_type = response.error ? 'error' : 'success';
            swal({
                position: 'center',
                type: message_type,
                title: response.message,
                showConfirmButton: false,
                timer: 2500
            })
        });
    });
    $('#wp-admin-bar-amistacx-redis-purge-button a').click(function(e) {
        e.preventDefault();
        $.post(custom_ajax.url, {action: 'amistacx_redis_purge_click' }, function(response) {
            swal({
                position: 'center',
                type: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 2500
            })
        });
    });
    $("#pagespeed_send").click(function(){
        var url = $('#pagespeed_url').val();
		$.post(ajaxurl, {action: 'amistacx_pagespeed_purge_url_page', 'pagespeed_url': url}, function(response) {
			var message_type = response.error ? 'error' : 'success';
			swal({
				position: 'center',
				type: message_type,
				title: response.message,
				showConfirmButton: false,
				timer: 2500
			})
		});
    });

});