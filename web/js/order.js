$(document).ready(function () {
	var addEntryUrl = $('#addEntryButton').attr('url');
	
	var showAddEntryModal = function() {
		$('#addEntry').kbModalAjax({url: addEntryUrl}); $('#addEntry').modal('show');
	}
	
	$('#addEntryButton').on('click', function () {
		showAddEntryModal();
	});
	
	// refresh pending orders periodically
	setInterval(function () {
		$.pjax.reload({container: '#pendingOrdersGridview'});
	}, 30000);
	
	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#productsGridview'});
		showAddEntryModal();
	});

	$(document).on('click', '.productDelete', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#productsGridview'});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});
	
	$(document).on('click', '.productUpdate', function (event) {
		event.preventDefault();
		$("#addEntry").kbModalAjax({url: $(this).attr('href')});
		$('#addEntry').modal('show');
	});
});