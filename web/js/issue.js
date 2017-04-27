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
	
	var setUpUpdateButtons = function (domElem) {
		domElem.on('click', '.productUpdate', function (event) {
			event.preventDefault();
			$("#addEntry").kbModalAjax({url: $(this).attr('href')});
			$('#addEntry').modal('show');
		});
	};
	
	var addEntryUrl = $('#addEntryButton').attr('url');
	
	var showAddEntryModal = function() {
		$('#addEntry').kbModalAjax({url: addEntryUrl});
		$('#addEntry').modal('show');
	};
	
	var showAddCommentModal = function() {
		$('#addComment').modal('show');
	};
	
	$('#addEntryButton').on('click', function () {
		showAddEntryModal();
	});
	
	$('#addCommentButton').on('click', function () {
		showAddCommentModal();
	});
	
	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#productsGridview'});
		showAddEntryModal();
	});
	
	$('#addComment').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$('#addComment').modal('hide');
		$.pjax.reload({container: '#commentsGridview'});
	});
	
	$('#addEntry').on('kbModalShow', function (event, xhr, settings) {
		focus();
	});
	
	$('#addComment').on('kbModalShow', function (event, xhr, settings) {
		focusComment();
	});
	
	$('#addEntry').on('kbModalSubmit', function (event, xhr, settings) {
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
	
	setUpUpdateButtons($(document));
});