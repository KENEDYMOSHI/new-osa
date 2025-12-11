<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Currency <span class="text-danger">*</span></label>
            <select class="form-control" name="Ccy" id="Ccy">
                <option value="TZS">TZS</option>
                <!-- <option value="USD">USD</option> -->

            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Days<span class="text-danger"></span></label>
                    <input type="number" min="1" max="30" id="" class="form-control" oninput="calculateDate(this.value)" required>

                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="">Expiry Date<span class="text-danger"></span></label>
                    <input type="text" name="BillExprDt" id="expiryDate" readonly class="form-control" required>

                </div>
            </div>
        </div>
    </div>



    <div class="col-md-4">
        <div class="form-group">
            <label for="">Payment Option</label>
            <select class="form-control" name="BillPayOpt" id="BillPayOpt" required>
                <!-- <option value="1">Full</option> -->
                <option value="">--Select Payment Option--</option>
                <option value="3">Exact</option>
                <option value="2">Partial</option>
            </select>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-4">
        <label for="">Method</label><br>
        <div class="form-check form-check-inline mb-1">
            <label class="form-check-label" for="mobile">
                <input class="form-check-input" style="accent-color:#DB611E;transform:scale(1.25)" type="radio" checked="checked" name="method" id="mobile" value="MobileTransfer" onchange="changeTransfer(this.value)"> Mobile Money Or Bank
            </label>
        </div>
        <div class="form-check form-check-inline">
            <label class="form-check-label" for="bank">
                <input class="form-check-input" style="accent-color:#DB611E;transform:scale(1.25)" type="radio" name="method" id="bank" value="BankTransfer" oncanplay="
                                " onchange="changeTransfer(this.value)"> Electronic Fund Transfer
            </label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Transfer To Bank</label>
            <select class="form-control" disabled name="SwiftCode" id="swiftCode" required>
                <option value="">--Select Bank--</option>
                <option value="NMIBTZTZ">National Microfinance Bank</option>
                <option value="CORUTZTZ">CRDB Bank</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Set Reminder<span class="text-danger">*</span></label>
            <div class="form-check">
                <input id="remember" class="form-check-input" name="RemFlag" type="checkbox" checked="" style="transform:scale(1.3) ; accent-color:#DB611E;cursor:pointer"> &nbsp;
                <label for="remember" class="form-check-label">Yes</label>
            </div>

        </div>
    </div>
</div>