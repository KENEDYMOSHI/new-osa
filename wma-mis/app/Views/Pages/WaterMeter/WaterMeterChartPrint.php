<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<style>
    .details{
        padding: 7px;
    }
    .td{
        padding: 20px;
    }
</style>
<div class="" style="font-size:10px;">
    <table border="0"  style="width: 45%; margin-top:41px;">
        <tr>
            <td class="details"><b>Client:</b></td>
            <td class="details"><?= $report[0]->name ?></td>
        </tr>
        <tr>
            <td class="details"><b>Brand Name:</b></td>
            <td class="details"><?= $report[0]->brand ?></td>
        </tr>
        <tr>
            <td class="details"><b>Meter Size:</b></td>
            <td class="details"><?= $report[0]->meter_size ?></td>
        </tr>
        <tr>
            <td class="details"><b>Flow Rate:</b></td>
            <td class="details"><?= $report[0]->flow_rate ?> </td>
        </tr>
        <tr>
            <td class="details"><b>Class:</b></td>
            <td class="details"><?= $report[0]->class ?> </td>
        </tr>
        <tr>
            <td class="details"><b>Testing Center:</b></td>
            <td class="details"><?= $report[0]->lab ?></td>
        </tr>
        <tr>
            <td class="details"><b>Testing Method:</b></td>
            <td class="details"><?= $report[0]->testing_method ?></td>
        </tr>
        <tr>
            <td class="details"><b>Testing Date:</b></td>
            <td class="details"><?= dateFormatter($report[0]->created_at) ?></td>
        </tr>
        <tr>
            <td class="details"><b>Verified By:</b></td>
            <td class="details"><?= $report[0]->verifier ?></td>
        </tr>
        <tr>
            <td class="details"><b>Meters Passed</b></td>
            <td class="details"><?= $passedMeters ?></td>
        </tr>
        <tr>
            <td  class="details"><b>Meters Failed</b></td>
            <td  class="details"><?= $failedMeters ?></td>
        </tr>

    </table>
    <br>

    <?php $index = 1;  ?>
    <table class="table table-sm table-bordered"  style="font-size: 10px;">
        <thead class="thead-dark">
            <tr>
                <th>No.</th>
                <th>Meter Serial No.</th>
                <th>Initial Reading</th>
                <th>Final Reading</th>
                <th>Indicated Volume Vi(L)</th>
                <th>Actual Volume Va(L)</th>
                <th>% Error</th>
                <th>Decision</th>
                <th>Seal No</th>
                <th>Rejection No</th>
            </tr>

        </thead>

        <tbody>
            <?php foreach ($report as $meter) : ?>
                <tr>
                    <td class="td"><?= $index++ ?></td>
                    <td class="td"><?= $meter->serial_number ?></td>
                    <td class="td"><?= $meter->initial_reading ?></td>
                    <td class="td"><?= $meter->final_reading ?></td>
                    <td class="td"><?= $meter->indicated_volume ?></td>
                    <td class="td"><?= $meter->actual_volume ?></td>
                    <td class="td"><?= $meter->error ?></td>
                    <td class="td"><?= $meter->decision ?></td>
                    <td class="td"><?= $meter->decision === 'PASS' ? $meter->tag : '-' ?></td>
                    <td class="td"><?= $meter->decision === 'FAIL' ? $meter->tag : '-' ?></td>

                </tr>
            <?php endforeach; ?>


        </tbody>



    </table>



    <br>
    <!-- <table class="table text-bold">
        <tr>
            <td>
                <h4><b>Verifier</b></h4>
                <?= $report[0]->first_name . ' ' . $report[0]->last_name  ?><br>

                Weights & Measures Officer <br>
                Weights & Measures Agency

            </td>

            
        </tr>
    </table> -->
    <table class="table">
        <tr class="">
            <td>
                <div>
                    <!-- Left Column Content -->
                    <h6 class="bold">Verifier </h6>
                    <br>
                    <h6 class="bold"><?=  $report[0]->verifier  ?></h6>
                    <br>
                    <h6 class="bold">Weights And Measures Officer</h6>
                    <br>
                    <h6 class="bold">P.O Box <?= wmaCenter($report[0]->collection_center)->address ?></h6>

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
                    <h6 class="bold">Customer</h6>
                    <br>
                    <h6 class="bold"><?= $report[0]->name ?></h6>
                    <br>
                    <h6 class="bold"><?= !(isset($report[0]->postal_address)) ? '' : $report[0]->postal_address ?></h6>
                    <br>
                    <!-- <br>
                <h6><span class="bold">Name</span>: ....................................................</h6>
                <br>
                <br> -->
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
<script>
        // Function to close the page
        function closePage() {
            window.close();
        }

        // Attach the onafterprint event listener
        window.onafterprint = closePage;

        // Use the window.onload event to execute the print function when the page is fully loaded
        window.onload = function () {
            window.print(); // This will trigger the browser's print dialog
        };
    </script>
<?= $this->endSection(); ?>