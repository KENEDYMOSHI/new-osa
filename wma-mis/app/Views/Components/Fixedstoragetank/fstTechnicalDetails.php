<div class="form-group">
    <input id="customerHash" class="form-control" type="text" name="customer_hash">
</div>
<div class="form-group">
    <label>Name Of Filling Station</label>
    <input name="fillingstation" type="text" class="form-control" id="" placeholder="Enter  Filling Station"
        value="<?= set_value('fillingstation') ?>">
    <span class="text-danger"><?= displayError($validation, 'fillingstation') ?></span>


</div>
<div class="row">
    <div class="form-group col-md-9">
        <label> Number Of Tanks </label>
        <input name="numberoftanks" type="number" class="form-control" id="" placeholder="Enter Number Of Tanks"
            value="<?= set_value('numberoftanks') ?>">

        <span class="text-danger"><?= displayError($validation, 'numberoftanks') ?></span>

    </div>
    <div class="button-group col-md-3" role="group">
        <label for="">Add Another</label>
        <div>
            <button type="button" class="btn btn-primary" id="addBst"><i class="fal fa-plus-circle"></i></button>
            <button type="button" class="btn btn-danger" id="removeBst"><i class="fal fa-minus-circle"></i></button>
        </div>
    </div>
</div>




<div class="form-group">
    <label>Tank Capacity</label>
    <input name="tankcapacity[]" type="number" class="form-control" id="" placeholder="Enter  Tank Capacity ">

</div>
<div class="form-group">
    <label>Product</label>
    <input name="product[]" type="text" class="form-control" id="" placeholder="Enter  Product ">




</div>


<div class="bst"></div>
<div class="form-group">
    <label for="my-textarea">Remark</label>
    <textarea id="my-textarea" class="form-control" name="remark" rows="3">
    <?= set_value('remark') ?>
    </textarea>
</div>



<div class="card">
    <div class="card-header">
        <b>FIXED STORAGE TANK RESULTS</b>
    </div>
    <?= $this->include('Widgets/ResultsWithCalibration'); ?>
</div>