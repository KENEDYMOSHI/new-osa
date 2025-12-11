<div class="card card-cyan">
    <div class="card-header">
        Types Of Scales
    </div>
    <div class="card-body">

        <div id="accordion" role="tablist">
            <div class="card card">
                <div class="card-header" role="tab" id="headingOne">
                    <p class="mb-0">
                        <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <b><i class="far fa-balance-scale"></i> Commercial/Trade Scales </b>
                        </a>
                    </p>
                </div>

                <div id="collapseOne" class="collapse " role="tabpanel" aria-labelledby="headingOne"
                    data-parent="#accordion">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Type of Scale</label>
                            <select name="scaletype" id="" class="form-control">
                                <option selected <?= set_select('scaletype', $record->trade_scale_type) ?>
                                    value="<?= $record->trade_scale_type ?>">
                                    <?= $record->trade_scale_type ?></option>

                                <?php foreach (showOption([$record->trade_scale_type], $scales) as $scale) : ?>
                                <option value="<?= $scale ?>"><?= $scale ?></option>
                                <?php endforeach; ?>


                            </select>
                            <span class="text-danger"><?= displayError($validation, 'tradeScaleType') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Number of Scales</label>
                            <input id="tradeScaleQuantity" class="form-control" type="number" name="tradeScaleQuantity"
                                value="<?= $record->trade_scale_quantity ?>">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Scale Capacity</label>
                            <input id="my-input" class="form-control" type="number" name="tradeScaleActualCapacity"
                                value="<?= $record->trade_scale_capacity ?>">
                        </div>

                        <div class="form-group">
                            <label>Denomination Capacity</label>
                            <select name="tradeScaleDenomination" id="tradeScaleCapacity" class="form-control"
                                value="<?= set_value('tradeScaleCapacity') ?>">

                                <?php foreach ($denominations as $denomination) : ?>
                                <option <?= set_select('tradeScaleCapacity', $denomination['capacity']) ?>
                                    value="<?= $denomination['price'] ?>">
                                    <?= $denomination['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                            <span class="text-danger"><?= displayError($validation, 'scalecapacity') ?></span>
                        </div>

                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input class="form-control" id="tradeScalesAmount" type="text" name="tradeScalesAmount"
                                value="<?= $record->trade_scale_amount ?>">
                        </div>
                    </div>
                </div>
            </div>
            <!-- ================Precious Stones Scales================ -->
            <div class="card">
                <div class="card-header" role="tab" id="headingTwo">
                    <p class="mb-0">
                        <a class="collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false"
                            aria-controls="collapseTwo">
                            <b><i class="far fa-gem"></i> Precious Stones Scales </b>
                        </a>
                    </p>
                </div>
                <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo"
                    data-parent="#accordion">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Type of Scale</label>
                            <select name="preciousStoneScaleType" id="" class="form-control"
                                value="<?= set_value('preciousStoneScaleType') ?>">
                                <option selected disabled>Select a scale</option>
                                <?php foreach ($scales as $scale) : ?>
                                <option <?= set_select('preciousStoneScaleType', $scale) ?> value="<?= $scale ?>">
                                    <?= $scale ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'preciousStoneScaleType') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Number of Scales</label>
                            <input id="preciousStoneScaleQuantity" class="form-control" type="number"
                                name="preciousStoneScaleQuantity">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Scale Capacity</label>
                            <input id="my-input" class="form-control" type="number" name="stoneScaleActualCapacity">
                        </div>

                        <div class="form-group">
                            <label>Denomination Capacity For Scales </label>
                            <select name="preciousStoneScaleDenomination" id="preciousScaleCapacity"
                                class="form-control">
                                <option selected disabled>Denomination Capacity</option>
                                <?php foreach ($denominations as $denomination) : ?>
                                <option <?= set_select('preciousStoneScaleCapacity', $denomination['capacity']) ?>
                                    value="<?= $denomination['price'] ?>">
                                    <?= $denomination['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'preciousScaleCapacity') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input class="form-control" id="preciousScaleAmount" type="text" name="preciousScaleAmount"
                                value="<?= $record->stone_scale_amount ?>">
                        </div>

                    </div>
                </div>
            </div>
            <!-- ====================================================== -->
            <div class="card">
                <div class="card-header" role="tab" id="headingThree">
                    <p class="mb-0">
                        <a class="collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false"
                            aria-controls="collapseThree">
                            <b> <i class="far fa-weight"></i> Laboratory Pharmaceuticals Scales</b>
                        </a>
                    </p>
                </div>
                <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree"
                    data-parent="#accordion">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Type of Scale</label>
                            <select name="labScaletype" id="" class="form-control"
                                value="<?= set_value('labScaletype') ?>">
                                <option selected disabled>Select a scale</option>
                                <?php foreach ($scales as $scale) : ?>
                                <option <?= set_select('labScaletype', $scale) ?> value="<?= $scale ?>"><?= $scale ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'labScaletype') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Number of Scales</label>
                            <input id="labScaleQuantity" class="form-control" type="number" name="labScaleQuantity">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Scale Capacity</label>
                            <input id="my-input" class="form-control" type="number" name="labScaleActualCapacity">
                        </div>

                        <div class="form-group">
                            <label>Denomination Capacity For Scales </label>
                            <select name="labScaleDenomination" id="labScaleCapacity" class="form-control">
                                <option selected disabled>Denomination Capacity</option>
                                <?php foreach ($denominations as $denomination) : ?>
                                <option <?= set_select('labScaleCapacity', $denomination['capacity']) ?>
                                    value="<?= $denomination['price'] ?>">
                                    <?= $denomination['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'labScaleCapacity') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input class="form-control" id="labScaleAmount" type="text" name="labScaleAmount"
                                value="<?= $record->lab_scale_amount ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>




<!-- =====================Weights================================== -->

<div class="card card-teal">
    <div class="card-header">
        Weights For Scales
    </div>
    <div class="card-body">
        <div id="accordion" role="tablist">
            <div class="card card">
                <div class="card-header" role="tab" id="headingOne">
                    <p class="mb-0">
                        <a data-toggle="collapse" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            <b><i class="far fa-weight-hanging"></i> Weights Lower Class(M3)</b>
                        </a>
                    </p>
                </div>

                <div id="collapse1" class="collapse " role="tabpanel" aria-labelledby="headingOne"
                    data-parent="#accordion">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Weights For Scales </label>
                            <select name="lowerClassWeights" id="lowerClassWeights" class="form-control"
                                value="<?= set_value('lowerClassWeights') ?>">
                                <option selected disabled>Scales Weights</option>
                                <option value="0">No Weights</option>
                                <?php foreach ($loweClassWeights as $lowerClass) : ?>
                                <option <?= set_select('lowerClassWeights', $lowerClass['capacity']) ?>
                                    value="<?= $lowerClass['price'] ?>">
                                    <?= $lowerClass['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="my-input">Sets of weights</label>
                            <input id="lowerClassWeightSets" class="form-control" type="number"
                                name="lowerClassCapacity" value="<?= $record->lower_class_capacity ?>">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input class="form-control" id="lowerClassAmount" type="text" name="lowerClassAmount"
                                value="<?= $record->lower_class_amount ?>">
                        </div>


                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" role="tab" id="headingTwo">
                    <p class="mb-0">
                        <a class="collapsed" data-toggle="collapse" href="#collapse2" aria-expanded="true"
                            aria-controls="collapse2">
                            <b><i class="far fa-weight-hanging"></i> Weights Higher Class(M2)</b>
                        </a>
                    </p>
                </div>
                <div id="collapse2" class="collapse" role="tabpanel" aria-labelledby="headingTwo"
                    data-parent="#accordion">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Weights For Scales </label>
                            <select name="higherClassWeights" id="higherClassWeights" class="form-control"
                                value="<?= set_value('higherClassWeights') ?>">
                                <option selected disabled>Scales Weights</option>
                                <option value="0">No Weights</option>
                                <?php foreach ($higherClassWeights as $higherClass) : ?>
                                <option <?= set_select('higherClassWeights', $higherClass['capacity']) ?>
                                    value="<?= $higherClass['price'] ?>">
                                    <?= $higherClass['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>


                        </div>

                        <div class="form-group">
                            <label for="my-input">Sets of weights</label>
                            <input id="higherClassWeightSets" class="form-control" type="number"
                                name="higherClassCapacity" value="<?= $record->higher_class_capacity ?>">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input id="higherClassAmount" class="form-control" type="text" name="higherClassAmount"
                                value="<?= $record->higher_class_amount ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" role="tab" id="headingThree">
                    <p class="mb-0">
                        <a class="collapsed" data-toggle="collapse" href="#collapse3" aria-expanded="false"
                            aria-controls="collapse3">
                            <b><i class="far fa-weight-hanging"></i> Metric Carat Weight</b>
                        </a>
                    </p>
                </div>
                <div id="collapse3" class="collapse" role="tabpanel" aria-labelledby="headingThree"
                    data-parent="#accordion">
                    <div class="card-body">

                        <div class="form-group">
                            <label>Weights For Scales </label>
                            <select name="metricWeights" id="metricWeights" class="form-control"
                                value="<?= set_value('metricWeights') ?>">
                                <option selected disabled>Scales Weights</option>
                                <option value="0">No Weights</option>
                                <?php foreach ($metricScale as $metric) : ?>
                                <option <?= set_select('metricWeights', $metric['capacity']) ?>
                                    value="<?= $metric['price'] ?>">
                                    <?= $metric['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="my-input">Sets of weights</label>
                            <input id="metricWeightsSets" class="form-control" type="number" name="metricCapacity">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input id="metricAmount" class="form-control" type="text" name="metricAmount">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- =============================Capacity Measuring===================================== -->

<div class="card card-info">
    <div class="card-header">
        Capacity Measuring
    </div>
    <div class="card-body">
        <div id="accordion" role="tablist">
            <div class="card card">
                <div class="card-header" role="tab" id="headingOne">
                    <p class="mb-0">
                        <a data-toggle="collapse" href="#collapse4" aria-expanded="true" aria-controls="collapse4">
                            <b><i class="far fa-blender"> </i>Capacity Measure for Customer</b>
                        </a>
                    </p>
                </div>

                <div id="collapse4" class="collapse " role="tabpanel" aria-labelledby="headingOne"
                    data-parent="#accordion">
                    <div class="card-body">

                        <div class="form-group">
                            <label>Koroboi</label>
                            <select name="customerCapacity" id="customerCapacity" class="form-control"
                                value="<?= set_value('customerCapacity') ?>">
                                <option selected disabled>Select Koroboi</option>
                                <option value="0">No Koroboi</option>
                                <?php foreach ($capacityForCustomer as $koroboi) : ?>
                                <option <?= set_select('customerCapacity', $koroboi['capacity']) ?>
                                    value="<?= $koroboi['price'] ?>">
                                    <?= $koroboi['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'scalecapacity') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Number Of Measuring Capacity</label>
                            <input id="my-input" class="form-control" type="number" name="customerCapacityQuantity">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input id="customerCapacityAmount" class="form-control" type="text"
                                name="customerCapacityAmount">
                        </div>


                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" role="tab" id="headingTwo">
                    <p class="mb-0">
                        <a class="collapsed" data-toggle="collapse" href="#collapse5" aria-expanded="false"
                            aria-controls="collapse5">
                            <b><i class="far fa-blender"> </i>Capacity Measure For Verification</b>
                        </a>
                    </p>
                </div>
                <div id="collapse5" class="collapse" role="tabpanel" aria-labelledby="headingTwo"
                    data-parent="#accordion">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Koroboi</label>
                            <select name="verificationCapacity" id="verificationCapacity" class="form-control"
                                value="<?= set_value('koroboi') ?>">
                                <option selected disabled>Select Koroboi</option>
                                <option value="0">No Koroboi</option>
                                <?php foreach ($capacityForVerification as $koroboi) : ?>
                                <option <?= set_select('verificationCapacity', $koroboi['capacity']) ?>
                                    value="<?= $koroboi['price'] ?>">
                                    <?= $koroboi['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'scalecapacity') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="my-input">Number Of Measuring Capacity</label>
                            <input id="my-input" class="form-control" type="number" name="verificationCapacityQuantity">
                        </div>
                        <div class="form-group">
                            <label for="my-input">Amount</label>
                            <input id="verificationCapacityAmount" class="form-control" type="text"
                                name="verificationCapacityAmount">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>





<!-- Animated Form -->
<div class="scale-result">
    <div class="card card-default">
        <div class="card-header">
            SCALE RESULTS
        </div>
        <?= $this->include('Widgets/Results.php'); ?>
    </div>



</div>