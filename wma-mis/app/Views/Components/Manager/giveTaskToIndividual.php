<div class="card card-olive ">
    <div class="card-header">
        <h3 class="card-title">Assign task to an individual</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
            </button>

        </div>
    </div>
    <div class="card-body">
        <?= form_open() ?>
        <div class="row">
            <div class="form-group col-md-12">
                <label>Activity </label>

                <select name="activity" class="form-control select2bs4 ">
                    <option selected=" selected" disabled>Select a Activity</option>
                    <?php foreach ($activities as $activity) : ?>
                    <option <?= set_select('activity', $activity) ?> value="<?= $activity ?>"><?= $activity ?></option>
                    <?php endforeach; ?>
                </select>


                <span class="text-danger"><?= displayError($validation, 'activity') ?></span>
            </div>


            <div class="form-group col-md-3">
                <label>Select An Officer</label>
                <select name="officer" class="form-control select2bs4" style="width: 100%;">
                    <option selected=" selected" disabled>Select Officer</option>


                    <?php foreach ($officers as $officer) : ?>
                    <option value="<?= $officer->unique_id ?>"><?= $officer->first_name . ' ' . $officer->last_name ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger"><?= displayError($validation, 'officer') ?></span>
            </div>
            <div class="form-group col-md-3">
                <label>Region</label>
                <select name="region" class="form-control select2bs4 " name="region">
                    <option selected=" selected" disabled>Select a Region</option>
                    <?php foreach ($regions as $region) : ?>
                    <option <?= set_select('region', $region)  ?> value="<?= $region ?>"><?= $region ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger"><?= displayError($validation, 'region') ?></span>
            </div>

            <div class="form-group col-md-3">
                <label>District</label>
                <select name="district" class="form-control select2bs4 " name="district">
                    <option selected=" selected" disabled>Select District</option>
                    <?php foreach ($districts as $district) : ?>
                    <option <?= set_select('district', $district) ?> value="<?= $district ?>">
                        <?= $district ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger"><?= displayError($validation, 'district') ?></span>
            </div>
            <div class="form-group col-md-3">
                <label>Ward</label>
                <input class="form-control" type="text" name="ward" placeholder="Enter Ward"
                    value="<?= set_value('ward') ?>">
                <span class="text-danger"><?= displayError($validation, 'ward') ?></span>
            </div>
            <div class="form-group col-md-12">
                <label>Task Description</label>
                <div class="">
                    <textarea class="textarea" name="description"
                        style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
                        <?= set_value('taskdescription') ?>
                    </textarea>
                </div>


            </div>
        </div>


        <div class="form-group ">

            <button class="btn btn-primary mt-3">Assign</button>
        </div>

        <?= form_close() ?>
    </div>
    <br>

    <!-- /.card-body -->
</div>