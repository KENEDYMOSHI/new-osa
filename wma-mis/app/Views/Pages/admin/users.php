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
<div class="modal fade" id="editUserModal" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="userEditForm" name="userEditForm">


                    <input type="text" id="id" name="id" class="form-control" required hidden>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">First Name</label>
                            <input type="text" id="firstName" name="firstName" class="form-control" required placeholder="First Name" required>

                        </div>

                        <div class="form-group col-md-6">
                            <label for="">Last Name</label>
                            <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Last Name" required>

                        </div>

                        <div class="form-group col-md-6">
                            <label for="">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required>

                        </div>

                        <div class="form-group col-md-6">
                            <label for="">Collection Center</label>
                            <select class="form-control select2bs4" id="collectionCenter" name="collectionCenter" required>
                                <option disabled selected>Select Center</option>
                                <?php foreach ($centers as $center) : ?>
                                    <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="">User Group</label>
                            <select class="form-control select2bs4" name="userGroup" id="userGroup" required>
                                <option disabled selected>Select User Group</option>
                                <?php foreach ($groups as $group => $title) : ?>
                                    <option value="<?= $group ?>"><?= $title['title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                      
                    </div>






            </div>
            <fieldset class="border p-3 border-top-2">
                <legend>Permissions</legend>
                <div class="row">



                    <?php foreach ($permissions as $permission) : ?>
                        <?php $id = str_replace('.', '', $permission);  ?>
                        <div class="col-md-3 m-2">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" name="permissions[]" value="<?= $permission ?>" id="<?= $id  ?>">
                                <label for="<?= $id ?>"><?= $permission ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>



                </div>

            </fieldset>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- ======================================================== -->


<section class="content body">

    <div class="container-fluid">
        <?php if ($pageSession->getFlashdata('Success')) : ?>
            <div id="message" class="alert alert-success text-center" role="alert">
                <?= $pageSession->getFlashdata('Success'); ?>
            </div>
        <?php endif; ?>
        <?php if ($pageSession->getFlashdata('error')) : ?>
            <div id="message" class="alert alert-danger text-center" role="alert">
                <?= $pageSession->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Users</h3>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-sm" style="float: right;" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus"></i> Add User
                </button>

                <!-- Modal -->
                <div class="modal fade" id="addUserModal" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create New User</h5>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <form id="userForm" name="userForm">

                                    <div class="sign">


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="">First Name</label>
                                                <input type="text" name="first_name" class="form-control" placeholder="First Name">
                                                <span class="text-danger" id="first_name"></span>

                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="">Last Name</label>
                                                <input type="text" name="last_name" class="form-control" placeholder="Last Name">
                                                <span class="text-danger" id="last_name"></span>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="">Email</label>
                                                <input type="email" name="email" class="form-control" placeholder="Email Address">
                                                <span class="text-danger" id="emailAddress"></span>
                                            </div>


                                            <div class="form-group col-md-6">
                                                <label for="">Collection Center</label>
                                                <select class="form-control select2bs4" name="collection_center">
                                                    <option disabled value="">Select Center</option>
                                                    <?php foreach ($centers as $center) : ?>
                                                        <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="">User Group</label>
                                                <select class="form-control select2bs4" name="userGroup" required>
                                                    <option disabled value="">Select User Group</option>
                                                    <?php foreach ($groups as $group => $title) : ?>
                                                        <option value="<?= $group ?>"><?= $title['title'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <!-- <div class="form-group col-md-6">
                                                <label for="">User Role</label>
                                                <select class="form-control" name="role" required>
                                                    <option selected disabled>Select Role</option>
                                                    <option value="7">Admin</option>
                                                    <option value="3">Head Of Section</option>
                                                    <option value="2">Manager</option>
                                                    <option value="1">Officer</option>
                                                </select>
                                            </div> -->
                                        </div>



                                        <fieldset class="border p-3 border-top-2">
                                            <legend>Permissions</legend>
                                            <div class="row">

                                                <?php $n = 0 ?>
                                                <?php foreach ($permissions as $permission) : ?>
                                                    <?php $i = $n++ ?>

                                                    <div class="col-md-3 m-2">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" name="permissions[]" value="<?= $permission ?>" id="<?= $i ?>">
                                                            <label for="<?= $i ?>"><?= $permission ?></label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>



                                            </div>

                                        </fieldset>



                                    </div>

                                    <!-- <div class="option">
                                    <div class="option__item">
                                        <button type="submit" class="button">Register</button>
                                    </div>
                                </div> -->

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <!-- <button type="submit" class="btn btn-primary btn-sm">Register</button> -->

                                <button id="submit" class="btn btn-primary btn-sm" type="submit">
                                    <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>

                                    Register
                            </div>

                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- <?php
                    Printer($users);
                    //exit;
                    ?> -->

            <div class="card-body">

                <table id="usersTable" class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Collection Center</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= $user->first_name . ' ' . $user->last_name ?></td>
                                <td><?= $user->email ?></td>
                                <td><?= $user->phone_number ?></td>
                                <td><?= $user->centerName ?></td>
                                <td>
                                    <?= implode(',', $user->getGroups()) ?>

                                </td>
                                <td>
                                    <?php if ($user->active == 1) : ?>
                                        <span class="badge badge-pill bg-green">Active</span>
                                    <?php else : ?>
                                        <span class="badge badge-pill badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->active == 0) : ?>
                                        <a data-toggle="tooltip" data-placement="top" title="Activate" href="<?= base_url() ?>/admin/activateAccount/<?= $user->unique_id ?>" class="btn btn-danger btn-xs"><i class="fas fa-lock-alt"></i></a>
                                    <?php elseif ($user->active == 1) : ?>
                                        <a data-toggle="tooltip" data-placement="top" title="Deactivate" href="<?= base_url() ?>/admin/deactivateAccount/<?= $user->unique_id ?>" class="btn btn-success btn-xs"><i class="fas fa-lock-open"></i></a>
                                    <?php endif; ?>

                                    <!-- <a href="<?= base_url() ?>/admin/editUser/<?= $user->unique_id ?>" class="btn btn-primary btn-sm"><i class="fas fa-pen"></i></a> -->

                                    <button data-toggle="tooltip" data-placement="top" title="Edit" type="button" onclick="editUser('<?= $user->unique_id ?>')" class="btn btn-primary btn-xs">
                                        <div class="">
                                            <i class="fas fa-pen"></i>

                                        </div>

                                    </button>

                                    <button data-toggle="tooltip" data-placement="top" title="Reset Password" type="button" onclick="resetPassword('<?= $user->unique_id ?>',this)" class="btn bg-black btn-xs"><i class="fas fa-sync-alt"></i></button>

                                </td>

                            </tr>

                        <?php endforeach; ?>
                    </tbody>

                 
                </table>
            </div>

        </div>
        <!-- /.card-header -->


        <style>
            .spin {
                animation-name: spinner;
                animation-duration: 1000ms;
                animation-iteration-count: infinite;
                animation-timing-function: linear;
            }

            .swal-button--danger {
                background: #0075F2;
                color: #fff;
            }

            .swal-button--danger:hover {
                background: #2250CE !important;
            }

            @keyframes spinner {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);

                }

            }
        </style>


        <!-- /.card-body -->
    </div>


    </div>
    <!-- /.card -->

    <script>
        function testing(id, el) {

            console.log(id);
            el.childNodes[0].classList.add('spin')

        }

        function formValidation() {
            // Select all input elements with a name attribute
            const inputElements = document.querySelectorAll('input[name]');

            // Loop through each input element
            inputElements.forEach(input => {
                // Get the value of the name attribute
                const name = input.getAttribute('name');
                // Check if the value of the name attribute matches any key in the validation object
                if (validation.hasOwnProperty(name)) {
                    // Create a span element and set its text content to the error message from the validation object
                    const errorSpan = document.createElement('span');
                    errorSpan.textContent = validation[name];
                    // Append the span element to the parent element of the input element
                    input.parentNode.appendChild(errorSpan);
                } else {
                    // Remove any existing span element from the parent element of the input element
                    const errorSpan = input.parentNode.querySelector('span');
                    if (errorSpan) {
                        errorSpan.remove();
                    }
                }
            });

        }


        function checkEmail(email) {

            $.ajax({
                type: "POST",
                url: "checkEmail",
                data: {
                    email: email
                },
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },
                success: function(response) {
                    // updateToken(response.token)
                    document.querySelector('.token').value = response.token
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





        $(document).ready(function() {
            $('#usersTable').DataTable({

                "responsive": true,
                "autoWidth": false,
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                lengthMenu:[30,50,100,200]
            });
        });

        // $("#userForm").validate({
        //     rules: {
        //         firstName: {
        //             required: true
        //         },
        //         lastName: {
        //             required: true
        //         },
        //         email: {
        //             required: true,
        //             email: true,
        //             // remote: {
        //             //     url: "<?= base_url() ?>/checkEmail",
        //             //     type: 'post',
        //             //     cache: false,
        //             //     data: {
        //             //         email: function() {
        //             //             return $("#email").val();
        //             //         },
        //             //     }
        //             // }
        //         },
        //         collectionCenter: {
        //             required: true,

        //         },
        //         role: {
        //             required: true,


        //         },

        //     },
        //     messages: {
        //         email: {
        //             required: "Email is required",
        //             remote: 'Email Already Taken'
        //         },



        //     },
        // })

        function loading() {
            $('.spinner').show();
            $('.spinner').attr('disabled', 'disabled');
            $('.label').hide();
        }

        function done() {
            $('.spinner').hide();
            $('.spinner').attr('disabled', '');
            $('.label').show();
        }

        // function showError(form, errors) {
        //    const inputs = form.querySelectorAll('input[type="text"], input[type="password"], input[type="email"],select')
        //     inputs.forEach(input => {
        //         const inputName = input.getAttribute('name');
        //         const error = errors[inputName];
        //         const span = input.nextElementSibling;
        //         if (error) {
        //             if (!span || !span.classList.contains('text-danger')) {
        //                 const newSpan = document.createElement('span');
        //                 newSpan.classList.add('text-danger');
        //                 input.insertAdjacentElement('afterend', newSpan);
        //                 span = newSpan;
        //             }
        //             span.textContent = error;
        //         } else if (span && span.classList.contains('text-danger')) {
        //             span.remove();
        //         }
        //     });
        // }



        const userForm = document.querySelector('#userForm')
        userForm.addEventListener('submit', e => {
            e.preventDefault()
            submitInProgress(e.submitter)
            const formData = new FormData(userForm)
            fetch('createUserAccount', {
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
                        status,
                        msg,
                        token,
                        errors
                    } = data

                    showError(userForm, errors)
                    // if (errors.length == []) 
                    document.querySelector('.token').value = token

                    if (status == 1) {
                        submitDone(e.submitter)
                        userForm.reset()
                        $('#addUserModal').modal('hide')
                        swal({
                            title: msg,
                            icon: "success",

                        });
                    } else if (status == 0) {
                        submitDone(e.submitter)

                    }
                    console.log(data)
                })
        })



        function editUser(id) {
            // $('#editUserModal').modal('show')    

            $.ajax({
                type: "post",
                url: "getSingleUser",
                data: {
                    id: id
                },
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                },
                success: function(response) {
                    // updateToken(response.token)
                    const {
                        token,
                        unique_id,
                        first_name,
                        last_name,
                        role,
                        collection_center,
                        id
                    } = response.data
                    document.querySelector('.token').value = response.token
                    $('#id').val(unique_id)
                    $('#firstName').val(first_name)
                    $('#lastName').val(last_name)
                    $('#email').val(response.email)
                    // $('#role').val(role).change()
                    $('#userGroup').val(response.group).change()
                    $('#collectionCenter').val(collection_center).change()
                    $('#editUserModal').modal('show')
                    console.log(response);
                    // console.log(response.email);


                    const permissions = response.permissions;
                    for (let i = 0; i < permissions.length; i++) {
                        const permission = permissions[i].replace(/\./g, '');
                        const checkbox = document.getElementById(permission);

                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    }

                }
            });

        }

        //=================submit data for update====================
        $("#userEditForm").validate()
        $('#userEditForm').on('submit', function(e) {
            e.preventDefault()

            if ($('#userEditForm').valid()) {

                let formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: "updateUser",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                    },
                    success: function(response) {
                        document.querySelector('.token').value = response.token
                        console.log(response);
                        // updateToken(response.token)
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
                    buttons: ["No", "Yes"],
                    dangerMode: true,
                })
                .then((willRun) => {

                    if (willRun) {
                        $.ajax({
                            type: "post",
                            url: "resetPassword",
                            data: {
                                id: id
                            },
                            dataType: "json",
                            beforeSend: function(xhr) {
                                icon.classList.add('spin')
                                xhr.setRequestHeader('X-CSRF-TOKEN', $('.token').val());
                            },

                            success: function(response) {
                                document.querySelector('.token').value = response.token
                                console.log(response);

                                icon.classList.remove('spin')

                                swal({
                                    title: response.msg,
                                    icon: response.status == 1 ? "success" : "warning",

                                });


                            }
                        });


                    } else {
                        swal("Password Is Not Reset ");
                    }
                });



        }
    </script>


</section>

<?= $this->endSection(); ?>