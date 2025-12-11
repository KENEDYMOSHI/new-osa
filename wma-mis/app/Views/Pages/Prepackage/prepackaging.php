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
<style>

</style>
<!-- Modal -->


<div class="container-fluid">
    <?= view('Components/ClientsBlock') ?>

</div>

<!-- <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="customerForm" name="customerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Name Of The Packer/Client</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name Of The Packer/Client" required>

                                </div>
                            </div>
                            <div class="col-md-6">

                                <label>Region</label>
                                <select id="region" name="region" class="form-control select2bs4" required>
                                    <?php foreach (renderRegions() as $region) : ?>
                                        <option value="<?= $region['region'] ?>"><?= $region['region'] ?></option>
                                    <?php endforeach; ?>
                                </select>



                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Location</label>
                                    <input type="text" name="location" id="location" class="form-control" placeholder="Location" required>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Physical Address</label>
                                    <input type="text" name="physicalAddress" id="physicalAddress" class="form-control" placeholder="Physical Address" required>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Postal Address</label>
                                    <input type="text" name="postalAddress" id="postalAddress" class="form-control postal" placeholder="Postal Address">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Postal code</label>
                                    <input type="text" name="postalCode" id="postalCode" class="form-control " placeholder="Postal Code">

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Phone Number</label>
                                    <input type="text" name="phoneNumber" id="phoneNumber" class="form-control phone" placeholder="Phone Number" required>

                                </div>
                            </div>

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div> -->

<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="searchForm" name="searchForm">
                    <div class="input-group">
                        <input class="form-control" name="keyword" type="text" placeholder="Search here..." id="keyword" required>
                        <button class="btn btn-success btn-flat" type="submit">Search</button>
                    </div>
                </form>

                <ul class="list-group mt-2" id="searchResults">



                </ul>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<!-- /.content-header -->
<div class="container-fluid">


    <div class="card">

        <?php if (url_is('prePackage')) : ?>


            <div class="card-header">

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-sm" onclick="openProductModal()">
                    <i class="fal fa-plus"></i> Add Product
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="getAllProducts()">
                    <i class="fal fa-hand-pointer"></i> Select Products
                </button>



            </div>
        <?php else : ?>
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" onclick="getCompleteProducts()">
                    <i class="fal fa-hand-eye"></i> View Products
                </button>
            </div>
        <?php endif; ?>


        <div class="card-body">

            <div class="form-group">
                <label for="">Products</label>
                <select class="form-control" name="" id="products" onchange="selectProduct(this.value)">



                </select>
            </div>
        </div>
        <div id="commodityInfo"></div>
    </div>

    <div class="container-fluid">
        <div class="modal fade" id="measurementSheet" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Data Observation Sheet And Recommendation For T1 And T2 Test
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="">Measurement Quantity</label>
                                <select class="form-control" name="" id="measurementOptions" onchange="javascript: $('#currentQuantity').val(this.value.slice(11).replace(/[^\d.-]/g, '')); $('#productQuantity').val(this.value.replace( /(<([^>]+)>)/ig, ''));$('#measurementSheetForm')[0].reset();checkQuantityId(this.value)">


                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" id="switch" hidden>
                                <input type="text" id="sampleSizeOptions" hidden>
                                <h6 id="dimensions"></h6>
                                <!-- <select class="form-control" name="" id="sampleSizeOptions" onchange="renderMeasurementData(this.value)">


                                    </select> -->
                            </div>

                        </div>
                        <div class="form-group col-md-12">
                            <input type="text" class="form-control" id="currentQuantity" hidden>
                            <input type="text" class="form-control" id="productQuantity" hidden>
                        </div>

                        <form id="measurementSheetForm">
                            <table class="table">
                                <div id="dataSheetTable">

                                </div>

                            </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                            Save
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>



<!-- Button trigger modal -->
<!-- <button type=" button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#measurementSheet">
            Launch
        </button> -->

<!-- Modal -->
<div class="modal fade" id="measurementsPreview" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Measurement Sheet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>

                    <table class="table table-bordered table-sm" id='measurementPreviewTable'>



                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<!-- visible only for add prepackage page -->
