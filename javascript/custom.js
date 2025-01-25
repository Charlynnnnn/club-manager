// function toggleMenu() {
//     const sidePanel = document.querySelector('.side-panel');
//     sidePanel.style.display = (sidePanel.style.display === 'flex') ? 'none' : 'flex';

//     const toggleBtn = document.querySelector('.menu-toggle button');
//     toggleBtn.dataset.state = (sidePanel.style.display === 'flex') ? 'close' : 'open';
// }

// document.querySelector('.menu-toggle button').addEventListener('click', () => {
//     toggleMenu();
// });

// Initialize Bootstrap tabs
// var tabs = new bootstrap.Tab(document.getElementById('myTab'));
// tabs.show();

// DATA TABLE
$(document).ready(function() {
	//Only needed for the filename of export files.
	//Normally set in the title tag of your page.
	// document.title='Simple DataTable';
	// DataTable initialisation
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
    "scrollY": 420,
    "scrollX": true
});

$("#kt_datatable_horizontal_scroll").DataTable({
	"scrollY": 420,
    "scrollX": true
});




// Data Table Reuse

$('#myTabContent').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var targetPane = $(e.target).attr("href");
    // Load content for the targetPane dynamically here
});


// Filter Status

// FOR #RD DATA TABLE
$("#kt_datatable_fixed_columns").DataTable({
	scrollY:        "300px",
	scrollX:        true,
	scrollCollapse: true,
	fixedColumns:   {
		left: 2
	}
});


// Return button
function goBack() {
	window.history.back();
}