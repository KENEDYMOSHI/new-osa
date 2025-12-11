 <div class="form-group">
     <input id="customerHash" class="form-control" type="text" name="customer_hash">
 </div>
 <div class="form-group">
     <label>Industry Name </label>
     <input name="industryname" type="text" class="form-control" id="" placeholder="Enter industry Name"
         value="<?= set_value('industryname') ?>">
     <span class="text-danger"><?= displayError($validation, 'industryname') ?></span>

 </div>
 <div class="form-group">
     <label>Product</label>
     <input name="product" type="text" class="form-control" id="" placeholder="Enter  product Name"
         value="<?= set_value('product') ?>">
     <span class="text-danger"><?= displayError($validation, 'product') ?></span>


 </div>


 <div class="card">
     <div class="card-header">
         <b>PACKAGE RESULTS</b>
     </div>

     <?= $this->include('Widgets/ResultsNoSticker'); ?>
 </div>