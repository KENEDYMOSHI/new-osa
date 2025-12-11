<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="ppgForm">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">



                        <div class="card-body">
                            <div id="technical">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Inspection Type </label>
                                            <select class="form-control" name="type" id="type" onchange="switchType(this.value)">
                                                <option selected disabled>--Inspection Type--</option>
                                                <option value="Local">Local</option>
                                                <option value="Imported">Imported</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Task /Activity </label>
                                            <select class="form-control" name="activityType" id="activityType">
                                                <option selected disabled>--Select Activity--</option>
                                                <option value="Verification">Initial (Inspection)</option>
                                                <option value="Reverification">Inspection</option>
                                                <option value="Inspection">Market Surveillance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <div class="form-group">
                                                <label for="">Category Of Analysis</label>
                                                <select class="form-control" name="categoryAnalysis" id="categoryAnalysis" onchange="javascript: if(this.value =='Linear 2' || this.value =='Area & Linear' || this.value =='Area_Linear') {$('#otherQuantity').show(); $('#gaugeBlock').show()} else {$('#otherQuantity').hide();$('#gaugeBlock').hide()}">
                                                    <option value="General">General</option>
                                                    <option value="Linear">Linear</option>
                                                    <option value="Linear 2">Two Linear Measurements</option>
                                                    <option value="Area">Area</option>
                                                    <option value="Area_Linear">Area & Linear</option>
                                                    <option value="Count">Count</option>
                                                    <option value="Cubic">Cubic</option>
                                                    <option value="Bread">Bread</option>
                                                    <option value="Fruits">Fruits & Vegetables</option>
                                                    <option value="Poultry">Poultry Products</option>
                                                    <option value="Medical_Gases">Industrial & Medical Gases</option>
                                                    <option value="Seeds">Seed Number</option>
                                                    <!-- <option value="Coal">Coal</option> -->
                                                    <option value="Anthracite">Anthracite,Coal,Coke & Charcoal</option>
                                                    <!-- <option value="Coke">Coke</option> -->
                                                    <option value="Sheets">Sheets</option>
                                                    <option value="Gases">Liquid Petroleum Gases</option>


                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Commodity/Brand</label>
                                            <input type="text" name="commodity" id="commodity" class="form-control" placeholder="Commodity">

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Quantity</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Quantity" min="0">
                                            <!-- <small id="declaredQty" class="text-muted">Gross Quantity = 2000 g</small> -->

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Unit</label>
                                            <select class="form-control" name="unit" id="unit" onchange="calcGross(this.value)">
                                                <optgroup label="Weight">
                                                    <option value="mg">mg</option>
                                                    <option value="g">g</option>
                                                    <option value="kg">kg</option>
                                                </optgroup>
                                                <optgroup label="Volume">
                                                    <option value="mL">mL</option>
                                                    <option value="L">L</option>
                                                    <option value="cubCm">cm <sup>3</sup></option>
                                                    <option value="cubM">m <sup>3</sup></option>
                                                </optgroup>
                                                <optgroup label="Area">
                                                    <option value="sqCm">cm <sup>2</sup></option>
                                                    <option value="sqM">m <sup>2</sup></option>
                                                </optgroup>
                                                <optgroup label="Length">
                                                    <option value="mm">mm </option>
                                                    <option value="cm">cm </option>
                                                    <option value="m">m </option>

                                                </optgroup>
                                                <optgroup label="Count">
                                                    <option value="Pieces">Pieces </option>


                                                </optgroup>


                                            </select>
                                        </div>
                                    </div>

                                    <div id="otherQuantity" class="col-md-12" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Quantity(Diameter / Gauge)</label>
                                                    <input type="number" name="quantity2" id="quantity2" class="form-control" placeholder="Quantity" min="0">
                                                    <!-- <small id="declaredQty" class="text-muted">Gross Quantity = 2000 g</small> -->

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Unit</label>
                                                    <select onchange="calcGrossUnit2(this.value)" class=" form-control" name="unit2" id="unit2">
                                                        <!-- <optgroup label="Weight">
                                    <option value="mg">mg</option>
                                    <option value="g">g</option>
                                    <option value="kg">kg</option>
                                </optgroup>
                                <optgroup label="Volume">
                                    <option value="mL">mL</option>
                                    <option value="L">L</option>
                                    <option value="cubCm">cm <sup>3</sup></option>
                                    <option value="cubM">m <sup>3</sup></option>
                                </optgroup>
                                <optgroup label="Area">
                                    <option value="sqCm">cm <sup>2</sup></option>
                                    <option value="sqM">m <sup>2</sup></option>
                                </optgroup> -->
                                                        <optgroup label="Length">
                                                            <option value="mm">mm </option>
                                                            <option value="cm">cm </option>
                                                            <!-- <option value="m">m </option> -->

                                                        </optgroup>


                                                    </select>
                                                </div>
                                            </div>

                                        </div>


                                    </div>




                                </div>

                                <div class="row">
                                    <div class="col-md-3 imported" style="display: none;">
                                        <div class="form-group">
                                            <label for="">TANSAD Number</label>
                                            <input type="text" name="tansardNumber" id="tansardNumber" class="form-control tansad" placeholder="TANSAD Number">

                                        </div>
                                    </div>
                                    <div class="col-md-3 imported" style="display: none;">
                                        <div class="form-group">
                                            <label for="">F.O.B Value</label>
                                            <input type="number" step="any" name="fob" id="fob" class="form-control" placeholder="F.O.B" min="0">

                                        </div>
                                    </div>
                                    <div class="col-md-3 imported" style="display: none;">
                                        <div class="form-group">
                                            <label for="">Date</label>
                                            <input type="date" name="date" id="date" class="form-control" placeholder="Date">

                                        </div>
                                    </div>







                                    <div class="col-md-3 local">
                                        <div class="form-group">
                                            <label for="">Ref Number</label>
                                            <input type="text" name="refNumber" id="refNumber" class="form-control" placeholder="Ref Number">

                                        </div>
                                    </div>

                                    <div class="col-md-3 local">
                                        <div class="form-group">
                                            <label for="">Batch Number</label>
                                            <input type="text" name="batchNumber" id="batchNumber" class="form-control" placeholder="Batch Number">

                                        </div>
                                    </div>
                                    <div class="col-md-3 local">
                                        <div class="form-group">
                                            <label for="">Batch Size Or Inspection Lot</label>
                                            <input oninput="calculateSampling(this.value)" type="number" name="batchSize" id="batchSize" class="form-control" placeholder="Batch Size Or Inspection Lot" min="0">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Method To Be Applied</label>
                                            <select class="form-control" name="method" id="method">
                                                <option disabled selected>--Select Method--</option>
                                                <option value="Destructive">Destructive</option>
                                                <option value="Non Destructive">Non Destructive</option>


                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 imported" style="display: none;">
                                        <div class="form-group">
                                            <label for="exampleInputFile">Assessment Document</label>
                                            <div class="custom-file">
                                                <input type="file" name="tansardDocument" class="custom-file-input" id="tansardDocument" accept=".pdf,.jpg,.png,.jpeg">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>



                            </div>

                        </div>
                        <div class="card" id="tareBlock" style="display: none;">
                            <!-- <div class="card-header">Tare Weight Calculation</div> -->
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-5">

                                        <div class="input-group input-group mt-4">
                                            <button type="button" class="btn btn-info" onclick="openModal()">Calculate Tare</button>
                                            <input type="text" min="0" name="tareWeight" id="tareWeight" class="form-control" placeholder="Tare Weight" readonly>
                                            <span class="input-group-append">
                                            </span>
                                        </div>

                                        <!-- <button style="margin-top:1.8rem" type="button" class="btn btn-primary " onclick="openModal()">
            Calculate Tare
        </button> -->

                                        <h4 id="tareMsg"></h4>

                                        <!-- <div class="col-md-7">

            <div class="form-group">
                <label for="">Declared Tare Weight</label>
                <input type="text" min="0" name="tareWeight" id="tareWeight" class="form-control" placeholder="Tare Weigh" readonly>

            </div>

        </div> -->

                                        <!-- Modal -->
                                        <div class="modal fade" id="tareModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tare Weight Calculation</h5>
                                                        <button type="button" class="close" onclick="closeTareModal()" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="">Measurement Data</label>
                                                            <select class="form-control" name="" id="" onchange="renderInputs(this.value)">
                                                                <option selected> Select Size </option>

                                                                <option value="10">10</option>
                                                                <option value="25">25</option>


                                                            </select>
                                                        </div>

                                                        <div class="row mt-2" id="weights"></div>
                                                        <p>10% Of Qn: <span id="ten"></span></p>
                                                        <p>Average: <span id="avg"></span></p>
                                                        <div class="form-group">
                                                            <label for="">Tare Weight</label>
                                                            <input type="number" name="calculatedTare" id="calculatedTare" class="form-control" placeholder="" readonly>
                                                            <small id="msg" class="text-muted"></small>

                                                        </div>
                                                        <button type="button" onclick="calculateDeviation()" class="btn btn-outline-primary btn-sm" id="sdCalcBtn" style="display: none;">Calculate Standard Deviation</button>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeTareModal()">Close</button>
                                                        <button disabled onclick="approveTareWeight()" type="button" id="tareApprover" class="btn btn-primary btn-sm">Approve Tare Weight</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">Labelling Requirement</div>
                            <div class="card-body">
                                <div class="form-group">

                                    <div class="row">
                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <label>Packer identified</label><br>
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input label-check" name="packerIdentification" id="packerIdentification1" type="radio" onchange="checkLabelling()" value="Correct">

                                                <label class="form-check-label" for="packerIdentification1">Correct</label>
                                            </div>
                                            <div class="icheck-success d-inline">
                                                <input class="form-check-input label-check" name="packerIdentification" id="packerIdentification2" type="radio" onchange="checkLabelling()" value="Not Correct">

                                                <label class="form-check-label" for="packerIdentification2">Not Correct</label>
                                            </div>
                                        </div>

                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <label>Product Identified</label><br>
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input label-check" name="productIdentification" id="productIdentification1" type="radio" onchange="checkLabelling()" value="Correct">

                                                <label class="form-check-label" for="productIdentification1">Correct</label>
                                            </div>
                                            <div class="icheck-success d-inline">
                                                <input class="form-check-input label-check" name="productIdentification" id="productIdentification2" type="radio" onchange="checkLabelling()" value="Not Correct">

                                                <label class="form-check-label" for="productIdentification2">Not Correct</label>
                                            </div>
                                        </div>
                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <!-- <label>Correct Measuring Unit</label> -->

                                            <label for=""> Measuring </label><br>
                                            <select class="form-control" name="correctUnit" id="correctUnit">
                                                <option value="Mass">Mass</option>
                                                <option value="Volume">Volume</option>
                                                <option value="Length">Length</option>
                                                <option value="Area">Area</option>
                                                <option value="Pieces">Pieces</option>
                                            </select>


                                        </div>
                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <label> Measuring Symbol</label><br>
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input label-check" name="correctSymbol" id="correctSymbol1" type="radio" onchange="checkLabelling()" value="Correct">

                                                <label class="form-check-label" for="correctSymbol1">Correct</label>
                                            </div>
                                            <div class="icheck-success d-inline">
                                                <input class="form-check-input label-check" name="correctSymbol" id="correctSymbol2" type="radio" onchange="checkLabelling()" value="Not Correct">

                                                <label class="form-check-label" for="correctSymbol2">Not Correct</label>
                                            </div>
                                        </div>
                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <label>Height</label><br>
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input label-check" name="correctHeight" id="correctHeight1" type="radio" onchange="checkLabelling()" value="Correct">

                                                <label class="form-check-label" for="correctHeight1">Correct</label>
                                            </div>
                                            <div class="icheck-success d-inline">
                                                <input class="form-check-input label-check" onchange="checkLabelling()" name="correctHeight" id="correctHeight2" type="radio" onchange="checkLabelling()" value="Not Correct">

                                                <label class="form-check-label" for="correctHeight2">Not Correct</label>
                                            </div>
                                        </div>
                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <label> Prescribed Quantity(If Applicable)</label><br>
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input label-check" name="correctQuantity" id="correctQuantity1" type="radio" onchange="checkLabelling()" value="Correct">

                                                <label class="form-check-label" for="correctQuantity1">Correct</label>
                                            </div>
                                            <div class="icheck-success d-inline">
                                                <input class="form-check-input label-check" name="correctQuantity" id="correctQuantity2" type="radio" onchange="checkLabelling()" value="Not Correct">

                                                <label class="form-check-label" for="correctQuantity2">Not Correct</label>
                                            </div>
                                        </div>
                                        <div class="box elevation-1 col-md-4 p-2 ">
                                            <label>General Appearance Of The Package</label><br>
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input label-check" name="generalAppearance" id="generalAppearance1" type="radio" onchange="checkLabelling()" value="Deceptive">

                                                <label class="form-check-label" for="generalAppearance1">Deceptive</label>
                                            </div>
                                            <div class="icheck-success d-inline">
                                                <input class="form-check-input label-check" name="generalAppearance" id="generalAppearance2" type="radio" onchange="checkLabelling()" value="Non Deceptive">

                                                <label class="form-check-label" for="generalAppearance2">Non Deceptive</label>
                                            </div>
                                        </div>
                                        <div class="box elevation-1 col-md-8 p-2 ">
                                            <!-- <label>Correct Measuring Unit</label> -->

                                            <label for="">Recommendation</label><br>
                                            <h6 id="recommendation"></h6>
                                            <input type="text" name="recommendation" class="form-control recommendation" hidden>


                                        </div>
                                    </div>








                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row">


                                    <div class="col-md-4" id="plan">
                                        <div class="form-group">
                                            <label for="">Sampling Plan</label>
                                            <select class="form-control" name="sampling" id="sampling">
                                                <option disabled selected>--Select Sampling--</option>
                                                <option value="Sampling">Sampling</option>
                                                <option value="Non Sampling">Non Sampling</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4" id="measurementX">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="">Unit Of Measurement</label>
                                                <select class="form-control" name="measurementUnit" id="measurementUnit">
                                                    <option selected disabled>--Unit Of Measurement--</option>
                                                    <option value="Grams">Grams</option>
                                                    <option value="Milliliters">Milliliters</option>
                                                    <option value="Meter">Meter</option>
                                                    <option value="Milliliters">Millimeters</option>
                                                    <option value="Square Cm">Square Cm</option>
                                                    <option value="Square M">Square M</option>
                                                    <option value="Cubic Cm">Cubic Cm</option>
                                                    <option value="Cubic M">Cubic M</option>
                                                    <option value="Number">Number</option>
                                                    <option value="Pieces">Pieces</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="measurementX">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="">Packing Declaration</label>
                                                <select class="form-control" name="packingDeclaration" id="packingDeclaration">
                                                    <option selected disabled>--Packing Declaration--</option>
                                                    <option value="Weight">Weight</option>
                                                    <option value="Volume">Volume</option>
                                                    <option value="Area">Area</option>
                                                    <option value="Height">Height</option>
                                                    <option value="Length">Length</option>
                                                    <option value="Pieces">Pieces</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">






                                    <div class="col-md-4" id="nature">
                                        <div class="form-group">
                                            <label for="">Nature Of Measurement(Gross Or Net)</label>
                                            <select class="form-control" name="measurementNature" id="measurementNature">

                                                <option selected disabled>--Nature Of Measurement--</option>
                                                <option value="Gross">Gross</option>
                                                <option value="Net">Net</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4" id="tare">

            <div class="row">
                tare was here

            </div>

        </div> -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Declared Gross Quantity(gram or milliliter or Millimeters )</label>
                                            <input type="text" name="grosValue" id="grosValue" class="form-control" placeholder="Gross Weight" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-4" id="gaugeBlock" style="display: none;">
                                        <div class="form-group">
                                            <label for="">Gauge/Diameter/Thickness(Millimeters)</label>
                                            <input type="text" name="grosValue2" id="grosValue2" class="form-control" placeholder="Gauge/Diameter/Thickness" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Nature Of The Product</label>
                                            <select class="form-control" name="productNature" id="productNature" onchange="toggleDensity(this.value)">

                                                <option selected disabled>--Nature Of The Product--</option>
                                                <option value="Solid"> Solid</option>
                                                <option value="Liquid">Liquid</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Sample Size</label>
                                            <input type="number" name="sampleSize" id="sampleSize" class="form-control" placeholder="Sample Size" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="densityBlock" style="display: none;">

                                        <div class="input-group input-group mt-4">
                                            <button type="button" class="btn btn-info" id="densityBtn" data-toggle="modal" data-target="#densityModal">Calculate Density</button>
                                            <input type="text" name="density" id="density" class="form-control density" placeholder="" readonly>
                                            <span class="input-group-append">
                                            </span>
                                        </div>
                                        <!-- <button id="densityBtn" type="button" class="btn btn-outline-primary btn sm mt-4" data-toggle="modal" data-target="#densityModal">Calculate Density</button> -->
                                        <!-- Button trigger modal -->


                                        <!-- Modal -->
                                        <div class="modal fade d-modal" id="densityModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId">
                                            <div class="modal-dialog" role="document11">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"> Density</h5>
                                                        <button type="button" class="close" onclick="closeDensityModal()" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="">Mass Of Pycnometer</label>
                                                            <input type="number" name="pycnometerMass" id="pycnometerMass" class="form-control" placeholder="Mass Of Pycnometer">

                                                        </div>
                                                        <div class="form-group">
                                                            <label for="">Mass Of Pycnometer + Product</label>
                                                            <input type="number" name="pycnometerProduct" id="pycnometerProduct" class="form-control" placeholder="Mass Of Pycnometer + Product">

                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""></label>
                                                            <select class="form-control" name="pycnometerVolume" id="pycnometerVolume">
                                                                <option value="50">50 mL</option>
                                                                <option value="100">100 mL</option>

                                                            </select>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeDensityModal()">Close</button>
                                                        <button type="button" class="btn btn-primary btn-sm" onclick="calculateDensity()">Calculate</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <!-- <div class="card-body">
    
