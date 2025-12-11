$(document).ready(function() {
    const saveButton = $('#save-customer')
    const searchCustomer = $('.searchCustomer')

    saveButton.click(function (e) { 
       e.preventDefault();

       const firstName = $('#firstName')
       const lastName = $('#lastName')
       const gender = $('#gender')
       const region = $('#region')
       const district = $('#district')
       const ward = $('#ward')
       const village = $('#village')
       const postalAddress = $('#postalAddress')
       const phoneNumber = $('#phoneNumber')
       function validateCustomerDetail(formInput){

        if(formInput.val() == ''){
            formInput.css('border','1px solid red')
            return false
        }
    
        else{
            formInput.css('border','')
            return true
        }
    
    }

    function clearInputs(){
        const firstName = $('#firstName').val('')
        const lastName = $('#lastName').val('')
        const gender = $('#gender').val('')
        const region = $('#region').val('')
        const district = $('#district').val('')
        const ward = $('#ward').val('')
        const village = $('#village').val('')
        const postalAddress = $('#postalAddress').val('')
        const phoneNumber = $('#phoneNumber').val('')  
    }

    
     
     
       
       if(validateCustomerDetail(firstName) && validateCustomerDetail(lastName) && validateCustomerDetail(region) && validateCustomerDetail(district) && validateCustomerDetail(phoneNumber)){

        $.ajax({
            type: "POST",
            url: "newCustomer",
            data: {
                firstName:firstName.val(),
                lastName:lastName.val(),
                gender:gender.val(),
                region:region.val(),
                district:district.val(),
                ward:ward.val(),
                village:village.val(),
                postalAddress:postalAddress.val(),
                phoneNumber:phoneNumber.val()
            },
            dataType: "text",
            success: function (response) {
              clearInputs()
              $('.modal').modal('hide');
              swal({
                 title: response,
                 // text: "You clicked the button!",
                 icon: "success",
                  button: "Ok",
               });
             //  alert(response) 
            }
        });
      
       }
  
   });

 

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

