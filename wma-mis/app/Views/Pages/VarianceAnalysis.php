<?php

use App\Libraries\ArrayLibrary;

?>
<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $page['heading'] ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">



        <h4 class="text-center" style="text-transform: uppercase;">VARIANCE ANALYSIS <?=$monthAndYear ?></h4>
        <div class="card">


            <div class="card-header">
                <form action="<?=base_url('filterEstimates')?>" method="POST">
                <?= tokenField() ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="">Date</label>
                              <input type="date"
                                class="form-control" name="date" id="" value="<?= set_value('date') ?>" aria-describedby="helpId" placeholder="Date">
                              
                            </div>
                        </div>
                        <div class="col-md-2"><button type="submit" class="btn btn-primary btn-sm mt-4">Filter</button></div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover " id="variance">

                    <thead class="thead-dark">
                        <tr>
                            <th>Region</th>
                            <th>Collection For <?= $currentDate ?></th>
                            <th>Accumulated Actual Collection - A </th>
                            <th>Estimates <?=$monthAndYear ?> - B</th>
                            <th>Variance (TZS)</th>
                            <th>Variance (%) (A-B)/B</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($collections as $collection) : ?>
                            <?php
                            $today = number_format($collection->today ?? 0);
                            $accumulated = number_format($collection->accumulated ?? 0);
                            $estimate = number_format($collection->estimate ?? 0);
                            $variance = number_format($collection->variance ?? 0);
                            $variancePercentage = number_format($collection->variancePercentage ?? 0);

                            ?>
                            <tr>
                                <td><?= $collection->region ?></td>
                                <td><?= $today ?></td>
                                <td><?= $accumulated ?></td>
                                <td><?= $estimate ?></td>
                                <td><?= $variance ?></td>
                                <td><?= $variancePercentage ?> %</td>

                            </tr>
                          

                        <?php endforeach; ?>
                        <?php
                        $totalToday = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->today)->get();
                        $totalAccumulated = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->accumulated)->get();
                        $totalEstimate = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->estimate)->get();
                        $totalVariance = (new ArrayLibrary($collections))->reduce(fn ($x, $y) => $x + $y->variance)->get();
                        $totalVariancePercentage = ($totalEstimate != 0) ? ($totalVariance / $totalEstimate) * 100 : 0;

                        ?>
                        <tr>
                            <th><b>TOTAL</b></th>
                            <th><?= number_format($totalToday) ?></th>
                            <th><?= number_format($totalAccumulated) ?></th>
                            <th><?= number_format($totalEstimate) ?></th>
                            <th><?= number_format($totalVariance) ?></th>
                            <th> <?= number_format($totalVariancePercentage) ?>%</th>
                        </tr>

                    </tbody>
                    
                </table>
            </div>
            <div class="card-footer">
                <a href="<?= $link ?>" target="_blank" type="button" class="btn btn-sm btn-primary"><i class="far fa-download" aria-hidden="true"></i> Download</a>
            </div>
        </div>



    </div><!-- /.container-fluid -->

    <script>
         $(document).ready(function() {
        $('#variance').DataTable({
            dom: '<"top"lBfrtip>',
            buttons: [
                'excel', 'pdf' 
            ],
            lengthMenu: [35]
        });
    });
    </script>

</section>
<?= $this->endSection(); ?>