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

<?php if ($role == 1) : ?>


    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
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
    </div>

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
    <div class="container">

        <div class="card">
            <div class="card-header">

                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#searchModal">
                    <i class="fal fa-search"></i> Search Customer
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                    <i class="fal fa-plus"></i> Add Customer
                </button>


            </div>
            <div class="card-body">
                <input class="form-control mb-1" type="text" id="customerId" name="customerId" hidden>
                <table class="table table-sm">

                    <tbody id="customerInfo">


                    </tbody>
                </table>
            </div>
            <!-- <div class="card-footer text-muted">
            Footer
        </div> -->
        </div>
        <div class="card">
            <div class="card-header">

                <!-- Button trigger modal -->
                <button style="float: right;" type="button" class="btn btn-primary btn-sm" onclick="openProductModal()">
                    <i class="fal fa-plus"></i> Add Product
                </button>

                <!-- Modal -->
                <!-- product modal -->
                <!-- product modal -->
            </div>


            <div class="card-body">
                <div class="form-group">
                    <label for="">Products</label>
                    <select class="form-control" name="" id="products" onchange="selectProduct(this.value)">



                    </select>
                </div>
            </div>
            <div id="commodityInfo"></div>
        </div>

        <div class="container">
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
                                <div class="form-group col-md-6">
                                    <label for="">Measurement Quantity</label>
                                    <select class="form-control" name="" id="measurementOptions" onchange="javascript: $('#currentQuantity').val(this.value.replace(/[^\d.-]/g, '')); $('#productQuantity').val(this.value);$('#dataSheetForm')[0].reset();">


                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Sample Size</label>
                                    <select class="form-control" name="" id="sampleSizeOptions" onchange="renderMeasurementData(this.value)">


                                    </select>
                                </div>

                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" class="form-control" id="currentQuantity" hidden>
                                <input type="text" class="form-control" id="productQuantity" hidden>
                            </div>

                            <form id="dataSheetForm" name="dataSheetForm">

                                <table class="table">
                                    <div id="dataSheetTable">

                                    </div>

                                </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm" onclick="">Save</button>
                            </form>
                        </div>
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

                        <table class="table table-bordered table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>S./No</th>
                                    <th>Measured Gross Quantity</th>
                                    <th>Net Quantity</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody id="measurementTable">


                            </tbody>
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

    <div class="container">
        <div class="card">
            <div class="card-header">Billing</div>
            <div class="card-body">
                <form id="billingForm" name="billingForm">
                    <div class="form-group">
                        <label for="">Task /Activity </label>
                        <select class="form-control" name="activityType" id="activityType" required onchange="triggerActivity(this.value)">
                            <option selected disabled>--Select Activity--</option>
                            <option value="Verification">Initial (Inspection)</option>
                            <option value="Reverification">Inspection</option>
                            <option value="Inspection">Market Surveillance</option>
                        </select>
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


                    <div class="col-sm-6" id="paymentCheck" style="display:none">
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

                    <div id="payment" style="display:none">
                        <div class="form-group">

                            <label for="">Amount</label>
                            <input type="text" name="totalAmount" id="totalAmount" class="form-control" placeholder="" readonly>

                        </div>
                        <?= $this->include('Components/controlNumber') ?>
                    </div>


            </div>
            <div class="card-footer">
                <button type="submit" style="float: right;" class="btn btn-primary btn-sm">Save</button>
            </div>
            </form>
        </div>
    </div>






    <!-- Modal -->
    <div class="container">

        <div class="card">
            <div class="card-body">
                <div id="prePackageSummary">

                </div>




            </div>
        </div>
    </div>


    <?= $this->include('components/prepackage/productForm') ?>
    <?= csrf_field() ?>



    </div>



    <script>
        //data-target="#productModal" onclick="openProductModal()"

        function createMeasurements(sampleSize) {
            $('#measurementSheet').modal('show')
            $('#sampleSizeOptions').html(`
            
             <option selected disabled> --Select Sample Size--</option>
             <option value="${sampleSize}">${sampleSize}</option>
            `)
            console.log(sampleSize)

        }


        function openProductModal() {
            const id = document.querySelector('#customerId').value
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
        const quantity = document.querySelector('#quantity').value
        // const unit = document.querySelector('#unit').value
        // const calculatedTare = document.querySelector('#calculatedTare')




        function productBillSmallLot(lot) {
            if (lot > 0 && lot <= 100) {
                return 30000;
            } else if (lot >= 101 && lot <= 500) {
                return 100000;
            } else if (lot >= 501 && lot <= 3200) {
                return 100000;
            }
        }

        function productBillFirstFiveLargeLot(lot) {
            if (lot > 3200) {
                return 300000;
            }
        }

        function productBillSecondFiveLargeLot(lot) {
            if (lot > 3200) {
                return 200000;
            }
        }

        function productBillMoreThanTenLot(lot) {
            if (lot > 3200) {
                return 100000;
            }
        }



        function triggerActivity(activity) {
            const totalAmountInput = document.querySelector('#totalAmount')
            totalAmountInput.value = ''

            if (activity == 'Inspection') {
                document.querySelector('#paymentCheck').style.display = 'block'
                document.querySelector('#payment').style.display = 'none'
                totalAmountInput.removeAttribute('readonly', 'readonly')
            } else {
                totalAmountInput.setAttribute('readonly', 'readonly')
                document.querySelector('#paymentCheck').style.display = 'none'
                document.querySelector('#payment').style.display = 'block'
            }
            const customerId = document.querySelector('#customerId').value
            // const activity = 'Inspection'

            function formatNumber(val) {
                let formatted = new Intl.NumberFormat('en-Us')
                return formatted.format(val)
            }

            $.ajax({
                type: "POST",
                url: "getProductsWithMeasurements",
                data: {
                    customerId: customerId,
                    activity: activity
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);

                    let amount = 0
                    if (response.products != '') {
                        //get all lot sizes
                        const lotSizes = response.products.map(product => {
                            // return Number(product.lot)
                            return product

                        })
                        //get less than 3200 lot size products
                        // const smallLot = lotSizes.filter((lot) => {
                        //     return lot.lot <= 3200;

                        // });
                        const smallLotSizes = response.products.filter((product) => {
                            return product.lot <= 3201


                        }).map(product => {
                            return {
                                id: product.id,
                                commodity: product.commodity,
                                hash: product.hash,
                                lot: product.lot,
                                result: product.result,
                            }
                        })





                        //get more than 3200 lot size products
                        const largeLotSizes = response.products.filter((product) => {
                            return product.lot >= 3201



                        }).map(product => {
                            return {
                                id: product.id,
                                commodity: product.commodity,
                                hash: product.hash,
                                lot: product.lot,
                                result: product.result,
                            }
                        })

                        //get all products with less than 3200 lot sizes

                        const allSmallLot = smallLotSizes
                            .map((product) => {

                                let amount = productBillSmallLot(product.lot)
                                return {
                                    amount: product.result == 'Pass' ? amount : amount * 2,
                                    id: product.id,
                                    commodity: product.commodity,
                                    hash: product.hash,
                                    lot: product.lot,
                                    result: product.result,
                                }



                            })

                        // get the first five in greater than 3200   lot sizes
                        const firstFiveLot = largeLotSizes
                            .map((product) => {

                                let amount = productBillFirstFiveLargeLot(product.lot)
                                return {
                                    amount: product.result == 'Pass' ? amount : amount * 2,
                                    id: product.id,
                                    commodity: product.commodity,
                                    hash: product.hash,
                                    lot: product.lot,
                                    result: product.result,
                                }



                            }).splice(0, 5);
                        // // get the second five in greater than 3200   lot sizes
                        const secondFiveLot = largeLotSizes
                            .map((product) => {

                                let amount = productBillSecondFiveLargeLot(product.lot)
                                return {
                                    amount: product.result == 'Pass' ? amount : amount * 2,
                                    id: product.id,
                                    commodity: product.commodity,
                                    hash: product.hash,
                                    lot: product.lot,
                                    result: product.result,
                                }


                            }).splice(5, 5);
                        // // get more than 10 in greater than 3200   lot sizes
                        const moreThanTenLot = largeLotSizes
                            .map((product) => {

                                let amount = productBillMoreThanTenLot(product.lot)
                                return {
                                    amount: product.result == 'Pass' ? amount : amount * 2,
                                    id: product.id,
                                    commodity: product.commodity,
                                    hash: product.hash,
                                    lot: product.lot,
                                    result: product.result,
                                }

                            }).splice(10);




                        // console.log(largeLotSizes)
                        // console.log('first large five')
                        // console.log(firstFiveLot)
                        // console.log('second large five')
                        // console.log(secondFiveLot)

                        // console.log('Large lot Sizes')
                        // console.log(largeLotSizes)

                        // console.log('Small ')
                        // console.log(allSmallLot)

                        // console.log('More than 10 ')
                        // console.log(moreThanTenLot)


                        const theProducts = firstFiveLot.concat(secondFiveLot, moreThanTenLot, allSmallLot)
                        // // xx.push(firstFiveLot)
                        // // xx.push(secondFiveLot)

                        const total = theProducts.map(product => {
                            return product.amount
                        }).reduce((x, y) => {
                            return x + y
                        }, 0)



                        document.querySelector('#totalAmount').value = activity == 'Inspection' ? 0 : total

                        let productsTable = ``
                        theProducts.forEach(product => {
                            // console.log(product) v7;

                            productsTable += `

                            <tr>
                            
                            <td>
                             <input   type="text" name="customerHash[]" id="customerHash[]" value="${product.hash}" class="form-control mb-1" hidden>
                            ${product.commodity} 
                             <input   type="text" name="prodId[]" id="prodId[]" value="${product.id}" class="form-control mb-1" hidden>
                            </td>
                            <td>
                            ${product.result}
                            </td>
                            <td>
                            <input   type="text" name="prodMount[]" id="prodMount[]" value="${product.amount}" class="form-control mb-1"" hidden>
                           Tsh ${formatNumber(product.amount)}
                            </td>
                             
                            </tr>
                   
                       
                    
                    `

                        });

                        $('#productsTable').html('')
                        $('#productsTable').html(productsTable)

                        /*

 <input   type="text" name="customerHash[]" id="prodId[]" value="${product.hash}" class="form-control mb-1">
                        <input   type="text" name="prodId[]" id="prodId[]" value="${product.id}" class="form-control mb-1">
                        <input   type="text" name="prodMount[]" id="prodMount[]" value="${product.amount}" class="form-control mb-1"">
                        */

                        // console.log(LotSizes);
                        // console.log('Large ' + largeLotSizes.length);
                        // console.log(amount);
                        // console.log(prods);
                    } else {
                        $('#productsTable').html('')
                        swal({
                            title: 'No Data Found',
                            icon: "warning",
                            timer: 2500
                        });

                    }


                }
            });

            let dataArray = []
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

                success: function(response) {
                    console.log(response);

                    if (response.status == 1) {
                        $('#billingForm')[0].reset()



                        swal({
                            title: response.msg,
                            icon: "success",
                        });
                    } else {
                        swal({
                            title: 'Something Went Wrong',
                            icon: "warning",
                            timer: 3500
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





        function openModal() {
            const nominalQuantity = unitConverter(unit, quantity)
            document.querySelector('#calculatedTare').value = ''
            $('#tareModal').modal('show')
        }
        //percentage calculator
        function percentage(percent, theValue) {
            return (percent / 100) * theValue
        }




        //calculating standard deviation
        const standardDeviation = (arr, usePopulation = false) => {
            const mean = arr.reduce((acc, val) => acc + val, 0) / arr.length;
            return Math.sqrt(
                arr
                .reduce((acc, val) => acc.concat((val - mean) ** 2), [])
                .reduce((acc, val) => acc + val, 0) /
                (arr.length - (usePopulation ? 0 : 1))
            );
        };

        //metric unit converter

        function unitConverter(unit, value) {
            switch (unit) {
                case 'mg':

                    return value / 1000

                    break;
                case 'g':



                    return +value

                    break;
                case 'kg':

                    return value * 1000

                    break;
                case 'L':

                    return value * 1000

                    break;
                case 'mL':

                    return +value

                    break;
                case 'm':

                    return value * 1000

                    break;
                case 'sqCm':
                case 'sqM':
                    return value

                default:
                    break;
            }
        }

        function calcGross(unit) {
            const qty = document.querySelector('#quantity').value
            const grossQty = unitConverter(unit, qty)
            document.querySelector('#grossWeightValue').value = grossQty

            //console.log(grossQty);
        }

        //function to calculate tolerable deficiency of the nominal quantity

        function tolerableDeficiency(nQ) {
            const categoryOfAnalysis = document.querySelector('#categoryOfAnalysis').value
            switch (categoryOfAnalysis) {
                case 'General':
                    if (nQ == 0 && nQ <= 49) {
                        return percentage(9, nQ)
                    } else if (nQ >= 50 && nQ <= 99) {
                        return 4.5
                    } else if (nQ >= 100 && nQ <= 199) {
                        return percentage(4.5, nQ)
                    } else if (nQ >= 200 && nQ <= 299) {
                        return 9
                    } else if (nQ >= 300 && nQ <= 499) {
                        return percentage(3, nQ)
                    } else if (nQ >= 500 && nQ <= 999) {
                        return 15
                    } else if (nQ >= 1000 && nQ <= 9999) {
                        return percentage(1.5, nQ)
                    } else if (nQ >= 10000 && nQ <= 14999) {
                        return 150
                    } else if (nQ > 15000) {
                        return percentage(1, nQ)
                    }
                    break;
                case 'Linear':
                case 'Linear 2':
                    if (nQ < 5000) {
                        return percentage(0, nQ)
                    } else if (nQ > 5000) {
                        return percentage(2, nQ)
                    }
                    break;
                case 'Area':
                    return percentage(3, nQ)


                default:
                    break;
            }

        }

        $('#measurement').hide();
        $('#mthd').attr('class', 'col-md-4');
        $('#batch').attr('class', 'col-md-4');
        // selecting dom elements
        const customerForm = document.querySelector('#customerForm')
        const searchForm = document.querySelector('#searchForm')


        // ajax call method
        function ajaxCall(data, boolOpt) {
            $.ajax({
                type: "POST",
                url: data.url,
                data: data.formData,
                cache: boolOpt,
                processData: boolOpt,
                contentType: boolOpt,
                dataType: "json",

                success: function(response) {
                    switch (response.action) {
                        case 'search':
                            renderSearchResults(response)
                            break;
                        case 'save':
                            // console.log(response);
                            $('#addModal').modal('hide')
                            $('#customerForm')[0].reset();
                            getCustomerInfo(response.hash)

                            break;

                        default:
                            break;
                    }



                },
                error: function(err) {
                    // console.log(err);
                }

            });
        }


        // rendering search results
        function renderSearchResults(res) {
            // console.log(res);

            let list = ``
            if (res.data == '') {
                $('#searchResults').html('<h5>No Match Found!</h5>');
                //  console.log('no match');
            } else {
                res.data.forEach(customer => {
                    list += `
           <li onclick="getCustomerInfo('${customer.hash}')" class="list-group-item d-flex justify-content-between align-items-center " style="cursor:pointer">
             ${customer.name}|  ${customer.physical_address} |  ${customer.phone_number}
           </li>
          `
                });
            }






            $('#searchResults').html('')
            $('#searchResults').append(list)


        }


        function calculatePrice(lots) {
            function lotCalculator(lot) {
                if (lot > 0 && lot <= 100) {
                    return 100000
                } else if (lot > 101 && lot <= 500) {

                }
            }


            //(lots);
        }


        //get customer info based on hash value
        function getCustomerInfo(hash) {

            // let lotSizes = []



            $.ajax({
                type: "post",
                url: "editPrePackageCustomer",
                data: {
                    hash: hash
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    $('#searchModal').modal('hide')

                    let lotSizes = response.products.map(product => {
                        return product.lot
                    })

                    calculatePrice(lotSizes)



                    let products = ``
                    products += '<option selected disabled>--Products--</option>'
                    const customer = response.data
                    document.querySelector('#customerId').value = customer.hash
                    // console.log(response.products);

                    response.products.forEach(product => {
                        products += `
                    
                    <option value="${product.id}">${product.commodity}  ${product.quantity} ${product.unit}</option>`

                    })

                    $('#products').html(products)



                    $('#customerInfo').html(`
               
                  <tr>
                        <td> <b>Name</b></td>
                        <td>${customer.name}</td>
                    </tr>
                    <tr>
                        <td> <b>Physical Address </b></td>
                        <td>${customer.physical_address}</td>
                    </tr>
                    <tr>
                        <td><b>Postal Address</b></td>
                        <td>${customer.postal_address}</td>
                    </tr>
                    <tr>
                        <td><b>Postal Code</b></td>
                        <td>${customer.postal_code}</td>
                    </tr>
                    <tr>
                        <td><b>Phone Number</b></td>
                        <td>${customer.phone_number}</td>
                    </tr>


                `)
                }
            });
        }

        //saving customer info
        $('#customerForm').validate()
        customerForm.addEventListener('submit', (e) => {
            e.preventDefault()
            const data = {
                url: 'addPrePackageCustomer',
                formData: new FormData(customerForm)
            }

            if ($('#customerForm').valid()) {

                ajaxCall(data, false)
            }



        })


        // searching customer
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault()
            const data = {
                url: 'searchPrePackageCustomer',
                formData: new FormData(searchForm)
            }
            ajaxCall(data, false)



        })




        function calculateSampling(lot) {
            if (lot < 100) {
                $('#batchSize').css('border', '1px solid red')
            } else {
                $('#batchSize').css('border', '1px solid green')
            }
        }

        /// render inputs for samples
        function renderInputs(size) {

            let inputs = ``
            for (let index = 1; index <= size; index++) {
                inputs += `
            <div class="form-group col-md-4">
            <label>Sample ${index}:</label>
            <input type="number" required name="tareSampleSize" id="tareSampleSize" class="form-control weight" min="0" oninput="calcAverage()">
            </div>
            `

            }
            $('#weights').html('')
            $('#weights').append(inputs)


        }


        //console.log('Percent: ' + percentage(10, 50000) + '%');



        function calcAverage() {
            const calculatedTare = document.querySelector('#calculatedTare')
            const inputs = document.querySelectorAll('.weight')
            const quantity = document.querySelector('#quantity').value
            const unit = document.querySelector('#unit').value
            let total = 0
            let tareWeight = 0
            let nominalQuantity = unitConverter(unit, quantity)




            //check if the tare samples are empty
            inputs.forEach(input => {
                total += +input.value

                if (input.value == '') {
                    input.style.border = '1px solid red'
                    calculatedTare.value = ''
                    document.querySelector('#tareApprover').setAttribute('disabled', 'disabled')
                } else {
                    input.style.border = '1px solid  #ced4da'
                    // calculatedTare.value = 100
                    document.querySelector('#tareApprover').setAttribute('disabled', 'disabled')
                }


            })

            // calculate average 
            const average = total / inputs.length

            //get 10% of the nominal quantity
            const tenPercentOfQn = percentage(10, nominalQuantity)
            calculatedTare.value = average

            //check if the average tare weight is less than the 10 % of the qn
            if (average <= tenPercentOfQn) {
                calculatedTare.style.border = '1px solid green'
                document.querySelector('#msg').textContent = ''
                document.querySelector('#tareApprover').removeAttribute('disabled', 'disabled')
                $('#sdCalcBtn').hide()
            } else {
                document.querySelector('#tareApprover').setAttribute('disabled', 'disabled')
                calculatedTare.style.border = '1px solid red'
                document.querySelector('#msg').textContent = 'Tare Weight is invalid, calculate standard deviation'
                $('#sdCalcBtn').show()


            }



            document.querySelector('#ten').textContent = tenPercentOfQn + ' g'
            document.querySelector('#avg').textContent = average.toFixed(2) + ' g'


        }



        // calculating sample standard deviation
        function calculateDeviation() {
            let nominalQuantity = unitConverter(document.querySelector('#unit').value, document.querySelector('#quantity').value)
            const inputs = document.querySelectorAll('.weight')
            let array = []
            inputs.forEach(input => {
                array.push(+input.value)
            })

            const T = tolerableDeficiency(nominalQuantity)

            const sd = standardDeviation(array)

            if (sd <= (T * 0.25)) {
                // console.log('Tare Weight passed');
                document.querySelector('#tareApprover').removeAttribute('disabled', 'disabled')

                let inputs = ``
                for (let index = 1; index <= 15; index++) {
                    inputs += `
            <div class="form-group col-md-4">
            <label>Sample ${10+index}:</label>
            <input type="number" required name="tareSampleSize" id="tareSampleSize" class="form-control weight" min="0" oninput="calcAverage()">
            </div>
            `

                }
                // $('#weights').html('')
                $('#weights').append(inputs)

            } else {
                // console.log('Tare Weight FAILED');
                // $('#tareMsg').html('Tare Weight failed please select destructive method')
                // document.querySelector('#tareMsg').textContent = 'Tare Weight failed please select destructive method '

                swal({
                    title: 'Tare Weight failed please select destructive method',
                    icon: "warning",
                    timer: 4500
                });

                // alert('Tare Weight failed please select destructive method')

                document.querySelector('#tareApprover').setAttribute('disabled', 'disabled')
            }


            // console.log('SD ' + sd + ' grams');
            // console.log('Tolerable ' + (T * 0.25) + ' grams');
            // console.log(array);

            // let dv = standardDeviation(array)


            // console.log('Dev = ' + dv);


        }

        //approve tare weight if its valid
        function approveTareWeight() {
            let tare = document.querySelector('#calculatedTare').value
            document.querySelector('#tareWeight').value = tare
            $('#tareModal').modal('hide')
        }

        let myArray = []

        function checkForError(param) {


            const tareWeight = document.querySelector('#productTare').value
            const quantity = document.querySelector('#currentQuantity').value

            const tolerable = tolerableDeficiency(quantity)
            const parent = param.parentNode.parentNode
            const enteredQuantity = param.value

            const netQuantity = parent.children[2].children[0]
            const comment = parent.children[3].children[0]
            const status = parent.children[4].children[0]


            if (enteredQuantity) calculateError(+enteredQuantity, +quantity, +tolerable, +tareWeight, netQuantity, comment, status)

            // console.log(myArray);


        }


        function checkForAreaError(param) {
            const enteredQuantity = param.value
            const quantity = document.querySelector('#currentQuantity').value
            const tareWeight = 0
            const tolerable = tolerableDeficiency(quantity)
            const parent = param.parentNode.parentNode
            const width = parent.children[1].children[0].value
            const height = parent.children[2].children[0].value
            const area = parent.children[3].children[0]
            const comment = parent.children[4].children[0]
            const status = parent.children[5].children[0]
            area.value = Number(width * height)

            if (width && height) calculateError(+enteredQuantity, +quantity, +tolerable, +tareWeight, area, comment, status)



        }
        //calculate  T1 and T2 error
        //calculate  T1 and T2 error c1
        function calculateError(enteredQuantity, quantity, tolerable, tareWeight, netQuantity, comment, status) {
            const productNature = document.querySelector('#natureOfProduct').value
            const productDensity = document.querySelector('#productDensity').value
            const categoryAnalysis = document.querySelector('#categoryOfAnalysis').value
            comment.value = ''
            const grossWt = Number(quantity + tareWeight)
            let net = 0

            console.log(categoryAnalysis)

            switch (categoryAnalysis) {
                case 'General':
                    net += Number(enteredQuantity - tareWeight)
                    break;
                case 'Linear':
                case 'Linear 2':
                    net += Number(enteredQuantity - tolerable)
                    break;
                case 'Area':
                    net += Number(netQuantity.value - tolerable)
                    break;

                default:
                    break;
            }


            if (productNature == 'Liquid' && productDensity != '') {

                netQuantity.value = (net / +productDensity).toFixed(3)
            } else {
                netQuantity.value = net
            }



            console.log('nature ' + productNature);
            console.log('density ' + productDensity);
            console.log('Net ' + net);


            let T1 = Number(quantity - tolerable)
            let T2 = Number(quantity - (tolerable * 2))

            console.log('T1 VALUE ' + T1);
            console.log('T2 VALUE ' + T2);
            console.log('TOLERABLE ' + tolerable);
            // console.log('grossWt ' + grossWt);
            console.log('Tare ' + tareWeight);
            // console.log('Net weight ' + net / +productDensity);

            netQuantity.removeAttribute('data-status')



            const theNetValue = +netQuantity.value
            console.log('NET WT ' + theNetValue);

            if (theNetValue < T1 && theNetValue > T2) {
                comment.value = 'Has  T1 Error'
                status.value = 1
                netQuantity.style.border = '1px solid  red'
                netQuantity.setAttribute('data-status', 'T1Error')

            } else if (theNetValue < T2) {
                comment.value = 'Has T1 And T2 Error'
                status.value = 2
                netQuantity.style.border = '1px solid  red'
                netQuantity.setAttribute('data-status', 'T2Error')
            } else {
                comment.value = 'Pass T1  And T2 Error'
                status.value = 0
                netQuantity.style.border = '1px solid  green'
                netQuantity.setAttribute('data-status', 'Pass')
            }

        }


        //rendering measurement data based on sample size
        function renderMeasurementData(sampleSize) {

            console.log('SELECTED SAMPLES' + sampleSize);

            // $('#measurementSheet').modal('show')

            const categoryOfAnalysis = document.querySelector('#categoryOfAnalysis').value
            console.log(categoryOfAnalysis)
            let table = ``
            let tableHeader = ``
            let indexNumber = 1
            switch (categoryOfAnalysis) {
                case 'General':
                case 'Linear':
                case 'Linear 2':
                    tableHeader += `
                 <thead class="thead-dark">
                  <tr>
                   <th>#</th>
                   <th>Gross Quantity</th>
                   <th>Net Quantity</th>
                   <th>Comment</th>
                 </tr>
              </thead>
            <tbody>
            `
                    for (let index = 1; index <= sampleSize; index++) {
                        table += `<tr>
                        <td>${index}</td>
                       <td style="width: ;"><input tabindex="${index}" id="grossInputs" name="weightGross[]" oninput="checkForError(this)" class="form-control measuredQty" type="number" required></td>
                       <td style="width: ;"><input step="any" name="weightNet[]" readonly data-weight='net'   class="form-control netQuantity" type="number" required></td>
                       <td >
                       <input data-comment="comment" readonly name="comment[]" required class="form-control style=" border:0;outline: none;"/>
                       
                       </td>
                       <td >
                       <input  name="status[]"  class="form-control" required style=" border:0;outline: none;color:transparent;"/>
                       
                       </td>
                     </tr>`

                    }
                    break;
                case 'Area':
                    tableHeader += `
                 <thead class="thead-dark">
                  <tr>
                   <th>#</th>
                   <th>Width</th>
                   <th>Length</th>
                   <th>Area</th>
                   <th>Comment</th>
                   <th></th>
                 </tr>
              </thead>
            <tbody>
            `
                    for (let index = 0; index <= (sampleSize * 2) - 1; index++) {
                        // index+=2
                        index++
                        index++
                        index--
                        table += `<tr>
                        <td>${indexNumber++}</td>

                       <td>
                            <input tabindex="${index}" id="width" name="width[]" oninput="checkForAreaError(this)" class="form-control measuredQty" type="number" required>
                       </td>

                       <td>
                        <input tabindex="${index+1}" id="height" step="any" name="height[]" oninput="checkForAreaError(this)"  data-weight='net'   class="form-control netQuantity" type="number" required>
                        </td>

                      
                       <td>
                        <input step="any" name="area[]" readonly data-weight='net'   class="form-control netQuantity" type="number" required>
                        </td>

                       <td>
                       <input data-comment="comment" readonly name="comment[]" required class="form-control style=" border:0;outline: none;"/>
                       </td>

                       <td>
                       <input  name="status[]"  class="form-control" required style=" border:0;outline: none;color:transparent;"/>
                       
                       </td>
                     </tr>`

                    }
                    break;

                default:
                    break;
            }




            //





            tableHeader += table
            $('#dataSheetTable').html('')
            $('#dataSheetTable').append(tableHeader)






        }


        //=================evaluating sample status====================

        let dataForm = document.querySelector('#dataSheetForm')
        dataForm.addEventListener('submit', function evaluateStatus(e) {

            e.preventDefault()

            const formData = new FormData(dataForm)
            let commodityId = document.querySelector('#commodityId').value
            formData.append("commodityId", commodityId);
            formData.append("commodityCategory", document.querySelector('#commodityCategory').value);
            formData.append("currentQuantity", document.querySelector('#productQuantity').value);





            $.ajax({
                type: "POST",
                url: "saveMeasurementData",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {

                    let td = document.querySelector('#actions')
                    //TODO Check if if 2 sets of measurements are required before removing create measurement button
                    $('#actions').html(
                        ` <button class="btn btn-primary btn-sm" onclick="getMeasurements('${commodityId}')">View Measurement Sheet</button>
                            `
                    )
                    console.log(td)
                    console.log(response)
                    $('#dataSheetForm')[0].reset()
                    $('#dataSheetTable').html('')
                    $('#measurementSheet').modal('hide')

                    if (response.status == 1) {

                        swal({
                            title: response.msg,
                            icon: "success",
                            //  timer: 2500
                        });
                        selectProduct(response.commodityId)
                        console.log('@@@@@@@@@@@@@@@@@@@@@@@@@@@')
                        console.log('@@@@@@@@@@@@@@@@@@@@@@@@@@@')
                        console.log(response.commodityId)
                    } else {
                        swal({
                            title: 'Something went wrong',
                            icon: "warning",
                            timer: 4500
                        });
                    }

                }
            });
        })



        const productDetailsForm = document.querySelector('#productDetailsForm')
        // productDetailsForm.addEventListener('submit',function(e){
        //     e.preventDefault()

        //     console.log('testing...');

        // })
        $("#productDetailsForm").validate()
        $('#productDetailsForm').on('submit', function(e) {
            e.preventDefault()
            if ($('#productDetailsForm').valid()) {

                let formData = new FormData(this);
                // console.log(formData);
                formData.append("customerId", document.querySelector('#customerId').value);
                // formData.append("csrf_hash", document.querySelector('.token').value);
                $.ajax({
                    type: "POST",
                    url: "addProductDetails",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    beforeSend: function() {
                        // $('#preloader').show();
                    },
                    success: function(response) {

                        console.log(response);

                        $('#productDetailsForm')[0].reset()

                        $('#productModal').modal('hide')

                        let lotSizes = response.products.map(product => {
                            return product.lot
                        })

                        calculatePrice(lotSizes)

                        // console.log(response);
                        swal({
                            title: 'Product Added',
                            icon: "success",
                            // timer: 2500
                        });


                        //=================get last added product====================
                        selectProduct(response.lastProduct.id)


                        //=================update product dropdown====================

                        let productList = ``

                        response.products.forEach(product => {
                            productList += `<option value="${product.id}">${product.commodity}  ${product.quantity} ${product.unit}</option>`

                        })

                        $('#products').html(productList)


                    },
                    error: function(err) {
                        //console.log(err);
                    }

                });
            } else {
                return false
                // console.log('invalid');
            }

        })

        function renderUnit(unit) {
            switch (unit) {
                case 'mL':
                case 'L':
                case 'cubCm':
                case 'cubM':
                    return 'mL'
                    break;
                case 'mg':
                case 'g':
                case 'kg':
                    return 'g'
                    break;
                case 'mm':
                case 'cm':
                case 'm':
                    return 'mm'
                    break;
                case 'sqCm':
                case 'sqM':
                    return 'cm<sup>2</sup>'
                    break;

                default:
                    break;
            }
        }

        //select product details  based on the id
        function selectProduct(id) {
            $('#dataSheetTable').html('')


            //get all the product measurements
            //getMeasurements(id)

            $.ajax({
                type: "POST",
                url: "selectProduct",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);

                    let grossOptions = []
                    grossOptions.push(response.data.gross_quantity, response.data.quantity_2)

                    const measurementOptions = grossOptions.filter(n => n)
                    let options = ``
                    measurementOptions.forEach(q => {
                        options += `
                      <option value="${q + ' '+ renderUnit(response.data.unit)}">${q} ${renderUnit(response.data.unit)}</option>
                      `
                    })

                    $('#measurementOptions').html('')
                    $('#measurementOptions').html(options)
                    $('#currentQuantity').val(measurementOptions[0].replace(/[^\d.-]/g, ''))
                    $('#productQuantity').val(measurementOptions[0] + ' ' + renderUnit(response.data.unit))

                    console.log(measurementOptions)
                    console.log(options)

                    // console.log('******000000000000000************')

                    if (response.data.product_nature == 'Solid') {
                        document.querySelector('#densityBtn').style.display = 'none'
                        document.querySelector('.density').style.display = 'none'
                    } else {
                        document.querySelector('#densityBtn').style.display = 'block'
                        document.querySelector('.density').style.display = 'block'
                    }





                    $('#commodityInfo').html(`
                
            <table class="table table-sm">

                
            <input class="form-control" id="commodityId" value="${response.data.id}"  hidden />
            <input class="form-control cat" id="categoryOfAnalysis" value="${response.data.analysis_category}" hidden  />
               
                    <tr>
                    
                        <td>Task/Activity</td>
                        <td>${response.data.activity}</td>

                    </tr>
                    <tr>
                    
                        <td>Commodity</td>
                        <td>${response.data.commodity}</td>

                    </tr>
                     <tr>
                       
                        <td>Quantity</td>
                        <td>${response.data.quantity} <span style="font-family: 'Roboto', sans-serif;" class="unit">${response.data.unit}</span> </td>
                    </tr>
                    <tr>
                    
                        <td>Packer Correctly Identified</td>
                        <td>${response.data.packer_identification}</td>

                    </tr>
                    <tr>
                    
                        <td>Product Correctly Identified</td>
                        <td>${response.data.product_identification}</td>

                    </tr>
                    <tr>
                    
                        <td>Correct Measuring Unit</td>
                        <td>${response.data.correct_unit}</td>

                    </tr>
                    <tr>
                    
                        <td>Correct Measuring Symbol</td>
                        <td>${response.data.correct_symbol}</td>

                    </tr>
                    <tr>
                    
                        <td>Correct  Height</td>
                        <td>${response.data.correct_height}</td>

                    </tr>
                    <tr>
                    
                        <td>Correct Prescribed Quantity(If Applicable)</td>
                        <td>${response.data.correct_quantity}</td>

                    </tr>
                    
                    
                    <tr>
                    
                        <td>General Appearance Of The Package</td>
                        <td>${response.data.general_appearance}</td>

                    </tr>
                    <tr>
                    
                        <td>Recommendation</td>
                        <td>${response.data.recommendation}</td>

                    </tr>
                    <tr>
                       
                        <td></td>
                        <td> </td>
                    </tr>
                   
                    <tr>
                       
                        <td>Packing Declaration</td>
                        <td>${response.data.packing_declaration} </td>
                    </tr>
                    <tr>
                    
                        <td>Batch Size / Inspection Lot</td>
                        <td>${response.data.lot}</td>
                        <input class="form-control" id="lotSize" value="${response.data.lot}" hidden/>

                    </tr>
                    <tr>
                    
                        <td>Sample Size</td>
                        <td>${response.data.sample_size}</td>
                        <input class="form-control" id="productSampleSize" value="${response.data.sample_size}" hidden/>

                    </tr>
                    
                    <tr>
                       
                        <td>Category Analysis</td>
                        <td>${response.data.analysis_category}  </td>
                        <input class="form-control" id="commodityCategory" value="${response.data.analysis_category}" hidden/>
                    </tr>
                    <tr>
                       
                        <td>Nature Of The Product</td>
                        <td>${response.data.product_nature}  </td>
                        <input class="form-control" id="natureOfProduct" value="${response.data.product_nature}" hidden/>
                    </tr>
                    ${response.data.density ? 
                    `<tr>
                       
                        <td>Declared Product Density</td>
                        <td>${response.data.density}  </td>
                         <input class="form-control" id="productDensity" value="${response.data.density}" hidden/>
                    </tr>
                    `
                    : `<input class="form-control" id="productDensity" value="${response.data.density}" hidden/>`
                    
                    }
                    <tr>
                       
                        <td>Mass / Volume (gram or milliliter)</td>
                        <td>${response.data.gross_quantity } </td>
                        <input class="form-control" id="productGrossWeight" value="${response.data.gross_quantity}" hidden/>
                    </tr>
                    <tr>
                       
                        <td>Sampling Plan</td>
                        <td> ${response.data.sampling}</td>
                    </tr>
                    <tr>
                       
                        <td>Method To Be Applied </td>
                        <td> ${response.data.method}</td>
                        <input class="form-control" id="appliedMethod" value="${response.data.method}" hidden />
                    </tr>

                    <tr>
                        <td>Declared Tare  Weight</td>
                        <td>${response.data.tare} g</td>
                         <input class="form-control" id="productTare" value="${response.data.tare}" hidden/>
                    </tr>

                    <tr>
                        <td>Action</td>
                        <td id='actions'>
                        ${
                            (response.measurements)?
                            `
                            <div class="form-group">
                                <label for="">Quantity</label>
                                <select class="form-control" name="" id="quantityId">
                                        ${(function fun() {
                                            let select = ''
                                        response.quantityIds.forEach(id=>{
                                            select+= `<option value='${id}'>${id.substr(11)}</option>`
                                        })
                                        return select
                                        })()}
                                   
                                 </select>
                            </div>

                             <button class="btn btn-primary btn-sm" onclick="getMeasurements('${response.data.id}')">View Measurement Sheet</button>
                            `:
                            `
                            <button type="button" class="btn btn-primary btn-sm mb-3"  onclick="createMeasurements('${response.data.sample_size}')">
                            Create Measurement Sheet
                            </button>
                            `
                        }
                       
                          

                       
                        
                        
                        
                        </td>
                    </tr>
                 
              
            </table>
                
                `)
                }
            });

        }


        function getMeasurements(id) {

            const quantityId = document.querySelector('#quantityId').value

            // console.log(id)
            console.log(quantityId)




            $.ajax({
                type: "post",
                url: "getMeasurementData",
                data: {
                    includeQuantity: true,
                    productId: id,
                    quantityId: quantityId,
                },
                dataType: "json",
                success: function(response) {
                    console.log(response)
                    //collectiveDecision()
                    $('#measurementsPreview').modal('show')
                    // let declaredQuantity = document.querySelector('#productGrossWeight').value
                    let declaredQuantity = response.data[0].quantity_id.substr(11).replace(/[^\d.-]/g, '')
                    // console.log('declaredQuantity ' + declaredQuantity)
                    if (response.data.length == 0) {
                        // console.log('there is no measurement data for this product please create one');
                    }
                    const withT1Error = response.data.filter((data) => data.status == 1)
                    const withT2Error = response.data.filter((data) => data.status == 2)

                    const netQuantities = response.data.map(net => {
                        return Number(net.net_quantity - declaredQuantity)
                    })

                    // console.log('*********************net q**********************');
                    // console.log(netQuantities);
                    // console.log('*******************************************');


                    const individualError = netQuantities.reduce((prev, next) => {
                        return prev + next
                    }, 0)



                    renderSummaryTable(withT1Error, withT2Error, individualError, netQuantities, declaredQuantity, response.results)
                    renderMeasurementTable(response.data)


                    // let individualError = netQuantities.forEach(qty => {

                    // })
                    //console.log('1111111111111111111111111111111111111111111111111')
                    // console.log(withT1Error);
                    // console.log('Data Length ' + netQuantities.length);
                    // console.log('Individual error is ' + individualError);
                }
            });
        }



        function renderMeasurementTable(data) {
            let tb = ``
            let i = 1

            data.forEach(d => {
                tb += `
        <tr>
          <td>${i++}</td>
          <td>${d.gross_quantity}</td>
          <td>${d.net_quantity}</td>
          <td>${d.comment}</td>
        </tr>
           `
            })

            $('#measurementTable').html(tb)
        }

        function collectiveDecision(data) {
            console.log(data)
        }

        function renderSummaryTable(t1, t2, individualError, g, declaredQuantity, results) {

            console.log(results)
            const individualStandardDeviation = standardDeviation(g).toFixed(4)


            //console.log('individualStandardDeviation........ ' + individualStandardDeviation);



            let theSampleSize = document.querySelector('#productSampleSize').value
            // let productQuantity = document.querySelector('#productGrossWeight').value
            let productQuantity = declaredQuantity

            const sampleLimit = tolerableDeficiency(productQuantity)

            const sampleErrorLimit = productQuantity - sampleLimit


            function nominalQtyPercent(nQ) {
                if (nQ == 0 && nQ <= 49) {
                    return 9
                } else if (nQ >= 100 && nQ <= 199) {
                    return 4.5
                } else if (nQ >= 300 && nQ <= 499) {
                    return 3
                } else if (nQ >= 1000 && nQ <= 9999) {
                    return 1.5
                } else if (nQ > 15000) {
                    return 1
                } else {
                    return 0
                }
            }

            function nominalQtyGram(nQ) {
                if (nQ >= 50 && nQ <= 99) {
                    return 4.5
                } else if (nQ >= 200 && nQ <= 299) {
                    return 9
                } else if (nQ >= 500 && nQ <= 999) {
                    return 15
                } else if (nQ >= 10000 && nQ <= 15000) {
                    return 150
                } else {
                    return 0
                }
            }





            let realT1 = t1.map(t => {
                return t.net_quantity
            })

            let t2Percentage = t2.length * 100 / +theSampleSize

            let realT2 = t2.map(t => {
                return t.net_quantity
            })

            const samplesWithError = realT1.concat(realT2)

            let t1Percentage = samplesWithError.length * 100 / +theSampleSize

            const tolerableAmount = tolerableDeficiency(productQuantity)

            const tError = realT1.concat(realT2)

            let averageError = individualError / +theSampleSize

            // if (samplesWithError.length == 0) {
            //     averageError += 0
            // } else {
            //     averageError += samplesWithError.reduce((prev, next) => {
            //         return +prev + +next
            //     }, 0) / samplesWithError.length
            // }






            // console.log('................................');
            // console.log(samplesWithError);
            // console.log('................................');



            let approved = 0

            let correctionFactor = 0

            let decision = ''

            let lotX = document.querySelector('#lotSize').value
            let appliedMethod = document.querySelector('#appliedMethod').value

            if (lotX >= 100 && lotX <= 500 && appliedMethod == 'Non Destructive') {
                approved += 3

                if (t1.length > 3 && appliedMethod == 'Non Destructive') {
                    decision = ' Sample Failed the required test reject'
                }

                correctionFactor += 0.379
            } else if (lotX >= 501 && lotX <= 3200 && appliedMethod == 'Non Destructive') {
                approved += 5
                if (t1.length > 5) {
                    decision = ' Sample Failed the required test reject'
                }
                correctionFactor += 0.295
            } else if (lotX > 3200 && appliedMethod == 'Non Destructive') {
                approved += 7
                if (t1.length > 7) {
                    decision = ' Sample Failed the required test reject'
                }
                correctionFactor += 0.234
            } else if (lotX >= 100 && appliedMethod == 'Destructive') {
                approved += 1
                if (t1.length > 1) {
                    decision = ' Sample Failed the required test reject'
                }
                correctionFactor += 0.640
            }

            function checkPositiveOrNegative(individualError) {
                if (individualError > 0) {
                    return 'Positive ' + individualError
                } else {
                    return 'Negative ' + individualError
                }
            }

            const theSampleErrorLimit = individualStandardDeviation * correctionFactor
            console.log('##########################################################');
            console.log({
                appliedMethod: appliedMethod,
                samplesWithError: samplesWithError,
                lotX: lotX,
                approved: approved,
                decision: decision,
                correctionFactor: correctionFactor,
                individualError: individualError,
                averageError: averageError,
                theSampleErrorLimit: theSampleErrorLimit,
                correctedAVG: Number(averageError + theSampleErrorLimit)

            });
            console.log('##########################################################');



            // const correctedAverageError = Number((individualError / theSampleSize) * correctionFactor)
            // (individualError / theSampleSize)
            const correctedAverageError = Number(averageError + theSampleErrorLimit)

            console.log('correctedAverageError ' + correctedAverageError);

            let table = `
 ${results.category == 'Linear 2'||results.category== 'Area'? `
    
     <table class="table table-bordered table-sm">
                     <thead class="thead-dark">
                        <tr>
                            <th><b>Item</b></th>
                            <th><b>Status</b></th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>${results.quantity1}</b></td>
                            <td>${results.quantity1Status}</td>
                        </tr>
                        <tr>
                            <td><b>${results.quantity2}</b></td>
                            <td>${results.quantity2Status}</td>
                        </tr>
                       
                        
                        
                        <tr>
                            <td><b>Overall Decision </b></td>
                            <td><b>${results.overallStatus}</b></td>
                        </tr>

                    </tbody>
                </table>
                <hr>
    
    
    
    ` :''}
          




         <table class="table table-bordered table-sm">
                    <thead class="thead-dark">

                        <tr>
                            <th>Test Type</th>
                            <th>Result & Recommendation</th>
                            <th>Observed</th>
                            <th>Approved Limit</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>T1 Test Result</td>
                            <td>${
                                (tError.length > approved && appliedMethod == 'Non Destructive')  
                                ?'Sample Failed T1-Reject'
                                : (tError.length > 1 && appliedMethod == 'Destructive') 
                                ? 'Sample Failed T1-Reject'  
                                :'Sample Passed T1 Test- Go For T2 Test ' 
                            
                            
                            }  </td>
                            <td>${tError.length}</td>
                            <td>${approved}</td>
                        </tr>

                        <tr>
                            <td>T2 Test Result</td>
                            <td>${t2.length > 0 ?'Sample Failed T2-Reject' :'Sample Passed T2 Test'} </td>
                            <td>${t2.length}</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Individual Pre-Package Error Test - Result</td>
                            <td>${individualError > 0 ?'Samples Passed Individual Pre-packages Error Test'
                            :'Samples Failed Individual Pre-packages Error Test'}</td>
                            <td> (${checkPositiveOrNegative(individualError.toFixed(3))})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Corrected Average Error Test Results</td>
                            <td>${Number(correctedAverageError) > 0 ?'Samples Passed Corrected Average Error Test Result' :'Samples Failed Corrected Average Error Test Result'}</td>
                               <td> (${(checkPositiveOrNegative(correctedAverageError.toFixed(3)))})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Conclusion Remarks</td>
                            <td colspan="3">${( individualError < 0 || correctedAverageError < 0 || realT2.length > 0  )?'Sample Failed All required test-Reject' :'Sample Passed All required Test- Approve'}</td>
                        </tr>


                    </tbody>
                </table>

                <hr>

                <h3>Analysis Details For The Required Test</h3>

                <table class="table table-bordered table-sm">
                     <thead class="thead-dark">
                        <tr>
                            <th><b>Item</b></th>
                            <th><b>Figure</b></th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Percent of Qn</b></td>
                            <td>${nominalQtyPercent(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td><b>g or mL</b></td>
                            <td>${nominalQtyGram(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td><b>Minimum For T1</b></td>
                            <td>${Number(productQuantity - tolerableAmount )}</td>
                        </tr>
                        <tr>
                            <td><b>Number of Item With T1 Error</b></td>
                            <td>${tError.length}</td>
                        </tr>
                        <tr>
                            <td><b>Percent No T1 / Sample Size</b></td>
                            <td>${t1Percentage}%</td>
                        </tr>
                        <tr>
                            <td><b>Decision At This Stage </b></td>
                            <td><b>${tError.length > approved ?'Sample Fail T1 Test- Reject':'Sample Pass T1 Test- Go for T2 Test'}</b></td>
                        </tr>

                    </tbody>
                </table>

                <hr>
                  <table class="table table-bordered table-sm">
                     <thead class="thead-dark">
                        <tr>
                            <th><b>Item</b></th>
                            <th><b>Figure</b></th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Percent of Qn</b></td>
                            <td>${nominalQtyPercent(productQuantity)*2}</td>
                        </tr>
                        <tr>
                            <td><b>g or mL</b></td>
                            <td>${nominalQtyGram(productQuantity) * 2}</td>
                        </tr>
                        <tr>
                            <td><b>Minimum For T2</b></td>
                           <td>${Number(productQuantity - (tolerableAmount*2) )}</td>
                        </tr>
                        <tr>
                            <td><b>Number of Item With T2 Error</b></td>
                            <td>${t2.length}</td>
                        </tr>
                        <tr>
                            <td><b>Percent No T2 / Sample Size</b></td>
                            <td>${t2Percentage}%</td>
                        </tr>
                        <tr>
                            <td><b>Decision At This Stage </b></td>
                            <td><b>${t2.length > 0  ? 'Sample Fail T2 Test-Reject': 'Sample Pass T2 Test- Go for Pre-package Error Test'}</b></td>
                        </tr>

                    </tbody>
                </table>

                <hr>
                <table class="table table-bordered table-sm">
                 <thead class="thead-dark">
                        <tr>
                            <th><b>Item</b></th>
                            <th><b>Figure</b></th>
                        </tr>
                    </thead>
                 <tbody>
                  <tr>
                    <td><b>Total Pre-Package Error</b></td>
                    <td>${(individualError).toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Average Error</b></td>
                    <td>${(averageError) .toFixed(3) }</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b></td>
                    <td><b>${individualError > 0 ? ' Sample Pass Individual Pre Package Error Test':'Sample Fail Individual Pre Package Error Test-Reject'}</b></td>
                 </tr>
                 </tbody>
                </table>
                <hr>
                <table class="table table-bordered table-sm">
               
                 <tbody>
                  <tr>
                    <td><b>Standard Deviation of The "Individual  Pre - Package Errors"</b></td>
                    <td>${individualStandardDeviation}</td>
                 </tr>
                  <tr>
                    <td><b>Sample Size</b></td>
                    <td>${theSampleSize}</td>
                 </tr>
                  <tr>
                    <td><b>Sample Correction Factor</b></td>
                    <td>${correctionFactor}</td>
                 </tr>
                  <tr>
                    <td><b>Number Of Pre Packages in Sample Allowed To Have T1 Error</b></td>
                    <td>${approved}</td>
                 </tr>
                  <tr>
                    <td><b>Sample Error Limit</b></td>
              <td>${Number(theSampleErrorLimit).toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Corrected Average Error</b></td>
                    <td>${(correctedAverageError).toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b> </td>
                    <td><b>${(correctedAverageError) < 0 ? 'Sample Fail Corrected Average Error Test - Advice the client' :'Sample Pass Corrected Average Error Test'}</b></td>
                 </tr>
                 </tbody>
                </table>
        
        
        `

            $('#prePackageSummary').html(table)
        }
    </script>

<?php else : ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">
                    ACCESS TO THIS SECTION IS ALLOWED TO INSPECTORS ONLY
                </h5>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection(); ?>