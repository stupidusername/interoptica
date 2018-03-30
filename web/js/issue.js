$(document).ready(function () {

	$('#productsGridview').on('pjax:success', function() {
			$.pjax.reload({container: '#issueErrors'});
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

	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#productsGridview'});
		showAddEntryModal();
	});

	$('#addComment').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$('#addComment').modal('hide');
		$.pjax.reload({container: '#commentsGridview'});
	});

	$('#addInvoice').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#invoicesGridview'});
		$('#addInvoice').modal('hide');
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

	$('#addEntry').on('kbModalSubmit', function (event, xhr, settings) {
		focus();
		setUpUpdateButtons($('#addEntry'));
	});

	$('#addComment').on('kbModalSubmit', function (event, xhr, settings) {
		focus();
		setUpUpdateButtons($('#addEntry'));
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

	$(document).on('click', '.invoiceDelete', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#invoicesGridview'});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});

	setUpUpdateButtons($(document));
});
