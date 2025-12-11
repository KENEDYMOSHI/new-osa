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
    <div class="card">



        <div class="card-body">
            <div class="form-group">
                <label for="">Products</label>
                <select class="form-control" name="" id="products" onchange="selectProduct(this.value)">



                </select>
            </div>
            <div id="commodityInfo"></div>
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
<div class="">

    <div class="card">
        <div class="card-body">
            <div id="prePackageSummary">

            </div>




        </div>
        <div class="card-footer">
            <a id="downloadBtn" target="_blank" class="btn btn-primary btn-sm"><i class="fal fa-download "></i> Download</a>
        </div>
    </div>
</div>



<?= csrf_field() ?>






</div>










<script>
    // function productBill(lot, products) {



    //     if (lot > 0 && lot <= 100) {
    //         return 30000
    //     } else if (lot >= 101 && lot <= 500) {
    //         return 100000
    //     } else if (lot >= 501 && lot <= 3200) {
    //         return 100000
    //     } else if (lot > 3201) {
    //         if (products >= 1 && products <= 5) {
    //             return 300000
    //         } else if (products >= 6 && products <= 10) {
    //             return 200000
    //         } else if (products > 11) {
    //             return 100000
    //         }
    //     }
    // }


    // function calculateTotal() {
    //     const customerId = document.querySelector('#customerId').value

    //     let dataArray = []


    //     $.ajax({
    //         type: "POST",
    //         url: "getCompleteProducts",
    //         data: {
    //             customerId: customerId
    //         },
    //         dataType: "json",
    //         success: function(response) {

    //             console.log(response);

    //             let amount = 0
    //             if (response.products.length > 0) {

    //                 const LotSizes = response.products.map(product => {
    //                     return Number(product.lot)
    //                 })

    //                 const largeLotSizes = response.products.filter(product => Number(product.lot) > 3201).map(product => {
    //                     return Number(product.lot)
    //                 })

    //                 LotSizes.forEach(lot => {
    //                     amount += productBill(lot, largeLotSizes.length)
    //                     // console.log(`lot Size Of ${lot} has Amount of ${productBill(lot, largeLotSizes.length)}`);
    //                 })


    //                 document.querySelector('#totalAmount').value = amount

    //                 let productGroup = ``
    //                 const prods = response.products.map(product => {
    //                     return ({
    //                         id: product.id,
    //                         hash: product.hash,
    //                         lot: product.lot,
    //                         amount: productBill(product.lot, largeLotSizes.length)

    //                     })
    //                 }).forEach(product => {
    //                     console.log(product);

    //                     productGroup += `

    //                     <input hidden  type="text" name="customerHash[]" id="prodId[]" value="${product.hash}" class="form-control mb-1">
    //                     <input hidden  type="text" name="prodId[]" id="prodId[]" value="${product.id}" class="form-control mb-1">
    //                     <input hidden  type="text" name="prodMount[]" id="prodMount[]" value="${product.amount}" class="form-control mb-1"">

    //                 `

    //                 })

    //                 $('#productGroup').html(productGroup)
    //             } else {

    //                 swal({
    //                     title: 'No Data Found',
    //                     icon: "warning",
    //                     timer: 2500
    //                 });

    //             }

    //             // console.log(LotSizes);
    //             // console.log('Large ' + largeLotSizes.length);
    //             // console.log(amount);
    //             // console.log(prods);


    //         }
    //     });
    // }







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
        } else {
            return percentage(0, nQ)
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





    //get customer info based on hash value mvc
    function getCustomerInfo(hash) {

        // let lotSizes = []


        $.ajax({
            type: "post",
            url: "getPrePackageCustomer",
            data: {
                hash: hash
            },
            dataType: "json",
            success: function(response) {
                $('#searchModal').modal('hide')

                let lotSizes = response.products.map(product => {
                    return product.lot
                })

                // calculatePrice(lotSizes)







                let products = ``
                products += '<option disabled selected>--Products--</option>'
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

        // const qId = '6190125215-12000 mm'
        const qId = '001'

        const customerIdentifier = document.querySelector('#customerId').value

        const download = document.querySelector('#downloadBtn')

        const link = `<?= base_url() ?>/downloadProductData/${customerIdentifier}/${id}/${qId}`

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
                        
                        <div class = "form-group" >
                    <label for = "" > Quantity </label> 
                    <select class = "form-control"id = "quantityId">
                    ${
                        (function fun() {
                            let select = ''
                            response.quantityIds.forEach(id => {
                                select += `<option value='${id}'>${id.substr(11)}</option>`
                            })
                            return select
                        })()
                    }

                    </select> 
                    </div>

                    <button class = "btn btn-primary btn-sm"
                    onclick = "getMeasurements('${response.data.id}')" > View Measurement Sheet </button>
                    
                        </td>
                    </tr>
                 
              
            </table>
                
                `)
            }
        });

    }


    function getMeasurements(id) {

        //uncommented

        $('#measurementsPreview').modal('show')
        const customerIdentifier = document.querySelector('#customerId').value
        const quantityId = document.querySelector('#quantityId').value

        const download = document.querySelector('#downloadBtn')

        const link = `<?= base_url() ?>/downloadProductData/${customerIdentifier}/${id}/${quantityId ? quantityId: '00'}`

        download.setAttribute('href', link)


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
                includeQuantity: true,
                productId: id,
                quantityId: quantityId ? quantityId : '',
            },
            dataType: "json",
            success: function(response) {
                console.log(response)
                // collectiveDecision()
                $('#measurementsPreview').modal('show')
                // let declaredQuantity = document.querySelector('#productGrossWeight').value
                let declaredQuantity = response.data[0].quantity_id.substr(11).replace(/[^\d.-]/g, '')
                console.log('declaredQuantity ' + declaredQuantity)
                if (response.data.length == 0) {
                    // console.log('there is no measurement data for this product please create one');
                }
                const withT1Error = response.data.filter((data) => data.status == 1)
                const withT2Error = response.data.filter((data) => data.status == 2)

                const netQuantities = response.data.map(net => {
                    return Number(net.net_quantity - declaredQuantity)
                })

                console.log('*********************net q**********************');
                console.log(netQuantities);
                console.log('*******************************************');


                const individualError = netQuantities.reduce((prev, next) => {
                    return prev + next
                }, 0)




                renderSummaryTable(withT1Error, withT2Error, individualError, netQuantities, declaredQuantity,response.results)
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

    function renderSummaryTable(t1, t2, individualError, g, declaredQuantity,results) {


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






        console.log('..........$$$$$$$$$$$$$$$$$$$......................');
        console.log('productQuantity ' + productQuantity);
        console.log('tolerableAmount ' + tolerableAmount);
        console.log('................................');



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

<?= $this->endSection(); ?>