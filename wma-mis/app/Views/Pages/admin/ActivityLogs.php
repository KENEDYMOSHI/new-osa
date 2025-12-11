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
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/AdminDashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->

<!-- ======================================================== -->


<section class="content body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <table id="logsTable" class="table table-bordered table-striped table-sm" >
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Region</th>
                            <th>Email</th>
                            <th>Login Time</th>
                            <th>Logout Time</th>
                            <th>Ip Address</th>
                            <th>Browser/Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) : ?>
                            <tr>

                                <td><?= $log->name ?></td>
                                <td><?= $log->region ?></td>
                                <td><?= $log->email ?></td>
                                <td><?= $log->loginTime ?></td>
                                <td><?= $log->logoutTime ?></td>
                                <td><?= $log->ipAddress ?></td>
                                <td><?= $log->userAgent ?></td>


                            </tr>

                        <?php endforeach; ?>
                    </tbody>


                </table>
            </div>
        </div>
    </div>
    <!-- /.card -->


    <script>
        $(document).ready(function() {
            $('#logsTable').DataTable({

                "responsive": true,
                "autoWidth": false,
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
            });
        });
    </script>

</section>

<?= $this->endSection(); ?>