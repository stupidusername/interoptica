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
	
	$('#addEntry').on('kbModalSubmitSuccess', function (event, xhr, settings) {
		$.pjax.reload({container: '#productsGridview'});
		showAddEntryModal();
	});
	
	$('#addEntry').on('kbModalShow', function (event, xhr, settings) {
		focus();
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