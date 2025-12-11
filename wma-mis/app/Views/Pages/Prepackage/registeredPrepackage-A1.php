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
<div class="container">
    <div class="card">
        <div class="card-header">

            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#searchModal">
                <i class="fal fa-search"></i> Search Customer
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

</div>


<!-- /.content-header -->
<div class="container">

    <!-- <div class="card">
        <div class="card-header">

            <?php if ($role == 7 || $role == 3) : ?>
                <div class="form-group">
                    <label for=""></label>
                    <select class="form-control" name="" id="">
                        <option></option>
                        <option></option>
                        <option></option>
                    </select>
                </div>
            <?php else : ?>
            <?php endif; ?>



        </div>
        <div class="card-body">
            <table class="table table-sm">

                <tbody id="customerInfo">


                </tbody>
            </table>
        </div>
       
    </div> -->
    <div class="card">



        <div class="card-body">
            <div class="form-group">
                <label for="">Products</label>
                <select class="form-control" name="" id="products" onchange="selectProduct(this.value)">



                </select>
            </div>
        </div>
        <div id="commodityInfo"></div>
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







<!-- Modal -->
<div class="container">

    <div class="card">
        <div class="card-body">
            <div id="prePackageSummary">

            </div>




        </div>
        <div class="card-footer">
            <a href="" id="downloadBtn" target="_blank" class="btn btn-primary btn-sm">Download</a>
        </div>
    </div>
</div>



<?= csrf_field() ?>






</div>










<script>
    //percentage calculator
    function percentage(percent, theValue) {
        return (percent / 100) * theValue
    }




    //calculating standard deviation
    const standardDeviation = (arr, usePopulation = false) => {
        const mean = arr.reduce((acc, val) => acc + val, 0) / arr.length;

        return Math.sqrt(
            arr
            .reduce((acc, val) => acc.concat(), []) //(val - mean) ** 2
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


    //function to calculate tolerable deficiency of the nominal quantity

    function tolerableDeficiency(nQ) {
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
    }


    // selecting dom elements
    const searchForm = document.querySelector('#searchForm')


    // ajax call method


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

                // calculatePrice(lotSizes)







                let products = ``
                products += '<option selected>--Products--</option>'
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





    // searching customer


    searchForm.addEventListener('submit', (e) => {
        e.preventDefault()

        console.log('testing....');

        $.ajax({
            type: "POST",
            url: 'searchPrePackageCustomer',
            data: {
                keyword: document.querySelector('#keyword').value
            },

            dataType: "json",

            success: function(response) {

                renderSearchResults(response)
                // getCustomerInfo(response.hash)


            },
            error: function(err) {
                console.log(err);
            }

        });





    })










    //console.log('Percent: ' + percentage(10, 50000) + '%');









    // let myArray = []









    function selectProduct(id) {

        const customerIdentifier = document.querySelector('#customerId').value

        const download = document.querySelector('#downloadBtn')

        const link = `<?= base_url() ?>/downloadProductData/${customerIdentifier}/${id}`

        download.setAttribute('href', link)

        console.log('Link:' + link);


        $.ajax({
            type: "POST",
            url: "selectProduct",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                // getMeasurements(id, response)



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


    function getMeasurements(id, data) {

        //uncommented

        $('#measurementsPreview').modal('show')




        // console.log('form submitted...');
        // const samples = document.querySelectorAll('[data-status]');
        // const measuredQty = document.querySelectorAll('.measuredQty');

        // let netQuantities = document.querySelectorAll('.netQuantity')
        // let declaredQuantity = document.querySelector('#productGrossWeight').value
        // let netQuantityArray = []


        // netQuantities.forEach(net => {
        //     console.log(+net.value);
        //     netQuantityArray.push(+declaredQuantity - +net.value)
        // })

        // console.log(netQuantityArray);

        let T1 = 0
        let T2 = 0
        let pass = 0



        // qtyArray = []
        // measuredQty.forEach(qty => {

        //     qtyArray.push(+qty.value)
        // })
        // //    //uncommented 

        // console.log('Samples With T1 Error ' + T1);
        // console.log('Samples With T2 Error ' + T2);
        // console.log('Samples Passed ' + pass);
        // const total = qtyArray.reduce((previous, next) => {
        //     return previous + next
        // })

        // const individualError = total / qtyArray.length


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
                    return Number(net.net_weight - declaredQuantity)
                })

                console.log('*******************************************');
                console.log(netQuantities);
                console.log('*******************************************');


                const individualError = netQuantities.reduce((prev, next) => {
                    return prev + next
                }, 0)



                renderSummaryTable(withT1Error, withT2Error, individualError, netQuantities)
                renderMeasurementTable(response)


                // let individualError = netQuantities.forEach(qty => {

                // })

                // console.log(withT1Error);
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


        // console.log('individualStandardDeviation........ ' + individualStandardDeviation);



        let theSampleSize = document.querySelector('#productSampleSize').value
        let productQuantity = document.querySelector('#productGrossWeight').value

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
            return t.net_weight
        })

        let t2Percentage = t2.length * 100 / +theSampleSize

        let realT2 = t2.map(t => {
            return t.net_weight
        })

        const samplesWithError = realT1.concat(realT2)

        let t1Percentage = t1.length * 100 / +theSampleSize

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
                            <td>${t1.length > 3 ?'Sample Failed T1-Reject' :'Sample Passed T1 Test- Go For T2 Test '}  </td>
                            <td>${t1.length}</td>
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
                            <td>${individualError> 0 ?'Samples Passed Individual Pre-packages Error Test'
                            :'Samples Failed Individual Pre-packages Error Test'}</td>
                            <td> (${checkPositiveOrNegative(individualError.toFixed(3))})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Corrected Average Error Test Results</td>
                            <td>${(Number(individualError / theSampleSize ) * correctionFactor) >0 ?'Samples Passed Corrected Average Error Test Result' :'Samples Failed Corrected Average Error Test Result'}</td>
                               <td> (${checkPositiveOrNegative((Number(individualError / theSampleSize ) * correctionFactor).toFixed(3))})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Conclusion Remarks</td>
                            <td colspan="3">${realT2.length > 0 ?'Sample Failed All required test-Reject' :'Sample Passed All required Test- Approve'}</td>
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
                            <td><b>${t1.length > approved ?'Sample Fail T1 Test- Reject':'Sample Pass T1 Test- Go for T2 Test'}</b></td>
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
                            <td><b>Number of Item With T2 Error</b></td>
                            <td>${t2.length}</td>
                        </tr>
                        <tr>
                            <td><b>Percent No T2 / Sample Size</b></td>
                            <td>${t2Percentage}%</td>
                        </tr>
                        <tr>
                            <td><b>Decision At This Stage </b></td>
                            <td><b>${t2.length >0 ? 'Sample Fail T2 Test-Reject': 'Sample Pass T2 Test- Go for Pre-package Error Test'}</b></td>
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
                    <td>${(individualError /theSampleSize) .toFixed(3) }</td>
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
              <td>${Number(individualStandardDeviation * correctionFactor).toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Corrected Average Error</b></td>
                    <td>${(individualError + Number(individualStandardDeviation * correctionFactor)).toFixed(3) }</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b> </td>
                    <td><b>${(individualError * correctionFactor) < 0 ? 'Sample Fail Corrected Average Error Test' :'Sample Pass Corrected Average Error Test'}</b></td>
                 </tr>
                 </tbody>
                </table>


        `

        $('#prePackageSummary').html(table)
    }
</script>

<?= $this->endSection(); ?>