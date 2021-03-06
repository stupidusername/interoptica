$(document).ready(function () {

	var focus = function () {
		var timer = setInterval(function () {
			if ($('#orderproduct-product_id').hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				$('#orderproduct-product_id').on('select2:select', function () {
					$('#orderproduct-quantity').focus();
				});
				clearInterval(timer);
			}
			if ($('#orderproductsform-productids').hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				$('#orderproductsform-productids').on('select2:select', function () {
					$('#orderproductsform-quantity').focus();
				});
				clearInterval(timer);
			}
		}, 25);
	};

	var focusInvoice = function () {
		$('#orderinvoice-number').focus();
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

	var showAddInvoiceModal = function() {
		$('#addInvoice').modal('show');
	};

	$('#addEntryButton').on('click', function () {
		showAddEntryModal();
	});

	$('#addInvoiceButton').on('click', function () {
		showAddInvoiceModal();
	});

	// refresh order details periodically
	setInterval(function () {
		$.pjax.reload({container: '#orderSummary', timeout: 30000}).done(function() {
			var displayOrders = $('#pendingOrders').css('display');
			var displayIssues = $('#pendingIssues').css('display');
			$.pjax.reload({container: '#pendingGridview', timeout: 30000}).done(function() {
				$('#pendingOrders').css('display', displayOrders);
				$('#pendingIssues').css('display', displayIssues);
			});
		});
	}, 30000);

	$('#addInvoice').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#invoicesGridview', timeout: 30000});
			$('#addInvoice').modal('hide');
		}
	});

	$('#addEntry').on('shown.bs.modal', function (event, xhr, settings) {
		focus();
	});

	$('#addInvoice').on('shown.bs.modal', function (event, xhr, settings) {
		focusInvoice();
	});

	$('#addEntry').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#productsGridview', timeout: 30000});
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
				$.pjax.reload({container: '#productsGridview', timeout: 30000});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});

	$(document).on('click', '.invoiceDelete', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#invoicesGridview', timeout: 30000});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});

	setUpUpdateButtons($(document));
});
