<div id="add-ship" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">VESSEL DETAILS</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="my-input">Ship Name</label>
                        <input id="shipName" class="form-control" type="text" name="" data-clear>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="my-input">Captain</label>
                        <input id="captain" class="form-control" type="text" name="" data-clear>
                    </div>
                </div>
                <div class="form-group ">
                    <label for="my-input">Arrival Date</label>
                    <input id="ArrivalDate" class="form-control" type="date" name="" data-clear>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="my-input">Cargo</label>
                        <input id="cargo" class="form-control" type="text" name="" data-clear>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="my-input">Quantity</label>
                        <input id="quantity" class="form-control" type="number" name="" data-clear>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="my-input">IMO</label>
                        <input id="imo" class="form-control" type="number" name="" data-clear>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="my-select">Port</label>
                        <select id="port" class="form-control" name="">
                            <option value="Dar es Salaam">Dar es Salaam</option>
                            <option value="Tanga">Tanga</option>
                            <option value="Mtwara">Mtwara</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="my-input">Terminal</label>
                        <select id="terminal" class="form-control" name="">
                            <option value="Berth No 3">Berth No 3</option>
                            <option value="CBM">CBM</option>
                            <option value="Koj 1">Koj 1</option>
                            <option value="Koj 2">Koj 2</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="my-input">DRAFT</label>
                        <input id="draft" class="form-control" type="number" name="" data-clear>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="my-input">AFT</label>
                        <input id="aft" class="form-control" type="number" name="" oninput="calculateTrim(this.value)"
                            data-clear>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="my-input">Trim</label>
                        <input id="trim" class="form-control" type="text" name="" readonly data-clear>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="my-input">List</label>
                        <input id="list" class="form-control" type="number" name="" oninput="getAft(this.value)"
                            data-clear>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="my-input">Density @ 15 &deg;C</label>
                        <input id="density15Centigrade" class="form-control" type="text" data-clear>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="my-input">Density @ 20 &deg;C</label>
                        <input id="density20Centigrade" class="form-control" type="number" name="" data-clear>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" name="saveTimeLog" id="saveShipDetails"
                    class="btn btn-primary btn-sm">Add</button>
            </div>
        </div>
    </div>
</div>

<script>
//=================initialize a time log====================
const getFormValue = (value) => {
    return document.querySelector(value)
}

function calculateTrim(aft) {
    const draft = getFormValue('#draft').value
    const trimValue = parseFloat(draft - aft)
    if (trimValue == 0) {

        const trim = getFormValue('#trim').value = 'K.E'
    } else {
        const trim = getFormValue('#trim').value = trimValue
    }
}
const saveTimeLog = document.querySelector('#saveShipDetails');

saveTimeLog.addEventListener('click', e => {
    e.preventDefault()
    const customerHash = getFormValue('#customerHash')
    const shipName = getFormValue('#shipName')
    const captain = getFormValue('#captain')
    const ArrivalDate = getFormValue('#ArrivalDate')
    const cargo = getFormValue('#cargo')
    const quantity = getFormValue('#quantity')
    const imo = getFormValue('#imo')
    const port = getFormValue('#port')
    const draft = getFormValue('#draft')
    const aft = getFormValue('#aft')
    const trim = getFormValue('#trim')
    const list = getFormValue('#list')
    const density15Centigrade = getFormValue('#density15Centigrade')
    const density20Centigrade = getFormValue('#density20Centigrade')



    function validateInput(formInput) {

        if (formInput.value == '') {

            formInput.style.border = '1px solid #ff6348'
            return false
        } else {
            formInput.style.border = '1px solid #2ed573'
            return true
        }

    }

    if (validateInput(shipName) && validateInput(captain) && validateInput(ArrivalDate) && validateInput(
            cargo) && validateInput(quantity) && validateInput(imo) &&
        validateInput(port) && validateInput(terminal) && validateInput(draft) && validateInput(aft) &&
        validateInput(trim) && validateInput(list) && validateInput(density15Centigrade) && validateInput(
            density20Centigrade)) {
        $.ajax({
            type: "POST",
            url: "addShipParticulars",
            data: {
                // customerHash: customerHash.value,
                shipName: shipName.value,
                captain: captain.value,
                ArrivalDate: ArrivalDate.value,
                cargo: cargo.value,
                quantity: quantity.value,
                imo: imo.value,
                port: port.value,
                terminal: terminal.value,
                draft: draft.value,
                aft: aft.value,
                trim: trim.value,
                list: list.value,
                density15Centigrade: density15Centigrade.value,
                density20Centigrade: density20Centigrade.value,
            },
            dataType: "json",
            success: function(response) {

                // console.log(response)
                if (response == 'Added') {
                    $('#add-ship').modal('hide');

                    clearInputs()
                    swal({
                        title: 'Ship Added',
                        // text: "You clicked the button!",
                        icon: "success",
                        button: "Ok",
                    });

                    //grabLastMeter()
                } else {
                    swal({
                        captain: 'Something Went Wrong!',
                        // text: "You clicked the button!",
                        icon: "error",
                        button: "Ok",
                    });
                }
            }
        });
    }



})
</script>