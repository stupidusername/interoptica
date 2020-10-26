$(document).ready(function () {

	var showAddSuitcaseModal = function() {
		$('#addSuitcase').modal('show');
	};

	var focusSuitcase = function () {
		var timer = setInterval(function () {
			if ($('#salesmansuitcase-customer_id').hasClass('select2-hidden-accessible')) {
				$('.select2-selection').focus();
				$('#salesmansuitcase-customer_id').on('select2:select', function () {
					$('#salesmansuitcase-suitcase_id').focus();
				});
				clearInterval(timer);
			}
		}, 25);
	};

	$('#addSuitcaseButton').on('click', function () {
		showAddSuitcaseModal();
	});

	$('#addSuitcase').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#suitcasesGridview', timeout: 30000});
			$('#addSuitcase').modal('hide');
		}
	});

	$('#addSuitcase').on('shown.bs.modal', function (event, xhr, settings) {
		focusSuitcase();
	});

	$(document).on('click', '.removeSuitcase', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#suitcasesGridview', timeout: 30000});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});
});
