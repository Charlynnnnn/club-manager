
$(document).ready(function() {
	$('#example').DataTable(
		{
			"dom": '<"dt-buttons"Bf><"clear">lirtp',
			"paging": true,
			"autoWidth": true,
			"buttons": [
				'colvis',
				'copyHtml5',
        'csvHtml5',
				'excelHtml5',
        'pdfHtml5',
				'print'
			]
		}
	);
});

// Scroll
$("#kt_datatable_both_scrolls").DataTable({
    "scrollY": 320,
    "scrollX": true
});

$("#kt_datatable_horizontal_scroll").DataTable({
	"scrollY": 310,
    "scrollX": true
});
$('#myTabContent').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var targetPane = $(e.target).attr("href");
});

// FOR #RD DATA TABLE
$("#kt_datatable_fixed_columns").DataTable({
	scrollY:        "300px",
	scrollX:        true,
	scrollCollapse: true,
	fixedColumns:   {
		left: 2
	}
});