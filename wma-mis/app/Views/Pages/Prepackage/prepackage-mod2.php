<?= $this->extend('layouts/coreLayout'); ?>
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
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name Of The Packer/Client">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Physical Address</label>
                                <input type="text" name="physicalAddress" id="physicalAddress" class="form-control" placeholder="Physical Address">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Postal Address</label>
                                <input type="text" name="postalAddress" id="postalAddress" class="form-control" placeholder="Postal Address">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Phone Number</label>
                                <input type="text" name="phoneNumber" id="phoneNumber" class="form-control" placeholder="Phone Number">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Ref Number</label>
                                <input type="text" name="refNumber" id="refNumber" class="form-control" placeholder="Ref Number">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Batch Number</label>
                                <input type="text" name="batchNumber" id="batchNumber" class="form-control" placeholder="Batch Number">

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
            <button style="float: right;" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#productModal">
                Add Product
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
                        <div class="form-group">
                            <label for=""></label>
                            <select class="form-control" name="" id="" onchange="renderMeasurementData(this.value)">
                                <option selected>Select Sample Size</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
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
    <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#measurementSheet"">
        Create Measurement Sheet
    </button>
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
                                    <th>Measured Gross Weight</th>
                                    <th>Net Weight</th>
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
                <div class="form-group">
                    <label for="">Amount</label>
                    <input type="text" name="" id="totalAmount" value="15200" class="form-control" placeholder="" aria-describedby="helpId">

                </div>
                <?= $this->include('Components/controlNumber') ?>
            </div>
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



    <div class="card">
        <div class="card-header">Billing</div>
        <div class="card-body">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Impedit exercitationem earum commodi unde nesciunt nisi blanditiis numquam quisquam mollitia repellendus.

        </div>
    </div>



</div>








