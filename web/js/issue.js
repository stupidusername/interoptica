$(document).ready(function () {

	$('#productsGridview').on('pjax:success', function() {
			$.pjax.reload({container: '#issueErrors', timeout: 30000});
	});;

	var focus = function () {
		var timer = setInterval(function () {
			if ($('#issueproduct-product_id').hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				$('#issueproduct-product_id').on('select2:select', function () {
					$('#issueproduct-fail_id').focus();
				});
				clearInterval(timer);
			}
		}, 25);
	};

	var focusComment = function () {
		var timer = setInterval(function () {
			if ($("#issuecomment-comment").is(":focus")) {
				clearInterval(timer);
			}
			$("#issuecomment-comment").focus();
		}, 25);
	};

	var focusInvoice = function () {
		$('#issueinvoice-number').focus();
	};

	var setUpUpdateButtons = function (domElem) {
		domElem.on('click', '.productUpdate', function (event) {
			event.preventDefault();
			$("#addEntry").kbModalAjax({url: $(this).attr('href')});
			$('#addEntry').modal('show');
		});
		domElem.on('click', '.commentUpdate', function (event) {
			event.preventDefault();
			$("#addComment").kbModalAjax({url: $(this).attr('href')});
			$('#addComment').modal('show');
		});
	};

	var addEntryUrl = $('#addEntryButton').attr('url');
	var addCommentUrl = $('#addCommentButton').attr('url');

	var showAddEntryModal = function() {
		$('#addEntry').kbModalAjax({url: addEntryUrl});
		$('#addEntry').modal('show');
	};

	var showAddCommentModal = function() {
		$('#addComment').kbModalAjax({url: addCommentUrl});
		$('#addComment').modal('show');
	};

	var showAddInvoiceModal = function() {
		$('#addInvoice').modal('show');
	};

	$('#addEntryButton').on('click', function () {
		showAddEntryModal();
	});

	$('#addCommentButton').on('click', function () {
		showAddCommentModal();
	});

	$('#addInvoiceButton').on('click', function () {
		showAddInvoiceModal();
	});

	$('#addEntry').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#productsGridview', timeout: 30000});
			showAddEntryModal();
			setUpUpdateButtons($('#addEntry'));
		}
		focus();
	});

	$('#addComment').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$('#addComment').modal('hide');
			$.pjax.reload({container: '#commentsGridview', timeout: 30000});
			setUpUpdateButtons($('#addComment'));
		}
		focusComment();
	});

	$('#addInvoice').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#invoicesGridview', timeout: 30000});
			$('#addInvoice').modal('hide');
		}
	});

	$('#addEntry').on('shown.bs.modal', function (event, xhr, settings) {
		focus();
	});

	$('#addComment').on('shown.bs.modal', function (event, xhr, settings) {
		focusComment();
	});

	$('#addInvoice').on('shown.bs.modal', function (event, xhr, settings) {
		focusInvoice();
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
