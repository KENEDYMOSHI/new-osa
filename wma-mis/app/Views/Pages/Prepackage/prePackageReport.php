<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"></h1> -->
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- Modal -->

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <?php if ($user->inGroup('admin', 'superadmin')) : ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Region</label>
                            <select id="region" name="region" class="form-control select2bs4">
                                <?php foreach (renderRegions() as $region) : ?>
                                    <option value="<?= $region['region'] ?>"><?= $region['region'] ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                    </div>

                <?php endif; ?>
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary" style="margin-top: 1.74rem;" onclick="generateReport()">Generate</button>
                </div>
            </div>
        </div>

        <style>
            .dark {
                background: #333;
                color: #fff;

            }

            .dark th {
                padding: 7px 5px;
            }
        </style>

        <div class="card-body">
            <div id="dataReport"></div>



        </div>
        <div class="card-footer">
            <a href="" id="downloadBtn" target="_blank" class="btn btn-primary btn-sm">Download</a>
        </div>
    </div>

</div>

</div>

<script>
    function generateReport() {

        params = {
            region: '<?=$user->collection_center ?>'
        }
        $.ajax({
            type: "POST",
            url: '<?= base_url() ?>generatePrepackageReport',
            data: params,

            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
            },

            success: function(response) {

                //console.log(response.data.length);
                console.log(response.data);
                // const productData = response.data.customer
                document.querySelector('.token').value = response.token
                // console.log(productData);
                if (response.status == 0) {
                    document.querySelector('#downloadBtn').setAttribute('disabled', 'disabled')
                    swal({
                        title: 'No Data Found',
                        icon: "warning",
                        timer: 2500
                    });
                } else {
                    document.querySelector('#downloadBtn').removeAttribute('disabled', 'disabled')
                }


                function renderProducts(product) {
                    let tr = ` `
                    product.forEach(product => {

                        console.log(product.commodity);
                        tr += `
                      <tr>
                        <td>${product.commodity}</td>
                      </tr>
                     `
                    })

                    return tr
                }

                function renderProductStatus(product) {
                    let tr = ` `
                    product.forEach(product => {


                        tr += `
                      <tr>
                        <td>${product.status}</td>
                      </tr>
                     `
                    })

                    return tr
                }

                function renderControlNumber(product) {
                    let tr = ` `
                    product.forEach(product => {


                        tr += `
                      <tr>
                        <td>${product.controlNumber}</td>
                      </tr>
                     `
                    })

                    return tr
                }

                function renderProductFee(product) {
                    let tr = ` `
                    product.forEach(product => {


                        tr += `
                      <tr>
                        <td>Tsh${product.amount}</td>
                      </tr>
                     `
                    })

                    return tr
                }




                response.data.forEach(data => {






                    table += `
                <tr>
                    
                    <td>${data.date}</td>
                    <td>${data.customer}</td>
                    <td>${data.region}</td>
                    <td>${data.location}</td>
                    <td>

                        <table cellspacing="0" border="1" style="width: 100%;">
                           ${renderProducts(data.productData)}
                        </table>

                    </td>
                    <td>

                        <table cellspacing="0" border="1" style="width: 100%;">
                            ${renderProductStatus(data.productData)}

                        </table>

                    </td>
                    <td>

                        <table cellspacing="0" border="1" style="width: 100%;">
                             ${renderProductFee(data.productData)}
                        </table>

                    </td>

                    <td>
                  <table cellspacing="0" border="1" style="width: 100%;">
                             ${renderControlNumber(data.productData)}
                        </table>
                    </td>
                    <td> - </td>
                </tr>

                `;

                });

                table += `
               </tbody>
        </table>
            `

                $('#dataReport').html(table)


            },
            error: function(err) {
                console.log(err);
            }

        });
    }
</script>




<?php $x = true; ?>




<!-- <script>
    (function($) {
        $(document).ready(function() {
            function generateReport() {

                let params
                let region
                <?php if ($x) : ?>
                    region = document.querySelector('#region').value

                    params = {
                        region: region
                    }
                <?php else : ?>
                    region = '<?= $region ?>';
                    params = {
                        region: region
                    }
                <?php endif; ?>

                const download = document.querySelector('#downloadBtn')

                const link = `<?= base_url() ?>/downloadPrepackageReport/${region}`

                download.setAttribute('href', link)


                let table = `
                <table cellspacing="0" border="1" style="width: 100%;">
                        <!-- <table class="table1  table-bordered1" border="1"> -->
<thead class="dark">
    <tr>
        <th>Date</th>
        <th>Name Of Client</th>
        <th>Region</th>
        <th>Location</th>
        <th>Product</th>
        <th>Results</th>
        <th>Fees</th>
        <th>Control Number</th>
        <th>Measures Taken</th>
    </tr>
</thead>
<tbody>
    `;


    }

    });
    })(jQuery);
    </script> -->

    <?= $this->endSection(); ?>