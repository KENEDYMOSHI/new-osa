<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>


<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
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

<style>
    .stickerBody {
        /* width: 500px;
        height: 300px; */
        background: #eee4c9;
        border: 1px solid #444;
        display: flex;
        flex-direction: column;
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .stickerHeader {
        display: flex;
        align-items: center;
        border-bottom: 2px solid #444;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .leftLogo img {
        height: 60px;
        margin-right: 20px;
    }

    .heading {
        flex-grow: 1;
        text-align: center;
    }

    .heading h5 {
        margin: 5px 0;
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }

    .stickerContent {
        display: flex;
        flex-grow: 1;
        margin-bottom: 15px;
    }

    .details {
        flex: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .details h6 {
        margin: 5px 0;
        font-size: 13px;
        color: #333;
    }

    .wmaLogo {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .wmaLogo img {
        height: 80px;
        opacity: 0.8;
    }

    .stickerFooter {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 2px solid #444;
        padding-top: 10px;
    }

    .qrCode img {
        height: 60px;
    }

    .stickerNumber h4 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
        color: #333;
        background-color: rgba(255, 255, 255, 0.7);
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>

<div class="container-fluid">





    <div class="card">

        <div class="card-header">
            <form id="searchingForm">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <!-- <label for="my-select">Text</label> -->
                            <select name="activity" class="form-control select2bs4" style="width:100%" required>
                                <option value="">--Select Activity--</option>
                                <option value="<?= setting('Gfs.vtv') ?>">Vehicle Tank Verification </option>
                                <option value="<?= setting('Gfs.sbl') ?>">Sand & Ballast lorries</option>
                                <option value="<?= setting('Gfs.counterScale') ?>">Counter Scale</option>
                                <option value="<?= setting('Gfs.wagonTank') ?>">Wagon Tank</option>
                                <option value="<?= setting('Gfs.fuelPump') ?>">Fuel Pump</option>
                                <option value="<?= setting('Gfs.cngFillingStation') ?>">CNG Filling Station</option>
                                <option value="<?= setting('Gfs.flowMeter') ?>">Flow Meter</option>
                                <option value="<?= setting('Gfs.taxiMeter') ?>">Taxi Meter</option>
                                <option value="<?= setting('Gfs.springBalance') ?>">Spring Balance</option>
                                <option value="<?= setting('Gfs.weigher') ?>">Weigher</option>
                                <option value="<?= setting('Gfs.automaticWeigher') ?>">Automatic Weigher</option>
                                <option value="<?= setting('Gfs.beamScale') ?>">Beam Scale</option>
                                <option value="<?= setting('Gfs.weighBridge') ?>">Weigh Bridge</option>
                                <option value="<?= setting('Gfs.domesticGasMeter') ?>">Domestic Gas Meter</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <!-- <label for="my-input">Activity</label> -->

                            <div class="input-group">
                                <input id="controlNumber" name="controlNumber" class="form-control" type="text" placeholder="Control Number" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary"> <i class="far fa-search"></i>
                                        Search</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>


    </div>
    <div class="card searchCard" id="ItemBlock" style="display: block;">

        <div class="card-body">
            <div id="stickers"></div>

        </div>

    </div>





</div>
</div>
<script src="<?= base_url('assets/js/BluetoothPrinter.js') ?>"></script>
<script>
    const searchingForm = document.querySelector('#searchingForm')

    searchingForm.addEventListener('submit', (e) => {
        e.preventDefault()



        console.log('searching....')
        const formData = new FormData(searchingForm)
        // formData.append('token',document.querySelector('.token').value)
        const searchResults = document.querySelector('#searchResults')



        fetch('searchSticker', {
                method: 'POST',

                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('.token').value
                },


                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const {
                    msg,
                    token,
                    stickers
                } = data
                console.log(data)
                document.querySelector('#stickers').innerHTML = stickers
                document.querySelector('.token').value = token



            });
    })

    async function printSticker(stickerNumber) {
        let printer = null;
        try {
            const stickerData = document.querySelector(`#${stickerNumber}`).value;
            const sticker = JSON.parse(stickerData);
            console.log('Sticker to print:', sticker);

            const printData = {
                stickerId: sticker.stickerId,
                stickerNumber: sticker.stickerNumber,
                verificationDate: sticker.verificationDate,
                nextVerification: sticker.nextVerification,
                certificateNumber: sticker.certificateNumber,
                instrument: sticker.instrument,
                baseUrl: '<?= base_url() ?>',
            };

            printer = new BluetoothPrinter();
            await printer.connect(); // Connect to the printer

            console.log('Printing sticker:', printData);
            await printer.print(printData);

            console.log('Sticker printed successfully');
        } catch (error) {
            console.error('Error printing sticker:', error);
        } finally {
            if (printer) {
                await printer.disconnect(); // Ensure disconnection
            }
        }
    }

    async function printAllStickers() {
        let printer = null;
        try {
            const allStickers = document.querySelector('#allStickers').value;
            const stickers = JSON.parse(allStickers);
            console.log('Stickers to print:', stickers);

            printer = new BluetoothPrinter();
            await printer.connect(); // Connect once

            for (const sticker of stickers) {
                try {
                    const printData = {
                        stickerId: sticker.stickerId,
                        stickerNumber: sticker.stickerNumber,
                        verificationDate: sticker.verificationDate,
                        nextVerification: sticker.nextVerification,
                        certificateNumber: sticker.certificateNumber,
                        instrument: sticker.instrument,
                        baseUrl: '<?= base_url() ?>',
                    };
                    console.log('Printing sticker:', printData);
                    await printer.print(printData);
                } catch (error) {
                    console.error('Error printing sticker:', sticker, error);
                    // Continue with the next sticker
                }
            }
            console.log('All stickers printed successfully');
        } catch (error) {
            console.error('Error parsing stickers or connecting to printer:', error);
        } finally {
            if (printer) {
                await printer.disconnect(); // Ensure disconnection
            }
        }
    }
</script>



<?= $this->endSection(); ?>