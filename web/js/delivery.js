$(document).ready(function () {
	
	var addEntryUrl;

	var focus = function () {
		var timer = setInterval(function () {
			var field = $('#deliveryorder-order_id');
			if (!field) {
				field = $('#deliveryissue-issue_id');
			}
			if (field.hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				clearInterval(timer);
			}
		}, 25);
	};
	
	
	var showAddEntryModal = function() {
		$('#addEntry').kbModalAjax({url: addEntryUrl}); $('#addEntry').modal('show');
	};
	
	$('.addEntryButton').on('click', function () {
		addEntryUrl = $(this).attr('url');
		showAddEntryModal();
	});
	
	// refresh order details periodically
	setInterval(function () {
		$.pjax.reload({container: '#deliveryDetail'}).done(function() {
			$.pjax.reload({container: '#entriesGridviews'});
		});
	}, 30000);
	
	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#entriesGridviews'}).done(function() {
			$.pjax.reload({container: '#deliveryDetail'});
		});
		showAddEntryModal();
	});
	
	$('#addEntry').on('kbModalShow', function (event, xhr, settings) {
		focus();
	});
	
	$('#addEntry').on('kbModalSubmit', function (event, xhr, settings) {
		focus();
		setUpUpdateButtons($('#addEntry'));
	});

	$(document).on('click', '.entryDelete', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#entriesGridviews'});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});
});
