<div class="row">
    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label class="must" for="">Name Of The Company/Client</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Name Of The Company/Client" required>

        </div>
    </div>

    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label class="must" for="">Region</label>
            <select class="form-control   theSelect" name="region" id="regions" onchange="getDistricts(this.value)">


            </select>
        </div>
    </div>

    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label for="">District</label>
            <select class="form-control  theSelect" name="district" id="districts" onchange="getWards(this.value)" required>
            </select>
        </div>
    </div>
    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label class="must" for="">Ward</label>
            <select class="form-control  theSelect" name="ward" id="wards" onchange="getPostcodes(this.value)" required>
            </select>
        </div>
    </div>
    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label for="">Post Code</label>
            <input type="text" class="form-control" name="postalCode" id="postCode" readonly>

        </div>
    </div>
    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label for="">Village/Street</label>
            <input type="text" class="form-control" name="village" id="village" >

        </div>
    </div>
    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label class="must" for="">Physical Address</label>
            <input type="text" class="form-control" name="physicalAddress" id="physicalAddress" required>

        </div>
    </div>
    <div class="col-md-<?= $colSize ?>">
        <div class="form-group">
            <label for="">Location</label>
            <input type="text" class="form-control" name="location" id="location">

        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="">Postal Address</label>
            <input type="text" name="postalAddress" id="postalAddress" class="form-control postal" placeholder="Postal Address">

        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label class="must" for="">Phone Number</label>
            <input type="text" name="phoneNumber" id="phoneNumber" class="form-control "  placeholder="Phone Number" required oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10)" maxlength="10">

        </div>
    </div>
</div>

<script>
     $('.theSelect').select2({
            theme: 'bootstrap4',
            dropdownParent: $("#addModal"),
        });
    //get all regions
    httpRequest('regions', 'fetchRegions', 'all')


    // get all districts
    function getDistricts(region) {
        document.querySelector('#wards').innerHTML = ''
        document.querySelector('#postCode').value = ''
        httpRequest('districts', 'fetchDistricts', region)

    }
    // get all wards from the district
    function getWards(district) {
        httpRequest('wards', 'fetchWards', district)

    }

    function getPostcodes(ward) {
        httpRequest('postcodes', 'fetchPostCodes', ward)

    }

    function httpRequest(element, url, param) {
         const appToken = document.querySelector('.token')
        const selectBox = document.querySelector('#' + element)
        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': appToken.value
                },

                body: JSON.stringify({
                    param: param,
                    // csrf_hash: appToken.value
                }),

            }).then(response => response.json())
            .then(data => {
                const {
                    status,
                    token,
                    dataList
                } = data
                appToken.value = token
                if (url == 'fetchPostCodes') {
                    document.querySelector('#postCode').value = dataList.postcode != undefined ? dataList.postcode : ''
                } else {
                    const options = dataList.map(list =>
                        `<option value="${list.name}">${list.name}</option>`
                    )

                    selectBox.innerHTML = '<option selected disabled>--select--</option>' + options
                }



            })
    }
</script>