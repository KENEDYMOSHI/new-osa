
  
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

function selectProduct(id) {
  $('#dataSheetTable').html('');

  getMeasurements(id);

  $.ajax({
    type: 'POST',
    url: 'selectProduct',
    data: {
      id: id,
    },
    dataType: 'json',
    success: function (response) {
      // console.log(response);

      if (response.data.product_nature == 'Solid') {
        document.querySelector('#densityBtn').style.display = 'none';
        document.querySelector('.density').style.display = 'none';
      } else {
        document.querySelector('#densityBtn').style.display = 'block';
        document.querySelector('.density').style.display = 'block';
      }

      $('#commodityInfo').html(`
                
            <table class="table table-sm">

                
            <input class="form-control" id="commodityId" value="${
              response.data.id
            }"  hidden />
            <input class="form-control cat" id="categoryOfAnalysis" value="${
              response.data.analysis_category
            }" hidden  />
               
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
                        <td>${
                          response.data.quantity
                        } <span style="font-family: 'Roboto', sans-serif;" class="unit">${
        response.data.unit
      }</span> </td>
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
                        <input class="form-control" id="lotSize" value="${
                          response.data.lot
                        }" hidden/>

                    </tr>
                    <tr>
                    
                        <td>Sample Size</td>
                        <td>${response.data.sample_size}</td>
                        <input class="form-control" id="productSampleSize" value="${
                          response.data.sample_size
                        }" hidden/>

                    </tr>
                    
                    <tr>
                       
                        <td>Category Analysis</td>
                        <td>${response.data.analysis_category}  </td>
                    </tr>
                    <tr>
                       
                        <td>Nature Of The Product</td>
                        <td>${response.data.product_nature}  </td>
                        <input class="form-control" id="natureOfProduct" value="${
                          response.data.product_nature
                        }" hidden/>
                    </tr>
                    ${
                      response.data.density
                        ? `<tr>
                       
                        <td>Declared Product Density</td>
                        <td>${response.data.density}  </td>
                         <input class="form-control" id="productDensity" value="${response.data.density}" hidden/>
                    </tr>
                    `
                        : `<input class="form-control" id="productDensity" value="${response.data.density}" hidden/>`
                    }
                    <tr>
                       
                        <td>Mass / Volume (gram or milliliter)</td>
                        <td>${response.data.gross_weight} </td>
                        <input class="form-control" id="productGrossWeight" value="${
                          response.data.gross_weight
                        }" hidden/>
                    </tr>
                    <tr>
                       
                        <td>Sampling Plan</td>
                        <td> ${response.data.sampling}</td>
                    </tr>
                    <tr>
                       
                        <td>Method To Be Applied </td>
                        <td> ${response.data.method}</td>
                        <input class="form-control" id="appliedMethod" value="${
                          response.data.method
                        }" hidden />
                    </tr>

                    <tr>
                        <td>Declared Tare  Weight</td>
                        <td>${response.data.tare} g</td>
                         <input class="form-control" id="productTare" value="${
                           response.data.tare
                         }" hidden/>
                    </tr>

                    <tr>
                        <td>Action</td>
                        <td><button class="btn btn-primary btn-sm" onclick="getMeasurements('${
                          response.data.id
                        }')">View Measurement Sheet</button></td>
                    </tr>
                 
              
            </table>
                
                `);
    },
  });
}


