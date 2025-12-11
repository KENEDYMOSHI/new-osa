<button type="button" class="btn btn-primary btn-sm" id="addSblButton"><i class="far fa-plus"></i> Add </button>
<button type="button" class="btn btn-success btn-sm" onclick="syncLorries()"><i class="far fa-sync"></i> Check</button>

<div class="input-group  mt-2">
    <input class="form-control" type="text" placeholder="Plate Number" id="licensePlate" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')">
    <div class="input-group-append">
        <button type="button" class="btn btn-primary btn-sm" id="plateSearch"><i class="far fa-search"></i>
            Search</button>
    </div>
</div>

<?= $this->include('Components/Lorries/searchSbl') ?>




<div class="Sbl"></div>



<!-- Search customer -->

