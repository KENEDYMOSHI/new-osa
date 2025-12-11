$(document).ready(function() {
    const searchCustomer = $('.searchCustomer')

   
 

   function renderResults(customers){ 
       const searchResults = $('.searchResults')
       if(customers.length > 0) {
        customers.forEach(customer=>{

          
            searchResults.html(
                  `
                  <p data-dismiss="modal" onclick="selectCustomer('${customer.hash}')">${customer.first_name}  ${customer.last_name}  - ${customer.region} - ${customer.phone_number}</p>
                  <div class="dropdown-divider"></div>
                  `
               )
          
           })

       }else{
        searchResults.html('<h4>No Match Found</h4>') 
       }
      

       
    }
   

    

   searchCustomer.click(function(){
       const searchKeyword = $('#existingCustomer').val()

       $.ajax({
        type: "GET",
        url: "searchExistingCustomer",
        data: {
            searchKeyword:searchKeyword,
           
        },
        dataType: "json",
        success: function (response) {
            renderResults(response)
          console.log(response) 
          //alert(response) 
        }
    });
   })
})