//=====================================
function renderSummaryTable(t1, t2, individualError, g) {
  const individualStandardDeviation = standardDeviation(g).toFixed(4);

  //console.log('individualStandardDeviation........ ' + individualStandardDeviation);

  let theSampleSize = document.querySelector('#productSampleSize').value;
  let productQuantity = document.querySelector('#productGrossWeight').value;

  const sampleLimit = tolerableDeficiency(productQuantity);

  const sampleErrorLimit = productQuantity - sampleLimit;

  function nominalQtyPercent(nQ) {
    if (nQ == 0 && nQ <= 49) {
      return 9;
    } else if (nQ >= 100 && nQ <= 199) {
      return 4.5;
    } else if (nQ >= 300 && nQ <= 499) {
      return 3;
    } else if (nQ >= 1000 && nQ <= 9999) {
      return 1.5;
    } else if (nQ > 15000) {
      return 1;
    } else {
      return 0;
    }
  }

  function nominalQtyGram(nQ) {
    if (nQ >= 50 && nQ <= 99) {
      return 4.5;
    } else if (nQ >= 200 && nQ <= 299) {
      return 9;
    } else if (nQ >= 500 && nQ <= 999) {
      return 15;
    } else if (nQ >= 10000 && nQ <= 15000) {
      return 150;
    } else {
      return 0;
    }
  }

  let realT1 = t1.map((t) => {
    return t.net_weight;
  });

  let t2Percentage = (t2.length * 100) / +theSampleSize;

  let realT2 = t2.map((t) => {
    return t.net_weight;
  });

  const samplesWithError = realT1.concat(realT2);

  let t1Percentage = (samplesWithError.length * 100) / +theSampleSize;

  const tolerableAmount = tolerableDeficiency(productQuantity);

  const tError = realT1.concat(realT2);

  let averageError = individualError / +theSampleSize;

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

  let approved = 0;

  let correctionFactor = 0;

  let decision = '';

  let lotX = document.querySelector('#lotSize').value;
  let appliedMethod = document.querySelector('#appliedMethod').value;

  if (lotX >= 100 && lotX <= 500 && appliedMethod == 'Non Destructive') {
    approved += 3;

    if (t1.length > 3 && appliedMethod == 'Non Destructive') {
      decision = ' Sample Failed the required test reject';
    }

    correctionFactor += 0.379;
  } else if (
    lotX >= 501 &&
    lotX <= 3200 &&
    appliedMethod == 'Non Destructive'
  ) {
    approved += 5;
    if (t1.length > 5) {
      decision = ' Sample Failed the required test reject';
    }
    correctionFactor += 0.295;
  } else if (lotX > 3200 && appliedMethod == 'Non Destructive') {
    approved += 7;
    if (t1.length > 7) {
      decision = ' Sample Failed the required test reject';
    }
    correctionFactor += 0.234;
  } else if (lotX >= 100 && appliedMethod == 'Destructive') {
    approved += 1;
    if (t1.length > 1) {
      decision = ' Sample Failed the required test reject';
    }
    correctionFactor += 0.64;
  }

  function checkPositiveOrNegative(individualError) {
    if (individualError > 0) {
      return 'Positive ' + individualError;
    } else {
      return 'Negative ' + individualError;
    }
  }

  const theSampleErrorLimit = individualStandardDeviation * correctionFactor;
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
    correctedAVG: Number(averageError + theSampleErrorLimit),
  });
  console.log('##########################################################');

  // const correctedAverageError = Number((individualError / theSampleSize) * correctionFactor)
  // (individualError / theSampleSize)
  const correctedAverageError = Number(averageError + theSampleErrorLimit);

  console.log('correctedAverageError ' + correctedAverageError);

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
                            <td>${
                              tError.length > approved &&
                              appliedMethod == 'Non Destructive'
                                ? 'Sample Failed T1-Reject'
                                : tError.length > 1 &&
                                  appliedMethod == 'Destructive'
                                ? 'Sample Failed T1-Reject'
                                : 'Sample Passed T1 Test- Go For T2 Test '
                            }  </td>
                            <td>${tError.length}</td>
                            <td>${approved}</td>
                        </tr>

                        <tr>
                            <td>T2 Test Result</td>
                            <td>${
                              t2.length > 0
                                ? 'Sample Failed T2-Reject'
                                : 'Sample Passed T2 Test'
                            } </td>
                            <td>${t2.length}</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Individual Pre-Package Error Test - Result</td>
                            <td>${
                              individualError > 0
                                ? 'Samples Passed Individual Pre-packages Error Test'
                                : 'Samples Failed Individual Pre-packages Error Test'
                            }</td>
                            <td> (${checkPositiveOrNegative(
                              individualError.toFixed(3)
                            )})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Corrected Average Error Test Results</td>
                            <td>${
                              Number(correctedAverageError) > 0
                                ? 'Samples Passed Corrected Average Error Test Result'
                                : 'Samples Failed Corrected Average Error Test Result'
                            }</td>
                               <td> (${checkPositiveOrNegative(
                                 correctedAverageError.toFixed(3)
                               )})</td>
                            <td>Equal Or Greater</td>
                        </tr>
                        <tr>
                            <td>Conclusion Remarks</td>
                            <td colspan="3">${
                              individualError < 0 ||
                              correctedAverageError < 0 ||
                              realT2.length > 0
                                ? 'Sample Failed All required test-Reject'
                                : 'Sample Passed All required Test- Approve'
                            }</td>
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
                            <td>${Number(
                              productQuantity - tolerableAmount
                            )}</td>
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
                            <td><b>${
                              tError.length > approved
                                ? 'Sample Fail T1 Test- Reject'
                                : 'Sample Pass T1 Test- Go for T2 Test'
                            }</b></td>
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
                            <td>${nominalQtyPercent(productQuantity) * 2}</td>
                        </tr>
                        <tr>
                            <td><b>g or mL</b></td>
                            <td>${nominalQtyGram(productQuantity) * 2}</td>
                        </tr>
                        <tr>
                            <td><b>Minimum For T2</b></td>
                           <td>${Number(
                             productQuantity - tolerableAmount * 2
                           )}</td>
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
                            <td><b>${
                              t2.length > 0
                                ? 'Sample Fail T2 Test-Reject'
                                : 'Sample Pass T2 Test- Go for Pre-package Error Test'
                            }</b></td>
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
                    <td>${individualError.toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Average Error</b></td>
                    <td>${averageError.toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b></td>
                    <td><b>${
                      individualError > 0
                        ? ' Sample Pass Individual Pre Package Error Test'
                        : 'Sample Fail Individual Pre Package Error Test-Reject'
                    }</b></td>
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
                    <td>${correctedAverageError.toFixed(3)}</td>
                 </tr>
                  <tr>
                    <td><b>Decision At This Stage</b> </td>
                    <td><b>${
                      correctedAverageError < 0
                        ? 'Sample Fail Corrected Average Error Test - Advice the client'
                        : 'Sample Pass Corrected Average Error Test'
                    }</b></td>
                 </tr>
                 </tbody>
                </table>
        
        
        `;

  $('#prePackageSummary').html(table);
}
