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
            <table class="table">

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
    <button type="button" class="btn btn-primary btn-sm mb-3" onclick="renderMeasurementData()">
        Create Measurement Sheet
    </button>
</div>



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#measurementsPreview">
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

                    <table class="table table-bordered">
                        <thead>
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
        } else if (nQ <= 51 && nQ <= 100) {
            return 4.5
        } else if (nQ <= 101 && nQ <= 200) {
            return percentage(4.5, nQ)
        } else if (nQ <= 201 && nQ <= 300) {
            return 9
        } else if (nQ <= 301 && nQ <= 500) {
            return percentage(3, nQ)
        } else if (nQ <= 501 && nQ <= 1000) {
            return 15
        } else if (nQ <= 1001 && nQ <= 10000) {
            return percentage(1.5, nQ)
        } else if (nQ <= 10001 && nQ <= 15000) {
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


    //get customer info based on hash value
    function getCustomerInfo(hash) {


        $.ajax({
            type: "post",
            url: "editPrePackageCustomer",
            data: {
                hash: hash
            },
            dataType: "json",
            success: function(response) {
                $('#searchModal').modal('hide')

                let products = ``
                const customer = response.data
                // console.log(response.products);

                response.products.forEach(product => {
                    products += `<option value="${product.id}">${product.commodity}  ${product.quantity} ${product.unit}</option>`

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
            <input type="number" required name="sampleSize" id="sampleSize" class="form-control weight" min="0" oninput="calcAverage()">
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
            <input type="number" required name="sampleSize" id="sampleSize" class="form-control weight" min="0" oninput="calcAverage()">
            </div>
            `

            }
            // $('#weights').html('')
            $('#weights').append(inputs)

        } else {
            console.log('Tare Weight FAILED');

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


        const tareWeight = document.querySelector('#tareWeight').value
        const quantity = document.querySelector('#grossWeightValue').value

        const tolerable = tolerableDeficiency(quantity)

        let enteredWeight = gross.value
        let netWeightInput = gross.parentNode.nextElementSibling.childNodes[0];
        let netWeight = netWeightInput;

        let comment = gross.parentNode.nextElementSibling.nextElementSibling.childNodes[0].nextElementSibling;

        console.log(comment);





        // netWeight.value = +element.value - tare
        // console.log('gross =' + grossWeight);
        // console.log('net ' + netWeight.value);
        // console.log('tareWeight ' + tareWeight);
        // console.log('quantity ' + quantity);
        // console.log('Tolerable ' + tolerable);
        calculateError(+enteredWeight, +quantity, +tolerable, +tareWeight, netWeight, comment)

        console.log(myArray);


    }
    //calculate  T1 and T2 error
    function calculateError(enteredWeight, quantity, tolerable, tareWeight, netWeight, comment) {
        const productNature = document.querySelector('#productNature').value
        const productDensity = document.querySelector('#density').value
        comment.value = ''
        const grossWt = Number(quantity + tareWeight)
        let net = (enteredWeight - tareWeight)

        if (productNature == 'Liquid' && productDensity != '') {

            netWeight.value = (net / +productDensity).toFixed(2)
        } else {
            netWeight.value = net
        }



        // console.log('nature ' + productNature);
        //console.log('density ' + productDensity);
        // console.log('Net ' + net);


        let T1 = Number(quantity - tolerable)
        let T2 = Number(quantity - (tolerable * 2))

        console.log('T1 ' + T1);
        console.log('T2 ' + T2);
        console.log('grossWt ' + grossWt);

        netWeight.removeAttribute('data-status')


        if (enteredWeight <= T1 && enteredWeight > T2) {
            comment.value = 'Has  T1 Error'
            netWeight.style.border = '1px solid  red'
            netWeight.setAttribute('data-status', 'T1Error')

        } else if (enteredWeight <= T2) {
            comment.value = 'Has T1 And T2 Error'
            netWeight.style.border = '1px solid  red'
            netWeight.setAttribute('data-status', 'T2Error')
        } else {
            comment.value = 'Has Pass T1  And T2 Error'
            netWeight.style.border = '1px solid  green'
            netWeight.setAttribute('data-status', 'Pass')
        }

    }



    //rendering measurement data based on sample size
    function renderMeasurementData(dataSize) {

        $('#measurementSheet').modal('show')
        const sampleSize = document.querySelector('#sampleSize').value

        const categoryAnalysis = document.querySelector('#categoryAnalysis')
        let general = `
         <thead>
           <tr>
            <th>Gross Quantity</th>
            <th>Net Quantity</th>
            <th>Comment</th>
          </tr>
        </thead>
      <tbody>

        `
        let table = ``
        //

        for (let index = 1; index <= sampleSize; index++) {
            table += `<tr>
                       <td style="width: ;"><input id="grossInputs" name="weightGross[]" oninput="checkForError(this)" class="form-control measuredQty" type="number"></td>
                       <td style="width: ;"><input step="any" name="weightNet[]" data-weight='net'   class="form-control netQuantity" type="number"></td>
                       <td >
                       <input data-comment="comment" name="comment[]" class="form-control style="border="0";outline:none; "readonly/>
                       
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

                    console.table(response);


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

                $('#commodity').val(response.data.commodity)
                $('#quantity').val(response.data.quantity)
                $('#unit').val(response.data.unit).select()
                $('#categoryAnalysis').val(response.data.analysis_category).select()
                $('#batchSize').val(response.data.lot)
                $('#method').val(response.data.method).select()
                $('#measurementUnit').val(response.data.measurement_unit).select()
                $('#sampling').val(response.data.sampling).select()
                $('#measurementNature').val(response.data.measurement_nature).select()
                $('#tareWeight').val(response.data.tare)
                $('#grossWeightValue').val(response.data.gross_weight)
                $('#productNature').val(response.data.product_nature).select()
                $('#density').val(response.data.density)
                $('#sampleSize').val(response.data.sample_size)



                $('#commodityInfo').html(`
                
            <table class="table">

                
               <tr>
               <td>
               <input class="form-control" id="commodityId" value="${response.data.id}"/>
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
                       
                        <td>Category Analysis</td>
                        <td>${response.data.analysis_category}  </td>
                    </tr>
                    <tr>
                       
                        <td>Nature Of The Product</td>
                        <td>${response.data.product_nature}  </td>
                    </tr>
                    <tr>
                       
                        <td>Density</td>
                        <td>${response.data.density}  </td>
                    </tr>
                    <tr>
                       
                        <td>Gross Weight (gram or milliliter)</td>
                        <td>${response.data.gross_weight } </td>
                    </tr>
                    <tr>
                       
                        <td>Sampling</td>
                        <td> ${response.data.sampling}</td>
                    </tr>
                    <tr>
                       
                        <td>Method</td>
                        <td> ${response.data.method}</td>
                    </tr>

                    <tr>
                        <td>Tare</td>
                        <td>${response.data.tare} g</td>
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
                let declaredQuantity = document.querySelector('#grossWeightValue').value
                if (response.length == 0) {
                    console.log('there is no measurement data for this product please create one');
                }
                const withT1Error = response.filter((data) => data.status == 'Has T1 Error')
                const withT2Error = response.filter((data) => data.status == 'Has T1 And T2 Error')

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

                // console.log(netQuantities);
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
          <td>${d.status}</td>
        </tr>
           `
        })

        $('#measurementTable').html(tb)
    }

    function renderSummaryTable(t1, t2, individualError, g) {


        const individualErrorStandardDeviation = standardDeviation(g).toFixed(3)


        console.log('individualStandardDeviation........ ' + individualStandardDeviation);



        let theSampleSize = document.querySelector('#sampleSize').value
        let productQuantity = document.querySelector('#grossWeightValue').value


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






        const averageError = samplesWithError.reduce((prev, next) => {
            return +prev + +next
        }, 0) / samplesWithError.length



        // console.log('................................');
        // console.log(averageError);
        // console.log('................................');



        let approved = 0

        let correctionFactor = 0

        let decision = ''

        let lotX = document.querySelector('#batchSize').value

        if (lotX >= 100 && lotX <= 500) {
            approved += 3

            if (t1.length > 3) {
                decision = 'Failed the required test reject'
            }

            correctionFactor += 0.379
        } else if (lotX >= 501 && lotX <= 3200) {
            approved += 5
            if (t1.length > 5) {
                decision = 'Failed the required test reject'
            }
            correctionFactor += 0.295
        } else if (lotX > 3200) {
            approved += 7
            if (t1.length > 7) {
                decision = 'Failed the required test reject'
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
         <table class="table table-bordered">
                    <thead>

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
                            <td>Sample Failed T1 Reject </td>
                            <td>${t1.length}</td>
                            <td>${approved}</td>
                        </tr>

                        <tr>
                            <td>T2 Test Result</td>
                            <td>Sample Pass T1 Test - Go For T2 Test</td>
                            <td>${t2.length}</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Individual Pre-Package Error Test</td>
                            <td>Sample Pass Pre-Package Error Test</td>
                            <td> (${checkPositiveOrNegative(individualError)})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Corrected Average Error Test Result</td>
                            <td>Samples Pass Average Error Test Result</td>
                               <td> (${checkPositiveOrNegative(individualError * correctionFactor)})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Conclusion Remarks</td>
                            <td colspan="3">${decision}</td>
                        </tr>


                    </tbody>
                </table>

                <hr>

                <h3>Analysis Details For The Required Test</h3>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Figure</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Percent of Qn</td>
                            <td>${nominalQtyPercent(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td>g or mL</td>
                            <td>${nominalQtyGram(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td>Minimum For T1</td>
                            <td>${Math.min(...realT1)}</td>
                        </tr>
                        <tr>
                            <td>Number of Items With T1</td>
                            <td>${t1.length}</td>
                        </tr>
                        <tr>
                            <td>Percent No T1 / Sample Size</td>
                            <td>${t1Percentage}%</td>
                        </tr>
                        <tr>
                            <td>Decision At This Stage </td>
                            <td>Sample Failed T1 Test, Reject</td>
                        </tr>

                    </tbody>
                </table>

                <hr>
                  <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Figure</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Percent of Qn</td>
                            <td>${nominalQtyPercent(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td>g or mL</td>
                            <td>${nominalQtyGram(productQuantity)}</td>
                        </tr>
                        <tr>
                            <td>Minimum For T2</td>
                            <td>${Math.min(...realT2)}</td>
                        </tr>
                        <tr>
                            <td>Number of Items With T2</td>
                            <td>${t2.length}</td>
                        </tr>
                        <tr>
                            <td>Percent No T2 / Sample Size</td>
                            <td>${t2Percentage}%</td>
                        </tr>
                        <tr>
                            <td>Decision At This Stage </td>
                            <td>Sample Failed T2 Test, Reject</td>
                        </tr>

                    </tbody>
                </table>

                <hr>
                <table class="table table-bordered">
                <thead>
                        <tr>
                            <th>Item</th>
                            <th>Figure</th>
                        </tr>
                    </thead>
                 <tbody>
                  <tr>
                    <td>Total Pre Package Error</td>
                    <td>${samplesWithError.length}</td>
                 </tr>
                  <tr>
                    <td>Average Error</td>
                    <td>${averageError.toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td>Decision At This Stage</td>
                    <td></td>
                 </tr>
                 </tbody>
                </table>
                <hr>
                <table class="table table-bordered">
               
                 <tbody>
                  <tr>
                    <td>Standard Deviation of Individual Package Error</td>
                    <td>${individualErrorStandardDeviation}</td>
                 </tr>
                  <tr>
                    <td>Sample Size</td>
                    <td>${theSampleSize}</td>
                 </tr>
                  <tr>
                    <td>Number Of Samples Allowed To Have T1</td>
                    <td>${approved}</td>
                 </tr>
                  <tr>
                    <td>Sample Error Limit</td>
                    <td></td>
                 </tr>
                  <tr>
                    <td>Corrected Average Error</td>
                    <td></td>
                 </tr>
                  <tr>
                    <td>Decision At This Stage </td>
                    <td>Sample Failed T2 Test, Reject</td>
                 </tr>
                 </tbody>
                </table>
        
        
        `

        $('#prePackageSummary').html(table)
    }
</script>

<?= $this->endSection(); ?>