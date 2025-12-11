<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="overflow: scroll;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="productForm1">
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
                                            <label class="must" for="">Inspection Type </label>
                                            <select class="form-control" name="type" id="type" onchange="switchType(this.value)" required>
                                                <option selected disabled>--Inspection Type--</option>
                                                <option value="Local">Local</option>
                                                <option value="Imported" disabled >Imported</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Task /Activity </label>
                                            <select class="form-control" name="activityType" id="activityType" required>
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
                                                <label class="must" for="">Category Of Analysis</label>
                                                <select class="form-control" name="categoryAnalysis" id="categoryAnalysis" onchange="handleCategoryChange(this.value)" required>
                                                    <option value="">--Select Category--</option>
                                                    <option value="General">General</option>
                                                    <option value="Linear">Linear</option>
                                                    <option value="Linear 2">Two Linear Measurements</option>
                                                    <option value="Area">Area</option>
                                                    <option value="Area_Linear">Area & Linear</option>
                                                    <option value="Count">Count</option>
                                                    <option value="Cubic">3 Dimensional Measurement</option>
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
                                            <label class="must" for="">Commodity/Brand</label>
                                            <input type="text" name="commodity" id="commodity" class="form-control" placeholder="Commodity" required>

                                        </div>
                                    </div>
                                    <div class="col-md-12" id="volume" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="must" for="">Length</label>
                                                <input type="number" name="length" id="length" class="form-control" oninput="calculateVolume()" placeholder="length" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="must" for="">Width</label>
                                                <input type="number" name="width" id="width" class="form-control" oninput="calculateVolume()" placeholder="width" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="must" for="">Height | Gauge</label>
                                                <input type="number" name="height" id="height" class="form-control" oninput="calculateVolume()" placeholder="height" required>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="must" for="">Declared Size/Quantity/Dimension/Measures</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Size/Quantity/Dimension/Measures" min="0" required oninput="getGrosQuantity(this.value)" readonly>
                                            <!-- <small id="declaredQty" class="text-muted">Gross Quantity = 2000 g</small> -->

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- <input type="text" id="unit_3" class="form-control"> -->
                                        <div class="form-group">
                                            <label class="must" for="">Unit</label>
                                            <select class="form-control" name="unit" id="unit" onchange="calcGross(this.value)" required>
                                                <option value="">--Select Unit --</option>
                                                <optgroup label="Weight">
                                                    <option value="mg">mg</option>
                                                    <option selected value="g">g</option>
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
                                                    <label class="must" for="">Quantity(Diameter / Gauge)</label>
                                                    <input type="number" name="quantity2" id="quantity2" class="form-control" placeholder="Quantity" min="0" oninput="getGrosQuantity2(this.value)" required>
                                                    <!-- <small id="declaredQty" class="text-muted">Gross Quantity = 2000 g</small> -->

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="must" for="">Unit</label>
                                                    <select onchange="calcGrossUnit2(this.value)" class=" form-control" name="unit2" id="unit2" required>
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
                                                        <option value=""> --Select Unit-- </option>
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
                                            <label class="must" for="">TANSAD Number</label>
                                            <input type="text" name="tansardNumber" id="tansardNumber" class="form-control tansad" placeholder="TANSAD Number" required>

                                        </div>
                                    </div>
                                    <div class="col-md-3 imported" style="display: none;">
                                        <div class="must" class="form-group">
                                            <label for="">F.O.B Value</label>
                                            <input type="number" step="any" name="fob" id="fob" class="form-control" placeholder="F.O.B" min="0" required>

                                        </div>
                                    </div>
                                    <div class="col-md-3 imported" style="display: none;">
                                        <div class="form-group">
                                            <label class="must" for="">Date</label>
                                            <input type="date" name="date" id="date" class="form-control" placeholder="Date" required>

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
                                            <label class="must" for="">Batch Number</label>
                                            <input type="text" name="batchNumber" id="batchNumber" class="form-control" placeholder="Batch Number" required>

                                        </div>
                                    </div>
                                    <div class="col-md-3 local">
                                        <div class="form-group">
                                            <label class="must" for="">Batch Size Or Inspection Lot</label>
                                            <input oninput="calculateSampling(this.value)" type="number" name="batchSize" id="batchSize" class="form-control" placeholder="Batch Size Or Inspection Lot" min="0" required>

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="must" for="">Method To Be Applied</label>
                                            <select class="form-control" name="method" id="method" required>
                                                <option disabled selected>--Select Method--</option>
                                                <option value="Destructive">Destructive</option>
                                                <option value="Non Destructive">Non Destructive</option>


                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 imported" style="display: none;">
                                        <div class="form-group">
                                            <label class="must" for="exampleInputFile">Assessment Document</label>
                                            <div class="custom-file">
                                                <input type="file" name="tansardDocument" class="custom-file-input" id="tansardDocument" accept=".pdf,.jpg,.png,.jpeg" required>
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
                                            <input type="text" min="0" name="tareWeight" id="tareWeight" class="form-control" placeholder="Tare Weight" readonly required>
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
                                                        <input type="text" id="allow25" class="form-control" readonly hidden>
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
                                            <select class="form-control" name="correctUnit" id="correctUnit" required>
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
                                        <!-- <div class="box elevation-1 col-md-8 p-2 ">
                                          

                                            <label for="">Recommendation</label><br>
                                            <h6 id="recommendation"></h6>
                                            <input type="text" name="recommendation" class="form-control recommendation" hidden>


                                        </div> -->
                                    </div>








                                </div>
                                <p class="text-danger" id="label-error-container"></p>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row">


                                    <div class="col-md-4" id="plan">
                                        <div class="form-group">
                                            <label class="must" for="">Sampling Plan</label>
                                            <select class="form-control" name="sampling" id="sampling" required>
                                                <option disabled selected>--Select Sampling--</option>
                                                <option value="Sampling">Sampling</option>
                                                <option value="Non Sampling">Non Sampling</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4" id="measurementX">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label class="must" for="">Unit Of Measurement Declaration</label>
                                                <select class="form-control" name="measurementUnit" id="measurementUnit" required>
                                                    <option disabled>--Unit Of Measurement--</option>
                                                    <option value="Gram">Gram</option>
                                                    <option value="Milliliter">Milliliter</option>
                                                    <option value="Meter">Meter</option>
                                                    <option value="Milliliter">Millimeter</option>
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
                                            <label class="must" for="">Packing Declaration</label>
                                            <select class="form-control" name="packingDeclaration" id="packingDeclaration" required>
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
                        <div class="card">
                            <div class="card-body">
                                <div class="row">






                                    <div class="col-md-4" id="nature">
                                        <div class="form-group">
                                            <label class="must" for="">Nature Of Measurement(Gross Or Net)</label>
                                            <select class="form-control" name="measurementNature" id="measurementNature" required>

                                                <option selected value="" disabled>--Nature Of Measurement--</option>
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
                                            <label class="must" for="">Declared Net Quantity(gram or milliliter or Millimeters )</label>
                                            <input type="text" name="grosValue" id="grosValue" class="form-control" placeholder="Gross Weight" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-4" id="gaugeBlock" style="display: none;">
                                        <div class="form-group">
                                            <label class="must" for="">Gauge/Diameter/Thickness(Millimeters)</label>
                                            <input type="text" name="grosValue2" id="grosValue2" class="form-control" placeholder="Gauge/Diameter/Thickness" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="must" for="">Nature Of The Product</label>
                                            <select class="form-control" name="productNature" id="productNature" onchange="toggleDensity(this.value)" required>

                                                <option selected value="" disabled>--Nature Of The Product--</option>
                                                <option value="Solid"> Solid</option>
                                                <option value="Liquid">Liquid</option>
                                                <option value="Gas">Gas</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="must" for="">Sample Size</label>
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
                                                            <label for="">Pycnometer Capacity</label>
                                                            <select class="form-control" name="pycnometerVolume" id="pycnometerVolume">
                                                                <option value="50">50 mL</option>
                                                                <option value="100">100 mL</option>
                                                                <option value="200">200 mL</option>

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

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>


                        <button id="button" class="btn btn-primary btn-sm" type="submit" style="transition: 1s ease;">
                            <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>

                            Save
                        </button>
            </form>
        </div>
    </div>
