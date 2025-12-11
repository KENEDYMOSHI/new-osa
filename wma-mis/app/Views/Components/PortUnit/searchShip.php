<div id="search-ship" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">Search Existing Ship</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group">

                    <input id="searchKeyWord" class="form-control" type="text" name="">
                    <button type="button" id="searchShipButton" class="btn btn-primary"><i class="far fa-search"></i>
                        Search</button>
                </div>

                <div id="searchResults">

                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>




<script>
$(document).ready(function() {
    $('#searchShipButton').click(function() {
        $('#searchResults').html('');
        $('#currentShip').innerHTML = '';
        $('.selectedVessel').innerHTML = '';
        const searchField = $('#searchKeyWord').val()
        const regex = new RegExp(searchField, 'i');
        $.getJSON('searchExistingShips', function(data) {

            // console.log(data)
            $.each(data, function(key, ship) {
                if (ship.ship_name.search(regex) != -1 || ship.captain
                    .search(regex) != -1) {
                    $('#searchResults').append(`
                        <div class="dropdown-divider"></div>
                        <p style="cursor:pointer" data-dismiss="modal" onclick="selectShip('${ship.ship_id }')"> Ship:${ship.ship_name} | ${ship.captain} | Port: ${ship.port} | Terminal : ${ship.terminal}</p>
                         <div class="dropdown-divider"></div>
                  `);


                }
            });
        })
    })


});

function selectShip(id) {
    const shipId = document.querySelector('#shipId')
    const currentShip = document.querySelector('#selectedShip')
    const selectedVessel = document.querySelector('.selectedVessel')
    shipId.value = id

    $.ajax({
        type: "POST",
        url: "selectedShip",
        data: {
            id: id
        },
        dataType: "json",
        success: function(res) {
            //console.log(res)
            //=================call a method to render all docs====================
            // getTheShipId(res.ship_id);

            // selectShipDocuments(res.ship_id);
            // fetchTheShipId(res.ship_id);
            shipId.value = res.ship_id


            // selectShipDocuments(res.ship_id)

            currentShip.innerHTML = (
                `
                <h4>Ship Name :${res.ship_name} |Captain ${res.captain} | Port: ${res.port} | Terminal : ${res.terminal}</h4> `
            )


            // selectedVessel.innerHTML = (`<h3>Ship Name: ${res.ship_name} </h3>`)


        }
    });
}

function test(id) {

}
</script>