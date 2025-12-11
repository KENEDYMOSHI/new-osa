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

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">Type</label>
                        <select name="" id="type" class="form-control">
                            <option value="">Select Recon Type</option>
                            <option value="Normal">Normal</option>
                            <option value="tr">TR</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">Date</label>
                        <input type="date" name="" id="date" class="form-control" placeholder="" aria-describedby="helpId">
                    </div>
                </div>
            </div>

            <button type="button" id="requestBtn" class="btn btn-primary btn-sm">Send Request</button>
        </div>
        <div class="card-body">


            <table class="table table-sm" id="reconTable">
                <thead>
                    <tr>
                        <th>Control Number</th>
                        <th>Paid Amount</th>
                        <th>Currency</th>
                        <th>Payment Red Id</th>
                        <th>Transaction Data & Time</th>
                        <th>Credited Account Number</th>
                        <th>Payment Chanel</th>
                        <th>Depositor Name</th>
                        <th>Depositor Phone Number</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($reconciliations as $recon) : ?>
                        <tr>
                            <td class="text-bold"><?= $recon->BillCtrNum ?></td>
                            <td><?= number_format($recon->PaidAmt) ?></td>
                            <td><?= $recon->CCy ?></td>
                            <td style="color:navy"><?= $recon->PayRefId ?></td>
                            <td><?= $recon->TrxDtTm ?></td>
                            <td><?= $recon->CtrAccNum ?></td>
                            <td><?= $recon->UsdPayChnl ?></td>
                            <td><?= $recon->DptName ?></td>
                            <td><?= $recon->DptCellNum ?></td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const requestBtn = document.querySelector('#requestBtn')

    requestBtn.addEventListener('click', (e) => {
        e.preventDefault()
        const type = document.querySelector('#type').value

        let url = type == 'Normal' ? 'wmaBillReconciliation' : 'billReconciliation'



        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },

                body: JSON.stringify({
                    ReconcOpt: 1,
                    date: document.querySelector('#date').value,
                }),
            })
            .then(response => response.json())
            .then(data => {
                const {
                    msg,
                    TnxCode,
                    reconStatusCode,
                    token
                } = data
                document.querySelector('.token').value = token
                if (reconStatusCode == '7101') {
                    swal({
                        title: msg,
                        text: reconStatusCode,
                        icon: "success",
                        //  timer: 2500
                    });
                } else {
                    swal({
                        title: msg,
                        text: reconStatusCode,
                        icon: "warning",
                        // timer: 2500
                    });

                }
                console.log(data)

            });
    })

    $(document).ready(function() {
        $('#reconTable').DataTable({
            dom: '<"top"lBfrtip>',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ],
            lengthMenu: [25, 35, 50, 100]
        });
    });

    // function cancelBill(controlNumber) {

    //     $('#cancelBill').modal({
    //         open: true,
    //         backdrop: 'static'
    //     })
    //     const billControlNumber = document.querySelector('#controlNumber')
    //     const description = document.querySelector('#description')
    //     billControlNumber.value = controlNumber
    //     const cancelBillForm = document.querySelector('#cancelBillForm')
    //     cancelBillForm.addEventListener('submit', (e) => {
    //         e.preventDefault()
    //         console.log(controlNumber)
    //         if (billControlNumber && description != '') {
    //             cancelBillForm.reset()
    //             $('#cancelBill').modal('hide')
    //             swal({
    //                 title: 'Bill Canceled Successfully',
    //                 icon: "success",
    //                 timer: 5500
    //             });
    //         }
    //     })


    // }

    // function viewBill(controlNumber, activity) {



    //     console.log(controlNumber)
    //     console.log(activity)

    //     const params = {
    //         document: document.querySelector('#document').value,
    //         controlNumber: controlNumber,
    //         activity
    //     }

    //     fetch('selectBill', {
    //             method: 'POST',

    //             headers: {
    //                 'Content-Type': 'application/json;charset=utf-8'
    //             },
    //             body: JSON.stringify(params)
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             console.log(data)

    //             if (data.status == 1) {

    //                 const bill = data
    //                 $('#billCustomer').html( /*html*/ `



    //                 <tr>
    //                    <td>Control Number:</td>
    //                   <td><b>${bill.controlNumber}</b></td>
    //                   </tr>
    //                 <tr>
    //                     <td>Payment Ref:</td>
    //                     <td><b>${bill.paymentRef}</b></td>
    //                 </tr>
    //                 <tr>
    //                     <td>Received From:</td>
    //                     <td>${bill.payer}</td>
    //                 </tr>
    //                 <tr>
    //                     <td>Payer Phone:</td>
    //                     <td>${bill.phoneNumber}</b></td>
    //                 </tr>
    //                 `)

    //                 let sn = 1
    //                 const items = bill.products.map(item => `
    //                 <tr>
    //                  <td>${sn++}</td>
    //                  <td>${item.product}</td>
    //                  <td>${item.amount}</td>
    //                 </tr>

    //                 `)

    //                 $('#billItems').html(items)
    //                 $('#billTotal').html(bill.billTotal)
    //                 $('#billTotalInWords').html(bill.billTotalInWords)
    //                 $('#preparedBy').html(bill.createdBy)
    //                 $('#printedBy').html(bill.printedBy)
    //                 $('#printedOn').html(bill.printedOn)

    //                 if (bill.document == 'receipt') {
    //                     $('#printedOn').html(bill.printedOn)

    //                 }

    //                 const refs = document.querySelectorAll('.ref')
    //                 refs.forEach(r => r.textContent = bill.controlNumber)

    //                 $('#printModal').modal({
    //                     open: true,
    //                     backdrop: 'static'
    //                 })
    //             }










    //         });



    // }
</script>
<?= $this->endSection(); ?>