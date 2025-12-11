<input id="customerHashId" class="form-control" type="text">

<div class="form-group">
    <label for="my-select">Activity</label>
    <select id="activity" class="form-control" name="activity">
        <option disabled selected>Select Activity</option>
        <option value="On Verification">On Verification</option>
        <option value="Reverification">Reverification</option>
        <option value="Inspection">Inspection</option>

    </select>
</div>

<div class="form-group">
    <label>Type of Scale</label>
    <select id="scaleType" class="form-control">
        <option selected disabled>Select a scale</option>
        <?php foreach ($scales as $scale) : ?>
        <option <?= set_select('tradeScaleType', $scale) ?> value="<?= $scale ?>">
            <?= $scale ?>
        </option>
        <?php endforeach; ?>
    </select>
    <span class="text-danger"><?= displayError($validation, 'tradeScaleType') ?></span>
</div>

<div class="form-group">
    <label for="my-select">Scale Category</label>
    <select id="scaleCategory" class="form-control" name="">
        <option value="Trade Scale">Trade Scale</option>
        <option value="Precious Stone Scale">Precious Stone Scale</option>
        <option value="Laboratory Scale">Laboratory Scale</option>
    </select>
</div>

<div class="form-group">
    <label for="my-select">Capacity</label>
    <select id="scaleCapacity" class="form-control" name="">
        <option disabled selected>Select Capacity</option>
        <option value="0.5">0.5 Kg</option>
        <option value="1">1 Kg</option>
        <option value="2">2 Kg</option>
        <option value="3">3 Kg</option>
        <option value="4">4 Kg</option>
        <option value="5-10">5-10 Kg</option>
        <option value="11-12">11-20 Kg</option>
        <option value="21-25">21-25 Kg</option>
        <option value="26-30">26-30 Kg</option>
        <option value="31-50">31-50 Kg</option>

    </select>
</div>


<div class="row">
    <div class="form-group col-md-6">
        <label for="my-input">Error</label>
        <input id="scaleError" class="form-control" type="number" name="scaleError">
    </div>
    <div class="form-group col-md-6">
        <label for="my-input">Sensitivity</label>
        <input id="scaleSensitivity" class="form-control" type="number" name="">
    </div>
</div>
<button class="btn btn-primary" id="checkStatus">Check</button>
<div class="form-group">
    <label for="my-input">Status</label>
    <input id="scaleStatus" class="form-control" type="text" name="" readonly>
</div>

<div class="form-group">
    <label>Denomination Capacity</label>
    <select name="" id="scaleDenomination" class="form-control" value="<?= set_value('tradeScaleCapacity') ?>">
        <option selected disabled>Denomination Capacity</option>
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
    <input id="scaleAmount" class="form-control" type="text" name="scaleAmount">
</div>