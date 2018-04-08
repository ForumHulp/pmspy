; (function ($, window, document) {
    $( "#autocomplete" ).autocomplete({
		source: function(request, response) {
			$.ajax({
				type: "POST",
				url: '',
				dataType: "json",
				data: {
					term: request.term,
					action: 'keyword',
				},
				error: function(xhr, textStatus, errorThrown) {
					alert('Error: ' + xhr.responseText);
				},
				success: function(data) {
					response($.map(data, function(c) {
						return {
							value: c.value,
							label: c.label,
						};
					}));
				}
			});
		},
		select: function(event, ui) { 
			event.preventDefault(); 
			window.location.href = base_url + '&action=select&userid=' + ui.item.value;
		},
		placeholder: 'Enter user ...',
		minLength: 2
    });
})(jQuery, window, document);
