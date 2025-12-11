<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.0/css/all.min.css" integrity="sha512-3PN6gfRNZEX4YFyz+sIyTF6pGlQiryJu9NlGhu9LrLMQ7eDjNgudQoFDK3WSNAayeIKc6B8WXXpo4a7HqxjKwg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>

    <style>
        .pagination {
            display: flex !important;
            padding-left: 0;
            list-style: none;
            /* border-radius: 0.25rem; */
        }



        .pagination li a {
            color: #C9571A;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.79rem;
            /* border-radius: 0.25rem; */
            transition: background-color 0.3s ease;
            font-size: small;
        }

        .pagination li a:hover {
            background-color: #e9ecef;

        }

        .pagination li.active a {
            z-index: 1;
            color: #fff;
            background-color: #C9571A;
            border-color: #C9571A;
        }

        .pagination li.disabled a {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .pagination li:first-child a {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .pagination li:last-child a {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        #nav {
            float: right;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Certificates</h1>
        <div class="row mb-3">
            <div class="col-md-2">
                <div>
                    <label for="" class="form-label">Show</label>
                    <select class="form-control " name="" id="perPage" onchange="updateAndFetch()">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="">
                    <label for="" class="form-label">Control Number</label>
                    <input
                        type="text"
                        class="form-control"
                        name=""
                        id="controlNumber"
                        aria-describedby="helpId"
                        placeholder=""
                    />
                </div>
                
            </div>
            <div class="col-md-2">
                <div>
                    <label for="" class="form-label">Month</label>
                    <select class="form-control " name="" id="month">
                        <option value="">select month</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option  value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div>
                    <label for="" class="form-label">Year</label>
                    <select class="form-control " name="" id="year">
                        elect class="form-control " name="" id="month">
                        <option value="">select year</option>
                        <option value="2024">2024</option>

                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <br>
                <button type="button" class="btn btn-primary mt-2" onclick="fetchCertificates()">Filter</button>
            </div>


        </div>


        <div class="mb-4">
            <table class="table table-bordered table-hover table-sm" id="dataTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Certificate Number</th>
                        <th>Control Number</th>
                        <th>Products</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="certificates-list">

                </tbody>
            </table>
        </div>


        <div id="nav">


        </div>
    </div>


    <!-- Bootstrap JS (For modals, popovers, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variable to keep track of the current page
        let currentPage = 1;

        // Function to construct URL query parameters
        function createUrlWithParams(params) {
            const queryParams = new URLSearchParams(params);
            return `&${queryParams.toString()}`;
        }

        // Fetch certificates based on the current filters and pagination
        function fetchCertificates(page = 1) {
            // Update the current page when fetching new data
            currentPage = page;

            const perPage = document.querySelector("#perPage").value;
            const month = document.querySelector("#month").value;
            const year = document.querySelector("#year").value;
            const controlNumber = document.querySelector("#controlNumber").value;

            const params = {
                month,
                year,
                controlNumber
            };

            const url = `<?= base_url('getCertificates') ?>?page=${page}&perPage=${perPage}${createUrlWithParams(params)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Handle pagination controls
                    const {
                        links,
                        certificates
                    } = data;

                    if (links) {
                        document.querySelector("#nav").innerHTML = links;
                    }

                    // Populate certificates


                    document.getElementById('certificates-list').innerHTML = certificates;

                })
                .catch(error => {
                    console.error('Error fetching certificates:', error);
                });
        }

        // Function to fetch data while maintaining the current page
        function updateAndFetch() {
            fetchCertificates(currentPage); // Maintain the current page when filters are applied
        }

        // Function to get data from pagination links
        function getCertificatesData(page) {
            fetchCertificates(page); // Fetch data for a specific page
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Fetch certificates on initial page load
            fetchCertificates(1);


        });
    </script>


</body>

</html>