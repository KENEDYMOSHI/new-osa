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
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>


<!-- /.content-header -->
<div class="container-fluid">
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="posModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="posForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">POS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="posId" id="posId" class="form-control" hidden>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="">Collection Center</label>
                                <select class="form-control select2bs4" name="centerName" id="centerName" required>
                                    <option disabled value="">Select Center</option>
                                    <?php foreach ($centers as $center) : ?>
                                        <option value="<?= $center->centerName ?>"><?= $center->centerName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Device Id</label>
                                    <input type="text" name="deviceId" id="deviceId" class="form-control" placeholder="Device Id Number" required>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">

            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#posModal">
                <i class="fal fa-plus"></i>
                Add Pos
            </button>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="posTable">
                <thead>
                    <tr>
                        <th>Collection Center</th>
                        <th>Device Id</th>
                        <th>Status</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Last User</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="posData">

                    <?= $posData ?>
                </tbody>
            </table>
        </div>
    </div>



</div>

<script>
    $('#posForm').validate()
    const posForm = document.querySelector('#posForm')
    posForm.addEventListener('submit', e => {
        e.preventDefault()
        submitInProgress(e.submitter)
        if ($('#posForm').valid()) {
            const formData = new FormData(posForm)
            const posId = document.querySelector('#posId').value
            const url = posId == '' ? 'addPos' : 'updatePos'
            fetch(url, {
                    method: 'POST',
                    headers: {
                        // 'Content-Type': 'application/json;charset=utf-8',
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSRF-TOKEN': document.querySelector('.token').value
                    },

                    body: formData,

                }).then(response => response.json())
                .then(data => {
                    const {
                        token,
                        status,
                        msg,
                        posData
                    } = data
                    document.querySelector('.token').value = token
                    if (status == 1) {
                        posForm.reset()
                        submitDone(e.submitter)
                        $('#posModal').modal('hide')
                        document.querySelector('#posData').innerHTML = posData
                        swal({
                            title: msg,
                            icon: "success",
                        });
                    } else {
                        swal({
                            title: msg,
                            icon: "warning",

                        });
                    }
                    console.log(data)
                })
        } else {
            return false
        }
    })

    function editPos(posId) {
        fetch('editPos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: JSON.stringify({
                    posId:posId
                }),

            }).then(response => response.json())
            .then(data => {
                const {
                    token,
                    status,
                    msg,
                    pos
                } = data
                document.querySelector('.token').value = token
                if (status == 1) {
                 
                    document.querySelector('#posId').value = pos.id
                    document.querySelector('#deviceId').value = pos.deviceId
                    $('#centerName').val(pos.centerName).select()
                    $('#posModal').modal('show')
                    
                }
                console.log(pos)
            })
    }
</script>



<?= $this->endSection(); ?>