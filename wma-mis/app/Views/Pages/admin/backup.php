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
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- /.content-header -->
<div class="container">

    <div class="card">
        <div class="card-header">
            <form id="backupForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><input class="check" style="transform:scale(1.3); margin-right:5px" value="month" type="checkbox" onchange="toggleMonth(this)">Number Of Months</label>
                            <select class="form-control period" name="month" id="month" disabled>

                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <option value="<?= $i ?>"> <?= $i ?> Month<?= $i > 2 ? 's' : ''  ?> </option>
                                <?php endfor; ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><input class="check" style="transform:scale(1.3); margin-right:5px" type="checkbox" onchange="toggleDay(this)" value="day">Number Of Days</label>
                            <input type="number" name="days" id="day" min="1" class="form-control period" placeholder="Number Of Days" disabled>

                        </div>
                    </div>
                </div>

                <button id="button"  class="btn btn-primary" type="submit" style="transition: 1s ease;">
                    <span style="display: none;" id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span id="title">Create Backup</span>
                </button>
            </form>
        </div>
        <div class="card-body">
            <h4 class="card-title">Last backup created on: </h4>
            <p class="card-text"><strong id="date"><?= $date ?></strong></p>
        </div>
        <!-- <div class="card-footer text-muted">
            Footer
        </div> -->
    </div>


    <?= csrf_field() ?>


</div>

<script>
    function checkOptions() {

        const generateBtn = document.querySelector('#button')

        const checkBoxes = document.querySelectorAll('.check')

        let inputs = []
        checkBoxes.forEach(checkBox => {

            if (checkBox.checked == true) {

                inputs.push('*')

            }
        })

        console.log(inputs)

        if (inputs.length > 1) {
            generateBtn.setAttribute('disabled', 'disabled')
            return swal({
                title: 'Please Choose One  Option',
                icon: "warning",
                timer: 4500
            });

            // return false
        } else {
            // return true
            generateBtn.removeAttribute('disabled')
        }

    }

    function toggleMonth(month) {
        checkOptions()
        if (month.checked == true) {
            document.querySelector('#month').removeAttribute('disabled')
        } else {
            //checkOptions()
            document.querySelector('#month').setAttribute('disabled', 'disabled')
        }
    }

    function toggleDay(day) {
        checkOptions()
        if (day.checked == true) {
            document.querySelector('#day').removeAttribute('disabled')
        } else {
            // checkOptions()
            document.querySelector('#day').setAttribute('disabled', 'disabled')
        }
    }




    const backupForm = document.querySelector('#backupForm')
    backupForm.addEventListener('submit', (e) => {
        e.preventDefault()
        const formData = new FormData(backupForm)
        $.ajax({
            type: "POST",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            url: "createBackup",
            beforeSend: function(xhr) {
                document.querySelector('#spinner').style.display = 'inline-block'
                document.querySelector('#title').textContent = 'Creating Backup Please Wait!'
                document.querySelector('#button').setAttribute('disabled', true)
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('.token').value)

            },
            // data: "data",
            dataType: "json",
            success: function(response) {
                console.log(response);
                document.querySelector('.token').value = response.token
                if (response.status == 1) {
                    document.querySelector('#spinner').style.display = 'none'
                    document.querySelector('#button').removeAttribute('disabled')
                    document.querySelector('#title').textContent = 'Create Backup'
                    document.querySelector('#date').textContent = response.data
                    swal({
                        title: response.msg,
                        icon: "success",

                    });
                } else {
                    document.querySelector('#spinner').style.display = 'none'
                    document.querySelector('#button').removeAttribute('disabled')
                    document.querySelector('#title').textContent = 'Create Backup'
                    swal({
                        title: response.msg,
                        icon: "warning",

                    });
                }

            }
        });
    })
</script>

<?= $this->endSection(); ?>