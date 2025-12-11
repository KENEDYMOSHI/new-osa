  <div class="form-group">
      <input id="customerHash" class="form-control" type="text" name="customer_hash">
  </div>
  <div class="form-group">
      <label for="my-input">Name Of Petrol Station</label>
      <input class="form-control" type="text" name="petrolstation" placeholder="Name Of Petrol  Station"
          value="<?= set_value('petrolstation') ?>">
      <span class="text-danger"><?= displayError($validation, 'petrolstation') ?></span>
  </div>
  <div class="form-group">
      <label>Product</label>
      <select name="product" class=" form-control">
          <option selected disabled>Select Product</option>
          <?php foreach ($products as $product) : ?>
          <option <?= set_select('product', $product) ?> value="<?= $product ?>"><?= $product ?></option>
          <?php endforeach; ?>
      </select>
      <span class="text-danger"><?= displayError($validation, 'product') ?></span>
  </div>
  <div class="form-group">
      <label>Type of Fuel Pumps</label>
      <select name="pumptype" id="" class="form-control">
          <option selected disabled>Select a Pump</option>
          <?php foreach ($pumps as $pump) : ?>
          <option <?= set_select('pumptype', $pump) ?> value="<?= $pump ?>"><?= $pump ?></option>
          <?php endforeach; ?>
      </select>
      <span class="text-danger"><?= displayError($validation, 'pumptype') ?></span>

  </div>
  <div class="form-group">
      <label for="my-input">Capacity</label>
      <input class="form-control" type="number" name="pumpcapacity" placeholder="Pump Capacity In Liters"
          value="<?= set_value('pumpcapacity') ?>">
      <span class="text-danger"><?= displayError($validation, 'pumpcapacity') ?></span>
  </div>


  <div class="form-group">
      <label for="my-input">Number Of Fuel Nozzles</label>
      <input class="form-control" type="number" name="numberofdispensers" placeholder="Number Of Fuel Nozzles"
          value="<?= set_value('numberofdispensers') ?>">
      <span class="text-danger"><?= displayError($validation, 'numberofdispensers') ?></span>
  </div>



  <!-- Animated Form -->
  <div class="">
      <div class="card card-default">
          <div class="card-header">
              <b> FUEL PUMP RESULTS</b>
          </div>
          <?= $this->include('Widgets/ResultsWithReport'); ?>

      </div>



  </div>