  <div class="form-group">
      <input id="customerHash" class="form-control" type="text" name="customer_hash">
  </div>
  <div class="form-group">
      <label for="my-input">Name Of Oil Company</label>
      <input class="form-control" type="text" name="oilcompany" placeholder="Enter Name Of Oil Company"
          value="<?= set_value('oilcompany') ?>">
      <span class="text-danger"><?= displayError($validation, 'oilcompany') ?></span>
  </div>
  <div class="form-group">
      <label>Flow Meter Type</label>
      <select name="metertype" class=" form-control">
          <option selected disabled>Select meterType</option>
          <?php foreach ($meterTypes as $meterType) : ?>
          <option <?= set_select('metertype', $meterType) ?> value="<?= $meterType ?>"><?= $meterType ?></option>
          <?php endforeach; ?>
      </select>
      <span class="text-danger"><?= displayError($validation, 'metertype') ?></span>
  </div>

  <div class="row">
      <div class="form-group col-md-6">
          <label for="my-input">Model Number</label>
          <input class="form-control" type="text" name="model" placeholder="Enter Model Number"
              value="<?= set_value('model') ?>">
          <span class="text-danger"><?= displayError($validation, 'model') ?></span>
      </div>
      <div class="form-group col-md-6">
          <label for="my-input">Serial Number</label>
          <input class="form-control" type="text" name="serial" placeholder="Enter serial Number"
              value="<?= set_value('serial') ?>">
          <span class="text-danger"><?= displayError($validation, 'serial') ?></span>
      </div>
  </div>

  <div class="row">
      <div class="form-group col-md-6">
          <label for="my-input">Flow Rate</label>
          <input class="form-control" type="text" name="flowrate" placeholder="Enter flowrate"
              value="<?= set_value('flowrate') ?>">
          <span class="text-danger"><?= displayError($validation, 'flowrate') ?></span>
      </div>

      <div class="form-group col-md-6">
          <label>Product</label>
          <select name="product" class=" form-control">
              <option selected disabled>Select product</option>
              <?php foreach ($products as $product) : ?>
              <option <?= set_select('product', $product) ?> value="<?= $product ?>"><?= $product ?></option>
              <?php endforeach; ?>
          </select>
          <span class="text-danger"><?= displayError($validation, 'product') ?></span>
      </div>
  </div>
  <div class="form-group ">
      <label for="my-input">Standard Capacity</label>
      <input class="form-control" type="text" name="capacity" placeholder="Enter Standard capacity Number"
          value="<?= set_value('capacity') ?>">
      <span class="text-danger"><?= displayError($validation, 'capacity') ?></span>
  </div>




  <!-- Animated Form -->
  <div class="">
      <div class="card card-default">
          <div class="card-header">
              <b> FLOW METER RESULTS</b>
          </div>
          <?= $this->include('Widgets/Results'); ?>

      </div>



  </div>