<?php if (url_is('prePackage')) : ?>



    <div class="container-fluid">
        <div class="card">
            <div class="card-header">Billing</div>
            <div class="card-body">
                <form id="billingForm" name="billingForm">
                    <div class="row">
                        <div class="col-md-2 ">
                            <label>Inspection Type</label><br>
                            <div class="icheck-primary d-inline ">
                                <input class="form-check-input type" name="typ" id="Local" type="radio" value="Local" checked onchange="javascript: $('#activityType').prop('selectedIndex', 0)">

                                <label class="form-check-label" for="Local">Local</label>
                            </div>
                            <div class="icheck-primary d-inline px-2">
                                <input class="form-check-input type" name="typ" id="Imported" type="radio" value="Imported" onchange="javascript: $('#activityType').prop('selectedIndex', 0)">

                                <label class="form-check-label" for="Imported"> Imported</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Task /Activity </label>
                            <select class="form-control" name="activityType" id="activityType" required onchange="triggerActivity(this.value)">
                                <option selected disabled>--Select Activity--</option>
                                <option value="Verification">Initial (Inspection)</option>
                                <option value="Reverification">Inspection</option>
                                <option value="Inspection">Market Surveillance</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                        <div class="icheck-primary d-inline">
                                <input class="form-check-input label-check" name="hasPenalty" id="fine" type="checkbox" onchange="toggleFine(this)">

                                <label class="form-check-label" for="fine">Include Fine & Penalty</label>
                            </div>
                        </div>

                    </div>
                    <div class="invoice mb-3">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <!-- <th>Hash</th> -->
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="productsTable">



                            </tbody>
                        </table>
                    </div>
                    <br>
                    <!-- <button class="btn btn-primary btn-sm mb-1" id="calcBtn" onclick="calculateTotal()">Calculate Total</button> -->


                    <div class="col-sm-6" id="paymentCheck" style="display:block">
                        <!-- checkbox -->
                        <div class="form-group clearfix">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="togglePayment">
                                <label for="togglePayment">
                                    Charge
                                </label>
                            </div>


                        </div>
                    </div>

                    <div id="payment" style="display:block">
                        <div class="form-group">

                            <label for="">Amount</label>
                            <input type="text" name="totalAmount" id="totalAmount" class="form-control" placeholder="" readonly>

                        </div>

                    </div>


            </div>

        </div>
        <div class="card" id="billBlock" style="display: none;">

            <div class="card-header">BILL DETAILS</div>
            <div class="card-body">
                <?= view('Components/billOptions') ?>
            </div>
            <div class="card-footer">
                <button type="submit" id="submit" class="btn btn-primary btn-sm ">
                    <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;">
                    </div>
                    Submit
                </button>
            </div>
            </form>
        </div>
    </div>


<?php endif; ?>



<!-- Modal -->


<div class="container-fluid">

    <div class="card">
        <div class="card-body">
            <div id="prePackageSummary">

            </div>




        </div>
        <?php if (url_is('registeredPrepackages/' . $user->collection_center)) : ?>
            <div class="card-footer">
                <a id="downloadBtn" target="_blank" class="btn btn-primary btn-sm"><i class="fal fa-download "></i> Download</a>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#printModal">
        Launch
    </button> -->

<!-- Modal -->

<!-- including receipt modal -->





<?= view('Components/bill') ?>
<?= view('Components/PrePackage/productForm') ?>



</div>

<script>
    function openProductModal() {
        const id = document.querySelector('#customerId')
        if (!id) {
            swal({
                title: 'Please Select Customer First',
                icon: "warning",
                timer: 3500
            });
        } else {

            $('#productModal').modal('show')
        }
    }
