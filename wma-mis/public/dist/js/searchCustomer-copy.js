const  searchKeyWord = document.querySelector('#searchKeyWord');
const  searchButton = document.querySelector('#searchButton');
const  searchResults  = document.querySelector('.searchResults');



  
     
      const searchCustomer = async searchText =>{
      const res = await fetch('searchExistingCustomer');
      const customers  = await res.json();

      
      
      let matches = customers.filter(customer =>{
          const regex = new RegExp(`^${searchText}`,'gi') 
          return customer.first_name.match(regex) || customer.last_name.match(regex) 
      })

      if(searchKeyWord.value === ''){
          matches = []
      }
       renderResults(matches)
     
     };
   
     function renderResults(customers) {
        let output = ''
         customers.forEach(customer => {
             output +=  `
            <p data-dismiss="modal" onclick="selectCustomer('${customer.hash}')">${customer.first_name}  ${customer.last_name}  - ${customer.region} - ${customer.phone_number}</p>
             <div class="dropdown-divider"></div>

             `
            });
            
            //console.log(output)
        searchResults.innerHTML = output 
     }
    

 
 
     searchButton.addEventListener('click',() => searchCustomer(searchKeyWord.value));