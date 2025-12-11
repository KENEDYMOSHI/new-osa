$(function() {
    let data = [];
    // const packages = [ 0, 1, 2,3,4, 5,6,7,8,9,10,11];
    const lorries = [ 0, 1, 2,3,4, 5,6,7,8,9,10,11,12,13];
    
    $("#example1").DataTable({
        "responsive": true,
        "autoWidth": false,
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        //"autoWidth": true, // Adds A tiny Blue btn
        "responsive": true,
        dom: 'Bfrtip',

        buttons: [
            // 'copy', 'csv', 'excel', 'print',
           
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [ 0, ':visible' ],
                    columns: packages
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible',
                    columns: packages
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',

                exportOptions: {
                    columns: packages
                }
            },
            'csv','print',
            // 'colvis'
        ]

       

    });


    //===============lorries=============
    $("#lorries").DataTable({
        "responsive": true,
        "autoWidth": false,
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        //"autoWidth": true, // Adds A tiny Blue btn
        "responsive": true,
        dom: 'Bfrtip',

        buttons: [
            // 'copy', 'csv', 'excel', 'print',
           
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [ 0, ':visible' ],
                    columns: lorries
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible',
                    columns: lorries
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',

                exportOptions: {
                    columns: lorries
                }
            },
            'csv','print',
            // 'colvis'
        ]

       

    });
    
    
});