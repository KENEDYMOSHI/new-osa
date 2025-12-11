<?= $this->extend('layouts/coreLayout'); ?>
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
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $page['heading'] ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <?php if ($scaleResults) : ?>
                            <table id="example1" class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th>Full Name <span class="invisible">Hello world</span> </th>
                                        <th>Gender</th>
                                        <th>City</th>
                                        <th>Ward</th>
                                        <th>Postal Address</th>
                                        <th>Phone Number</th>
                                        <th>Date</th>
                                        <th>Scale Type</th>
                                        <th>Scale Capacity</th>
                                        <th>Sticker Number</th>
                                        <th>Control Number</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($scaleResults as $scale) : ?>
                                    <tr>
                                        <td><?= $scale->first_name . '  ' . $scale->last_name ?></td>
                                        <td><?= $scale->gender ?></td>
                                        <td><?= $scale->city ?></td>
                                        <td><?= $scale->ward ?></td>
                                        <td><?= $scale->postal_address ?></td>
                                        <td><?= $scale->phone_number ?></td>
                                        <td><?= date("d M Y", strtotime($scale->date)) ?></td>
                                        <td><?= $scale->scale_type ?></td>
                                        <td><?= $scale->scale_capacity . ' ' ?>Kilograms</td>
                                        <td><?= $scale->sticker_number ?></td>
                                        <td><?= $scale->control_number ?></td>
                                        <td>
                                            <?php if ($scale->status == 'Pass') : ?>
                                            <span class="badge badge-success">Pass</span>
                                            <?php else : ?>
                                            <span class="badge badge-warning">Rejected</span>
                                            <?php endif; ?>



                                        </td>
                                        <td>
                                            <a href="" class="btn btn-primary btn-sm"><i class="far fa-edit"></i></a>
                                            <a href="" class="btn btn-danger btn-sm"><i
                                                    class="far fa-trash-alt"></i></a>
                                        </td>
                                        <!-- 
                                        <td class="d-flex ">
                                            <a href="" class="btn btn-primary"><i class="far fa-edit"></i></a>
                                            <a href="" class="btn btn-danger"><i class="far fa-trash-alt"></i></a>
                                        </td> -->


                                    </tr>
                                    <?php endforeach; ?>


                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                            <?php else : ?>
                            <h2>There Are No Records Currently Available</h2>
                            <?php endif; ?>
                            <!-- <table id="example1" class="my-table " border="1"> -->

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>

    </div>
    <!-- /.card -->

    </div>
    </div>

</section>
<script>
$(function() {
    $("#example1").DataTable({
        "responsive": true,
        "autoWidth": false,
    });
    $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    });
});
</script>

<?= $this->endSection(); ?>