$(document).ready(function () {

	var showAddBrandModal = function() {
		$('#addBrand').modal('show');
	};

	var focusBrand = function () {
		$('#suitcasebrand-brand_id').focus();
	};

	$('#addBrandButton').on('click', function () {
		showAddBrandModal();
	});

	$('#addBrand').on('kbModalSubmit', function (event, data, status, xhr) {
		if (data.success) {
			$.pjax.reload({container: '#brandsGridview', timeout: 30000});
			$('#addBrand').modal('hide');
		}
	});

	$('#addBrand').on('shown.bs.modal', function (event, xhr, settings) {
		focusBrand();
	});

	$(document).on('click', '.removeBrand', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		jQuery.ajax({
			type: 'POST',
			url: url,
			success: function (data, status, xhr) {
				$.pjax.reload({container: '#brandsGridview', timeout: 30000});
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText);
			}
		});
	});
});
