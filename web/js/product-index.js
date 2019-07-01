var editProductIds = [];
var stockEditable = false;

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    if (options.data && options.data.indexOf('hasEditable') != -1) {
		if (editProductIds.length) {
			options.data += '&' + $.param({ids: editProductIds});
		}
	}
});

var getProductId = function (domElem) {
	return parseInt($(domElem).attr('id').split('_')[2]);
};

var showChecked = function (productId) {
	$('#edit_check_' + productId).hide();
	$('#edit_uncheck_' + productId).show();
	$('tr[data-key=' + productId + ']').css('background-color', '#B2B2B2');
};

var showUnchecked = function (productId) {
	$('#edit_uncheck_' + productId).hide();
	$('#edit_check_' + productId).show();
	$('tr[data-key=' + productId + ']').css('background-color', "");
};

var showSelected = function () {
	editProductIds.forEach(function (e) {
		showChecked(e);
	});
};

var editCheck = function (productId) {
	editProductIds.push(productId);
	showChecked(productId);
};

var editUncheck = function (productId) {
	editProductIds = editProductIds.filter(function (e) {
		return e !== productId;
	});
	showUnchecked(productId);
};

var uncheckAll = function () {
	editProductIds.forEach(function (e) {
		editUncheck(e);
	});
};

var updateStockButtons = function() {
    $('.stock-editable').each(function() {
        $(this).prop('disabled', !stockEditable);
    });
};

$('#enable-stock-edition').click(function() {
    $(this).css('display', 'none');
    $('#disable-stock-edition').css('display', 'inline-block');
    stockEditable = true;
    updateStockButtons();
});

$('#disable-stock-edition').click(function() {
    $(this).css('display', 'none');
    $('#enable-stock-edition').css('display', 'inline-block');
    stockEditable = false;
    updateStockButtons();
});

var initializeMassEdit = function () {
	$('.edit_check').click(function () {
		editCheck(getProductId(this));
	});
	$('.edit_uncheck').click(function () {
		editUncheck(getProductId(this));
	});
	$('.product_delete').click(function () {
		editUncheck(getProductId(this));
	});
	$('#edit_clear').click(function () {
		uncheckAll();
	});

	showSelected();

    updateStockButtons();
};

$('#productsGridview').on('pjax:end', function () {
	initializeMassEdit();
});

initializeMassEdit();
