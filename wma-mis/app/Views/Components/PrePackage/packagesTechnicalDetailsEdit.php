 <div class="form-group">
     <label>Industry Name </label>
     <input name="industryname" type="text" class="form-control" id="" placeholder="Enter industry Name"
         value="<?= $record->industry_name ?>">
     <span class="text-danger"><?= displayError($validation, 'industryname') ?></span>

 </div>
 <div class="form-group">
     <label>Product</label>
     <input name="product" type="text" class="form-control" id="" placeholder="Enter  product Name"
         value="<?= $record->product ?>">
     <span class="text-danger"><?= displayError($validation, 'product') ?></span>


 </div>
 <div class="card">
     <div class="card-header">
         <b>PACKAGE RESULTS</b>
     </div>

     <?= $this->include('Widgets/ResultsNoStickerEdit'); ?>
 </div>