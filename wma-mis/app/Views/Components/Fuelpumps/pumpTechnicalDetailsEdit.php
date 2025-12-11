  <div class="form-group">
      <label for="my-input">Name Of Petrol Station</label>
      <input class="form-control" type="text" name="petrolstation" placeholder="Name Of Petrol  Station"
          value="<?= $record->petrol_station ?>">
      <span class="text-danger"><?= displayError($validation, 'petrolstation') ?></span>
  </div>
  <div class="form-group">
      <label>Product</label>
      <select name="product" class=" form-control">
          <option selected <?= set_select('pumptype', $record->product) ?> value="<?= $record->product ?>">
              <?= $record->product ?></option>

          <?php foreach (showOption([$record->product], $products) as $product) : ?>
          <option value="<?= $product ?>"><?= $product ?></option>
          <?php endforeach; ?>
      </select>
      <span class="text-danger"><?= displayError($validation, 'product') ?></span>
  </div>
  <div class="form-group">
      <label>Type of Fuel Pumps</label>
      <select name="pumptype" id="" class="form-control">
          <option selected <?= set_select('pumptype', $record->pump_type) ?> value="<?= $record->pump_type ?>">
              <?= $record->pump_type ?></option>

          <?php foreach (showOption([$record->pump_type], $pumps) as $pump) : ?>
          <option value="<?= $pump ?>"><?= $pump ?></option>
          <?php endforeach; ?>
      </select>
      <span class="text-danger"><?= displayError($validation, 'pumptype') ?></span>

  </div>
  <div class="form-group">
      <label for="my-input">Capacity</label>
      <input class="form-control" type="number" name="pumpcapacity" placeholder="Pump Capacity In Liters"
          value="<?= $record->capacity ?>">
      <span class="text-danger"><?= displayError($validation, 'pumpcapacity') ?></span>
  </div>


  <div class="form-group">
      <label for="my-input">Number Of Fuel Nozzles</label>
      <input class="form-control" type="number" name="numberofdispensers" placeholder="Number Of Fuel Dispensers"
          value="<?= $record->dispensers ?>">
      <span class="text-danger"><?= displayError($validation, 'numberofdispensers') ?></span>
  </div>

  <!-- Animated Form -->
  <div class="">
      <div class="card card-default">
          <div class="card-header">
              <b> FUEL PUMP RESULTS</b>
          </div>
          <?= $this->include('Widgets/ResultsWithReportEdit'); ?>
      </div>



  </div>