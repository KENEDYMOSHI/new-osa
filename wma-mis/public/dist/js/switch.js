$(document).ready(function () {
    let check = $('#status').val()
    if (check == 'Paid') {
        $("#status").prop("checked", true);
    } else if (check == 'Pending') {
        $("#status").prop("checked", false);
    }

    $('#status').bootstrapToggle({
        on: 'Paid',
        off: 'Pending',
        onstyle: 'success',
        offstyle: 'warning'
    });

    $('#customSwitch').change(function (e) {
        e.preventDefault();
        alert('Boom')
        // if ($(this).prop('checked')) {
        //     $('#task').val('Paid');
        // } else {
        //     $('#task').val('Pending');
        // }
        let demo = $('#textBox').val()
        $.ajax({
            // url: "http://localhost/demo/index",
            method: "POST",
            data: {
                payment: demo
            },
            success: function (data) {
                console.log(data);

            }
        });
    });



});