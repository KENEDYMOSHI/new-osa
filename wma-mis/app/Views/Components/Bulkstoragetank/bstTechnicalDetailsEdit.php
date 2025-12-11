<div class="form-group">
    <label>Name Of Filling Station</label>
    <input name="fillingstation" type="text" class="form-control" id="" placeholder="Enter  Filling Station"
        value="<?= $record->filling_station ?>">
    <span class="text-danger"><?= displayError($validation, 'fillingstation') ?></span>


</div>
<div class="form-group">
    <label> Number Of Tanks </label>
    <input name="numberoftanks" type="number" class="form-control" id="" placeholder="Enter Number Of Tanks"
        value="<?= $record->number_of_tanks ?>">
    <span class="text-danger"><?= displayError($validation, 'numberoftanks') ?></span>
</div>


<div class="form-group">
    <label>Tank Capacity</label>
    <input name="tankcapacity[]" type="text" class="form-control" id="" placeholder="Enter  Tank Capacity "
        value="<?= $record->capacity ?>">


    <span class="text-danger"><?= displayError($validation, 'tankcapacity') ?></span>


</div>
<div class="form-group">
    <label>Product</label>
    <input name="product[]" type="text" class="form-control" id="" placeholder="Enter  Product "
        value="<?= $record->product ?>">


    <span class="text-danger"><?= displayError($validation, 'tankcapacity') ?></span>


</div>
<div class="form-group">
    <label for="my-textarea">Remark</label>
    <textarea id="my-textarea" class="form-control" name="remark" rows="3">
    <?= $record->remark ?>
    </textarea>
</div>



<div class="card">
    <div class="card-header">
        <b>BULK STORAGE TANK RESULTS</b>
    </div>
    <?= $this->include('Widgets/ResultsWithCalibrationEdit'); ?>
</div>