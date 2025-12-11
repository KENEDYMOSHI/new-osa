$(document).ready(function () {
      $('#searchButton').click(function(){
            $('.searchResults').html('');
            const searchField = $('#searchKeyWord').val()
            const regex = new RegExp(searchField,'i');
            $.getJSON('searchExistingCustomer',function(data){
              $.each(data, function (key, customer) { 
                   if (customer.name.search(regex) != -1 || customer.physical_address.search(regex) != -1) {
                        $('.searchResults').append(`
                        <div class="dropdown-divider"></div>
                        <p style="cursor:pointer" data-dismiss="modal" onclick="selectCustomer('${customer.hash}')">${customer.name}  ${customer.physical_address} | ${customer.region} | ${customer.ward} | ${customer.phone_number}</p>
                         <div class="dropdown-divider"></div>
                  `
                        );
                   }  
              });
            })
      })
});