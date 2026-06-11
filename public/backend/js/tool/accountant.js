var typingTimer;
var doneTypingInterval = 600;

function quantityFunction(event) {
    var name = event.target["name"];
    $("input[name=" + name + "]").on({
        keyup: function () {
            formatQuantity($(this));
        },
        input: function () {
            var split = name.split("_");
            var id = split[2];
            var order_quantity = $("input[name=" + name + "]").val();
            var order_cost = $(".order_cost_" + id).val();
            if (order_quantity != "" && order_cost != "") {
                var order_cost_format = order_cost
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var total =
                    parseInt(order_quantity) * parseInt(order_cost_format);
                var order_price = new Intl.NumberFormat("vi-VN").format(total);
                $(".order_price_" + id).val(order_price);
            } else {
                // $('.order_cost_' + id).val(0);
                $(".order_price_" + id).val(0);
            }
        },
    });
}
function costFunction(event) {
    var name = event.target["name"];

    $("input[name=" + name + "]").on({
        keyup: function () {
            formatCurrency($(this));
        },
        blur: function () {
            formatCurrency($(this), "blur");
        },
        input: function () {
            var split = name.split("_");
            var id = split[2];
            var order_quantity = $(".order_quantity_" + id).val();
            var order_cost = $("input[name=" + name + "]").val();
            if (order_quantity != "" && order_cost != "") {
                var order_cost_format = order_cost
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var total =
                    parseInt(order_quantity) * parseInt(order_cost_format);
                var order_price = new Intl.NumberFormat("vi-VN").format(total);
                $(".order_price_" + id).val(order_price);
            } else {
                $(".order_price_" + id).val(0);
            }
            var order_cost_replace = order_cost
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
            var order_cost_format = new Intl.NumberFormat("vi-VN").format(
                order_cost_replace
            );
            $(".order_cost_" + id).val(order_cost_format);
        },
    });
}
function priceFunction(event) {
    var name = event.target["name"];

    $("input[name=" + name + "]").on({
        keyup: function () {
            formatCurrency($(this));
        },
        blur: function () {
            formatCurrency($(this), "blur");
        },
        input: function () {
            var split = name.split("_");
            var id = split[2];
            var order_price = $(this).val();
            var order_price_replace = order_price
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
            var order_price_format = new Intl.NumberFormat("vi-VN").format(
                order_price_replace
            );
            $(".order_price_" + id).val(order_price_format);
        },
    });
}
function amountPaidFunction(event) {
    var name = event.target["name"];

    $("input[name=" + name + "]").on({
        keyup: function () {
            formatCurrency($(this));
        },
        blur: function () {
            formatCurrency($(this), "blur");
        },
        input: function () {
            var split = name.split("_");
            var id = split[3];
            var order_price = $(".order_price_" + id).val();
            var amount_paid = $(this).val();
            if (amount_paid != "" && order_price != "") {
                var order_price_format = order_price
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var amount_paid_format = amount_paid
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var owe =
                    parseInt(order_price_format) - parseInt(amount_paid_format);
                var owe_format = new Intl.NumberFormat("vi-VN").format(owe);
                $(".accountant_owe_" + id).val(owe_format);
            } else {
                $(".accountant_owe_" + id).val(order_price);
            }
            var amount_paid_replace = amount_paid
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
            var amount_paid_format = new Intl.NumberFormat("vi-VN").format(
                amount_paid_replace
            );
            $(".accountant_amount_paid_" + id).val(amount_paid_format);
        },
    });
}
function discountFunction(event) {
    var name = event.target["name"];
    var split = name.split("_");
    var id = split[2];
    $("input[name=" + name + "]").on({
        keyup: function () {
            formatCurrency($(this));
        },
        blur: function () {
            formatCurrency($(this), "blur");
        },
        input: function () {
            var order_price = $(".order_price_" + id).val();
            var discount = $(this).val();
            var order_price_format = order_price
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");

            if (discount != "" && order_price != "") {
                var discount_format = discount
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var order_profit =
                    parseInt(order_price_format) - parseInt(discount_format);
                var order_profit_format = new Intl.NumberFormat("vi-VN").format(
                    order_profit
                );
                $(".order_profit_" + id).val(order_profit_format);
            } else {
                $(".order_profit_" + id).val(order_price);
            }
            var discount_replace = discount
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
            var discount_format = new Intl.NumberFormat("vi-VN").format(
                discount_replace
            );
            $(".order_discount_" + id).val(discount_format);
        },
        click: function () {
            var order_price = $(".order_price_" + id).val();
            var discount = $(this).val();
            var order_price_format = order_price
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");

            if (discount != "" && order_price != "") {
                var discount_format = discount
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var order_profit =
                    parseInt(order_price_format) - parseInt(discount_format);
                var order_profit_format = new Intl.NumberFormat("vi-VN").format(
                    order_profit
                );
                $(".order_profit_" + id).val(order_profit_format);
            } else {
                $(".order_profit_" + id).val(order_price);
            }
            var discount_replace = discount
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
            var discount_format = new Intl.NumberFormat("vi-VN").format(
                discount_replace
            );
            $(".order_discount_" + id).val(discount_format);
        },
    });
}
function ordFormFunction(event) {
    var name = event.target["name"];
    var split = name.split("_");
    var id = split[2];
    $("input[name=" + name + "]").on("keyup change", function () {
        var ordForm = $(this).val();
        var accountant_35X43 = $(".accountant_35X43_" + id).val();
        var order_quantity = $(".order_quantity_" + id).val();

        if (ordForm == "ko in") {
            if (accountant_35X43 == "") {
                var accountant_35X43_format = 0;
            } else {
                var accountant_35X43_format = accountant_35X43;
            }
            var accountant_film_bag = parseInt(accountant_35X43_format) * 4;
            $(".accountant_film_bag_" + id).val(accountant_film_bag);
        } else {
            $(".accountant_film_bag_" + id).val(order_quantity);
        }
    });
}
function accountant35X43Function(event) {
    var name = event.target["name"];
    var split = name.split("_");
    var id = split[2];

    $("input[name=" + name + "]").on("keyup change", function () {
        var accountant_35X43 = $(this).val();
        var ordForm = $(".ord_form_" + id).val();
        var order_quantity = $(".order_quantity_" + id).val();
        if (ordForm == "ko in") {
            if (accountant_35X43 != "") {
                var accountant_film_bag = parseInt(accountant_35X43) * 4;
                $(".accountant_film_bag_" + id).val(accountant_film_bag);
            } else {
                $(".accountant_film_bag_" + id).val(0);
            }
        } else {
            $(".accountant_film_bag_" + id).val(order_quantity);
        }
    });
}
function deadlineFunction(event) {
    var name = event.target["name"];
    var split = name.split("_");
    var id = split[2];

    $("input[name=" + name + "]").on("keyup change click", function () {
        var deadline = parseInt($(this).val());
        var date = $(".accountant_date_" + id).val();
        if (date != "") {
            var date1 = date.split("/");
            var date_format = date1[2] + "-" + date1[1] + "-" + date1[0];
            const day = new Date(date_format);
            const day_format = day.getDate() + deadline;
            day.setDate(day_format);
            var today = day
                .toLocaleDateString("en-GB", {
                    day: "numeric",
                    month: "numeric",
                    year: "numeric",
                })
                .split(" ")
                .join("-");
            $(".accountant_payment_" + id).val(today);
        } else {
            $(".accountant_payment_" + id).val("");
        }
    });
}
function dateFunction(event) {
    var name = event.target["name"];
    var split = name.split("_");
    var id = split[2];

    $("input[name=" + name + "]").on("keyup change", function () {
        var date = $(this).val();
        var deadline = $(".accountant_deadline_" + id).val();
        if (deadline != "" && date.length >= 10) {
            var date1 = date.split("/");
            var date_format = date1[2] + "-" + date1[1] + "-" + date1[0];
            const day = new Date(date_format);
            const day_format = day.getDate() + parseInt(deadline);
            day.setDate(day_format);
            var today = day
                .toLocaleDateString("en-GB", {
                    day: "numeric",
                    month: "numeric",
                    year: "numeric",
                })
                .split(" ")
                .join("-");
            $(".accountant_payment_" + id).val(today);
        } else {
            $(".accountant_payment_" + id).val("");
        }
    });
}
function getValues(order_id) {
    return [
        {
            name: "accountant_id",
            value: $('input[name="accountant_id_' + order_id + '"]').val(),
        },
        {
            name: "accountant_deadline",
            value: $(
                'input[name="accountant_deadline_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_number",
            value: $('input[name="accountant_number_' + order_id + '"]').val(),
        },
        {
            name: "accountant_date",
            value: $('input[name="accountant_date_' + order_id + '"]').val(),
        },
        {
            name: "order_vat",
            value: $('input[name="order_vat_' + order_id + '"]').val(),
        },
        {
            name: "order_quantity",
            value: $('input[name="order_quantity_' + order_id + '"]').val(),
        },
        {
            name: "order_cost",
            value: $('input[name="order_cost_' + order_id + '"]').val(),
        },
        {
            name: "order_price",
            value: $('input[name="order_price_' + order_id + '"]').val(),
        },
        {
            name: "accountant_payment",
            value: $('input[name="accountant_payment_' + order_id + '"]').val(),
        },
        {
            name: "accountant_day_payment",
            value: $(
                'input[name="accountant_day_payment_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_method",
            value: $('input[name="accountant_method_' + order_id + '"]').val(),
        },
        {
            name: "accountant_amount_paid",
            value: $(
                'input[name="accountant_amount_paid_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_owe",
            value: $('input[name="accountant_owe_' + order_id + '"]').val(),
        },
        {
            name: "order_percent_discount",
            value: $(
                'input[name="order_percent_discount_' + order_id + '"]'
            ).val(),
        },
        {
            name: "order_discount",
            value: $('input[name="order_discount_' + order_id + '"]').val(),
        },
        {
            name: "accountant_discount_day",
            value: $(
                'input[name="accountant_discount_day_' + order_id + '"]'
            ).val(),
        },
        {
            name: "order_profit",
            value: $('input[name="order_profit_' + order_id + '"]').val(),
        },
        {
            name: "accountant_doctor_read",
            value: $(
                'input[name="accountant_doctor_read_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_doctor_date_payment",
            value: $(
                'input[name="accountant_doctor_date_payment_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_35X43",
            value: $('input[name="accountant_35X43_' + order_id + '"]').val(),
        },
        {
            name: "accountant_polime",
            value: $('input[name="accountant_polime_' + order_id + '"]').val(),
        },
        {
            name: "accountant_8X10",
            value: $('input[name="accountant_8X10_' + order_id + '"]').val(),
        },
        {
            name: "accountant_10X12",
            value: $('input[name="accountant_10X12_' + order_id + '"]').val(),
        },
        {
            name: "accountant_film_bag",
            value: $(
                'input[name="accountant_film_bag_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_note",
            value: $('input[name="accountant_note_' + order_id + '"]').val(),
        },
    ];
}
function getListAccountant() {
    
    $.ajax({
        url: urlGetAccountant,
        method: "POST",
        async: true,
        data: {
            _token: _token,
        },
        beforeSend: function () {
            
        },
    })
        .then(function (data) {
            $(".table-content").html(data.html);
        })
        .always(function () {
            $(".loader").fadeOut();
            $("#preloder").fadeOut("slow");
        });
}
$(document).ready(function () {
    $(".loader").fadeIn();
    $("#preloder").fadeIn("slow");
    setTimeout(function() {getListAccountant()}, 500);
});
$(".order_profit").on({
    keyup: function () {
        formatCurrency($(this));
    },
    blur: function () {
        formatCurrency($(this), "blur");
    },
    input: function () {
        var order_profit = $(this).val();
        if (order_profit == "") {
            var order_profit_format = 0;
            $(".order_profit").val(order_profit_format);
        } else {
            var order_profit_format = order_profit
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
        }
    },
});

$(document).on("click", ".updateAccount", function () {
    var order_id = $(this).data("id");
    var data = getValues(order_id);
    data.push(
        { name: "order_id", value: order_id },
        { name: "_token", value: _token }
    );
    urlUpdateAccountant = urlUpdateAccountant.replace(":id", order_id);
    $(".loader").fadeIn();
    $("#preloder").fadeIn("slow");
    $.ajax({
        url: urlUpdateAccountant,
        method: "POST",
        data: data,
        success: function (data) {
            urlUpdateAccountant = urlUpdateAccountant.replace(order_id, ":id");
            $(".order_status_" + order_id).html(
                '<span style="color: #00d0e3;">Đã cập nhật doanh thu</span>'
            );
            $(".loader").fadeOut();
            $("#preloder").fadeOut("slow");
            successMsg(data.success);
        },
    });
});

$(document).on("click", ".completeAccount", function () {
    var order_id = $(this).data("id");
    var data = getValues(order_id);
    data.push(
        { name: "order_id", value: order_id },
        { name: "_token", value: _token }
    );
    urlCompleteAccountant = urlCompleteAccountant.replace(":id", order_id);
    $(".loader").fadeIn();
    $("#preloder").fadeIn("slow");
    $.ajax({
        url: urlCompleteAccountant,
        method: "POST",
        data: data,
        success: function (data) {
            urlCompleteAccountant = urlCompleteAccountant.replace(
                order_id,
                ":id"
            );
            $(".order_status_" + order_id).html(
                '<span style="color: #0071e3;">Đã xử lý</span>'
            );
            $(".update-account-" + order_id).html("");
            $(".loader").fadeOut();
            $("#preloder").fadeOut("slow");
            successMsg(data.success);
        },
    });
});

$(document).on(
    "keyup",
    ".search_target1, .search_target5, .search_target6, .search_target7",
    function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    }
);

$(document).on(
    "keydown",
    ".search_target1, .search_target5, .search_target6, .search_target7",
    function () {
        clearTimeout(typingTimer);
    }
);

function doneTyping() {
    var _token = $('input[name="_token"]').val();
    var month = $(".search_target1").val();
    var unitCode = $(".search_target5").val();
    var unitName = $(".search_target6").val();
    var ctyName = $(".search_target7").val();
    $(".loader").fadeIn();
    $("#preloder").fadeIn("slow");
    $.ajax({
        url: urlFilterAccountant,
        method: "POST",
        data: {
            _token: _token,
            month: month,
            unitCode: unitCode,
            unitName: unitName,
            ctyName: ctyName,
        },
        success: function (data) {
            var total_price = new Intl.NumberFormat("vi-VN").format(
                data.total_price
            );
            var total_owe = new Intl.NumberFormat("vi-VN").format(
                data.total_owe
            );
            var total_amount_paid = new Intl.NumberFormat("vi-VN").format(
                data.total_amount_paid
            );
            var total_quantity = new Intl.NumberFormat("vi-VN").format(
                data.total_quantity
            );
            var total_discount = new Intl.NumberFormat("vi-VN").format(
                data.total_discount
            );
            $("#total-price").text(total_price);
            $("#total-owe").text(total_owe);
            $("#total-amount-paid").text(total_amount_paid);
            $("#total-quantity").text(total_quantity);
            $("#total-discount").text(total_discount);
            $(".loader").fadeOut();
            $("#preloder").fadeOut("slow");
        },
    });
}