</script>
<script>
    // const printBtn = document.querySelector('#printBtn')
    // printBtn.addEventListener('click', () => {
    //     window.print()
    // })



 function toggleFine(){

    function toggleFine(box) {
   
   const penaltyBlock = document.querySelector('#penaltyBlock');
   if (box.checked == true) {
       penaltyBlock.style.display = 'block';
   } else {
       document.querySelector('#penaltyAmount').value = '';
       penaltyBlock.style.display = 'none';
   }
}
    let itemId = 3
    let hash = 'w4367ry'

  let  fine = `
<tr id='fine'>
    <td>
    <input   type="text" name="customerHash[]" id="customerHash[]" value="${hash}" class="form-control mb-1" hidden>
    <input   type="text" name="prodId[]" id="prodId[]" value="${itemId}" class="form-control mb-1" hidden>
    </td>
    <td>
    Fine
    </td>
    <td>
    <input   type="text" name="prodMount[]" id="prodMount[]"  class="form-control mb-1" >
    </td>
</tr>
`
$('#productsTable').append(fine)

}

 






    const quantity = document.querySelector('#quantity').value
    // const unit = document.querySelector('#unit').value
    // const calculatedTare = document.querySelector('#calculatedTare')









    <?php if (url_is('prePackage')) : ?>


        function loading() {
            document.querySelector('#spinner').style.display = 'inline-block'
            document.querySelector('#submit').classList.add('disabled')

        }

        //remove loading animation
        function done() {

            document.querySelector('#spinner').style.display = 'none'
            document.querySelector('#submit').classList.remove('disabled')
        }



        const billingForm = document.querySelector('#billingForm')
        billingForm.addEventListener('submit', function(e) {

            e.preventDefault()



            const billingData = new FormData(billingForm)

            // console.log(billingData);
            $.ajax({
                type: "POST",
                url: 'createBill',
                data: billingData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function(xhr) {
                    submitInProgress(e.submitter)
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },

                success: function(response) {
                    console.log(response);
                    document.querySelector('.token').value = response.token

                    submitDone(e.submitter)
                    if (response.status == 1) {
                        $('#billingForm')[0].reset()
                        printBill(response)


                        // swal({
                        //     title: response.msg,
                        //     icon: "success",
                        // });
                    } else {
                        swal({
                            title: response.msg,
                            icon: "warning",
                            timer: 4500
                        });


                    }



                },
                error: function(err) {
                    // console.log(err);
                }

            });

        })





        const togglePayment = document.querySelector('#togglePayment')
        togglePayment.addEventListener('change', () => {
            const amountElement = document.querySelector('#totalAmount')
            const controlNumberElement = document.querySelector('#controlNumber')
            const payment = document.querySelector('#payment')
            if (togglePayment.checked) {
                payment.style.display = 'block'
            } else {
                payment.style.display = 'none'
                amountElement.value = ''
                controlNumberElement.value = ''



            }
        })



    <?php endif; ?>

    function changeTransfer(method) {
        const swiftCode = document.querySelector('#swiftCode')
        if (method == 'BankTransfer') {
            swiftCode.removeAttribute('disabled')
        } else {
            swiftCode.setAttribute('disabled', 'disabled')

        }
    }

    function calculateDate(days) {
        const date = new Date();

        date.setDate(date.getDate() + Number(days));
        const expiryDate = `${date.getDate()}-${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()}`

        document.querySelector('#expiryDate').value = expiryDate
    }


    function printBill(billData) {
        const {
            status,
            bill,
            heading,
            qrCodeObject,
            token,

        } = billData
        // console.log(qrCodeObject)
        // console.log(heading)
        // console.log(token)

        const qrCode = new QRCodeStyling({

            width: 200,
            height: 200,
            type: "svg",
            data: JSON.stringify(qrCodeObject),
            image: "<?= base_url('assets/images/emblem.png') ?>",
            dotsOptions: {
                color: "#333333",
                type: "square"
            },
            backgroundOptions: {
                color: "#ffffff",
            },
            imageOptions: {
                crossOrigin: "anonymous",
                margin: 0,
                imageSize: 0.2
            }
        });

        console.log(bill)
        document.querySelector('#heading').textContent = heading
        document.querySelector('#billDetails').innerHTML = ''
        document.querySelector('#billDetails').innerHTML = bill
        qrCode.append(document.getElementById("canvas"));
        $('#printModal').modal({
            open: true,
            backdrop: 'static'
        })



    }











    $('#measurement').hide();
    $('#mthd').attr('class', 'col-md-4');
    $('#batch').attr('class', 'col-md-4');
















    //console.log('Percent: ' + percentage(10, 50000) + '%');
</script>



<?= $this->endSection(); ?>