<script>
    function renderPersonalDetails(customer) {
        const customerId = document.querySelector('#customerId').value = customer.hash
        // $('#customerId').val(customer.hash)
        // $('#hash').val(customer.hash)
        // $('#customerHashId').val(customer.hash)
        // $('#customerHash2').val(customer.hash)

        $('.selectedCustomerDetails').html(`

        
 <div class="card-body">
    <form id="customerUpdateForm" name="customerUpdateForm"> 
    
   
         
         <div class="row">
             <div class="form-group col-md-6 ">
             <label>Client Name / Company Name</label>
                 <input id="name_" class="form-control editing" type="text" value="${customer.name}" readonly>
             </div>
             <div class="form-group col-md-6 ">
             <label>Region</label>
                 <input id="region_" name="region" class="form-control editing" type="text" value="${customer.region}" readonly>
             </div>
           
             
         </div>
         <div class="row">
             
             <div class="form-group col-md-6 ">
              <label>Ward</label>
                 <input id="ward_" name="ward_" class="form-control editing" type="text" value="${customer.ward}" readonly>
             </div>
             <div class="form-group col-md-6 ">
             <label>Physical Address</label>
                 <input id="physicalAddress_" name="physicalAddress" class="form-control editing" type="text" value="${customer.physical_address}" readonly>
             </div>
         </div>
         <div class="row">
             <div class="form-group col-md-6 ">
             <label>Phone Number</label>
                 <input id="phoneNumber_" name="phoneNumber" class="form-control phone editing" type="text" value="${customer.phone_number}" readonly>
             </div>
             <div class="form-group col-md-6 ">
             <label>Postal Address</label>
                 <input id="postalAddress_" name="postalAddress" class="form-control editing" type="text" value="${customer.postal_address}"
                     readonly>
                     </div>
                     </div>
                     
     
     </div>
     <div class="card-footer">
     <input type="checkbox" class="check" onclick="enableEditing(this)" style="transform:scale(1.4);margin-right:5px;"><label>Edit</label>
     <button type="button" id="updateBtn" class="btn btn-success btn-sm update" onclick="updateCustomer('${customer.hash}')"  style="float: right;" >Update</button>
     </div>

      </form>


`);

    }

    function updateCustomer(hash) {




        $.ajax({
            type: "POST",
            url: "updateCustomer",
            data: {
                // csrf_hash: document.querySelector('.token').value,
                hash: hash,
                name: document.querySelector('#name_').value,
                region: document.querySelector('#region_').value,
                ward: document.querySelector('#ward_').value,
                phoneNumber: document.querySelector('#phoneNumber_').value,
                postalAddress: document.querySelector('#postalAddress_').value,
                physicalAddress: document.querySelector('#physicalAddress_').value
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                // document.querySelector('.token').value = response.token
                if (response.status == 1) {
                    swal({
                        title: 'Customer Updated',
                        icon: "success",
                        timer: 3500
                    });


                    const inputs = document.querySelectorAll('.editing')

                    for (let input of inputs) {
                        input.setAttribute('readonly', 'readonly')
                    }
                    updateBtn.classList.add('update')

                } else {
                    swal({
                        title: 'Something Went Wrong!',
                        icon: "warning",
                        timer: 4500
                    });
                }
            }
        });
    }

    function enableEditing(checkBox) {
        const updateBtn = document.querySelector('#updateBtn')
        const inputs = document.querySelectorAll('.editing')
        if (checkBox.checked == true) {
            for (let input of inputs) {
                input.removeAttribute('readonly')
            }
            updateBtn.classList.remove('update')
        } else {
            for (let input of inputs) {
                input.setAttribute('readonly', 'readonly')
            }
            updateBtn.classList.add('update')
        }
    }
</script>