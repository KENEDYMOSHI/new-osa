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
<!-- Modal for editing user -->

<!-- ======================================================== -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="regions" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Regions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="addresses" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Addresses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="regions">
                            <div class="card">
                                <div class="card-header">Regions & Districts
                                    <button style="float: right;" class="btn btn-primary btn-sm " onclick="addRegion()"><i class="fal fa-plus"></i> Add Region</button>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title">Title</h4>
                                    <p class="card-text">Body</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="addresses">
                            Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac,
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="regionModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="regionForm" class="regionBlock">

                    <div class="form-group">

                        <label for="">Region Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control">
                            <div class="input-group-prepend">
                                <span onclick="addRegionFiled()" class="input-group-text" style="cursor: pointer;"><i class="fal fa-plus"></i>
                            </div>
                        </div>
                        <!-- /.input group -->
                    </div>
                    <div id="regionInputs"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    function addRegion() {

        $('#regionModal').modal('show');

    }


    function addRegionFiled() {
        $('#regionInputs').append(`
         <div class="input-group mb-2">
            <input type="text" class="form-control">
            <div class="input-group-prepend">
             <span data-remove='remove' class="input-group-text bg-danger" style="cursor: pointer;"><i class="fal fa-ban"></i>
        </div>
         </div>
        
        
        `)
    }

    const regionBlock = document.querySelector('.regionBlock')
    regionBlock.addEventListener('click', (e) => {

        console.log(123);
        // if (e.target.hasAttribute('data-remove', 'remove')) {

        //     // const tr = e.target.parentElement.parentElement;


        //     // invoice.removeChild(tr)


        // }
    })

    function checkEmail(email) {

        $.ajax({
            type: "POST",
            url: "checkEmail",
            data: {
                csrf_hash: document.querySelector('.token').value,
                email: email
            },
            dataType: "json",
            success: function(response) {
                updateToken(response.token)
                console.log(response);

                if (response.status == 1) {
                    document.querySelector('#submit').setAttribute('disabled', 'disabled')
                    swal({
                        title: response.msg,
                        icon: "warning",
                        // timer: 2500
                    });

                } else if (response.status == 0) {
                    console.log('okay');
                    document.querySelector('#submit').removeAttribute('disabled', 'disabled')
                }


            }
        });


    }

    function updateToken(token) {

        document.querySelector('.token').value = token
    }
    $(document).ready(function() {
        $('#usersTable').DataTable();
    });

    $("#userForm").validate({
        rules: {
            firstName: {
                required: true
            },
            lastName: {
                required: true
            },
            email: {
                required: true,
                email: true,
                // remote: {
                //     url: "<?= base_url() ?>/checkEmail",
                //     type: 'post',
                //     cache: false,
                //     data: {
                //         email: function() {
                //             return $("#email").val();
                //         },
                //         csrf_hash: document.querySelector('.token').value
                //     }
                // }
            },
            region: {
                required: true,

            },
            role: {
                required: true,


            },

        },
        messages: {
            email: {
                required: "Email is required",
                remote: 'Email Already Taken'
            },



        },
    })


    $('#userForm').on('submit', function(e) {
        e.preventDefault()

        if ($('#userForm').valid()) {

            let formData = new FormData(this);
            formData.append("csrf_hash", document.querySelector('.token').value);
            $.ajax({
                type: "POST",
                url: "createUserAccount",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function() {
                    $('.spinner').show();
                    $('.spinner').attr('disabled', 'disabled');
                    $('.label').hide();
                },
                success: function(response) {
                    $('.spinner').hide();
                    $('.spinner').attr('disabled', '');
                    $('.label').show();
                    console.log(response);
                    updateToken(response.token)
                    $('#userForm')[0].reset()
                    $('#addUserModal').modal('hide');
                    swal({
                        title: response.msg,
                        // text: "You clicked 217 the button!",
                        icon: "success",
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 3000)

                },
                error: function(err) {
                    console.log(err);
                }

            });
        } else {
            return false
            // console.log('invalid');
        }

    })

    function editUser(id) {
        // $('#editUserModal').modal('show')    

        $.ajax({
            type: "post",
            url: "editUser",
            data: {
                csrf_hash: document.querySelector('.token').value,
                id: id
            },
            dataType: "json",
            success: function(response) {
                updateToken(response.token)
                $('#id').val(response.data.x_id)
                $('#firstName').val(response.data.first_name)
                $('#lastName').val(response.data.last_name)
                $('#email').val(response.data.email)
                $('#role').val(response.data.role).change()
                $('#region').val(response.data.city).change()
                $('#editUserModal').modal('show')
                console.log(response);
            }
        });

    }

    //=================submit data for update====================
    $("#userEditForm").validate()
    $('#userEditForm').on('submit', function(e) {
        e.preventDefault()

        if ($('#userEditForm').valid()) {

            let formData = new FormData(this);
            formData.append("csrf_hash", document.querySelector('.token').value);
            $.ajax({
                type: "POST",
                url: "updateUser",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function() {
                    // $('#preloader').show();
                },
                success: function(response) {

                    console.log(response);
                    updateToken(response.token)
                    // $('#userForm')[0].reset()
                    $('#editUserModal').modal('hide');
                    swal({
                        title: response.msg,
                        icon: "success",
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 3000)

                },
                error: function(err) {
                    console.log(err);
                }

            });
        } else {
            return false
            // console.log('invalid');
        }

    })


    function deleteUser(id) {
        console.log(id);
    }



    function resetPassword(id, targetElement) {

        let icon = targetElement.childNodes[0]


        swal({
                title: "Are You Sure You Want To Reset Password?",
                // text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                buttons: ["No!", "Yes I am"],
                dangerMode: true,
            })
            .then((willRun) => {

                if (willRun) {
                    $.ajax({
                        type: "post",
                        url: "resetPassword",
                        data: {
                            csrf_hash: document.querySelector('.token').value,
                            id: id
                        },
                        dataType: "json",
                        beforeSend: function() {
                            icon.classList.add('spin')
                        },
                        success: function(response) {
                            document.querySelector('.token').value = response.token
                            console.log(response);

                            if (response.status == 1) {
                                icon.classList.remove('spin')
                                swal({
                                    title: response.msg,
                                    icon: "success",

                                });

                            }
                        }
                    });


                } else {
                    swal("Password Is Not Reset ");
                }
            });



        // $.ajax({
        //     type: "post",
        //     url: "resetPassword",
        //     data: {
        //         csrf_hash: document.querySelector('.token').value,
        //         id: id
        //     },
        //     dataType: "json",
        //     success: function(response) {
        //         updateToken(response.token)
        //         console.log(response);
        //     }
        // });
    }
</script>

<?= $this->endSection(); ?>