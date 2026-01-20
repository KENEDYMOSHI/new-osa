<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="my-modal-title">REGIONAL COLLECTION TARGET</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="my-input">Activity</label>
                <select id="activity" class="form-control select2bs4">

                    <option value="vtc">Vehicle Tank Verification</option>
                    <option value="sbl">Sandy & Ballast Lorries</option>
                    <option value="waterMeter">Meters</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="my-input">Amount</label>
                <input id="amount" class="form-control" type="number" data-clear required>
            </div>
        </div>
        <div class="form-group">
            <label for="my-input">Instruments</label>
            <input id="instruments" class="form-control" type="number" data-clear required>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Month</label>
                <select id="month" class="form-control select2bs4">
                    <!-- <option value="0">All Months</option> -->
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <div class="form-group">
                    <label for="year-activity-target">Year</label>
                    <select id="year" class="form-control" name="">
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                    </select>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" id="activityTargetBtn" class="btn btn-primary btn-sm">Save</button>
    </div>
</div>