</div>
</div>




<div class="modal fade" id="productModal11" tabindex="1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="overflow-y: scroll; ">
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
            // Custom validation method for checking at least half of the checkboxes in a group are checked
            // $.validator.addMethod("halfChecked", function(value, element) {
            //     var checkboxes = $("input[type='radio']");
            //     var checkedCount = checkboxes.filter(":checked").length;
            //     var halfCount = checkboxes.length / 2;
            //     return checkedCount >= halfCount;
            // }, "Please Complete Labeling Requirements.");
            // Function to calculate the volume and update the quantity input field
            function calculateVolume() {
                const productCategory = document.getElementById('categoryAnalysis').value;
                const lengthInput = document.getElementById('length');
                const widthInput = document.getElementById('width');
                const heightInput = document.getElementById('height');
                const quantityInput = document.getElementById('quantity');
                quantityInput.setAttribute('readonly', 'readonly')
                const length = parseFloat(lengthInput.value);
                const width = parseFloat(widthInput.value);
                const height = parseFloat(heightInput.value);

                console.log('calculating...855000...')
                // Check if all inputs are valid numbers
                if (!isNaN(length) || !isNaN(width) || !isNaN(height)) {
                    let result;
                    console.log('calculating......')
                    switch (productCategory) {
                        case 'Cubic':
                            result = length * width * height;
                            break;
                        case 'Area':
                            result = length * width;
                            break;
                        default:
                            result = '';
                            break;
                    }

                    quantityInput.value = result;
                    getGrosQuantity(result)
                    console.log(result)
                } else {
                    quantityInput.value = ''; // Clear the input field if any input is invalid
                }
            }

            function handleCategoryChange(selectedValue) {
                const volumeBlock = document.querySelector('#volume')
                const lengthInput = document.getElementById('length');
                const widthInput = document.getElementById('width');
                const heightInput = document.getElementById('height');
                const quantityInput = document.getElementById('quantity');

                if (selectedValue == 'Cubic') {
                    volumeBlock.style.display = 'block'
                    heightInput.style.display = 'block'

                    //change input parent node col size
                    lengthInput.parentNode.classList.remove('col-md-6')
                    lengthInput.parentNode.classList.add('col-md-4')
                    widthInput.parentNode.classList.remove('col-md-6')
                    widthInput.parentNode.classList.add('col-md-4')



                } else if (selectedValue == "Area") {
                    volumeBlock.style.display = 'block'
                    heightInput.style.display = 'none'
                    lengthInput.parentNode.classList.remove('col-md-4')
                    lengthInput.parentNode.classList.add('col-md-6')
                    widthInput.parentNode.classList.remove('col-md-4')
                    widthInput.parentNode.classList.add('col-md-6')

                } else if (
                    selectedValue == "Linear 2" ||
                    selectedValue == "Area & Linear" ||
                    selectedValue == "Area_Linear"
                ) {
                    volumeBlock.style.display = 'none'
                    quantityInput.removeAttribute('readonly')
                    $('#otherQuantity').show();
                    $('#gaugeBlock').show();
                } else {
                    quantityInput.removeAttribute('readonly')
                    volumeBlock.style.display = 'none'
                    $('#otherQuantity').hide();
                    $('#gaugeBlock').hide();



                }
            }


            $('#productForm1').validate()
            const productDetailsForm = document.querySelector('#productForm1')
            productDetailsForm.addEventListener('submit', function(e) {
                e.preventDefault()

                if ($('#productForm1').valid()) {

                    submitInProgress(e.submitter)
                    let formData = new FormData(productDetailsForm);
                    // console.log(formData);
                    formData.append("customerId", document.querySelector('#customerId').value);
                    formData.append('tansardDocument', $('#tansardDocument')[0].files[0]);
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>addProductDetails",
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json",

                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());

                        },
                        success: function(response) {
                            document.querySelector('.token').value = response.token


                            submitDone(e.submitter)
                            if (response.status == 1) {

                                $('#productForm1')[0].reset()
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
                                console.log('***************current products******************')
                                console.log(response.products);

                            } else {
                                document.querySelector('#spinner').style.display = 'none'
                                document.querySelector('#button').removeAttribute('disabled')
                              pre
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

            function getAllProducts() {
                if (!document.querySelector('#customerId')) {
                    swal({
                        title: 'Please Select Customer First',
                        icon: "warning",
                        // timer: 2500
                    });
                } else {


                    fetch('<?=base_url()?>getAllProducts', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json;charset=utf-8',
                                "X-Requested-With": "XMLHttpRequest",
                                'X-CSRF-TOKEN': document.querySelector('.token').value
                            },

                            body: JSON.stringify({
                                customerId: document.querySelector('#customerId').value
                            }),

                        }).then(response => response.json())
                        .then(data => {
                            const {
                                token,
                                products
                            } = data
                            document.querySelector('.token').value = token
                            if (products != '') {
                                let productList = `<option value="" selected disabled> --Select Product --</option>`
                                console.log(products)

                                products.forEach(product => {
                                    productList += `<option value="${product.id}">${product.commodity}  ${product.quantity} ${product.unit}</option>`

                                })


                                document.querySelector('#products').innerHTML = productList
                            } else {

                                document.querySelector('#products').innerHTML = ''
                            }
                        })
                }
            }

            function triggerActivity(activity) {
                const totalAmountInput = document.querySelector('#totalAmount')
                totalAmountInput.value = ''

                let inspectionType = ''
                const type = document.querySelectorAll('.type')
                for (t of type) {
                    if (t.checked) inspectionType += t.value
                }

                console.log(inspectionType)

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
                    url: "<?=base_url()?>getProductsWithMeasurements",
                    data: {
                        customerId: customerId,
                        activity: activity,
                        inspectionType: inspectionType,

                    },
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                        $('#productsTable').html(`<div class="text-center">
    <div class="spinner-border text-primary mt-2" id="spinner" role="status" style="width: 1.5rem; height: 1.5rem;">
  
    </div>
    <div>Fetching Products</div>
</div>
`)
                    },
                    success: function(response) {
                        // $('#productsTable').html('<p>Please wait</p>')
                        document.querySelector('.token').value = response.token
                        let amount = 0
                        let theProducts = []
                        console.log(response);
                        if (response.products != '') {
                            //get all lot sizes
                            let total = 0
                            if (response.type == 'Local') {
                                const locals = Array.from(response.products)
                                const commodities = Object.values(response.products).map(obj => obj)



                                const localProducts = commodities.filter((product) => {
                                    return product.type == 'Local'


                                }).map(product => {
                                    return {
                                        id: product.id,
                                        commodity: product.commodity,
                                        hash: product.hash,
                                        lot: product.lot,
                                        result: product.result,
                                    }
                                })
                                const lotSizes = commodities.map(product => {
                                    // return Number(product.lot)
                                    return product

                                })
                                //get less than 3200 lot size products
                                // const smallLot = lotSizes.filter((lot) => {
                                //     return lot.lot <= 3200;

                                // });
                                // console.log('localProducts');
                                // console.log(localProducts);

                                const smallLotSizes = commodities.filter((product) => {
                                    return product.lot < 3200


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
                                const largeLotSizes = commodities.filter((product) => {
                                    return product.lot >= 3200



                                }).map(product => {
                                    return {
                                        id: product.id,
                                        commodity: product.commodity,
                                        hash: product.hash,
                                        lot: product.lot,
                                        result: product.result,
                                    }
                                })

                                // console.log('*****largeLotSizes*****');
                                // console.log(largeLotSizes);

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

                                //     console.log('*****allSmallLot*****');
                                // console.log(allSmallLot);

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

                                //     console.log('*****firstFiveLot*****');
                                // console.log(firstFiveLot);
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

                                //     console.log('*****secondFiveLot*****');
                                // console.log(secondFiveLot);
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

                                //     console.log('*****moreThanTenLot*****');
                                // console.log(moreThanTenLot);



                                theProducts = firstFiveLot.concat(secondFiveLot, moreThanTenLot, allSmallLot)
                                // // xx.push(firstFiveLot)
                                // // xx.push(secondFiveLot)



                            } else if (response.type == 'Imported') {
                                const theImports = Object.values(response.products)
                                const importedProducts = theImports.filter((product) => {
                                    return product.type == 'Imported'


                                }).map(product => {
                                    return {
                                        id: product.id,
                                        commodity: product.commodity,
                                        hash: product.hash,
                                        fob: product.fob,
                                        result: product.result,
                                    }
                                })

                                const firstTen = importedProducts
                                    .map((product) => {

                                        let amount = calculateFOB(product.fob)
                                        return {
                                            amount: product.result == 'Pass' ? amount : amount * 2,
                                            id: product.id,
                                            fob: product.fob,
                                            commodity: product.commodity,
                                            hash: product.hash,
                                            fob: product.fob,
                                            result: product.result,
                                        }



                                    }).splice(0, 9);

                                const theRest = importedProducts
                                    .map((product) => {

                                        let amount = 20000
                                        return {
                                            amount: product.result == 'Pass' ? amount : amount * 2,
                                            id: product.id,
                                            fob: product.fob,
                                            commodity: product.commodity,
                                            hash: product.hash,
                                            lot: product.lot,
                                            result: product.result,
                                        }



                                    }).splice(10);


                                theProducts = firstTen.concat(theRest)

                                // console.log('import')
                                // console.log(importedProducts)
                                // console.log('Ten')
                                // console.log(firstTen)
                                console.log('---------------ALL PRODUCTS----------')
                                console.log(theProducts)


                            }

                            total += theProducts.map(product => {
                                return product.amount
                            }).reduce((x, y) => {
                                return x + y
                            }, 0)



                            document.querySelector('#totalAmount').value = activity == 'Inspection' ? 0 : total
                            const billBock = document.querySelector('#billBlock');
                            if (total > 0) {
                                billBock.style.display = 'block'
                            } else {

                                billBock.style.display = 'none'
                            }

                            let productsTable = ``
                            theProducts.forEach(product => {
                                // console.log(product) v7;

                                productsTable += /*html*/ `

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
                            <input   type="text" name="prodMount[]" id="prodMount[]" value="${product.amount}" class="form-control mb-1" hidden>
                           Tsh ${(formatNumber(product.amount))}
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

            // function to calculate amount based on lot size 
            function productBillSmallLot(lot) {
                if (lot > 0 && lot <= 100) {
                    return 30000;
                } else if (lot >= 101 && lot <= 500) {
                    return 100000;
                } else if (lot >= 501 && lot < 3199) {
                    return 200000;
                }
            }
        
        
            function productBillFirstFiveLargeLot(lot) {
                if (lot > 3199) {
                    return 300000;
                }
            }
            //
            function productBillSecondFiveLargeLot(lot) {
                if (lot > 3199) {
                    return 200000;
                }
            }

            function productBillMoreThanTenLot(lot) {
                if (lot > 3199) {
                    return 100000;
                }
            }

            function calculateFOB(fob) {
                if (Number(fob) < 100000) {
                    return 100000
                } else {
                    const percent = percentage(0.2, fob)

                    if (percent < 100000) {
                        return 100000
                    } else {
                        return percent
                    }
                }
            }
//
            function getCompleteProducts() {
                fetch('<?=base_url()?>getCompleteProducts', {
                        method: 'POST',
                        headers: {
                        
                            'Content-Type': 'application/json;charset=utf-8',
                            "X-Requested-With": "XMLHttpRequest",
                            'X-CSRF-TOKEN': document.querySelector('.token').value
                        },

                        body: JSON.stringify({
                            customerId: document.querySelector('#customerId').value
                        }),

                    }).then(response => response.json())
                    .then(data => {
                        console.log(data)
                        const {
                            token,
                            status,
                            products
                        } = data
                        document.querySelector('.token').value = token
                        let productsList = ``
                        productsList += '<option selected disabled>--Products--</option>'

                        Object.values(products).map(obj => obj).forEach(product => {
                            productsList += `
                    
                    <option value="${product.id}">${product.commodity}  ${product.quantity} ${product.unit}</option>`

                        })

                        $('#products').html(productsList)
                    })
            }

            function checkQuantityId(id) {
                const switcher = document.querySelector('#switch')
                // const sampleSizeVal = document.querySelector('#sampleSizeOptions').value


                const params = {
                    quantityId: id,
                    productId: document.querySelector('#commodityId').value
                }
                fetch('<?=base_url()?>checkQuantityId', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json;charset=utf-8',
                            'X-CSRF-TOKEN': document.querySelector('.token').value
                        },
                        body: JSON.stringify(params)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        data.switch == true ? switcher.value = 'switch' : switcher.value = ''
                        document.querySelector('.token').value = data.token
                        console.log(data.sampleSize)
                        renderMeasurementData(id == 'x' ? 0 : data.sampleSize)

                        document.querySelector('#dimensions').textContent = data.dimensions

                        if (data.status == 1) {
                            document.querySelector('#dataSheetTable').innerHTML = ''
                            swal({
                                title: `Measurement For ${id.slice(11)} Already Exist`,
                                icon: "warning",
                                timer: 4500
                            });

                        } else {


                        }



                    });



            }

            //rendering measurement data based on sample size
            function renderMeasurementData(sampleSize) {
                const switcher = document.querySelector('#switch').value
                console.log('SELECTED SAMPLES' + sampleSize);

                // $('#measurementSheet').modal('show')

                const categoryOfAnalysis = document.querySelector('#categoryOfAnalysis').value
                console.log(categoryOfAnalysis)
                let table = ``
                let tableHeader = ``
                let indexNumber = 1
                let theCategory = ''
                switcher != '' ? theCategory += 'Linear' : theCategory += categoryOfAnalysis
                switch (theCategory) {
                    case 'General':
                    case 'Anthracite':
                    case 'Linear':
                    case 'Linear 2':
                    case 'Bread':
                    case 'Fruits':
                    case 'Medical_Gases':
                    case 'Gases':
                    case 'Seeds':
                    case 'Sheets':
                    case 'Poultry':
                    case 'Count':
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
                       <td style="width: ;"><input tabindex="${index}" id="grossInputs" name="weightGross[]" oninput="checkForError(this)" class="form-control measuredQty" type="number" min="0" max="1000000000" step="any" required></td>
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
                    case 'Area_Linear':
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
                            <input tabindex="${index}" id="width" name="width[]" oninput="checkForAreaError(this)" class="form-control measuredQty" type="number" min="0" max="100000000" step="any" required>
                       </td>

                       <td>
                        <input tabindex="${index+1}" id="length" min="0" max="100000000" step="any" name="length[]" oninput="checkForAreaError(this)"  data-weight='net'   class="form-control netQuantity" type="number" required>
                        </td>

                      
                       <td>
                        <input step="any" name="area[]" readonly data-weight='net'   class="form-control netQuantity" type="number" required min="0" max="100000000" step="any">
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


                    case 'Cubic':
                        tableHeader += `
                 <thead class="thead-dark">
                  <tr>
                   <th>#</th>
                   <th>Length</th>
                   <th>Width</th>
                   <th>Height | Thickness | Gauge</th>
                   <th>Volume</th>
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
                        <input tabindex="${index+1}" id="length" min="0" max="100000000" step="any" name="length[]" oninput="checkForCubicError(this)"  data-weight='net'   class="form-control netQuantity" type="number" required>
                        </td>

                       <td>
                            <input tabindex="${index+1}" id="width" name="width[]" oninput="checkForCubicError(this)" class="form-control measuredQty" type="number" min="0" max="100000000" step="any" required>
                       </td>

                       

                      
                       <td>
                        <input tabindex="${index+1}" id="height" min="0" max="100000000" step="any" name="height[]" oninput="checkForCubicError(this)"  data-weight='net'   class="form-control netQuantity" type="number" required>
                        </td>

                      
                       <td>
                        <input step="any" name="volume[]" readonly data-weight='net'   class="form-control netQuantity" type="number" required min="0" max="100000000" step="any">
                        </td>

                       <td>
                       <input data-comment="comment" readonly name="comment[]" required class="form-control style="border:0;outline:block;"/>
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


                tableHeader += table
                $('#dataSheetTable').html('')
                $('#dataSheetTable').append(tableHeader)

            }

            function checkForError(param) {


                const tareWeight = document.querySelector('#productTare').value
                const quantity = document.querySelector('#currentQuantity').value.replace(/[^\d.-]/g, '') //255

                const tolerable = tolerableDeficiencyMs(quantity)
                const parent = param.parentNode.parentNode
                const enteredQuantity = param.value

                const netQuantity = parent.children[2].children[0]
                const comment = parent.children[3].children[0]
                const status = parent.children[4].children[0]


                if (enteredQuantity) calculateError(+enteredQuantity, +quantity, +tolerable, +tareWeight, netQuantity, comment, status)

                console.log('The tolerable is' + tolerable);
                console.log('The quantity is' + quantity);


            }


            function checkForAreaError(param) {
                const enteredQuantity = param.value
                const quantity = document.querySelector('#currentQuantity').value.replace(/[^\d.-]/g, '')
                const tareWeight = 0
                const tolerable = tolerableDeficiencyMs(quantity)
                const parent = param.parentNode.parentNode
                const width = parent.children[1].children[0].value
                const length = parent.children[2].children[0].value
                const area = parent.children[3].children[0]
                const comment = parent.children[4].children[0]
                const status = parent.children[5].children[0]
                area.value = Number(width * length)

                console.log(Number(width * length))
                console.log('wd ' + width)
                console.log('ht ' + length)

                if (width && length) calculateError(+enteredQuantity, +quantity, +tolerable, +tareWeight, area, comment, status)



            }

            function checkForCubicError(param) {
                const enteredQuantity = param.value
                const quantity = document.querySelector('#currentQuantity').value.replace(/[^\d.-]/g, '')
                const tareWeight = 0
                const tolerable = tolerableDeficiencyMs(quantity)
                const parent = param.parentNode.parentNode
                const length = parent.children[1].children[0].value
                const width = parent.children[2].children[0].value
                const height = parent.children[3].children[0].value
                const volume = parent.children[4].children[0]
                const comment = parent.children[5].children[0]
                const status = parent.children[6].children[0]
                volume.value = Number(width * length * height)

                console.log(Number(length * width * height))
                console.log('length ' + length)
                console.log('width ' + width)
                console.log('height ' + height)

                if (width && length && height) calculateError(+enteredQuantity, +quantity, +tolerable, +tareWeight, volume, comment, status)



            }

            function getMeasurements(id) {

                const quantityId = document.querySelector('#quantityId').value

                <?php if (url_is('registeredPrepackages/' . $user->collection_center)) : ?>
                    const customerIdentifier = document.querySelector('#customerId').value

                    const download = document.querySelector('#downloadBtn')

                    const link = `<?= base_url() ?>/downloadProductData/${customerIdentifier}/${id}/${quantityId ? quantityId: '00'}`

                    download.setAttribute('href', link)
                <?php endif; ?>

                    //mxx

                $.ajax({
                    type: "post",
                    url: "<?=base_url()?>getMeasurementData",
                    data: {
                        includeQuantity: true,
                        productId: id,
                        quantityId: quantityId,
                    },
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                    },
                    success: function(response) {
                        console.log(response)
                        //collectiveDecision()
                        document.querySelector('.token').value = response.token
                        $('#measurementsPreview').modal('show')
                        // let declaredQuantity = document.querySelector('#productGrossWeight').value
                        let declaredQuantity = response.data[0].quantity_id.substr(11).replace(/[^\d.-]/g, '')
                        // console.log('declaredQuantity ' + declaredQuantity)
                        
                        const withT1Error = response.data.filter((data) => data.status == 1)
                        const withT2Error = response.data.filter((data) => data.status == 2)

                        const netQuantities = response.data.map(net => {
                            return Number(net.net_quantity - declaredQuantity)
                        })

                        // console.log('*********************net q**********************');
                        console.log(netQuantities);
                        // console.log('*******************************************');


                        const individualError = netQuantities.reduce((prev, next) => {
                            return prev + next
                        }, 0)



                        renderSummaryTable(withT1Error, withT2Error, individualError, netQuantities, declaredQuantity, response.results)
                        renderMeasurementTable(response.data, response.results.category, response.switcher)


                        // let individualError = netQuantities.forEach(qty => {

                        // }) Pa$$word123
                        //console.log('1111111111111111111111111111111111111111111111111')
                        // console.log(withT1Error);
                        // console.log('Data Length ' + netQuantities.length);
                        // console.log('Individual error is ' + individualError);
                    }
                });
            }
            //calculate  T1 and T2 error
            //calculate  T1 and T2 error c1
            function calculateError(enteredQuantity, quantity, tolerable, tareWeight, netQuantity, comment, status) {
                //switcher variable is used to determine when to switch to a different measurement sheet
                const switcher = document.querySelector('#switch').value
                const productNature = document.querySelector('#natureOfProduct').value
                const productDensity = document.querySelector('#productDensity').value
                const categoryAnalysis = document.querySelector('#categoryOfAnalysis').value
                const measurementNature = document.querySelector('#measurementNature').value
                comment.value = ''
                const grossWt = Number(quantity + tareWeight)
                console.log(switcher)
                let net = 0

                console.log(categoryAnalysis)
                console.log(' measurementNature ' + measurementNature)

                switch (categoryAnalysis) {
                    case 'General':
                    case 'Anthracite':
                    case 'Bread':
                    case 'Poultry':
                    case 'Fruits':
                    case 'Medical_Gases':
                    case 'Gases':
                    case 'Seeds':
                    case 'Sheets':

                        net += Number(enteredQuantity - tareWeight)
                        tolerable = 0
                        break;
                    case 'Linear':
                    case 'Linear 2':

                        net += Number(enteredQuantity)
                        tolerable = 0
                        break;
                    case 'Area':

                        net += Number(netQuantity.value)
                        tolerable = 0
                        break;
                    case 'Count':

                        measurementNature == 'Gross' ? net += Math.floor(Number(enteredQuantity)) :
                            net += Math.floor(Number(enteredQuantity))
                            tolerable = 0

                        break;
                    case 'Cubic':

                        net += Number(netQuantity.value)
                        tolerable = 0
                        break;

                    case 'Area_Linear':
                        if (switcher != '') {

                            tolerable = 0
                            net += Number(enteredQuantity)
                        } else {

                            net += Number(netQuantity.value)
                            tolerable = 0
                        }


                        // net += 120
                        break;



                    default:
                        break;
                }


                if (productNature == 'Liquid' && productDensity != '') {

                    netQuantity.value = (net / Number(productDensity)).toFixed(3)
                } else {
                    netQuantity.value = net
                }



                console.log('quantity ' + quantity);
                // console.log('nature ' + productNature);
                // console.log('density ' + productDensity);
                console.log('Net ' + netQuantity.value);

                const tolerableAmount = tolerable

                let T1 = Number(quantity - tolerableAmount)
                let T2 = Number(quantity - (tolerableAmount * 2))

                console.log('T1 VALUE ' + T1);
                console.log('T2 VALUE ' + T2);
                console.log('TOLERABLE ' + tolerable);
                console.log('grossWt ' + grossWt);
                console.log('Tare ' + tareWeight);
                // console.log('Net weight ' + net / +productDensity);

                netQuantity.removeAttribute('data-status')



                const theNetValue = +netQuantity.value
                console.log('NET WT ' + theNetValue);

                switch (categoryAnalysis) {
                    case 'Bread':
                    case 'Poultry':


                        if (theNetValue < T1) {
                            comment.value = 'Has  T1 And T2 Error'
                            status.value = 2
                            netQuantity.style.border = '1px solid  red'
                            netQuantity.setAttribute('data-status', 'T1Error')

                        } else if (theNetValue >= T1) {
                            comment.value = 'Pass T1  And T2 Error'
                            status.value = 0
                            netQuantity.style.border = '1px solid  green'
                            netQuantity.setAttribute('data-status', 'Pass')
                        }

                        break;

                    default:
                        //101
                        if (theNetValue < T1 && theNetValue > T2) {
                            comment.value = 'Has  T1 Error'
                            status.value = 1
                            netQuantity.style.border = '1px solid  red'
                            netQuantity.setAttribute('data-status', 'T1Error')

                        } else if (tolerable == 0 && theNetValue >= T1) {
                            console.log('check linear*****')
                            console.log('net    WT ' + theNetValue)
                            console.log('T1 VL ' + T1)
                            console.log('tolrable def ' + tolerable)
                            comment.value = 'Pass T1  And T2 Error'
                            status.value = 0
                            netQuantity.style.border = '1px solid  green'
                            netQuantity.setAttribute('data-status', 'Pass')
                        } else if (theNetValue <= T2) {
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


            }

            function calcAverage() {
                const calculatedTare = document.querySelector('#calculatedTare')
                const inputs = document.querySelectorAll('.weight')
                const quantity = document.querySelector('#quantity').value.replace(/\D/g, '')
                const unit = document.querySelector('#unit').value
                let total = 0
                let tareWeight = 0
                let nominalQuantity = unitConverter(unit, quantity).replace(/\D/g, '')

                let allow25 = document.querySelector('#allow25')


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
                    // document.querySelector('#allow25').value = ''
                    calculatedTare.style.border = '1px solid green'
                    document.querySelector('#msg').textContent = ''
                    document.querySelector('#tareApprover').removeAttribute('disabled', 'disabled')
                    $('#sdCalcBtn').hide()

                    console.log('-----------1------------------')
                    console.log('tenPercentOfQn'+ tenPercentOfQn )
                    console.log('average'+ tenPercentOfQn )

                } else if (average > tenPercentOfQn ) {

                    // const T = tolerableDeficiency(nominalQuantity)

                    // const sd = standardDeviation(array)
                    console.log('-----------2------------------')
                    console.log('tenPercentOfQn'+ tenPercentOfQn )
                    console.log('average'+ tenPercentOfQn )

                   // calculateDeviation() 


                   document.querySelector('#msg').textContent = 'Tare Weight is invalid, calculate standard deviation'
                   
                   if(document.querySelector('#allow25').value == 'allow25'){
                    document.querySelector('#msg').textContent = ''
                       document.querySelector('#tareApprover').removeAttribute('disabled', 'disabled')
                       $('#sdCalcBtn').hide()
                       calculatedTare.style.border = '1px solid green'
                    }else{
                        calculatedTare.style.border = '1px solid red'
                        document.querySelector('#tareApprover').setAttribute('disabled', 'disabled')
                         $('#sdCalcBtn').show()

                    }


                }



                document.querySelector('#ten').textContent = tenPercentOfQn + ' g'
                document.querySelector('#avg').textContent = average.toFixed(2) + ' g'


            }



            // calculating sample standard deviation
            function calculateDeviation() {
                let theUnit = document.querySelector('#unit').value
                let theQnt = document.querySelector('#quantity').value.replace(/\D/g, '')

                let NQ = unitConverter(theUnit, theQnt)
                let nominalQuantity = NQ.replace(/\D/g, '')
                const inputs = document.querySelectorAll('.weight')
                let array = []
                inputs.forEach(input => {
                    array.push(+input.value)
                })

                const T = tolerableDeficiency(nominalQuantity)

                const sd = standardDeviation(array)

                console.log('theUnit ' + theUnit)
                console.log('theQnt ' + theQnt)
                console.log('nominalQuantity ' + nominalQuantity)
                console.log('TOLERABLE ' + T)
                console.log('T * 0.25 '+ T * 0.25)
                console.log('sd '+ sd)

                if (sd <= (T * 0.25)) {
                    // console.log('Tare Weight passed');
                    document.querySelector('#allow25').value = 'allow25'
                    document.querySelector('#tareApprover').removeAttribute('disabled', 'disabled')
                    $('#sdCalcBtn').hide()
                    document.querySelector('#tareApprover').removeAttribute('disabled', 'disabled')
                    $('#sdCalcBtn').hide()
                    let inputs = ``
                    for (let index = 1; index <= 15; index++) {
                        inputs += /*html*/ `
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
                        title: 'Average Tare Mass failed please select destructive method',
                        icon: "warning",
                       // timer: 4500
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





            function renderMeasurementTable(data, category, switcher) {

                let tbl = ``
                let i = 1



                switch (switcher == 1 ? 'Linear' : category) {
                    case 'Area':
                    case 'Area_Linear':
                        // let tb = ``
                        tbl += /*html*/ `
            <thead class="thead-dark">
                                <tr>
                                    <th>S./No</th>
                                    <th>Width</th>
                                    <th>Height</th>
                                    <th>Area</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
          </tbody>
            `


                        data.forEach(d => {
                            tbl += /*html*/ `
        <tr>
          <td>${i++}</td>
          <td>${d.width}</td>
          <td>${d.length}</td>
          <td>${d.net_quantity}</td>
          <td>${d.comment}</td>
        </tr>
           `
                        })
                        break;
                    case 'Cubic':
                        // let tb = ``
                        tbl += /*html*/ `
            <thead class="thead-dark">
                                <tr>
                                    <th>S./No</th>
                                    <th>Length</th>
                                    <th>Width</th>
                                    <th>Height</th>
                                    <th>Volume</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
          </tbody>
            `


                        data.forEach(d => {
                            tbl += /*html*/ `
        <tr>
        <td>${i++}</td>
        <td>${d.length}</td>
        <td>${d.width}</td>
        <td>${d.height}</td>
          <td>${d.net_quantity}</td>
          <td>${d.comment}</td>
        </tr>
           `
                        })
                        break;

                    default:
                        tbl += /*html*/ `
            <thead class="thead-dark">
                                <tr>
                                <th>S./No</th>
                                <th>Gross Quantity</th>
                                <th>Net Quantity</th>
                                <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
          </tbody>
            `


                        data.forEach(d => {
                            tbl += `
        <tr>
          <td>${i++}</td>
           <td>${d.gross_quantity}</td>
          <td>${d.net_quantity}</td>
          <td>${d.comment}</td>
        </tr>
           `
                        })
                        break;
                }




                $('#measurementPreviewTable').html(tbl)


            }

            function collectiveDecision(data) {
                console.log(data)
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

            //approve tare weight if its valid
            function approveTareWeight() {
                let tare = document.querySelector('#calculatedTare').value
                document.querySelector('#tareWeight').value = tare
                $('#tareModal').modal('hide')
            }

            let myArray = []




            //=================evaluating sample status====================




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
                    case 'sqM':
                        return 'sqM'
                        break;
                    case 'sqCm':
                        return 'sqCm'
                        break;

                    default:
                        break;
                }
            }



            function checkLabelling() {
                const submitBtn = document.querySelector('#button')

                // const recommendation = document.querySelector('#recommendation')
                // const recommendationInput = document.querySelector('.recommendation')
                let labeling = []
                const radios = document.querySelectorAll('.label-check')
                radios.forEach(radio => {
                    if (radio.checked == true) labeling.push(radio.value)
                })

                console.log(labeling)
                if (labeling.length == 6) {
                    submitBtn.removeAttribute('disabled', 'disabled')
                    // if (labeling.includes('Not Correct') || labeling.includes('Non Deceptive')) {
                    //     recommendation.textContent = 'Advice The  Customer'
                    //     recommendationInput.value = 'Advice The  Customer'

                    // } else {
                    //     recommendation.textContent = ' Go To The Next Stage'
                    //     recommendationInput.value = 'Go To The Next Stage'
                    // }
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

                // both t1 and t 2 error
                const samplesWithError = realT1.concat(realT2)

                let t1Percentage = realT1.length * 100 / +theSampleSize

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

                let productSampleSize = document.querySelector('#productSampleSize').value
                let appliedMethod = document.querySelector('#appliedMethod').value

                if (productSampleSize == 20 && appliedMethod == 'Destructive') {
                    approved += 0

                    if (approved > 0) {
                        decision = ' Sample Failed the required test reject'
                    }

                    correctionFactor += 0.640


                } else if (productSampleSize == 50 && appliedMethod == 'Non Destructive') {
                    approved += 3
                    if (approved > 3) {
                        decision = ' Sample Failed the required test reject'
                    }
                    correctionFactor += 0.379
                } else if (productSampleSize == 80 && appliedMethod == 'Non Destructive') {
                    approved += 5
                    if (approved > 5) {
                        decision = ' Sample Failed the required test reject'
                    }
                    correctionFactor += 0.295
                } else if (productSampleSize == 125 && appliedMethod == 'Non Destructive') {
                    approved += 7
                    if (approved > 7) {
                        decision = ' Sample Failed the required test reject'
                    }
                    correctionFactor += 0.234
                }

                function checkPositiveOrNegative(individualError) {
                    if (individualError >= 0) {
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
                    approved: approved,
                    decision: decision,
                    correctionFactor: correctionFactor,
                    individualError: individualError,
                    averageError: averageError,
                    theSampleErrorLimit: theSampleErrorLimit,
                    correctedAVG: Number(averageError + theSampleErrorLimit)

                });
                console.log('##########################QTYYYYY################################');

                 console.log(productQuantity)
                 console.log(tolerableAmount)

                // const correctedAverageError = Number((individualError / theSampleSize) * correctionFactor)
                // (individualError / theSampleSize)
                const correctedAverageError = Number(averageError + theSampleErrorLimit)

                console.log('correctedAverageError ' + correctedAverageError);

                let table = /*html*/ `

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
                ${results.quantity2 !=null ? /*html*/`
                    <tr>
                    <td><b>${results.quantity2}</b></td>
                    <td>${results.quantity2Status}</td>
                </tr>
                    
                    ` : ''}
                
               
                
                
                <tr>
                    <td><b>Overall Decision </b></td>
                    <td><b>${results.overallStatus}</b></td>
                </tr>

            </tbody>
        </table>
        <hr>




  




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
                    <td>${individualError >= 0 ?'Samples Passed Individual Pre-packages Error Test'
                    :'Samples Failed Individual Pre-packages Error Test'}</td>
                    <td> (${checkPositiveOrNegative(individualError.toFixed(3))})</td>
                    <td>Equal Or Greater</td>
                </tr>
                <tr>
                    <td>Corrected Average Error Test Results</td>
                    <td>${Number(correctedAverageError) >= 0 ?'Samples Passed Corrected Average Error Test Result' :'Samples Failed Corrected Average Error Test Result'}</td>
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
                    <td>${realT1.length}</td>
                </tr>
                <tr>
                    <td><b>Percent No T1 / Sample Size</b></td>
                    <td>${t2Percentage}%</td>
                </tr>
                <tr>
                    <td><b>Decision At This Stage </b></td>
                    <td><b>${realT1.length > approved ?'Sample Fail T1 Test- Reject':'Sample Pass T1 Test- Go for T2 Test'}</b></td>
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
                if(lot <=99){
                    $('#sampleSize').val(lot)
                    data = lot
                }
               else if (lot >= 100 && lot <= 500) {

                    $('#sampleSize').val(50)
                    data = 50
                } else if (lot >= 501 && lot <= 3200) {
                    $('#sampleSize').val(80)
                    data = 80
                } else if (lot > 3200) {
                    $('#sampleSize').val(125)
                    data = 125
                }

                if (this.value == 'Destructive') {
                    $("#sampling").val('Non Sampling');
                    $('#tareBlock').hide()
                    //$('#densityBlock').hide()
                    data = 20
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

            function calcGross(unit) {
                const qty = document.querySelector('#quantity').value.replace(/\D/g, '')
                const grossQty = unitConverter(unit, qty)
                document.querySelector('#grosValue').value = grossQty

                //console.log(grossQty);
            }

            function calcGrossUnit2(unit) {
                const qty = document.querySelector('#quantity2').value
                const grossQty = unitConverter(unit, qty)
                document.querySelector('#grosValue2').value = grossQty

                //console.log(grossQty);
            }
            //metric unit converter
            function getGrosQuantity(qty) {
                const unit = document.querySelector('#unit').value
                const grossQty = unitConverter(unit, qty)
                document.querySelector('#grosValue').value = grossQty
            }

            function getGrosQuantity2(qty) {
                const unit2 = document.querySelector('#unit2').value
                const grossQty = unitConverter(unit2, qty)
                document.querySelector('#grosValue2').value = grossQty
            }

            function unitConverter(unit, value) {

                let ans
                switch (unit) {

                    case 'mg':

                        ans = value / 1000
                        return ans + ' g'

                        break;
                    case 'g':

                        return value + ' g'


                        break;
                    case 'kg':

                        ans = value * 1000

                        return ans + ' g'
                        break;
                    case 'L':

                        ans = value * 1000

                        return ans + ' ml'
                        break;
                    case 'mL':

                        return value + ' ml'


                        break;
                    case 'm':

                        ans = value * 1000

                        return ans + ' mm'
                        break;
                    case 'cm':

                       // ans = value * 10
                        return value + ' cm'

                        break;
                    case 'mm':

                        return value + ' mm'


                        break;
                    case 'sqCm':
                        return value + ' sqCm'

                        break;
                    case 'sqM':
                        return value + ' sqM'


                        break;
                    case 'cubCm':
                        return value + 'cubCm'

                        break;

                    case 'Pieces':

                        return value + 'pieces'

                    default:
                        break;
                }
            }


            //calculate price based on the lot size      
            function calculatePrice(lots) {
                function lotCalculator(lot) {
                    if (lot > 0 && lot <= 100) {
                        return 100000
                    } else if (lot > 101 && lot <= 500) {

                    }
                }


                //(lots);
            }

            function selectProduct(id) {
                $('#dataSheetTable').html('')


                //get all the product measurements
                //getMeasurements(id)

                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>selectProduct",
                    data: {
                        id: id,

                    },
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                    },
                    success: function(response) {
                        console.log(response);
                        document.querySelector('.token').value = response.token
                        console.log('UNIT 3' + response.data.unit_2)
                        // console.log(response.quantityIds);

                        let grossOptions = []

                        if (response.data.quantity1_id != '') {
                            grossOptions.push(response.data.quantity1_id)
                        }

                        if (response.data.quantity2_id != '') {
                            grossOptions.push(response.data.quantity2_id)
                        }


                        console.log('grossOptions...')
                        console.log(grossOptions)
                        //FIXME get the actual value of the quantity and its QuantityId
                        const measurementOptions = grossOptions.filter(n => n)
                        let options = `<option selected value="x">--Select Quantity--</option>`
                        grossOptions.forEach(q => {
                            options += `
                      
                      <option value="${q}">${q.slice(11)}</option>
                      `
                        })
                        //FIXME send a req to get product product net qty by using quantity_id

                        $('#measurementOptions').html('')
                        $('#measurementOptions').html(options)
                        $('#currentQuantity').val((grossOptions[0].replace(/[^\d.-]/g, '')).slice(11))
                        $('#productQuantity').val(grossOptions[0])

                        // console.log(measurementOptions)
                        // console.log(options)

                        // console.log('******000000000000000************')

                        if (response.data.product_nature == 'Solid') {
                            document.querySelector('#densityBtn').style.display = 'none'
                            document.querySelector('.density').style.display = 'none'
                        } else {
                            document.querySelector('#densityBtn').style.display = 'block'
                            document.querySelector('.density').style.display = 'block'
                        }





                        $('#commodityInfo').html( /*html*/ `
                
            <table class="table table-sm">

                
            <input class="form-control" id="commodityId" value="${response.data.id}"  hidden  />
            <input class="form-control cat" id="categoryOfAnalysis" value="${response.data.analysis_category}" hidden  />
            <input class="form-control cat" id="measurementNature" value="${response.data.measurement_nature}" hidden  />
               
                    <tr>
                    
                        <td>Task/Activity</td>
                        <td>${response.data.task}</td>

                    </tr>
                    <tr>
                    
                        <td>Inspection Type</td>
                        <td>${response.data.type}</td>

                    </tr>
                    ${response.data.type == 'Imported'
                    ?`
                     <tr>
                       
                        <td>TANSAD Number</td>
                        <td>${response.data.tansard_number} </td>
                    </tr>

                    ${response.data.tansard_file  != '' ? `
                        
                      <tr>
                       
                        <td>Assessment Document</td>
                        <td> <a class="btn btn-primary btn-sm" href="${response.data.tansard_file}" target="_blank"><i class="fal fa-download"></i>  Assessment Document</a> </td>
                    </tr>  
                        
                        ` : ''}
                     
                     <tr>
                       
                        <td>F.O.B Value</td>
                        <td>Tsh ${formatNumber(response.data.fob)} </td>
                    </tr>
                     <tr>
                       
                        <td>Date</td>
                        <td>${response.data.date} </td>
                    </tr>
                    `
                    : ''
                    }
                    <tr>
                    
                        <td>Commodity</td>
                        <td>${response.data.commodity}</td>

                    </tr>

                     <tr>
                       
                        <td>Quantity</td>
                        <td>${response.data.quantity} <span style="font-family: 'Roboto', sans-serif;" class="unit">${response.data.unit}</span> </td>
                    </tr>
                    ${response.data.quantity_2 != ''
                    ?`
                     <tr>
                       
                        <td>(Thickness / Diameter)</td>
                        <td>${response.data.quantity_2} <span style="font-family: 'Roboto', sans-serif;" class="unit">${response.data.unit_2}</span> </td>
                    </tr>
                    `
                    : ''
                    }
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
                       
                        <td>Nature Of Measurement</td>
                        <td> ${response.data.measurement_nature}</td>
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
                                <select class="form-control" name="" id="quantityId" >
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



            function createMeasurements(sampleSize) {
                $('#measurementSheet').modal('show')
                $('#sampleSizeOptions').val(sampleSize)
                document.querySelector('#dimensions').textContent = ''

                //console.log(sampleSize)

            }

            function toggleDensity(val) {
                console.log(val)
                if (val == 'Solid') {
                    document.querySelector('#densityBlock').style.display = 'none'
                    // document.querySelector('.density').style.display = 'none'
                } else {
                    document.querySelector('#densityBlock').style.display = 'block'
                    // document.querySelector('.density').style.display = 'block'
                }
            }

            //percentage calculator
            function percentage(percent, theValue) {
                return (percent / 100) * theValue
            }

            function openModal() {
                const nominalQuantity = unitConverter(unit, quantity)
                document.querySelector('#calculatedTare').value = ''
                $('#tareModal').modal('show')
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


            //function to calculate tolerable deficiency of the nominal quantity

            function tolerableDeficiency(nQ) {
                const categoryOfAnalysis = document.querySelector('#commodityCategory').value
               
                switch (categoryOfAnalysis) {
                    case 'General':
                    case 'Anthracite':
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
                            return 0
                        } else if (nQ > 5000) {
                            return percentage(2, nQ)
                        }
                        break;
                    case 'Area':
                    case 'Area_Linear':
                        return percentage(3, nQ)
                        break;
                    case 'Count':
                        if (nQ < 50) {
                            return 0
                        } else {

                            return percentage(1, nQ)
                        }
                        break;
                    case 'Cubic':
                    case 'Sheets':
                        return percentage(2, nQ)
                        break;
                    case 'Bread':
                        return percentage(5, nQ)
                        break;
                    case 'Poultry':
                        return 0
                        break;
                    case 'Fruits':
                        return percentage(5, nQ)
                        break;
                    case 'Medical_Gases':
                        return percentage(5, nQ)
                        break;
                    case 'Gases':
                        return percentage(3, nQ)
                        break;
                    case 'Seeds':
                        if (nQ <= 50) {
                            return 0

                        } else if (nQ > 50 && nQ <= 1000) {
                            return percentage(2, nQ)
                        } else {
                            return percentage(4, nQ)
                        }
                        break;

                    default:
                        return 10
                        break;
                }

            }
            function tolerableDeficiencyMs(nQ) {
                const categoryOfAnalysis = document.querySelector('#categoryOfAnalysis').value
                // const categoryOfAnalysis = document.querySelector('#categoryAnalysis').value
                switch (categoryOfAnalysis) {
                    case 'General':
                    case 'Anthracite':
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
                            return 0
                        } else if (nQ > 5000) {
                            return percentage(2, nQ)
                        }
                        break;
                    case 'Area':
                    case 'Area_Linear':
                        return percentage(3, nQ)
                        break;
                    case 'Count':
                        if (nQ < 50) {
                            return 0
                        } else {

                            return percentage(1, nQ)
                        }
                        break;
                    case 'Cubic':
                    case 'Sheets':
                        return percentage(2, nQ)
                        break;
                    case 'Bread':
                        return percentage(5, nQ)
                        break;
                    case 'Poultry':
                        return 0
                        break;
                    case 'Fruits':
                        return percentage(5, nQ)
                        break;
                    case 'Medical_Gases':
                        return percentage(5, nQ)
                        break;
                    case 'Gases':
                        return percentage(3, nQ)
                        break;
                    case 'Seeds':
                        if (nQ <= 50) {
                            return 0

                        } else if (nQ > 50 && nQ <= 1000) {
                            return percentage(2, nQ)
                        } else {
                            return percentage(4, nQ)
                        }
                        break;

                    default:
                        return 2023
                        break;
                }

            }

            let measurementSheetForm = document.querySelector('#measurementSheetForm')
            measurementSheetForm.addEventListener('submit', function evaluateStatus(e) {

                e.preventDefault()

                console.log('measurements data')
                const formData = new FormData(measurementSheetForm)
                let commodityId = document.querySelector('#commodityId').value
                formData.append("commodityId", commodityId);
                formData.append("commodityCategory", document.querySelector('#commodityCategory').value);
                formData.append("currentQuantity", document.querySelector('#productQuantity').value);
                formData.append("switcher", document.querySelector('#switch').value);



                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>saveMeasurementData",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    beforeSend: function(xhr) {
                        submitInProgress(e.submitter)
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                    },
                    success: function(response) {
                        document.querySelector('.token').value = response.token

                        let td = document.querySelector('#actions')
                        //TODO Check if if 2 sets of measurements are required before removing create measurement button
                        $('#actions').html(
                            ` <button class="btn btn-primary btn-sm" onclick="getMeasurements('${commodityId}')">View Measurement Sheet</button>
                            `
                        )
                        console.log(td)
                        console.log(response)
                        // $('#measurementSheetForm')[0].reset()
                        $('#dataSheetTable').html('')
                        $('#measurementSheet').modal('hide')

                        if (response.status == 1) {
                            submitDone(e.submitter)
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
            //134 949 770

            function calculateSampling(lot) {
                //const lot = $('#batchSize').val()
                let data = 0
                if (lot > 0 && lot < 100) {
                    $('#sampleSize').val(lot)
                } else if (lot >= 100 && lot <= 500) {

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

            
        </script>