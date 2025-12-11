<?= $this->extend('Layouts/pdfLayout') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="customer">
        <table border="0" style="width: 42%; margin-bottom:20px">

            <tr>
                <td><b>Name Of Packer/Client :</b></td>
                <td class="left"><?= $customerDetails->name ?></td>
            </tr>
            <tr>
                <td><b>Physical Address :</b></td>
                <td class="left"><?= $customerDetails->physical_address ?></td>
            </tr>
            <tr>
                <td><b>Postal Address :</b></td>
                <td class="left"><?= $customerDetails->postal_address ?></td>
            </tr>
            <tr>
                <td><b>Postal Code :</b></td>
                <td class="left"><?= $customerDetails->postal_code ?></td>
            </tr>
            <tr>
                <td><b>Phone Number :</b></td>
                <td class="left"><?= $customerDetails->phone_number ?></td>
            </tr>

        </table>
    </div>
</div>
<div class="container">
    <h4 style="margin: 2px 0; padding:0"><b>LABELLING REQUIREMENTS</b></h4>
    <table border="1" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 50%;"><b>Packer Correctly Identified</b></td>
            <td class="left">
                <?= $productDetails->packer_identification ?>

            </td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Product Correctly Identified</b></td>
            <td class="left">
                <?= $productDetails->product_identification  ?>

            </td>
        </tr>


        <tr>
            <td style="width: 50%;"><b>Correct Measuring Unit</b></td>
            <td class="left">
                <?= $productDetails->correct_unit ?>

            </td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Correct Symbol</b></td>
            <td class="left">
                <?= $productDetails->correct_symbol ?>


            </td>
        </tr>

        <tr>
            <td style="width: 50%;"><b>Correct Height</b></td>
            <td class="left">
                <?= $productDetails->correct_height ?>

            </td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Correct Prescribed Quantity(If Applicable)</b></td>
            <td class="left">
                <?= $productDetails->correct_quantity ?>

            </td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>General Appearance </b></td>
            <td class="left">
                <?= $productDetails->general_appearance ?>


            </td>
        </tr>

    </table>
    <table border="1" style="margin-bottom: 20px;" class="pageBreak">
        <tr>
            <td style="width: 50%;"><b>Category Of Analysis</b></td>
            <td class="left"><?= $productDetails->analysis_category  ?></td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Inspection Type</b></td>
            <td class="left"><?= $productDetails->type  ?></td>
        </tr>
        <?php if ($productDetails->type == 'Imported') : ?>
            <tr>
                <td style="width: 50%;"><b>Tansard Number</b></td>
                <td class="left"><?= $productDetails->tansard_number  ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><b>F.O.B</b></td>
                <td class="left">Tsh <?= number_format($productDetails->fob)  ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><b>Date</b></td>
                <td class="left"><?= $productDetails->date  ?></td>
            </tr>
        <?php else : ?>
            <tr>
                <td style="width: 50%;"><b>Batch Number</b></td>
                <td class="left"><?= $productDetails->batch_number ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><b>Reference Number</b></td>
                <td class="left"><?= $productDetails->ref_number ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><b>Batch Size / Inspection Lot Size</b></td>
                <td class="left"><?= $productDetails->lot  ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td style="width: 50%;"><b>Commodity / Brand</b></td>
            <td class="left"><?= $productDetails->commodity . ' ' . $productDetails->quantity . ' ' . $productDetails->unit ?></td>
        </tr>

        <tr>
            <td style="width: 50%;"><b>Quantity</b></td>
            <td class="left"><?= $productDetails->quantity . ' ' . $productDetails->unit ?></td>
        </tr>

        <?php if ($productDetails->quantity_2) : ?>
            <tr>
                <td style="width: 50%;"><b>(Thickness / Diameter)</b></td>
                <td class="left"><?= $productDetails->quantity_2 . ' ' . $productDetails->unit_2 ?></td>
            </tr>
        <?php endif; ?>


        <tr>
            <td style="width: 50%;"><b>Method To Be Applied</b></td>
            <td class="left"><?= $productDetails->method ?></td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Sampling Plan</b></td>
            <td class="left"><?= $productDetails->sampling ?></td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Nature Of Measurement</b></td>
            <td class="left"><?= $productDetails->measurement_nature ?></td>
        </tr>
        <!-- ---------- -->
        <tr>
            <td style="width: 50%;"><b>Nature Of Product</b></td>
            <td class="left"><?= $productDetails->product_nature ?></td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Packing Declaration</b></td>
            <td class="left"><?= $productDetails->packing_declaration ?></td>
        </tr>

        <?php if ($productDetails->density) : ?>
            <tr>
                <td style="width: 50%;"><b>Declared Product Density</b></td>
                <td class="left"><?= $productDetails->density ?></td>
            </tr>
        <?php endif; ?>

        <tr>


            <?php if($productDetails->packing_declaration == 'Length') : ?>
                <td class="left"><b>Length(Meters/Centimeters/Millimeters)</b></td>

            <?php elseif($productDetails->packing_declaration == 'Weight') : ?>
                <td class="left"><b>Weight(Kilograms/Grams)</b></td>
            <?php elseif($productDetails->packing_declaration == 'Area') : ?>
                <td class="left"><b>Area(Square Meters/Square Centimeters/Square Millimeters)</b></td>
            <?php elseif($productDetails->packing_declaration == 'Volume') : ?>
                <td class="left"><b>Volume(Liters/Milliliters)</b></td>
            <?php elseif($productDetails->packing_declaration == 'Pieces') : ?>
                <td class="left"><b>Pieces</b></td>
            <?php endif; ?>

            <td><?= number_format((float)preg_replace('/[^\d.]/', '', $productDetails->gross_quantity), 3) ?></td>

        </tr>

        <tr>
            <td style="width: 50%;"><b>Conversion Made On Measurements</b></td>
            <?php if($productDetails->packing_declaration == 'Length') : ?>
                <td class="left">Measured quantity converted into  Length</td>
            <?php elseif($productDetails->packing_declaration == 'Area') : ?>
                <td class="left">Measured  quantity converted into area</td>
            <?php elseif($productDetails->packing_declaration == 'Volume') : ?>
                <td class="left">Measured  quantity converted into volume</td>
            <?php elseif($productDetails->packing_declaration == 'Weight') : ?>
                <td class="left">Measured  quantity converted into weight</td>
            <?php elseif($productDetails->packing_declaration == 'Pieces') : ?>
                <td class="left">Measurement in Pieces</td>
          
            <?php else: ?>
            <?php endif; ?>
            <!-- <td class="left"><?= $productDetails->product_nature == 'Liquid' ? 'Measured Gross Weight Has Been Converted To Volume' : 'Measured Gross Weight Has Been Converted To Weight' ?></td> -->
        </tr>
        <tr>
            <td style="width: 50%;"><b>Declared Tare Weight</b></td>
            <td class="left"><?= $productDetails->tare ?> g</td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Declared Nominal <?= $productDetails->correct_unit  ?></b></td>
            <td class="left"><?= $productDetails->quantity . ' ' . $productDetails->unit ?></td>
        </tr>
        <tr>
            <td style="width: 50%;"><b>Sample Size Being Used</b></td>
            <td class="left"><?= $productDetails->sample_size ?></td>
        </tr>



    </table>
