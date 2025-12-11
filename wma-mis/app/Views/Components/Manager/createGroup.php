<div class="card card-primary ">
    <div class="card-header">
        <h3 class="card-title">Create A Group</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
            </button>

        </div>
    </div>
    <div class="card-body">
        <?= form_open() ?>
        <div class="input-group mb-1">
            <div class="input-group-prepend">
                <span class="input-group-text" id="my-addon">Group Name</span>
            </div>
            <input class="form-control" type="text" name="groupname" placeholder="" aria-label="Recipient's "
                aria-describedby="my-addon" value="<?= set_value('groupname') ?>">
        </div>
        <span class="text-danger"><?= displayError($validation, 'groupname') ?></span>

        <div class="form-group">

            <button class="btn btn-primary mt-3">Create Group</button>
        </div>
        <?= form_close() ?>
    </div>
    <!-- /.card-body -->
</div>