const formatAmount = (value) => {
    return new Intl.NumberFormat().format(value)
}
function base_url() {
  var pathparts = location.pathname.split('/');
  if (location.host == 'localhost') {
    var url = location.origin + '/' + pathparts[1].trim('/') + '/'; 
  } else {
    var url = location.origin; 
  }
  return url ;
}
console.log('BASE URL')
console.log(base_url)
axios
  .get('https://sites.local/mis/dataChartManager', {})
  .then(function (response) {
    const info = response.data;
    console.log('******************************************')
    console.log(info);
    getData(info);
    paid(info);
  })
  .catch(function (error) {
    console.log(error);
  });


function paid(data) {
    const paidAmount = document.querySelector('#paid')
    const pendingAmount = document.querySelector('#pending')
    const totalAmount = document.querySelector('#total')
    let paidMoney = 0
    let pendingMoney = 0
    let totalMoney = 0

    data.map((status) => {
      const arrData = Object.values(status);


      arrData.forEach((item) => {
          let amount = item.amount
            totalMoney += parseInt(amount)

            if (item.PaymentStatus == 'Paid') {

                paidMoney += parseInt(amount)
            } else if (item.PaymentStatus == 'Pending') {
                pendingMoney += parseInt(amount)
            }


        })

    })
    paidAmount.textContent = 'Tsh ' + formatAmount(paidMoney)
    pendingAmount.textContent = 'Tsh ' + formatAmount(pendingMoney)
    totalAmount.textContent = 'Tsh ' + formatAmount(totalMoney)
    console.log('PAID ' +paidMoney)
    console.log('PENDING '+pendingMoney)
    console.log(totalMoney)
}



const getData = (result) => {
    let months = {
        'january': 0,
        'february': 0,
        'march': 0,
        'april': 0,
        'may': 0,
        'june': 0,
        'july': 0,
        'august': 0,
        'september': 0,
        'october': 0,
        'november': 0,
        'december': 0,
    }


    let chartData = []
    result.map((newArr) => {
      const arr = Object.values(newArr);
 

      arr.forEach((res) => {
            const collectionMonth = new Date(res.CreatedAt);
            // console.log(collectionMonth.getMonth())
            if (collectionMonth.getMonth() == 0) {

                const collected = res.amount
                // console.log(money)
                months.january += parseInt(collected);

            } else if (collectionMonth.getMonth() == 1) {
                const collected = res.amount
                months.february += parseInt(collected)
            } else if (collectionMonth.getMonth() == 2) {
                const collected = res.amount
                months.march += parseInt(collected)
            } else if (collectionMonth.getMonth() == 3) {
                const collected = res.amount
                months.april += parseInt(collected)
            } else if (collectionMonth.getMonth() == 4) {
                const collected = res.amount
                months.may += parseInt(collected)
            } else if (collectionMonth.getMonth() == 5) {
                const collected = res.amount
                months.june += parseInt(collected)
            } else if (collectionMonth.getMonth() == 6) {
                const collected = res.amount
                months.july += parseInt(collected)
            } else if (collectionMonth.getMonth() == 7) {
                const collected = res.amount
                months.august += parseInt(collected)
            } else if (collectionMonth.getMonth() == 8) {
                const collected = res.amount
                months.september += parseInt(collected)
            } else if (collectionMonth.getMonth() == 9) {
                const collected = res.amount
                months.october += parseInt(collected)
            } else if (collectionMonth.getMonth() == 10) {
                const collected = res.amount
                months.november += parseInt(collected)
            } else if (collectionMonth.getMonth() == 11) {
                const collected = res.amount
                months.december += parseInt(collected)
            }

        });

    })
    //categories: ['january', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
    // 'September', 'October', 'November', 'December'
    //],

    // console.log('Tsh ' + months.january + ' In January')

    chartData.push(months.january, months.february, months.march, months.april, months.may, months.june, months
        .july, months.august, months.september, months.october, months.november, months.december)





    function renderChart() {



        // console.log(chartData)
        var options = {
          series: [
            {
              name: 'Collection',
              data: chartData,
              color: '#ff9257',
            },
          ],
          chart: {
            type: 'bar',
            height: 350,
          },

          title: {
            text: 'Monthly Collection In All Activities',
            align: 'center',
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '50%',
              dataLabels: {
                position: 'top', // top, center, bottom
              },
            },
          },
          dataLabels: {
            enabled: true,
            formatter: function (val) {
              return 'Tsh ' + formatAmount(val);
            },
            offsetY: -20,
            style: {
              fontSize: '12px',
              colors: ['#333'],
            },
          },
          stroke: {
            show: true,
            width: 1,
            colors: ['transparent'],
          },
          xaxis: {
            categories: [
              'Jan',
              'Feb',
              'Mar',
              'Apr',
              'May',
              'Jun',
              'Jul',
              'Aug',
              'Sep',
              'Oct',
              'Nov',
              'Dec',
            ],
          },
          yaxis: {
            title: {
              text: 'Collection in Tsh',
            },
          },
          fill: {
            opacity: 1,
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return 'Tsh ' + formatAmount(val);
              },
            },
          },
        };

        var chart = new ApexCharts(document.querySelector("#dataChart"), options);
        chart.render();



    }

    renderChart()



}