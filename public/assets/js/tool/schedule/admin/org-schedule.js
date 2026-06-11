function scheduleCancel(e) {
    var car_name = e.target.id;
    var order_id = $('input[name="order_id"]').val();
    $(".loader-over").fadeIn();
    $.ajax({
        url: url_schedule_cancel,
        method: "POST",
        data: {
            car_name: car_name,
            order_id: order_id,
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: function(data) {
            successMsg(data.success);
            $(".loader-over").fadeOut();
            window.setTimeout(function() {
                location.reload();
            }, 1000);
        }
    });
}
function handleSchedule(e) {
    var checkBox = document.getElementById("checkCar" + e);
    var block = document.getElementById("car" + e);

    if (checkBox.checked == true) {
        block.style.display = "block";
        $('#carActive' + e).val(1);

    } else {
        block.style.display = "none";
        $('#carActive' + e).val(0);
        const $select1 = document.querySelector('#select_driver_' + e);
        const $select2 = document.querySelector('#select_car_' + e);
        const $select3 = document.querySelector('#select_car_' + (e+7));
        $select1.value = '';
        $select2.value = '';
        $select3.value = '';
    }
}