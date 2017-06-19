$(document).ready(function () {
	
	var addEntryUrl;

	var focus = function () {
		var timer = setInterval(function () {
			var field = $('#deliveryorder-order_id');
			if (!field.length) {
				field = $('#deliveryissue-issue_id');
			}
			if (field.hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				field.on('select2:select', function () {
					$('#addEntry :submit').focus();
				});
				clearInterval(timer);
			}
		}, 25);
	};
	
	
	var showAddEntryModal = function() {
		$('#addEntry').kbModalAjax({url: addEntryUrl}); $('#addEntry').modal('show');
	};
	
	var setUpButtons = function () {
		$('.addEntryButton').on('click', function () {
		addEntryUrl = $(this).attr('url');
		showAddEntryModal();
		});
	};

	$('#deliveryDetail').on('pjax:complete', function() {
		$.pjax.reload({container: '#entriesGridviews'});
	});
	
	$('#entriesGridviews').on('pjax:complete', function() {
		setUpButtons();
	});

	setUpButtons();

	// refresh order details periodically
	setInterval(function () {
		$.pjax.reload({container: '#deliveryDetail'});
	}, 30000);
	
	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#entriesGridviews'});
		showAddEntryModal();
	});
	
	$('#addEntry').on('kbModalShow', function (event, xhr, settings) {
		focus();
	});
	
	$('#addEntry').on('kbModalSubmit', function (event, xhr, settings) {
		focus();
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