<script>
    const quantity = document.querySelector('#quantity').value
    // const unit = document.querySelector('#unit').value
    // const calculatedTare = document.querySelector('#calculatedTare')




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
        if (nQ == 0 && nQ <= 50) {
            return percentage(9, nQ)
        } else if (nQ >= 51 && nQ <= 100) {
            return 4.5
        } else if (nQ >= 101 && nQ <= 200) {
            return percentage(4.5, nQ)
        } else if (nQ >= 201 && nQ <= 300) {
            return 9
        } else if (nQ >= 301 && nQ <= 500) {
            return percentage(3, nQ)
        } else if (nQ >= 501 && nQ <= 1000) {
            return 15
        } else if (nQ >= 1001 && nQ <= 10000) {
            return percentage(1.5, nQ)
        } else if (nQ >= 10001 && nQ <= 15000) {
            return 150
        } else if (nQ > 15000) {
            return percentage(1, nQ)
        }
    }

    $('#measurement').hide();
    $('#mthd').attr('class', 'col-md-6');
    $('#batch').attr('class', 'col-md-6');
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
                        console.log(response);
                        $('#addModal').modal('hide')
                        $('#customerForm')[0].reset();
                        getCustomerInfo(response.hash)

                        break;

                    default:
                        break;
                }



            },
            error: function(err) {
                console.log(err);
            }

        });
    }


    // rendering search results
    function renderSearchResults(res) {
        console.log(res);

        let list = ``
        if (res.data == '') {
            $('#searchResults').html('<h5>No Match Found!</h5>');
            console.log('no match');
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


        console.log(lots);
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
                $('#searchModal').modal('hide')

                let lotSizes = response.products.map(product => {
                    return product.lot
                })

                calculatePrice(lotSizes)







                let products = ``
                products += '<option selected>Customers Products</option>'
                const customer = response.data
                // console.log(response.products);

                response.products.forEach(product => {
                    products += `
                    
                    <option value="${product.id}">${product.commodity}  ${product.quantity} ${product.unit}</option>`

                })

                $('#products').html(products)



                $('#customerInfo').html(`
               <input class="form-control mb-1" type="text" id="customerId" name="customerId" value="${customer.hash}" hidden>
                  <tr>
                        <td> <b>Name</b></td>
                        <td>${customer.name}</td>
                    </tr>
                    <tr>
                        <td> <b>Physical Address </b></td>
                        <td>4 ${customer.physical_address}</td>
                    </tr>
                    <tr>
                        <td><b>Postal Address</b></td>
                        <td>${customer.postal_address}</td>
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
    customerForm.addEventListener('submit', (e) => {
        e.preventDefault()
        const data = {
            url: 'addPrePackageCustomer',
            formData: new FormData(customerForm)
        }
        ajaxCall(data, false)


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

    //=====================================
    $('#categoryAnalysis').change(function() {
        if (this.value == 'General') {
            $('#measurement').hide();
            $('#mthd').attr('class', 'col-md-6');
            $('#batch').attr('class', 'col-md-6');
        } else {
            $('#measurement').show();
            $('#mthd').attr('class', 'col-md-4');
            $('#batch').attr('class', 'col-md-4');
        }
    })

    $('#method').change(function() {
        const lot = $('#batchSize').val()
        let data = 0
        if (this.value == 'Destructive') {
            $("#sampling").val('Non Sampling');
            $('#sampleSize').val(20)
            data += 20




        } else {
            if (lot >= 100 && lot <= 500) {

                $('#sampleSize').val(50)
                data += 50
            } else if (lot >= 501 && lot <= 3200) {
                $('#sampleSize').val(80)
                data += 80
            } else if (lot > 3200) {
                $('#sampleSize').val(125)
                data += 125
            }
        }



        console.log('data size  = ' + data);


    })
    $('#measurementNature').change(function() {
        if (this.value == 'Net') {
            $('#tare').hide();
            $('#plan').attr('class', 'col-md-6');
            $('#nature').attr('class', 'col-md-6');
        } else {
            $('#tare').show();
            $('#plan').attr('class', 'col-md-4');
            $('#nature').attr('class', 'col-md-4');
        }
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
            console.log('Tare Weight passed');
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
            console.log('Tare Weight FAILED');
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


        console.log('SD ' + sd + ' grams');
        console.log('Tolerable ' + (T * 0.25) + ' grams');
        console.log(array);

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

    function checkForError(gross) {


        const tareWeight = document.querySelector('#productTare').value
        const quantity = document.querySelector('#productGrossWeight').value

        const tolerable = tolerableDeficiency(quantity)

        let enteredWeight = gross.value
        let netWeightInput = gross.parentNode.nextElementSibling.childNodes[0];
        let netWeight = netWeightInput;

        let comment = gross.parentNode.nextElementSibling.nextElementSibling.childNodes[0].nextElementSibling;
        let status = gross.parentNode.nextElementSibling.nextElementSibling.nextElementSibling.childNodes[0].nextElementSibling;

        console.log(status);





        // netWeight.value = +element.value - tare
        // console.log('gross =' + grossWeight);
        // console.log('net ' + netWeight.value);
        // console.log('productTare ' + productTare);
        // console.log('quantity ' + quantity);
        // console.log('Tolerable ' + tolerable);
        calculateError(+enteredWeight, +quantity, +tolerable, +tareWeight, netWeight, comment, status)

        console.log(myArray);


    }
    //calculate  T1 and T2 error
    function calculateError(enteredWeight, quantity, tolerable, tareWeight, netWeight, comment, status) {
        const productNature = document.querySelector('#natureOfProduct').value
        const productDensity = document.querySelector('#productDensity').value
        comment.value = ''
        const grossWt = Number(quantity + tareWeight)
        let net = (enteredWeight - tareWeight)

        if (productNature == 'Liquid' && productDensity != '') {

            netWeight.value = (net / +productDensity).toFixed(2)
        } else {
            netWeight.value = net
        }



        console.log('nature ' + productNature);
        console.log('density ' + productDensity);
        console.log('Net ' + net);


        let T1 = Number(quantity - tolerable)
        let T2 = Number(quantity - (tolerable * 2))

        console.log('T1 VALUE ' + T1);
        console.log('T2 VALUE ' + T2);
        console.log('TOLERABLE ' + tolerable);
        console.log('grossWt ' + grossWt);
        console.log('Tare ' + tareWeight);
        console.log('Net weight ' + net / +productDensity);

        netWeight.removeAttribute('data-status')




        if (enteredWeight <= T1 && enteredWeight > T2) {
            comment.value = 'Has  T1 Error'
            status.value = 1
            netWeight.style.border = '1px solid  red'
            netWeight.setAttribute('data-status', 'T1Error')

        } else if (enteredWeight <= T2) {
            comment.value = 'Has T1 And T2 Error'
            status.value = 2
            netWeight.style.border = '1px solid  red'
            netWeight.setAttribute('data-status', 'T2Error')
        } else {
            comment.value = 'Pass T1  And T2 Error'
            status.value = 0
            netWeight.style.border = '1px solid  green'
            netWeight.setAttribute('data-status', 'Pass')
        }

    }



    //rendering measurement data based on sample size
    function renderMeasurementData(dataSize) {

        // console.log('SELECTED SAMPLES' + dataSize);

        // $('#measurementSheet').modal('show')
        const sampleSize = document.querySelector('#productSampleSize').value

        const categoryAnalysis = document.querySelector('#categoryAnalysis')
        let general = `
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
        let table = ``
        //

        for (let index = 1; index <= dataSize; index++) {
            table += `<tr>
                        <td>${index}</td>
                       <td style="width: ;"><input id="grossInputs" name="weightGross[]" oninput="checkForError(this)" class="form-control measuredQty" type="number"></td>
                       <td style="width: ;"><input step="any" name="weightNet[]" readonly data-weight='net'   class="form-control netQuantity" type="number"></td>
                       <td >
                       <input data-comment="comment" readonly name="comment[]" class="form-control style="border="0";outline:none; "/>
                       
                       </td>
                       <td >
                       <input  name="status[]" hidden class="form-control style="border="0";outline:none; "/>
                       
                       </td>
                     </tr>`

        }

        general += table

        if (categoryAnalysis.value == 'General') {

            $('#dataSheetTable').html('')
            $('#dataSheetTable').append(general)


        }



    }


    //=================evaluating sample status====================

    let dataForm = document.querySelector('#dataSheetForm')
    dataForm.addEventListener('submit', function evaluateStatus(e) {

        e.preventDefault()

        const formData = new FormData(dataForm)
        //commodityId
        formData.append("commodityId", document.querySelector('#commodityId').value);





        $.ajax({
            type: "POST",
            url: "saveMeasurementData",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                $('#dataSheetForm')[0].reset()
                if (response.status == 1) {
                    swal({
                        title: response.msg,
                        icon: "success",
                        //  timer: 2500
                    });
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
            console.log(formData);
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

                    $('#productDetailsForm')[0].reset()

                    $('#productModal').modal('hide')

                    let lotSizes = response.products.map(product => {
                        return product.lot
                    })

                    calculatePrice(lotSizes)

                    console.log(response);
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
                    console.log(err);
                }

            });
        } else {
            return false
            // console.log('invalid');
        }

    })

    function selectProduct(id) {

        getMeasurements(id)

        $.ajax({
            type: "POST",
            url: "selectProduct",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                console.log(response);


                if (response.data.product_nature == 'Solid') {
                    document.querySelector('#densityBtn').style.display = 'none'
                    document.querySelector('.density').style.display = 'none'
                } else {
                    document.querySelector('#densityBtn').style.display = 'block'
                    document.querySelector('.density').style.display = 'block'
                }





                $('#commodityInfo').html(`
                
            <table class="table table-sm">

                
               <tr>
               <td>
               <input class="form-control" id="commodityId" value="${response.data.id}" hidden/>
               </td>
               </tr>
                    <tr>
                    
                        <td>Commodity</td>
                        <td>${response.data.commodity}</td>

                    </tr>
                    <tr>
                       
                        <td>Quantity</td>
                        <td>${response.data.quantity} ${response.data.unit} </td>
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
                    </tr>
                    <tr>
                       
                        <td>Nature Of The Product</td>
                        <td>${response.data.product_nature}  </td>
                        <input class="form-control" id="natureOfProduct" value="${response.data.product_nature}" hidden/>
                    </tr>
                    <tr>
                       
                        <td>Declared Product Density</td>
                        <td>${response.data.density}  </td>
                         <input class="form-control" id="productDensity" value="${response.data.density}" hidden/>
                    </tr>
                    <tr>
                       
                        <td>Mass / Volume (gram or milliliter)</td>
                        <td>${response.data.gross_weight } </td>
                           <input class="form-control" id="productGrossWeight" value="${response.data.gross_weight}" hidden/>
                    </tr>
                    <tr>
                       
                        <td>Sampling Plan</td>
                        <td> ${response.data.sampling}</td>
                    </tr>
                    <tr>
                       
                        <td>Method To Be Applied</td>
                        <td> ${response.data.method}</td>
                    </tr>

                    <tr>
                        <td>Declared Tare  Weight</td>
                        <td>${response.data.tare} g</td>
                         <input class="form-control" id="productTare" value="${response.data.tare}" hidden/>
                    </tr>

                    <tr>
                        <td>Action</td>
                        <td><button class="btn btn-primary btn-sm" onclick="getMeasurements('${response.data.id}')">View Measurement Sheet</button></td>
                    </tr>
                 
              
            </table>
                
                `)
            }
        });

    }


    function getMeasurements(id) {

        $('#measurementsPreview').modal('show')


        // const grossInputs = document.querySelector('#grossInputs').value

        // console.log(grossInputs);

        // console.log('this................');

        // console.log('form submitted...');
        // const samples = document.querySelectorAll('[data-status]');
        // const measuredQty = document.querySelectorAll('.measuredQty');

        // let netQuantities = document.querySelectorAll('.netQuantity')
        // let declaredQuantity = document.querySelector('#grossWeightValue').value
        // let netQuantityArray = []


        // netQuantities.forEach(net => {
        //     console.log(+net.value);
        //     netQuantityArray.push(+declaredQuantity - +net.value)
        // })

        // console.log(netQuantityArray);

        // let T1 = 0
        // let T2 = 0
        // let pass = 0

        // const withT1Error = samples.forEach(sample => {
        //     if (sample.getAttribute('data-status') == 'T1Error') {
        //         T1++
        //     } else if (sample.getAttribute('data-status') == 'T2Error') {
        //         T2++
        //     } else {
        //         pass++
        //     }
        // })

        // qtyArray = []
        // measuredQty.forEach(qty => {

        //     qtyArray.push(+qty.value)
        // })

        // // console.log('Samples With T1 Error ' + T1);
        // // console.log('Samples With T2 Error ' + T2);
        // // console.log('Samples Passed ' + pass);
        // const total = qtyArray.reduce((previous, next) => {
        //     return previous + next
        // })

        // const individualError = total / qtyArray.length

        // console.log('total ' + total);
        // console.log('individual Error ' + total);

        $.ajax({
            type: "post",
            url: "getMeasurementData",
            data: {
                productId: id
            },
            dataType: "json",
            success: function(response) {
                let declaredQuantity = document.querySelector('#productGrossWeight').value
                if (response.length == 0) {
                    console.log('there is no measurement data for this product please create one');
                }
                const withT1Error = response.filter((data) => data.status == 1)
                const withT2Error = response.filter((data) => data.status == 2)

                const netQuantities = response.map(net => {
                    return +declaredQuantity - net.net_weight
                })


                const individualError = netQuantities.reduce((prev, next) => {
                    return prev + next
                }, 0) / netQuantities.length



                renderSummaryTable(withT1Error, withT2Error, individualError, netQuantities)
                renderMeasurementTable(response)


                // let individualError = netQuantities.forEach(qty => {

                // })

                console.log(withT1Error);
                // console.log('Data Length ' + netQuantities.length);
                // console.log('Individual error is ' + individualError);
            }
        });
    }



    function renderMeasurementTable(data) {
        let tb = ``

        data.forEach(d => {
            tb += `
        <tr>
          <td>${d.gross_weight}</td>
          <td>${d.net_weight}</td>
          <td>${d.comment}</td>
        </tr>
           `
        })

        $('#measurementTable').html(tb)
    }

    function renderSummaryTable(t1, t2, individualError, g) {


        const individualStandardDeviation = standardDeviation(g).toFixed(4)


        console.log('individualStandardDeviation........ ' + individualStandardDeviation);



        let theSampleSize = document.querySelector('#productSampleSize').value
        let productQuantity = document.querySelector('#productGrossWeight').value

        const sampleLimit = tolerableDeficiency(productQuantity)

        const sampleErrorLimit = productQuantity - sampleLimit


        function nominalQtyPercent(nQ) {
            if (nQ == 0 && nQ <= 50) {
                return 9
            } else if (nQ >= 101 && nQ <= 200) {
                return 4.5
            } else if (nQ >= 301 && nQ <= 500) {
                return 3
            } else if (nQ >= 1001 && nQ <= 10000) {
                return 1.5
            } else if (nQ > 15000) {
                return 1
            } else {
                return '-'
            }
        }

        function nominalQtyGram(nQ) {
            if (nQ >= 51 && nQ <= 100) {
                return 4.5
            } else if (nQ >= 201 && nQ <= 300) {
                return 9
            } else if (nQ >= 501 && nQ <= 1000) {
                return 15
            } else if (nQ >= 10001 && nQ <= 15000) {
                return 150
            } else {
                return '-'
            }
        }



        let t1Percentage = t1.length * 100 / +theSampleSize

        let realT1 = t1.map(t => {
            return t.net_weight
        })

        let t2Percentage = t2.length * 100 / +theSampleSize

        let realT2 = t2.map(t => {
            return t.net_weight
        })

        const samplesWithError = realT1.concat(realT2)

        const tolerableAmount = tolerableDeficiency(productQuantity)

        const tError = realT1.concat(realT2)






        const averageError = samplesWithError.reduce((prev, next) => {
            return +prev + +next
        }, 0) / samplesWithError.length



        console.log('................................');
        console.log(samplesWithError);
        console.log('................................');



        let approved = 0

        let correctionFactor = 0

        let decision = ''

        let lotX = document.querySelector('#lotSize').value

        if (lotX >= 100 && lotX <= 500) {
            approved += 3

            if (t1.length > 3) {
                decision = ' Sample Failed the required test reject'
            }

            correctionFactor += 0.379
        } else if (lotX >= 501 && lotX <= 3200) {
            approved += 5
            if (t1.length > 5) {
                decision = ' Sample Failed the required test reject'
            }
            correctionFactor += 0.295
        } else if (lotX > 3200) {
            approved += 7
            if (t1.length > 7) {
                decision = ' Sample Failed the required test reject'
            }
            correctionFactor += 0.234
        }

        function checkPositiveOrNegative(individualError) {
            if (individualError > 0) {
                return 'Positive ' + individualError
            } else {
                return 'Negative ' + individualError
            }
        }





        let table = `
         <table class="table table-bordered table-sm">
                    <thead class="thead-dark">

                        <tr>
                            <th>Test Type</th>
                            <th>Result & Recommendation</th>
                            <th>Observed</th>
                            <th>Approved Limit</th>+
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>T1 Test Result</td>
                            <td>${tError.length > 3 ?'Sample Failed T1 Reject' :'Sample Passed T1'}  </td>
                            <td>${tError.length}</td>
                            <td>${approved}</td>
                        </tr>

                        <tr>
                            <td>T2 Test Result</td>
                            <td>${t2.length > 0 ?'Sample Failed T2 Reject' :'Sample Passed T2'} </td>
                            <td>${t2.length}</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Individual Pre-Package Error Test - Result</td>
                            <td>${individualError> 0 ?'Samples Passed Individual Average Error Test Result'
                            :'Samples Failed Individual Average Error Test Result'}</td>
                            <td> (${checkPositiveOrNegative(individualError.toFixed(3))})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Corrected Average Error Test Result</td>
                            <td>${(individualError * correctionFactor) >0 ?'Samples Passed Corrected Average Error Test Result' :'Samples Failed Corrected Average Error Test Result'}</td>
                               <td> (${checkPositiveOrNegative((individualError * correctionFactor).toFixed(3))})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Conclusion Remarks</td>
                            <td colspan="3">${realT2.length > 0 ?'Sample Failed the required test reject' :'Sample Passed the required test'}</td>
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
                            <td><b>Number of Item With T1</b></td>
                            <td>${tError.length}</td>
                        </tr>
                        <tr>
                            <td><b>Percent No T1 / Sample Size</b></td>
                            <td>${t1Percentage}%</td>
                        </tr>
                        <tr>
                            <td><b>Decision At This Stage </b></td>
                            <td>${t1.length > approved ?'Sample Failed T1 Test, Reject':'Sample Passes T1 Test'}</td>
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
                            <td>${nominalQtyGram(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td><b>Minimum For T2</b></td>
                           <td>${Number(productQuantity - (tolerableAmount*2) )}</td>
                        </tr>
                        <tr>
                            <td><b>Number of Item With T2</b></td>
                            <td>${t2.length}</td>
                        </tr>
                        <tr>
                            <td><b>Percent No T2 / Sample Size</b></td>
                            <td>${t2Percentage}%</td>
                        </tr>
                        <tr>
                            <td><b>Decision At This Stage </b></td>
                            <td>${t2.length >0 ? 'Sample Failed T2 Test, Reject': 'Sample Passed T2 Test'}</td>
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
                    <td><b>Total Pre Package Error</b></td>
                    <td>${individualError *  theSampleSize}</td>
                 </tr>
                  <tr>
                    <td><b>Average Error</b></td>
                    <td>${individualError }</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b></td>
                    <td>${individualError > 0 ? 'Passed Individual Pre Package Error':'Failed Individual Pre Package Error'}</td>
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
              <td>${Number(individualStandardDeviation * correctionFactor).toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Corrected Average Error</b></td>
                    <td>${(individualError + Number(individualStandardDeviation * correctionFactor)).toFixed(3) }</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b> </td>
                    <td>${(individualError * correctionFactor) < 0 ? 'Sample Failed , Reject' :'Sample Pass'}</td>
                 </tr>
                 </tbody>
                </table>
        
        
        `

        $('#prePackageSummary').html(table)
    }
</script>

<?= $this->endSection(); ?>