</div>
<div class="card-footer">




</div> -->
                            <div class="modal-footer">




                                <!-- <button type="submit" class="btn btn-primary btn-sm">
        Save
    </button> -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>


                        <button id="button" class="btn btn-primary btn-sm" type="submit" style="transition: 1s ease;">
                            <span id="spinner" style="display:none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span id="title">Save</span>
                        </button>
            </form>
        </div>
    </div>
</div>
</div>

























<div class="modal fade" id="productModal2" tabindex="1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="overflow-y: scroll; ">
    <div class="modal-dialog modal-xl" role="document">
        <form name="prePackageForm" id="prePackageForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                </div>
        </form>


        <script>
            const productDetailsForm = document.querySelector('#productForm1')
            productDetailsForm.addEventListener('submit', function(e) {
                e.preventDefault()
                const x45 = true;
                if (x45) {

                    let formData = new FormData(productDetailsForm);
                    // console.log(formData);
                    formData.append("customerId", document.querySelector('#customerId').value);
                    formData.append('tansardDocument', $('#tansardDocument')[0].files[0]);
                    formData.append("csrf_hash", document.querySelector('.token').value);
                    $.ajax({
                        type: "POST",
                        url: "addProductDetails",
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        beforeSend: function() {
                            // document.querySelector('#spinner').style.display = 'inline-block'
                            // document.querySelector('#title').textContent = 'Saving...'
                            // document.querySelector('#button').setAttribute('disabled', true)
                        },
                        success: function(response) {
                            document.querySelector('.token').value = response.token
                            console.log(response);

                            if (response.status == 1) {
                                // document.querySelector('#spinner').style.display = 'none'
                                // document.querySelector('#button').removeAttribute('disabled')
                                // document.querySelector('#title').textContent = 'Save'
                                $('#productDetailsForm')[0].reset()
                                document.querySelector('#tansardDocument').value = null
                                document.querySelector('.custom-file-label').textContent = 'Choose File'

                                $('#productModal').modal('hide')

                                let lotSizes = response.products.map(product => {
                                    return product.lot
                                })

                                calculatePrice(lotSizes)

                                // console.log(response);
                                swal({
                                    title: response.msg,
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
                            } else {
                                document.querySelector('#spinner').style.display = 'none'
                                document.querySelector('#button').removeAttribute('disabled')
                                document.querySelector('#title').textContent = 'Save'
                                swal({
                                    title: response.msg,
                                    icon: "warning",
                                    // timer: 7500
                                });
                            }


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

            function checkLabelling() {
                const submitBtn = document.querySelector('#button')

                const recommendation = document.querySelector('#recommendation')
                const recommendationInput = document.querySelector('.recommendation')
                let labeling = []
                const radios = document.querySelectorAll('.label-check')
                radios.forEach(radio => {
                    if (radio.checked == true) labeling.push(radio.value)
                })

                console.log(labeling)
                if (labeling.length == 6) {
                    submitBtn.removeAttribute('disabled', 'disabled')
                    if (labeling.includes('Not Correct') || labeling.includes('Non Deceptive')) {
                        recommendation.textContent = 'Advice The  Customer'
                        recommendationInput.value = 'Advice The  Customer'

                    } else {
                        recommendation.textContent = ' Go To The Next Stage'
                        recommendationInput.value = 'Go To The Next Stage'
                    }
                } else {
                    submitBtn.setAttribute('disabled', 'disabled')
                }
            }

            function closeDensityModal() {
                $("#densityModal").modal('hide')


            }

            function closeTareModal() {
                $("#tareModal").modal('hide')
                console.log(123);


            }
            //=====================================
            $('#categoryAnalysis').change(function() {
                if (this.value == 'General') {
                    $('#measurement').hide();

                } else {
                    $('#measurement').show();

                }
            })

            $('#method').change(function() {
                const category = $('#categoryAnalysis').val()
                const lot = $('#batchSize').val()
                let data = 0

                if (lot >= 0 && lot <= 500) {

                    $('#sampleSize').val(50)
                    data += 50
                } else if (lot >= 501 && lot <= 3200) {
                    $('#sampleSize').val(80)
                    data += 80
                } else if (lot > 3200) {
                    $('#sampleSize').val(125)
                    data += 125
                }

                if (this.value == 'Destructive') {
                    $("#sampling").val('Non Sampling');
                    $('#tareBlock').hide()
                    //$('#densityBlock').hide()
                    data += 20
                    $('#sampleSize').val(20)




                } else if (this.value == 'Non Destructive' && (category == 'Linear' || category == 'Linear 2' || category == 'Area' || category == 'Count' || category == 'Cubic')) {
                    $('#tareBlock').hide()
                    $('#sampleSize').val(data)

                } else {

                    $('#tareBlock').show()
                    $('#sampleSize').val(data)
                    // $('#densityBlock').show()

                }



                // console.log('data size  = ' + data);


            })
            // $('#measurementNature').change(function() {
            //     if (this.value == 'Net') {
            //         $('#tare').hide();
            //         $('#plan').attr('class', 'col-md-6');
            //         $('#nature').attr('class', 'col-md-6');
            //     } else {
            //         $('#tare').show();
            //         $('#plan').attr('class', 'col-md-4');
            //         $('#nature').attr('class', 'col-md-4');
            //     }
            // })

            function switchType(type) {
                const local = document.querySelectorAll('.local')
                const imported = document.querySelectorAll('.imported')
                const file = document.querySelector('#tansardDocument')
                const fileLabel = document.querySelector('.custom-file-label')
                if (type == 'Local') {
                    file.value = null
                    fileLabel.textContent = 'Choose File'
                    for (l of local) {
                        l.style.display = 'block'
                    }
                    for (imp of imported) {
                        imp.style.display = 'none'
                    }
                } else {
                    for (l of local) {
                        l.style.display = 'none'
                    }
                    for (imp of imported) {
                        imp.style.display = 'block'
                    }
                }
            }


            function calculateSampling(lot) {
                //const lot = $('#batchSize').val()
                let data = 0

                if (lot >= 0 && lot <= 500) {

                    $('#sampleSize').val(50)
                    // data += 50
                } else if (lot >= 501 && lot <= 3200) {
                    $('#sampleSize').val(80)
                    // data += 80
                } else if (lot > 3200) {
                    $('#sampleSize').val(125)
                    // data += 125
                }
            }

            function calculateDensity() {
                const pycnometerMass = document.querySelector('#pycnometerMass').value
                const pycnometerProduct = document.querySelector('#pycnometerProduct').value
                const volume = document.querySelector('#pycnometerVolume').value

                const mass = Number(pycnometerProduct) - Number(pycnometerMass)

                let density = (mass / volume) * (0.99985 + 0.00012)

                document.querySelector('#density').value = density.toFixed(6)
                $("#densityModal").modal('hide')
            }

            function toggleDensity(val) {
                if (val == 'Solid') {
                    document.querySelector('#densityBlock').style.display = 'none'
                    // document.querySelector('.density').style.display = 'none'
                } else {
                    document.querySelector('#densityBlock').style.display = 'block'
                    // document.querySelector('.density').style.display = 'block'
                }
            }
        </script>