
var orderIds = [];
var issueIds = [];

$('#createDelivery').click(function (e) {
	if (!orderIds.length && !issueIds.length) {
		e.preventDefault();
	} else {
		var params = { orderIds: orderIds, issueIds: issueIds };
		console.log(jQuery.param(params));
		$(this).attr('href', $(this).attr('href') + '?' + decodeURIComponent(jQuery.param(params)));
	}
});

var getEntryId = function (domElem) {
	return parseInt($(domElem).attr('id').split('_')[2]);
};

var showOrderChecked = function (id) {
	$('#order_check_' + id).hide();
	$('#order_uncheck_' + id).show();
};

var showOrderUnchecked = function (id) {
	$('#order_uncheck_' + id).hide();
	$('#order_check_' + id).show();
};

var showIssueChecked = function (id) {
	$('#issue_check_' + id).hide();
	$('#issue_uncheck_' + id).show();
};

var showIssueUnchecked = function (id) {
	$('#issue_uncheck_' + id).hide();
	$('#issue_check_' + id).show();
};

var showSelected = function () {
	orderIds.forEach(function (e) {
		showOrderChecked(e);
	});
	issueIds.forEach(function (e) {
		showIssueChecked(e);
	});
};

var orderCheck = function (id) {
	orderIds.push(id);
	showOrderChecked(id);
};

var orderUncheck = function (id) {
	orderIds = orderIds.filter(function (e) {
		return e !== id;
	});
	showOrderUnchecked(id);
};

var issueCheck = function (id) {
	issueIds.push(id);
	showIssueChecked(id);
};

var issueUncheck = function (id) {
	issueIds = issueIds.filter(function (e) {
		return e !== id;
	});
	showIssueUnchecked(id);
};


var initialize = function () {
	$('.order_check').click(function () {
		orderCheck(getEntryId(this));
	});
	$('.order_uncheck').click(function () {
		orderUncheck(getEntryId(this));
	});
	$('#order_clear').click(function () {
		orderIds.forEach(function (e) {
			orderUncheck(e);
		});
	});
	$('.issue_check').click(function () {
		issueCheck(getEntryId(this));
	});
	$('.issue_uncheck').click(function () {
		issueUncheck(getEntryId(this));
	});
	$('#issue_clear').click(function () {
		issueIds.forEach(function (e) {
			issueUncheck(e);
		});
	});
	showSelected();
};

$('#orders-gridview').on('pjax:end', function () {
	initialize();
});

$('#issues-gridview').on('pjax:end', function () {
	initialize();
});

initialize();
