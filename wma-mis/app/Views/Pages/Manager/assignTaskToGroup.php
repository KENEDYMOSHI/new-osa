<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark"><?= $page['heading'] ?></h4>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->


<!-- Main content -->
<section class="content body">
    <?php if ($pageSession->getFlashdata('Success')) : ?>
        <div id="message" class="alert alert-success text-center" role="alert">
            <?= $pageSession->getFlashdata('Success'); ?>
        </div>
    <?php endif; ?>
    <div class="container-fluid row">
        
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    Create A Group
                </div>
                <div class="card-body">

                    <form id="createGroupForm" autocomplete="off">

                        <div class="form-group">
                            <label for="my-input">Group Name</label>
                            <input type="text" name="groupName" class="form-control client" required>
                        </div>
                        <table class="table table-light">
                            <thead class="thead-light">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>



                                <?php foreach ($officers as $officer) : ?>

                                    <tr>
                                        <td><input class="list-group-item" type="checkbox" name="officer[]" value="<?= $officer->unique_id ?>"></td>
                                        <td>
                                            <?= $officer->first_name . ' ' . $officer->last_name ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>


                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm">Create Group</button>
                        </div>

                    </form>


                </div>
            </div>
            <!-- ======================================= -->


            <div class="col-md-12">


                <?= $this->include('Components/Manager/giveTaskToGroup.php'); ?>
            </div>
        </div>
    </div>
    <script>
        const createGroupForm = document.querySelector('#createGroupForm')
        const taskForm = document.querySelector('#taskForm')
        createGroupForm.addEventListener('submit', (e) => {
            e.preventDefault()
            const formData = new FormData(createGroupForm)

            $.ajax({
                type: "POST",
                url: 'addGroup',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },

                success: function(response) {
                    console.log(response);

                    document.querySelector('.token').value = response.token
                    if (response.status == 1) {
                        // $('#billingForm')[0].reset()



                        swal({
                            title: response.msg,
                            icon: "success",
                        });
                        setTimeout(function() {
                            location.reload()
                        }, 3000)
                    } else {
                        swal({
                            title: response.msg,
                            icon: "warning",
                            timer: 3500
                        });
                    }



                },
                error: function(err) {
                    // console.log(err);
                }

            });

        })
        taskForm.addEventListener('submit', (e) => {
            e.preventDefault()
            const formData = new FormData(taskForm)

            $.ajax({
                type: "POST",
                url: 'createTask',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },

                success: function(response) {
                    console.log(response);
                    document.querySelector('.token').value = response.token

                    if (response.status == 1) {
                        // $('#billingForm')[0].reset()



                        swal({
                            title: response.msg,
                            icon: "success",
                        });
                        setTimeout(function() {
                            location.reload()
                        }, 3000)
                    } else {
                        swal({
                            title: response.msg,
                            icon: "warning",
                            timer: 3500
                        });
                    }



                },
                error: function(err) {
                    // console.log(err);
                }

            });

        })
    </script>
</section>

<?= $this->endSection(); ?>