</div>

<div class="container pageBreak">
    <table border="1">

        <thead class="thead-dark">
            <tr>
                <th><b>Item</b></th>
                <th><b>Status</b></th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b><?= $overallResults->quantity1 ?></b></td>
                <td><?= $overallResults->quantity1Status ?></td>
            </tr>
            <?php if ($overallResults->quantity2 != null) : ?>
                <tr>
                    <td><b><?= $overallResults->quantity2 ?></b></td>
                    <td><?= $overallResults->quantity2Status ?></td>
                </tr>

            <?php endif; ?>



            <tr>
                <td><b>Overall Decision </b></td>
                <td><b><?= $overallResults->overallStatus ?></b></td>
            </tr>

        </tbody>
    </table>
</div>
<br>


<div class="container ">
    <table border="1">
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
                <td>
                    <?php if (count($samplesWithError) > 3 && $productDetails->method == 'Non Destructive') : ?>
                        Sample Failed T1-Reject
                    <?php elseif (count($samplesWithError) > 1 && $productDetails->method == 'Destructive') : ?>
                        Sample Failed T1-Reject
                    <?php else : ?>
                        Sample Passed T1 Test- Go For T2 Test
                    <?php endif; ?>
                </td>
                <td><?= count($samplesWithError) ?></td>
                <td><?= $approved ?></td>
            </tr>

            <tr>
                <td>T2 Test Result</td>
                <td><?= $t2Items > 0 ? 'Sample Failed T2-Reject' : 'Sample Passed T2 Test' ?> </td>
                <td><?= $t2Items ?></td>
                <td>0</td>
            </tr>
            <tr>
                <td>Individual Pre-Package Error Test - Result</td>
                <td><?= $individualError >= 0 ? 'Samples Passed Individual Pre-packages Error Test'
                        : 'Samples Failed Individual Pre-packages Error Test' ?></td>
                <td> (<?= checkPositiveOrNegative($individualError) ?>)</td>
                <td>Equal Or Greater</td>
            </tr>
            <!-- variable -->
            <?php
            $sampleErrorLimit = standardDeviation($netQuantities) * $correctionFactor;
            $averageError = $individualError / $sampleSize;

            $correctedAverageError =  round(($averageError +  $sampleErrorLimit), 3)

            ?>


            <tr>
                <td>Corrected Average Error Test Results</td>
                <td><?= ($correctedAverageError) >= 0 ? 'Samples Passed Corrected Average Error Test Result' : 'Samples Failed Corrected Average Error Test Result' ?></td>
                <td> (<?= checkPositiveOrNegative($correctedAverageError) ?>)</td>
                <td>Equal Or Greater</td>
            </tr>
            <tr>
                <td>Conclusion Remarks</td>
                <td colspan="3"><?= ($individualError < 0 || $correctedAverageError < 0 || $t2Items > 0) ? 'Sample Failed All required test-Reject' : 'Sample Passed All required Test- Approve' ?></td>
            </tr>

            <!--  individualError < 0 || correctedAverageError < 0 || realT2.length > 0  -->
        </tbody>
    </table>



    <h3 class="pageBreak0">Analysis Details For The Required Test</h3>

    <table border="1">
        <thead class="thead-dark">
            <tr>
                <th><b>Item</b></th>
                <th><b>Figure</b></th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Percent of Qn</b></td>
                <td><?= nominalQtyPercent($productQuantity) ?></td>
            </tr>
            <tr>
                <td><b>g or mL</b></td>
                <td><?= nominalQtyGram($productQuantity) ?></td>
            </tr>
            <tr>
                <td><b>Minimum For T1</b></td>
                <td><?= ($productQuantity - tolerableDeficiency($productQuantity)) ?></td>
            </tr>
            <tr>
                <td><b> Number of Item With T1 Error</b></td>
                <td><?= $t1Items ?></td>
            </tr>
            <tr>
                <td><b>Percent No T1 / Sample Size</b></td>
                <td><?= $t2Percentage ?>%</td>
            </tr>
            <tr>
                <td><b>Decision At This Stage </b></td>
                <td><b><?= $t1Items > $approved ? 'Sample Fail T1 Test- Reject' : 'Sample Pass T1 Test- Go for T2 Test' ?></b></td>
            </tr>

        </tbody>
    </table>

    <br>
    <table border="1">
        <thead class="thead-dark">
            <tr>
                <th><b>Item</b></th>
                <th><b>Figure</b></th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Percent of Qn</b></td>
                <td><?= nominalQtyPercent($productQuantity) * 2 ?></td>
            </tr>
            <tr>
                <td><b>g or mL</b></td>
                <td><?= nominalQtyGram($productQuantity) * 2 ?></td>
            </tr>
            <tr>
                <td><b>Minimum For T2</b></td>
                <td><?= ($productQuantity - (tolerableDeficiency($productQuantity) * 2)) ?></td>
            </tr>
            <tr>
                <td><b>Number of Item With T2 Error</b></td>
                <td><?= $t2Items ?></td>
            </tr>
            <tr>
                <td><b>Percent No T2 / Sample Size</b></td>
                <td><?= $t2Percentage ?>%</td>
            </tr>
            <tr>
                <td><b>Decision At This Stage </b></td>
                <td><b><?= $t2Items > 0 ? 'Sample Fail T2 Test-Reject' : 'Sample Pass T2 Test- Go for Pre-package Error Test' ?></b></td>
            </tr>

        </tbody>
    </table>

    <br>
    <br>
    <table border="1" class="pageBreak0">
        <thead class="thead-dark">
            <tr>
                <th><b>Item</b></th>
                <th><b>Figure</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Total Pre-Package Error</b></td>
                <td><?= ($individualError) ?></td>
            </tr>
            <tr>
                <td><b>Average Error</b></td>
                <td><?= ($averageError) ?></td>
            </tr>
            <tr>
                <td><b>Decision At This Stage</b></td>
                <td><b><?= $individualError >= 0 ? ' Sample Pass Individual Pre Package Error Test' : 'Sample Fail Individual Pre Package Error Test-Reject' ?></b></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1">

        <tbody>
            <tr>
                <td><b>Standard Deviation of The "Individual Pre - Package Errors"</b></td>
                <td><?= round(standardDeviation($netQuantities), 3) ?></td>
            </tr>
            <tr>
                <td><b>Sample Size</b></td>
                <td><?= $sampleSize ?></td>
            </tr>
            <tr>
                <td><b>Sample Correction Factor</b></td>
                <td><?= $correctionFactor ?></td>
            </tr>
            <tr>
                <td><b>Number Of Pre Packages in Sample Allowed To Have T1 Error</b></td>
                <td><?= $approved ?></td>
            </tr>
            <tr>
                <td><b>Sample Error Limit</b></td>
                <td><?= round($sampleErrorLimit, 3) ?></td>
            </tr>
            <tr>
                <td><b>Corrected Average Error</b></td>
                <td><?= $correctedAverageError ?></td>
            </tr>
            <tr>
                <td><b>Decision At This Stage</b> </td>
                <td><b><?= ($individualError * $correctionFactor) < 0 ? 'Sample Fail Corrected Average Error Test' : 'Sample Pass Corrected Average Error Test' ?></b>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="container">
    <?php $category = '';

    $switcher == 1 ? $category .= 'Linear' : $category
        .= $productDetails->analysis_category;

    $sn = 1
    ?>
    <div class="pageBreak">
        <h4>Data Observation Sheet And Recommendation On T1 And T2 Tests</h4>

        <?php if ($category == 'General' || $category == 'Linear' || $category == 'Linear 2' || $category == 'Count' || $category == 'Seeds' || $category == 'Bread' || $category == 'Poultry' || $category == 'Gases' || $category == 'Medical_Gases' || $category == 'Sheets' || $category == 'Fruits') : ?>
            <table border="1">
                <tr>
                    <th>S/No.</th>
                    <th><?= $productDetails->measurement_nature ?> Quantity</th>
                    <th>Net Quantity</th>
                    <th>Comment</th>
                </tr>
                <?php foreach ($measurementSheet as $data) : ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= (float)preg_replace('/[^\d.]/', '', $data->gross_quantity) ?></td>
                        <td><?= $data->net_quantity ?></td>
                        <td><?= $data->comment ?></td>
                    </tr>

                <?php endforeach; ?>
            </table>
        <?php elseif (($category == 'Area' || $category == 'Area_Linear')) : ?>
            <table border="1">
                <tr>
                    <th>S/No.</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th>Area</th>
                    <th>Comment</th>
                </tr>
                <?php $sn = 1 ?>
                <?php foreach ($measurementSheet as $data) : ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= $data->width ?></td>
                        <td><?= $data->length ?></td>
                        <td><?= $data->net_quantity ?></td>
                        <td><?= $data->comment ?></td>
                    </tr>

                <?php endforeach; ?>
            </table>
        <?php elseif (($category == 'Cubic')) : ?>
            <table border="1">
                <tr>
                    <th>S/No.</th>
                    <th>Length</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th>Volume</th>
                    <th>Comment</th>
                </tr>
                <?php $sn = 1 ?>
                <?php foreach ($measurementSheet as $data) : ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= $data->length ?></td>
                        <td><?= $data->width ?></td>
                        <td><?= $data->height ?></td>
                        <td><?= $data->net_quantity ?></td>
                        <td><?= $data->comment ?></td>
                    </tr>

                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>
</div>
<br>
<br>
<table class="table">
    <tr class="">
        <td>
            <div>
                <!-- Left Column Content -->
                <h6 class="bold">Weights And Measures Officer</h6>
                <br>
                <h6 class="bold">P.O Box <?= $center->address ?></h6>
                <br>
                <h6 class="bold"><?= auth()->user()->username ?></h6>
                <br>
                <br>
                <p class="bold">.........................................................................</p>
                <br>

                <h6 class="bold">Signature</h6>
                <br>
                <h6 class="bold">Date: <?= dateFormatter(date('Y-m-d')) ?></h6>
            </div>
        </td>
        <td>
            <div>
                <h6 class="bold"><?= $customerDetails->name ?></h6>
                <br>
                <h6 class="bold"><?= $customerDetails->postal_address ?></h6>
                <br>
                <br>
                <h6><span class="bold">Name</span>: ....................................................</h6>
                <br>
                <br>
                <p class="bold">.........................................................................</p>
                <br>
                <h6 class="bold">Signature</h6>
                <br>
                <h6 class="bold">Date: <?= dateFormatter(date('Y-m-d')) ?></h6>
            </div>
        </td>

    </tr>
</table>
</div>
<?= $this->endSection() ?>