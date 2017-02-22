$(document).ready(function () {
	$('#addEntry').on('kbModalSubmit', function (event, xhr, settings) {
		$.pjax.reload({container: '#productsGridview'});
	});

	$(document).on('click', '.productDelete', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#productsGridview'});
			}
		});
	});
	
	$(document).on('click', '.productUpdate', function (event) {
		event.preventDefault();
		$("#addEntry").kbModalAjax({url: $(this).attr('href')});
		$('#addEntry').modal('show');
	});
});