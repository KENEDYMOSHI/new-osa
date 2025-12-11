<!-- <style>
    #controlNumber{
        -moz-appearance: textfield;
        -webkit-appearance: none;
    }
</style> -->
<div class="input-group">
    <button class="btn btn-success btn-flat" onclick="generateControlNumber()" type="button">Generate</button>
    <input class="form-control" type="number"  placeholder="Control Number" name="controlNumber" id="controlNumber" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); "  maxlength="12">
</div>
<script>
    function generateControlNumber() {
        const controlNumber = document.querySelector('#controlNumber')
        const totalAmount = document.querySelector('#totalAmount')
        if (totalAmount.value == '') {
            swal({
                title: 'No Amount Found!',
                icon: "warning",
                timer: 2500
            });
        } else {
            $.ajax({
                type: "POST",
                url: "getControlNumber",
                data: {
                    // csrf_hash: document.querySelector('.token').value
                },

                dataType: "json",
                success: function(response) {
                    // document.querySelector('.token').value = response.token
                    controlNumber.value = response.data
                }
            });
        }


    }
</script>