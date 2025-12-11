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



<!-- Modal -->
<div class="modal modal-static fade" id="payBill" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bill Payment Simulation</h5>

            </div>
            <div class="modal-body">
                <form id="payBillForm">
                    <div class="form-group">
                        <label for="">Control Number</label>
                        <input type="text" name="controlNumber" id="controlNumber" class="form-control" readonly required>
                        <input type="text" name="billId" id="billId" class="form-control" readonly required hidden>

                    </div>
                    <div class="form-group">
                        <label for="">Amount (<span id="amountLabel"></span>)</label>
                        <input type="text" name="amount" id="amount" class="form-control" oninput="formatAmount(this)" required>
                        <input type="text" id="data" class="form-control" hidden>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" id="payBtn" class="btn btn-primary btn-sm">
                    <div class="spinner-border spinner-border-sm" id="spinner" role="status" style="display: none;"></div>
                    Pay Now
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->

<div class="container-fluid">
    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
            <table id="billTable" class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Payer Name</th>
                        <th>Control Number</th>
                        <th>Payment Option</th>
                        <th>Billed Amount</th>
                        <th>Paid Amount</th>
                        <th>Outstanding</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bills as $bill) : ?>
                        <tr>
                            <td><?= $bill->PyrName ?></td>
                            <td><?= $bill->PayCntrNum ?></td>
                            <td><?= $bill->BillPayOpt == '2' ? 'Partial' : 'Exact' ?></td>
                            <td><?= number_format($bill->BillAmt) ?></td>
                            <td><?= number_format($bill->PaidAmount) ?></td>
                            <td><?= number_format($bill->BillAmt - $bill->PaidAmount) ?></td>
                            <td><button onclick="payBill('<?= $bill->BillId ?>','<?= $bill->PayCntrNum ?>','<?= $bill->BillAmt ?>','<?= $bill->BillPayOpt ?>','<?= $bill->PyrCellNum ?>','<?= $bill->PyrName ?>')" class="btn btn-primary btn-xs"> <i class="fas fa-check"></i> Pay</button></td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    <script>
        function formatAmount(input) {
            let value = input.value.replace(/,/g, ''); // Remove existing commas
            input.value = new Intl.NumberFormat().format(value);
        }

        function payBill(billId, controlNumber, billAmount, option, mobile, payer) {
            const currentDateTime = new Date().toISOString().replace('T', ' ').slice(0, 19);
            console.log(currentDateTime)


            $('#payBill').modal({
                open: true,
                backdrop: 'static'
            })
            const paymentData = {
                billId: billId,
                controlNumber: controlNumber,
                billAmount: billAmount,
                option: option,
                mobile: mobile,
                payer: payer,
            }


            const jsonString = JSON.stringify(paymentData)
            document.querySelector('#data').value = jsonString
            document.querySelector('#controlNumber').value = controlNumber
            let amount = document.querySelector('#amount')
            amount.value = new Intl.NumberFormat().format(billAmount)
            let theAmount = new Intl.NumberFormat().format(billAmount)
            let amountLabel = document.querySelector('#amountLabel').textContent = theAmount
            if (option == '3') {
                amount.value = theAmount
            } else {
                amount.value = ''
            }
        }

        function randomString(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return result;
        }

        // Usage example:
        const payRefId = randomString(10); // Generate a random string of length 10
        const trxId = randomString(10); // Generate a random string of length 10
        const pspReceiptNumber = randomString(10); // Generate a random string of length 10


        const payBtn = document.querySelector('#payBtn')
        payBtn.addEventListener('click', (e) => {
            e.preventDefault()

            const amount = document.querySelector('#amount').value
            const data = document.querySelector('#data').value
            const paymentData = JSON.parse(data)
            const currentDateTime = new Date().toISOString().replace('T', ' ').slice(0, 19);

            const xmlPayload = `
            <Gepg>
                <gepgPmtSpInfo>
                    <PymtTrxInf>
                        <TrxId>${randomString(20)}</TrxId>
                        <SpCode>1996</SpCode>
                        <PayRefId>${randomString(20)}</PayRefId>
                        <BillId>${paymentData.billId}</BillId>
                        <PayCtrNum>${paymentData.controlNumber}</PayCtrNum>
                        <BillAmt>${paymentData.billAmount}</BillAmt>
                       <PaidAmt>${amount.replace(/,/g, '')}</PaidAmt>
                        <BillPayOpt>${paymentData.option}</BillPayOpt>
                        <CCy>TZS</CCy>
                        <TrxDtTm>${currentDateTime}</TrxDtTm>
                        <UsdPayChnl>M</UsdPayChnl>
                        <PyrCellNum>${paymentData.mobile}</PyrCellNum>
                        <PyrName>${paymentData.payer}</PyrName>
                        <PyrEmail></PyrEmail>
                        <PspReceiptNumber>${randomString(20)}</PspReceiptNumber>
                        <PspName>WMA</PspName>
                        <CtrAccNum>023560022</CtrAccNum>
                    </PymtTrxInf>
                </gepgPmtSpInfo>
            </Gepg>
                `;


            // console.log(xmlPayload)

            // const payBillForm = document.querySelector('#payBillForm')


            // // formData.append('billId', billId)
            document.querySelector('#spinner').style.display = 'inline-block'
            fetch('billPaymentSimulation', {
                    method: 'POST',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        'Content-Type': 'application/xml'
                    },

                    body: xmlPayload,

                }).then(response => response.json())
                .then(data => {
                    document.querySelector('#spinner').style.display = 'none'
                    if (data.status == 1) {
                        payBillForm.reset()
                        $('#payBill').modal('hide')
                        // document.querySelector('.' + billId).remove()
                        swal({
                            title: data.msg,
                            icon: "success",
                            // timer: 5500
                        });
                    } else {
                        swal({
                            title: data.msg,

                            icon: "warning",
                            // timer: 18500
                        });
                    }
                    console.log(data)
                })


        })


        $(document).ready(function() {
            $('#billTable').DataTable({
                dom: '<"top"lBfrtip>',
                buttons: [
                    'csv', 'excel', 'pdf', 'print'
                ],
                lengthMenu: [30, 40, 50, 70, 100]
            });
        });
    </script>




    <?= $this->endSection(); ?>