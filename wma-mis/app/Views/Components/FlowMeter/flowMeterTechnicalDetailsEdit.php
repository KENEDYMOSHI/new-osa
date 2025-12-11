<div class="form-group">
    <label for="my-input">Name Of Oil Company</label>
    <input class="form-control" type="text" name="oilcompany" placeholder="Enter Name Of Oil Company"
        value="<?= $record->oil_company ?>">
    <span class="text-danger"><?= displayError($validation, 'oilcompany') ?></span>
</div>
<div class="form-group">
    <label>Flow Meter Type</label>
    <select name="metertype" class=" form-control">

        <option selected <?= set_select('metertype', $record->flow_meter_type) ?>
            value="<?= $record->flow_meter_type  ?>">
            <?= $record->flow_meter_type  ?></option>
        <?php foreach (showOption([$record->flow_meter_type], $meterTypes) as $type) : ?>
        <option value="<?= $type ?>"><?= $type ?></option>
        <?php endforeach; ?>


    </select>
    <span class="text-danger"><?= displayError($validation, 'metertype') ?></span>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label for="my-input">Model Number</label>
        <input class="form-control" type="text" name="model" placeholder="Enter Model Number"
            value="<?= $record->model_number ?>">
        <span class="text-danger"><?= displayError($validation, 'model') ?></span>
    </div>
    <div class="form-group col-md-6">
        <label for="my-input">Serial Number</label>
        <input class="form-control" type="text" name="serial" placeholder="Enter serial Number"
            value="<?= $record->serial_number ?>">
        <span class="text-danger"><?= displayError($validation, 'serial') ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label for="my-input">Flow Rate</label>
        <input class="form-control" type="text" name="flowrate" placeholder="Enter flowrate"
            value="<?= $record->flow_rate ?>">
        <span class="text-danger"><?= displayError($validation, 'flowrate') ?></span>
    </div>

    <div class="form-group col-md-6">
        <label>Product</label>
        <select name="product" class=" form-control">

            <option selected <?= set_select('metertype', $record->product) ?> value="<?= $record->product  ?>">
                <?= $record->flow_meter_type  ?></option>
            <?php foreach (showOption([$record->product], $products) as $product) : ?>
            <option value="<?= $product ?>"><?= $product ?></option>
            <?php endforeach; ?>
        </select>
        <span class="text-danger"><?= displayError($validation, 'product') ?></span>
    </div>
</div>
<div class="form-group ">
    <label for="my-input">Standard Capacity</label>
    <input class="form-control" type="text" name="capacity" placeholder="Enter Standard capacity Number"
        value="<?= $record->standard_capacity ?>">
    <span class="text-danger"><?= displayError($validation, 'capacity') ?></span>
</div>




<!-- Animated Form -->
<div class="">
    <div class="card card-default">
        <div class="card-header">
            <b> FLOW METER RESULTS</b>
        </div>
        <?= $this->include('Widgets/ResultsEdit'); ?>

    </div>



</div>