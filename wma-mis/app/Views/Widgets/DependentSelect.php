<div class="form-group col-md-6">
    <label for="my-select">City Or Region</label>
    <select id="region" name="region" class="form-control select2bs4" name="city">
        <?php foreach (renderRegions() as $region) : ?>
            <option value="<?= $region['region'] ?>"><?= $region['region'] ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group col-md-6">
    <label for="my-select">District</label>
    <select id="district" name="district" class="form-control select2bs4" name="district">
        <?php foreach (renderDistricts() as $district) : ?>
            <option value="<?= $district['district'] ?>"><?= $district['district'] ?></option>
        <?php endforeach; ?>
    </select>
</div>