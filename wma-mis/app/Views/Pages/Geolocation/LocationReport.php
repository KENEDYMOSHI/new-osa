<?= $this->extend('Layouts/coreLayout'); ?>

<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content body">
    <!-- Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">GEO LOCATION</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mapBox">
                        <div id="map"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary btn-sm">Save</button> -->
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form id="locationForm" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" name="name" id="" class="form-control" placeholder="" aria-describedby="helpId">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Activity</label>
                            <select class="form-control select2bs4" name="activity" id="">
                                <option value="" selected>--Select Activity--</option>
                                <?php foreach(gfscodes() as $code => $name): ?>
                                    <option value="<?=$code ?>"><?=$name ?></option>
                                <?php endforeach; ?>

                                
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Collection Center</label>
                            <select class="form-control select2bs4" name="collectionCenter" id="">
                                <option value="" selected>--Select Collection Center--</option>
                                <?php foreach (collectionCenters() as $center) : ?>
                                    <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Year</label>
                            <select class="form-control select2bs4" name="year" id="">
                                <option value="" selected>--Select Year--</option>
                                <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>


                            </select>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm " style="margin-top: 1.8rem;"><i class="fal fa-filter"></i> Filter</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <!-- <pre>
                    <?php print_r($params) ?>
                </pre>
                <h2><?=count([]) ?></h2> -->
                <!-- <pre>
                    <?php print_r($sbl) ?>
                </pre> -->
                <table class="table table-sm" id="locationTable" style="width:100%">
                   <?=$customers ?>
                </table>



            </div>
        </div>

    </div>
    <script>

        const tableOptions = {
                responsive: true, // Enable responsiveness
                lengthMenu: [
                    [10, 25, 35, 50, -1],
                    ['10 rows', '25 rows', '35 rows', '50 rows', 'Show all']
                ],
                dom: 'Bfrtip', // Add export buttons 134 949 770
                buttons: [{
                        extend: 'pageLength',

                    }, // Export to Excel
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }, // Export to Excel
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }, // Export to PDF
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    } // Export to CSV
                ],
            }
        $(document).ready(function() {
            $('#locationTable').DataTable(tableOptions);
        });


        const locationForm = document.querySelector('#locationForm')
        locationForm.addEventListener('submit', e => {
            e.preventDefault()
            const formData = new FormData(locationForm)
            fetch('filterLocationData', {
                    method: 'POST',
                    headers: {
                        // 'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: formData,

                }).then(response => response.json())
                .then(data => {
                    console.log(data.params)
                    console.log(data.customer)
                    const {
                        token,
                        tableData
                    } = data
                    document.querySelector('.token').value = token
                    document.querySelector('#locationTable').innerHTML = tableData
                    let table = $('#locationTable').DataTable();

                    // Destroy the DataTable
                    table.destroy();

                    // Remove the table
                    $('#locationTable').empty();

                    // Add a new table with updated `thead`
                    $('#locationTable').html(tableData);

                    // Re-initialize the DataTable with updated `thead`
                    table = $('#locationTable').DataTable(tableOptions);
                   
                })
        })


        //get location data and render a google map
        function openMap(latitude, longitude, name) {
            // console.log(latitude)
            // console.log(longitude)
            $('#mapModal').modal('show')
            let map;

            async function initMap() {
                const position = {
                    lat: parseFloat(latitude),
                    lng: parseFloat(longitude)
                };
                // Request needed libraries.
                //@ts-ignore
                const {
                    Map
                } = await google.maps.importLibrary("maps");
                const {
                    AdvancedMarkerView
                } = await google.maps.importLibrary(
                    "marker"
                );

                // The map, centered at 
                map = new Map(document.getElementById("map"), {
                    zoom: 15,
                    center: position,
                    mapId: '2023_WMA'
                });

                // The marker, positioned at 
                const marker = new AdvancedMarkerView({
                    map: map,
                    position: position,
                    title: name,
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: name,
                    position: position,
                });

                // Open the InfoWindow on the marker
                infoWindow.open(map, marker);

                // Create a div for the location name box
                const locationBox = document.createElement("div");
                locationBox.classList.add("location-box");
                locationBox.innerHTML = name;

                // Add the location name box to the map
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(locationBox);
            }

            initMap();
        }
    </script>
</section>

<?= $this->endSection(); ?>