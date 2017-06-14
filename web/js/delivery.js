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
	
	var setUpButtons = function () {
		$('.addEntryButton').on('click', function () {
		addEntryUrl = $(this).attr('url');
		showAddEntryModal();
		});
	};

	var reloadPjaxContainers = function() {
		$.pjax.reload({container: '#deliveryDetail'}).done(function() {
			$.pjax.reload({container: '#entriesGridviews'}).done(function() {
				setUpButtons();
			});
		});
	};
	
	setUpButtons();

	// refresh order details periodically
	setInterval(function () {
	}, 30000);
	
	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		reloadPjaxContainers();
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
				reloadPjaxContainers();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});
});
