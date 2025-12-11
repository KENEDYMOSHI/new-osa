function printBill(bill) {
            // console.log('printing ......')
            console.log(bill)
            $('#printModal').modal({
                open: true,
                backdrop: 'static'
            })
            $('#billCustomer').html( /*html*/ `
            
            <tr>
               <td>Control Number:</td>
              <td>${bill.controlNumber}</td>
              </tr>
            <tr>
                <td>Payment Ref:</td>
                <td>${bill.paymentRef}</td>
            </tr>
            <tr>
                <td>Payer:</td>
                <td>${bill.payer.name}</td>
            </tr>
            <tr>
                <td>Payer Phone:</td>
                <td>${bill.payer.phone_number}</td>
            </tr>
            `)
            let sn = 1
            const items = bill.products.map(item => `
            <tr>
             <td>${sn++}</td>
             <td>${item.product}</td>
             <td>${item.amount}</td>
            </tr>
            
            `)
            $('#billItems').html(items)
            $('#billTotal').html(bill.billTotal)
            $('#billTotalInWords').html(bill.billTotalInWords)
            $('#preparedBy').html(bill.createdBy)
            $('#printedBy').html(bill.printedBy)
            $('#printedOn').html(bill.printedOn)

            const refs = document.querySelectorAll('.ref')
            refs.forEach(r => r.textContent = bill.controlNumber)
        }