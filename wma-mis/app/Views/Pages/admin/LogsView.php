<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1 class="m-0 text-dark"></h1> -->
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">System Logs</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
<div class="card">
    <div class="card-header">SYSTEM LOGS</div>
    <div class="card-body">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
           
            <div class="list-group">
                <?php if(empty($files)): ?>
                    <a class="list-group-item liv-active">No Log Files Found</a>
                <?php else: ?>
                    <?php foreach($files as $file): ?>
                        <a href="?f=<?= base64_encode($file); ?>"
                           class="list-group-item <?= ($currentFile == $file) ? "llv-active" : "" ?>">
                            <?= $file; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-9 col-md-10 table-container">
            <?php if(is_null($logs)): ?>
                <div>
                    <br><br>
                    <strong>Log file > 50MB, please download it.</strong>
                    <br><br>
                </div>
            <?php else: ?>
                <table id="table-log" class="table table-striped table-sm" style="width:100%">
                    <thead>
                    <tr>
                        <th>Level</th>
                        <th>Date</th>
                        <th>Content</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach($logs as $key => $log): ?>
                        <tr data-display="stack<?= $key; ?>">

                            <td class="text-<?= $log['class']; ?>">
                                <span class="<?= $log['icon']; ?>" aria-hidden="true"></span>
                                &nbsp;<?= $log['level']; ?>
                            </td>
                            <td class="date"><?= $log['date']; ?></td>
                            <td class="text">
                                <?php if (array_key_exists("extra", $log)): ?>
                                    <a class="pull-right expand btn btn-default btn-xs"
                                       data-display="stack<?= $key; ?>">
                                       <i class="fal fa-expand"></i>
                                    </a>
                                <?php endif; ?>
                                <?=  substr($log['content'], 0, 110); ?>
                                <?php if (array_key_exists("extra", $log)): ?>
                                    <div class="stack" id="stack<?= $key; ?>"
                                         style="display: none; white-space: pre-wrap;">
                                        <?= $log['extra'] ?>
                                    </div>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <div>
                <?php if($currentFile): ?>
                    <a href="?dl=<?= base64_encode($currentFile); ?>">
                    <i class="fal fa-file-download"></i>
                        Download file
                    </a>
                    -
                    <a id="delete-log" href="?del=<?= base64_encode($currentFile); ?>">
                    <i class="fal fa-trash-alt"></i>Delete file</a>
                    <?php if(count($files) > 1): ?>
                        -
                        <a id="delete-all-log" href="?del=<?= base64_encode("all"); ?>"><i class="fal fa-folder-times"></i>Delete all files</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
    </div>
</div>


<script>
    $(document).ready(function () {

        $('.table-container tr').on('click', function () {
            $('#' + $(this).data('display')).toggle();
        });

        $('#table-log').DataTable({
            "order": [],
            "stateSave": true,
            "stateSaveCallback": function (settings, data) {
                window.localStorage.setItem("datatable", JSON.stringify(data));
            },
            "stateLoadCallback": function (settings) {
                var data = JSON.parse(window.localStorage.getItem("datatable"));
                if (data) data.start = 0;
                return data;
            },
            "responsive": false, // Enable responsive behavior
        });
        $('#delete-log, #delete-all-log').click(function () {
            return confirm('Are you sure?');
        });
    });
</script>

<?= $this->endSection(); ?>