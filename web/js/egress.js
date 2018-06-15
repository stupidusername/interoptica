$(document).ready(function () {

	var focus = function () {
		var timer = setInterval(function () {
			if ($('#egressproduct-product_id').hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				$('#egressproduct-product_id').on('select2:select', function () {
					$('#egressproduct-quantity').focus();
				});
				clearInterval(timer);
			}
		}, 25);
	};

	var setUpUpdateButtons = function (domElem) {
		domElem.on('click', '.productUpdate', function (event) {
			event.preventDefault();
			$("#addEntry").kbModalAjax({url: $(this).attr('href')});
			$('#addEntry').modal('show');
		});
	};

	var addEntryUrl = $('#addEntryButton').attr('url');

	var showAddEntryModal = function() {
		$('#addEntry').kbModalAjax({url: addEntryUrl}); $('#addEntry').modal('show');
	};

	$('#addEntryButton').on('click', function () {
		showAddEntryModal();
	});

	$('#addEntry').on('shown.bs.modal', function (event, xhr, settings) {
		focus();
	});

	$('#addEntry').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#productsGridview'});
			showAddEntryModal();
			setUpUpdateButtons($('#addEntry'));
		}
		focus();
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

	setUpUpdateButtons($(document));
});
