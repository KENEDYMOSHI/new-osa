<!-- ================================ -->









<!-- =====================Weights================================== -->
<div class="form-group">

    <input id="customerHash2" class="form-control" type="text" name="customer_hash" hidden>
    <input id="scaleId" class="form-control" type="text" name="scaleId">
</div>

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
                            <label for="my-input">Sets of weights</label>
                            <input id="lowerClassWeightSets" class="form-control" type="number"
                                name="lowerClassCapacity">
                        </div>
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
                            <label for="my-input">Amount</label>
                            <input class="form-control" id="lowerClassAmount" type="text" name="lowerClassAmount">
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
                            <label for="my-input">Sets of weights</label>
                            <input id="higherClassWeightSets" class="form-control" type="number"
                                name="higherClassCapacity">
                        </div>
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
                            <label for="my-input">Amount</label>
                            <input id="higherClassAmount" class="form-control" type="text" name="higherClassAmount">
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
                            <label for="my-input">Sets of weights</label>
                            <input id="metricWeightsSets" class="form-control" type="number" name="metricSets">
                        </div>

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
                            <label for="my-input">Number Of Measuring Capacity</label>
                            <input id="customerCapacityQuantity" class="form-control" type="number"
                                name="customerCapacityQuantity">
                        </div>

                        <div class="form-group">
                            <label>Koroboi</label>
                            <select name="customerCapacity" id="customerCapacity" class="form-control"
                                value="<?= set_value('customerCapacity') ?>">
                                <option selected disabled>Select Koroboi</option>
                                <option value="0">No Koroboi</option>
                                <?php foreach ($korobois as $koroboi) : ?>
                                <option <?= set_select('customerCapacity', $koroboi['capacity']) ?>
                                    value="<?= $koroboi['price'] ?>">
                                    <?= $koroboi['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'scalecapacity') ?></span>
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
                            <label for="my-input">Number Of Measuring Capacity</label>
                            <input id="verificationCapacityQuantity" class="form-control" type="number"
                                name="verificationCapacityQuantity">
                        </div>
                        <div class="form-group">
                            <label>Koroboi</label>
                            <select name="verificationCapacity" id="verificationCapacity" class="form-control"
                                value="<?= set_value('koroboi') ?>">
                                <option selected disabled>Select Koroboi</option>
                                <option value="0">No Koroboi</option>
                                <?php foreach ($korobois as $koroboi) : ?>
                                <option <?= set_select('verificationCapacity', $koroboi['capacity']) ?>
                                    value="<?= $koroboi['price'] ?>">
                                    <?= $koroboi['capacity'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= displayError($validation, 'scalecapacity') ?></span>
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

<script>

</script>