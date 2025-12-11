<div class="card card-primary ">
    <div class="card-header">
        <h3 class="card-title"><i class="fal fa-users icon"></i>Assign task to a group </h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
            </button>

        </div>
    </div>
    <div class="card-body">
        <form id="taskForm">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Activity </label>

                    <select name="activity" class="form-control select2bs4 " required>
                        <option selected=" selected" disabled>Select a Activity</option>
                        <?php foreach ($activities as $activity) : ?>
                            <option <?= set_select('activity', $activity) ?> value="<?= $activity ?>"><?= $activity ?></option>
                        <?php endforeach; ?>
                    </select>


                </div>

                <?php
                $createdGroups = [];
                foreach ($groups as $group) {
                    array_push($createdGroups, $group['group_name']);
                }


                ?>




                <div class="form-group col-md-6">
                    <label>Select A Group</label>
                    <select name="group" class="form-control select2bs4 " required>
                        <option selected=" selected" disabled>Select a group</option>
                        <?php foreach (array_unique($createdGroups) as $userGroup) : ?>
                            <option value="<?= $userGroup ?>"><?= $userGroup ?></option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Region</label>
                        <select class="form-control select2bs4" name="region" id="regions" onchange="getDistricts(this.value)">


                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">District</label>
                        <select class="form-control select2bs4" name="district" id="districts" onchange="getWards(this.value)" required>
                        </select>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Ward</label>
                        <select class="form-control select2bs4" name="ward" id="wards"  required>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <label>Task Description</label>
                    <div class="">
                        <textarea class="textarea" name="description" style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">

                    </textarea>
                    </div>


                </div>
            </div>



            <div class="form-group ">

                <button type="submit" class="btn btn-primary btn-sm mt-3">Assign</button>
            </div>

        </form>
    </div>
    <br>

    <!-- /.card-body -->
</div>




<script>
    //get all regions
    httpRequest('regions', 'fetchRegions', 'all')


    // get all districts
    function getDistricts(region) {
        document.querySelector('#wards').innerHTML = ''
        httpRequest('districts', 'fetchDistricts', region)

    }
    // get all wards from the district
    function getWards(district) {
        httpRequest('wards', 'fetchWards', district)

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