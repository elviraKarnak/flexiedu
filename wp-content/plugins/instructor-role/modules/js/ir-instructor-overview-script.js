jQuery(document).ready(
	function () {
		createCoursePieChart(ir_data.chart_data);
		createEarningsDonutChart(ir_data.earnings);

		jQuery('#instructor-courses-select').on(
			'change',
			function () {
				jQuery('.ir-course-reports .ir-ajax-overlay').show();
				$select = jQuery(this);
				jQuery.ajax(
					{
						type: 'post',
						dataType: 'JSON',
						url: ir_data.ajax_url,
						data: {
							action: 'ir-update-course-chart',
							course_id: $select.val()
						},
						success: function (chart_data) {
							jQuery('.ir-course-reports .ir-ajax-overlay').hide();
							createCoursePieChart(chart_data);
						}
					}
				);
			}
		);

		// Setup Datatables
		if (!jQuery('.ir-assignments-table .ir-no-data-found').length) {
			jQuery('.ir-assignments-table').DataTable(
				{
					'language': {
						"decimal": ir_data.i18n.decimal,
						"emptyTable": ir_data.i18n.emptyTable,
						"info": ir_data.i18n.info,
						"infoEmpty": ir_data.i18n.infoEmpty,
						"infoFiltered": ir_data.i18n.infoFiltered,
						"infoPostFix": ir_data.i18n.infoPostFix,
						"thousands": ir_data.i18n.thousands,
						"lengthMenu": ir_data.i18n.lengthMenu,
						"loadingRecords": ir_data.i18n.loadingRecords,
						"processing": ir_data.i18n.processing,
						"search": ir_data.i18n.search,
						"zeroRecords": ir_data.i18n.zeroRecords,
						"paginate": {
							"first": ir_data.i18n.paginate.first,
							"last": ir_data.i18n.paginate.last,
							"next": '',
							"previous": ''
						},
						"aria": {
							"sortAscending": ir_data.i18n.aria.sortAscending,
							"sortDescending": ir_data.i18n.aria.sortDescending
						}
					},
					"columnDefs": [
						{ "width": "20%", "targets": 3 }
					],
					"order": [3, 'desc'],
					"dom": '<"ir-sub-table-head"fl>rt<"ir-sub-table-foot"<"ir-extra-div"i>pi>'
				}
			);
		}
	}
);

function createCoursePieChart(chart_data) {
	jQuery('#ir-course-pie-chart-div').empty();
	var not_started_per = chart_data.not_started;
	var in_progress_per = chart_data.in_progress;
	var completed_per = chart_data.completed;
	var graph_heading = chart_data.title;

	if (0 === not_started_per + in_progress_per + completed_per) {
		jQuery('#ir-course-pie-chart-div').html(ir_data.empty_reports);
		return;
	}

	jQuery('.ir-tab-links').on(
		'click',
		function () {
			var selector = jQuery(this).data('tab');
			jQuery('.ir-tab-content').hide();
			jQuery('.ir-tab-links').removeClass('tab-active');
			jQuery(this).addClass('tab-active');
			jQuery('#' + selector).show().addClass('tab-active');
		}
	);

	var options = {
		series: [not_started_per, in_progress_per, completed_per],
		labels: [chart_data.not_started_label, chart_data.in_progress_label, chart_data.completed_label],
		chart: {
			type: 'pie',
			height: '250px',
			width: '100%',
			toolbar: {
				show: false
			}
		},
		plotOptions: {
			pie: {
				donut: {
					labels: {
						show: false,
						formatter: function( val ){
							return 'Value : ' + val;
						}
					}
				}
			}
		},
		colors: chart_data.colors,
		dataLabels: {
			enabled: false,
			dropShadow: {
				enabled: true,
			}
		},
		legend: {
			position: 'bottom'
		}
	};
	var chart = new ApexCharts(document.querySelector("#ir-course-pie-chart-div"), options);
	chart.render();
}

function createEarningsDonutChart(earnings) {
	var paid_per = earnings.paid;
	var un_paid_per = earnings.unpaid;

	var options = {
		series: [{
			name: earnings.default_units_value,
			data: [paid_per, un_paid_per]
		}],
		chart: {
			type: 'bar',
			height: 200,
			toolbar: {
				show: false
			}
		},
		plotOptions: {
			bar: {
				borderRadius: 0,
				horizontal: true,
				barHeight: '20%',
				borderRadius: 6,
				distributed: true,
				dataLabels: {
					position: 'bottom'
				},
			}
		},
		colors: earnings.colors,
		dataLabels: {
			enabled: false
		},
		xaxis: { // cspell:disable-line
			categories: [earnings.paid_label, earnings.unpaid_label],
			showAlways: true,
			labels: {
				show: true,
				formatter: function (value) {
					return ir_data.currency_symbol + value;
				},
				hideOverlappingLabels: true,
				rotate: -25,
				rotateAlways: true,
				// trim: true,
			}
		},
		yaxis: { // cspell:disable-line
			labels: {
				style: {
					cssClass : 'ir-earnings-chart-y-label'
				}
			}
		},
		legend: {
			show: false
		},
		tooltip: {
			enabled: true,
			y: {
				formatter: function (value) {
					return ir_data.currency_symbol + value;
				}
			}
		}
	};

	var chart = new ApexCharts(document.querySelector("#ir-earnings-pie-chart-div"), options);
	chart.render();
}
