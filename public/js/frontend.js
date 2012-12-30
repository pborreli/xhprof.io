$(function(){
	var alternate;
	$.ay.toggleElement({trigger: $('#navigation .button-filter'), target: $('#filter'), targetClass: 'hidden'});
	$.ay.toggleElement({trigger: $('#navigation .button-summary'), target: $('#metrics-summary'),targetClass: 'hidden'});
	$('table.ay-sort').ayTableSort();
	$('thead.ay-sticky').ayTableSticky();
	if ($('table.aggregated-callstack').length) {
		alternate = $('[data-ay-alternate]');
		alternate.on('ay-alternate', function (e, stage) {
			$(this).data('ay-alternate-stage', stage).html($(this).data('ay-alternate')[stage]);
		});
		alternate.on('click', function(){
			var data,
				stage;
			data = $(this).data('ay-alternate');
			stage = $(this).data('ay-alternate-stage');
			if (typeof stage == 'undefined') {
				stage = 0;
			} else if (typeof data[stage+1] != 'undefined') {
				++stage;
			} else {
				stage = 0;
			}
			alternate.trigger('ay-alternate', stage);
		});
	}
});