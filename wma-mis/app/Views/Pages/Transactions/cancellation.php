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

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Body
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <card class="card-body">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Control Number</th>
                    <th>Billed Amount</th>
                    <th>Paid Amount</th>
                    <th>Status</th>
                    <th>Payment Option</th>
                    <th>Exp Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bills as $bill) : ?>
                    <tr>
                        <td><?= $bill->PyrName ?></td>
                        <td><?= $bill->PayCntrNum ?></td>
                        <td>Tsh <?= number_format($bill->BillAmt) ?></td> 
                        <td>Tsh <?= number_format($bill->PaidAmount) ?></td> 
                        <td><?= $bill->Status ?></td> 
                        <td><?= $bill->BillPayOpt ?></td> 
                        <td><?= $bill->BillExprDt ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-xs" onclick="cancelBill('<?= $bill->BillId ?>')">
                                <i class="fas fa-trash-alt    "></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-xs" onclick="changeBill('<?= $bill->BillId ?>','<?= $bill->BillExprDt ?>')">
                                <i class="fas fa-share-alt    "></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </card>
</div>

<script>
    function cancelBill(BillId) {
        fetch('billCancellation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest"
                },

                body: JSON.stringify({
                    BillId: BillId,
                    CanclReasn: 'The client is over charged',
                    csrf_hash: document.querySelector('.token').value
                }),
            })
            .then(response => response.text())
            .then(data => {


                console.log(data)

            });
    }





    function changeBill(BillId, BillExprDt) {

        fetch('billChange', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest"
                },

                body: JSON.stringify({
                    BillId: BillId,
                    BillExprDt: BillExprDt,
                    csrf_hash: document.querySelector('.token').value
                }),
            })
            .then(response => response.text())
            .then(data => {


                console.log(data)

            });
    }




    function cancelTheBill(controlNumber) {

        $('#cancelBill').modal({
            open: true,
            backdrop: 'static'
        })
        const billControlNumber = document.querySelector('#controlNumber')
        const description = document.querySelector('#description')
        billControlNumber.value = controlNumber
        const cancelBillForm = document.querySelector('#cancelBillForm')
        cancelBillForm.addEventListener('submit', (e) => {
            e.preventDefault()
            console.log(controlNumber)
            if (billControlNumber && description != '') {
                cancelBillForm.reset()
                $('#cancelBill').modal('hide')
                swal({
                    title: 'Bill Canceled Successfully',
                    icon: "success",
                    timer: 5500
                });
            }
        })


    }

    function viewBill(controlNumber, activity) {



        console.log(controlNumber)
        console.log(activity)

        const params = {
            document: document.querySelector('#document').value,
            controlNumber: controlNumber,
            activity
        }

        fetch('selectBill', {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(params)
            })
            .then(response => response.json())
            .then(data => {
                console.log(data)

                if (data.status == 1) {

                    const bill = data
                    $('#billCustomer').html( /*html*/ `

                        

                    <tr>
                       <td>Control Number:</td>
                      <td><b>${bill.controlNumber}</b></td>
                      </tr>
                    <tr>
                        <td>Payment Ref:</td>
                        <td><b>${bill.paymentRef}</b></td>
                    </tr>
                    <tr>
                        <td>Received From:</td>
                        <td>${bill.payer}</td>
                    </tr>
                    <tr>
                        <td>Payer Phone:</td>
                        <td>${bill.phoneNumber}</b></td>
                    </tr>
                    `)

                    let sn = 1
                    const items = bill.products.map(item => `
                    <tr>
                     <td>${sn++}</td>
                     <td>${item.product}</td>
                     <td>${item.amount}</td>
                    </tr>

                    `)

                    $('#billItems').html(items)
                    $('#billTotal').html(bill.billTotal)
                    $('#billTotalInWords').html(bill.billTotalInWords)
                    $('#preparedBy').html(bill.createdBy)
                    $('#printedBy').html(bill.printedBy)
                    $('#printedOn').html(bill.printedOn)

                    if (bill.document == 'receipt') {
                        $('#printedOn').html(bill.printedOn)

                    }

                    const refs = document.querySelectorAll('.ref')
                    refs.forEach(r => r.textContent = bill.controlNumber)

                    $('#printModal').modal({
                        open: true,
                        backdrop: 'static'
                    })
                }










            });



    }
</script>
<?= $this->endSection(); ?>