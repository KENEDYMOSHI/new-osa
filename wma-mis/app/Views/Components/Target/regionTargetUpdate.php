<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="my-modal-title">UPDATE COLLECTION TARGET</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <form id="targeFormUpdate" name="targeFormUpdate">
            <div class="row">
                <input class="form-control" name="targetId" id="targetId" hidden></input>

                <div class="form-group col-md-6">
                    <label for="my-input">Month</label>
                    <select name="targetMonth" id="targetMonth" class="form-control select2bs4" required>
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
                        <label for="my-select">Year</label>
                        <select name="targetYear" id="targetYear" class="form-control" required>
                        <?php for ($i = date('Y'); $i >= 2023; $i--) : ?>
                                <option value="<?= $i ?>"><?= $i ?> </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>


                <div class="form-group col-md-12">
                    <label for="my-input">Region</label>
                    <select name="targetRegion" id="targetRegion" class="form-control select2bs4" required>

                    <?php foreach (collectionCenters() as $center) : ?>
                            <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="card col-md-12">

                    <div class="card-header">VTV</div>
                    <div class="card-body row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="my-input">VTV Instruments</label>
                                <input name="vtc" id="vtc" class="form-control" type="number" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="my-input">VTV Amount</label>
                                <input name="vtcAmt" id="vtcAmt" class="form-control" type="number" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-md-12">

                    <div class="card-header">SBL</div>
                    <div class="card-body row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="my-input">SBL Instruments</label>
                                <input name="sbl" id="sbl" class="form-control" type="number" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="my-input">VTV Amount</label>
                                <input name="sblAmt" id="sblAmt" class="form-control" type="number" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-md-12">

                    <div class="card-header">Meters</div>
                    <div class="card-body row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="my-input">Meters </label>
                                <input name="waterMeter" id="waterMeter" class="form-control" type="number" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="my-input">Meters Amount</label>
                                <input name="waterMeterAmt" id="waterMeterAmt" class="form-control" type="number" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>



            </div>



            <div class="modal-footer">
                <button type="submit" id="regionalTarget" class="btn btn-primary btn-sm">Update</button>
            </div>
        </form>
    </div>
</div>