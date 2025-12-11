<?=$this->extend('Layouts/coreLayout');?>
<?=$this->section('content');?>
<script>
const fetchTheShipId = (id) => {
    const logDownload = document.querySelector('#downloadTimeLog')
    logDownload.setAttribute('href', '<?=base_url()?>/downloadCertificateOfQuantity/' + id)
}
</script>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?=$page['heading']?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=base_url()?>/Dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?=$page['heading']?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
    <?=$this->include('Widgets/shipOptions.php')?>
        <?=$this->include('Components/shipDetails.php')?>
        <?=$this->include('Components/PortUnit/searchShip.php')?>

        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#timeLog-modal"
                    aria-pressed="false" autocomplete="off"><i class="far fa-plus-circle" aria-hidden="true"></i>Add
                    Certificate</button>
                <button type="button" onclick="getCertificateOfQuantity()" class="btn btn-success btn-sm"
                    id="refreshTimeLogs"><i class="far fa-sync" aria-hidden="true"></i>Check
                    Certificate</button>

                <h4 id="selectedShip"></h4>
            </div>
            <div class="card-body">
                <div id="currenCertificate">

                </div>

            </div>
            <div class="card-footer">
                <a id="downloadTimeLog" target="_blank" class="btn btn-success btn-sm"><i class="far fa-download"
                        aria-hidden="true"></i>Download</a>
            </div>
        </div>


        <div id="timeLog-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">ADD CERTIFICATE OF QUANTITY</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">

                            <input id="shipId" class="form-control" type="number" name="">
                        </div>



                        <div class="row">

                            <div class="form-group col-12">
                                <label for="my-input">US BBLS @ 60&deg;F</label>
                                <input type="number" class="form-control " id="USBBLS60"
                                    placeholder="US BBLS @ 60 Fahrenheit">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="my-input">US Gallons @ 60&deg;F</label>
                                <input type="number" class="form-control " id="USGallons60"
                                    placeholder="US Gallons @ 60 Fahrenheit">
                            </div>



                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveCertificate" class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </div>
                </div>
            </div>


        </div><!-- /.container-fluid -->
        <script>
        $(document).ready(function() {
            $('.').md();

        });
        </script>
        <script>
        function getTheShipIdNumber(id) {
            alert('its' + id)
        }

        function formatNumber(number) {
            return new Intl.NumberFormat().format(number)
        }
        const saveCertificate = document.querySelector('#saveCertificate');

        saveCertificate.addEventListener('click', (e) => {
            e.preventDefault()

            const getFormValue = (value) => {
                return document.querySelector(value)
            }

            const shipId = getFormValue('#shipId')

            const USBBLS_60 = getFormValue('#USBBLS60')
            const USGallons_60 = getFormValue('#USGallons60')

            // console.log(shipId.value)
            // console.log(date.value)
            // console.log(time.value)




            function validateInput(formInput) {

                if (formInput.value == '') {

                    formInput.style.border = '1px solid #ff6348'
                    return false
                } else {
                    formInput.style.border = '1px solid #2ed573'
                    return true
                }

            }

            if (validateInput(USBBLS_60) && validateInput(USGallons_60)) {
                $.ajax({
                    type: "POST",
                    url: "addCertificateOfQuantity",
                    data: {
                        shipId: shipId.value,

                        USBBLS_60: USBBLS_60.value,
                        USGallons_60: USGallons_60.value,

                    },
                    dataType: "json",
                    success: function(response) {


                        // console.log(response)
                        if (response == 'Added') {

                            getCertificateOfQuantity()
                            $('#timeLog-modal').modal('hide');

                            swal({
                                title: 'Certificate Saved',
                                // text: "You clicked the button!",
                                icon: "success",
                                button: "Ok",
                            });

                        } else {
                            swal({
                                title: 'Something Went Wrong!',
                                // text: "You clicked the button!",
                                icon: "error",
                                button: "Ok",
                            });
                        }
                    }
                }, );
            }



        })


        function formatDate(dateInput) {
            const date = new Date(dateInput);
            const formattedDate = date.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }).replace(/ /g, '-');

            return formattedDate
        }
        //=================Processing Certificate of Quantity====================
        function processCertificateOfQuantity(arr) {
            let metric_tons_in_air = 0
            let metric_tons_in_vac = 0
            let long_tons = 0
            let litres_20c = 0
            let observedVolume = 0
            let litres_15c = 0

            let density_15 = arr[0].density_15C
            let density_20 = arr[0].density_20C

            let WCFT_15 = arr[0].density_15C - 0.0011
            let WCFT_20 = arr[0].density_20C - 0.0011

            let usbbls_60F = arr[0].usbbls_60F
            let us_gallons_60F = arr[0].us_gallons_60F

            arr.forEach(element => {

                metric_tons_in_air += parseFloat(element.GSV20Centigrade * WCFT_20)
                metric_tons_in_vac += parseFloat(element.GSV20Centigrade * density_20)
                observedVolume += parseFloat((element.totalObservedVolume) * 1000)
                litres_20c += parseFloat((element.GSV20Centigrade) * 1000)
                litres_15c += parseFloat((element.GSV15Centigrade) * 1000)


            });



            return `

<table class="table" border="0" style="width: 35%;">

   <tbody>

       <tr>
           <td> Metric Tons in Air</td>
           <td colspan="2"> = </td>
           <td>${metric_tons_in_air.toFixed(3)}</td>
       </tr>
       <tr>
           <td>Metric Tons in Vac.</td>
           <td colspan="2"> = </td>
           <td>${metric_tons_in_vac.toFixed(3)}</td>
       </tr>
       <tr>
           <td> Long Tons</td>
           <td colspan="2"> = </td>
           <td>${(metric_tons_in_air * 0.984206).toFixed(3)}</td>
       </tr>
       <tr>
           <td> Litres @ 20&deg;C</td>
           <td colspan="2"> = </td>
           <td>${formatNumber(litres_20c)}</td>
       </tr>
       <tr>
           <td>Observed Volume (Liters)</td>
           <td colspan="2"> = </td>
           <td>${formatNumber(observedVolume)}</td>
       </tr>
       <tr>
           <td>Litres @ 15&deg;C</td>
           <td colspan="2"> = </td>
           <td>${formatNumber(litres_15c)}</td>
       </tr>

       <tr>
           <td> US BBLS @ 60&deg;F</td>
           <td colspan="2"> = </td>
           <td>${formatNumber(usbbls_60F)}</td>
       </tr>
       <tr>
           <td>US GALLONS @ 60&deg;F</td>
           <td colspan="2"> = </td>
           <td>${formatNumber(us_gallons_60F)}</td>
       </tr>
       <tr>
           <td>Std density@20</td>
           <td colspan="2"> = </td>
           <td>${density_20}</td>
       </tr>
       <tr>
           <td>Std density@15</td>
           <td colspan="2"> = </td>
           <td>${density_15}</td>
       </tr>

   </tbody>
   </table>
`



        }

        function getCertificateOfQuantity() {
            $('#currenCertificate').html('')
            /// const shipId = document.querySelector('#shipId')
            $.ajax({
                type: "POST",
                url: "getCertificateOfQuantity",
                data: {
                    shipId: shipId.value
                },
                dataType: "json",
                success: function(response) {

                    // console.log(response)

                    if (response == 'nothing') {
                        $('#currenCertificate').html('<h3>No Certificate Found</h3>')
                    } else {

                        fetchTheShipId(shipId.value)

                        console.log(response)
                        $('#currenCertificate').append(processCertificateOfQuantity(response))



                    }




                }
            });
        }
        </script>
</section>
<?=$this->endSection();?>