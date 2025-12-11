<div class="card card-primary ">
    <div class="card-header">
        <h3 class="card-title">Assign User To A Group</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
            </button>

        </div>
    </div>
    <div class="card-body">
        <?= form_open() ?>
        <div class="row">
            <div class="form-group col-md-6">
                <label>Select Officer</label>
                <select name="officer" class="form-control select2bs4" style="width: 100%;">
                    <option selected=" selected" disabled>Select Officer</option>


                    <?php foreach ($officers as $officer) : ?>
                    <option value="<?= $officer->unique_id ?>"><?= $officer->first_name . ' ' . $officer->last_name ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger"><?= displayError($validation, 'officer') ?></span>
            </div>
            <div class="form-group col-md-6">
                <label>Select A Group</label>
                <select name="group" class="form-control select2bs4" style="width: 100%;">
                    <option selected=" selected" disabled>Select a group</option>
                    <?php foreach ($groups as $group) : ?>
                    <option value="<?= $group->group_name ?>"><?= $group->group_name ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger"><?= displayError($validation, 'group') ?></span>
            </div>


        </div>


        <div class="form-group">

            <button class="btn btn-primary mt-3">Add To Group</button>
        </div>
        <?= form_close() ?>
    </div>
    <!-- /.card-body -->
</div>