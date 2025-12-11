
//=================disabling scroll  behaviour on input type number====================
document.addEventListener('wheel', function(e) {
    if (document.activeElement.type === 'number') {
        document.activeElement.blur()
    }
})

 function formatNumber(number) {
        return new Intl.NumberFormat().format(number)
    }

    function monthFormatter(month) {
        switch (month) {
            case 1:
                return 'January'

                break;
            case 2:
                return 'February'

                break;
            case 3:
                return 'March'

                break;
            case 4:
                return 'April'

                break;
            case 5:
                return 'May'

                break;
            case 6:
                return 'June'

                break;
            case 7:
                return 'July'

                break;
            case 8:
                return 'August'

                break;
            case 9:
                return 'September'

                break;
            case 10:
                return 'October'

                break;
            case 11:
                return 'November'

                break;
            case 12:
                return 'December'

                break;

            default:
                break;
        }
    }

    function clearInputs(){
      const formInputsClear =   document.querySelectorAll('[data-clear]')

      for(input of formInputsClear){
          input.value = ''
      }

    }

    function checkCustomer () { 
       
            return swal({
             title: 'Please Select Customer First!',
             icon: "warning",
             timer: 2500
             });

        
     }
    function checkShip () { 
       
            return swal({
             title: 'Please Select Ship First!',
             icon: "warning",
             timer: 2500
             });

        
     }
    function  succeed(msg) { 
       
            return swal({
             title: msg,
             icon: "success",
            //  timer: 2500
             });

        
     }

    function  checkId(msg) { 
       
            return swal({
             title: msg,
             icon: "warning",
             timer: 2500
             });

        
     }
    function  messageAlert(msg) { 
       
            return swal({
             title: msg,
             icon: "warning",
             timer: 3000
             });

        
     }

     

