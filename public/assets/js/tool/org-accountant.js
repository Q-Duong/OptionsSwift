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
            name: "accountant_status",
            value: $(".accountant_status_" + order_id).val(),
        },
        {
            name: "accountant_day_payment",
            value: $(
                'input[name="accountant_day_payment_' + order_id + '"]'
            ).val(),
        },
        {
            name: "accountant_method",
            value: $(".accountant_method_" + order_id).val(),
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
function getValuesFilter() {
    return [
        {
            name: "order_id",
            value: $(".order-id").val(),
        },
        {
            name: "status_id",
            value: $(".status-id").val(),
        },
        {
            name: "car_name",
            value: $(".car-name").val(),
        },
        {
            name: "unit_code",
            value: $(".unit-code").val(),
        },
        {
            name: "unit_name",
            value: $(".unit-name").val(),
        },
        {
            name: "ord_start_day",
            value: $(".ord-start-day").val(),
        },
        {
            name: "ord_cty_name",
            value: $(".ord-cty-name").val(),
        },
        {
            name: "ord_form",
            value: $(".ord-form").val(),
        },
        {
            name: "ord_note",
            value: $(".ord-note").val(),
        },
        {
            name: "accountant_month",
            value: $(".accountant-month").val(),
        },
        {
            name: "accountant_distance",
            value: $(".accountant-distance").val(),
        },
        {
            name: "accountant_deadline",
            value: $(".accountant-deadline").val(),
        },
        {
            name: "accountant_number",
            value: $(".accountant-number").val(),
        },
        {
            name: "accountant_date",
            value: $(".accountant-date").val(),
        },
        {
            name: "accountant_status",
            value: $(".accountant-status").val(),
        },
        {
            name: "accountant_day_payment",
            value: $(".accountant-day-payment").val(),
        },
        {
            name: "accountant_method",
            value: $(".accountant-method").val(),
        },
        {
            name: "accountant_amount_paid",
            value: $(".accountant-amount-paid").val(),
        },
        {
            name: "accountant_owe",
            value: $(".accountant-owe").val(),
        },
        {
            name: "accountant_discount_day",
            value: $(".accountant-discount-day").val(),
        },
        {
            name: "accountant_doctor_read",
            value: $(".accountant-doctor-read").val(),
        },
        {
            name: "accountant_doctor_date_payment",
            value: $(".accountant-doctor-date-payment").val(),
        },
        {
            name: "accountant_35X43",
            value: $(".accountant-35X43").val(),
        },
        {
            name: "accountant_polime",
            value: $(".accountant-polime").val(),
        },
        {
            name: "accountant_8X10",
            value: $(".accountant-8X10").val(),
        },
        {
            name: "accountant_10X12",
            value: $(".accountant-10X12").val(),
        },
        {
            name: "accountant_film_bag",
            value: $(".accountant-film-bag").val(),
        },
        {
            name: "accountant_note",
            value: $(".accountant-note").val(),
        },
        {
            name: "order_vat",
            value: $(".order-vat").val(),
        },
        {
            name: "order_quantity",
            value: $(".order-quantity").val(),
        },
        {
            name: "order_cost",
            value: $(".order-cost").val(),
        },
        {
            name: "order_price",
            value: $(".order-price").val(),
        },
        {
            name: "order_percent_discount",
            value: $(".order-percent-discount").val(),
        },
        {
            name: "order_discount",
            value: $(".order-discount").val(),
        },
        {
            name: "order_profit",
            value: $(".order-profit").val(),
        },
    ];
}
function getListAccountant(year, type) {
    $.ajax({
        url: url_get_accountant,
        method: "POST",
        async: true,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            year: year,
            type: type,
        },
        beforeSend: function () {},
    })
        .done(function (data) {
            $(".table-content").html(data.html);
            $("#total-price").text(
                new Intl.NumberFormat("vi-VN").format(data.totalPrice)
            );
            $("#total-owe").text(
                new Intl.NumberFormat("vi-VN").format(data.totalOwe)
            );
            $("#total-amount-paid").text(
                new Intl.NumberFormat("vi-VN").format(data.totalAmountPaid)
            );
            $("#total-quantity").text(
                new Intl.NumberFormat("vi-VN").format(data.totalQuantity)
            );
            $("#total-discount").text(
                new Intl.NumberFormat("vi-VN").format(data.totalDiscount)
            );
            $("#total-35").text(data.total35);
            $("#total-polime").text(data.totalPolime);
            $("#total-8").text(data.total8);
            $("#total-10").text(data.total10);
            $("#total-pack").text(data.totalPack);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            popupNotificationSessionExpired();
        })
        .complete(function () {
            $(".loader-over").fadeOut();
        });
}
$(document).ready(function () {
    setTimeout(function () {
        getListAccountant();
    }, 1000);
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
$(".year-filter, .type-filter").on("change", function () {
    $(".loader-over").fadeIn();
    getListAccountant($(".year-filter").val(), $(".type-filter").val());
});

$(document).on("change", ".accountant_status", function () {
    var value = $(this).val();
    if(value == 0){
        $(this).removeClass("acc-status-paid").addClass("acc-status-unpaid");
    }else{
        $(this).removeClass("acc-status-unpaid").addClass("acc-status-paid");
        $(this).closest("tr").removeClass("debt-overrated debt-warning");
    }
});

$(document).on(
    "change",
    ".order-id, .accountant-month, .ord-start-day, .car-name, .accountant-distance, .unit-code, .unit-name, .ord-cty-name, .accountant-deadline, .accountant-number, .accountant-date, .order-vat, .order-quantity, .order-cost, .order-price, .accountant-status, .accountant-day-payment, .accountant-method, .accountant-amount-paid, .accountant-owe, .order-percent-discount, .order-discount, .accountant-discount-day, .order-profit, .accountant-doctor-read, .accountant-doctor-date-payment, .ord-form, .accountant-35X43, .accountant-polime, .accountant-8X10, .accountant-10X12, .accountant-film-bag, .accountant-note, .ord-note, .status-id",
    function () {
        var data = getValuesFilter();
        $(".loader-over").fadeIn();
        $.ajax({
            url: url_filter_accountant,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: data,
        })
        .done(function (data) {
            data.flagEmpty
                    ? $(".clear-filter").addClass("hidden")
                    : $(".clear-filter").removeClass("hidden");
                $(".tbody-content").html(data.html);
                $("#total-price").text(
                    new Intl.NumberFormat("vi-VN").format(data.totalPrice)
                );
                $("#total-owe").text(
                    new Intl.NumberFormat("vi-VN").format(data.totalOwe)
                );
                $("#total-amount-paid").text(
                    new Intl.NumberFormat("vi-VN").format(data.totalAmountPaid)
                );
                $("#total-quantity").text(
                    new Intl.NumberFormat("vi-VN").format(data.totalQuantity)
                );
                $("#total-discount").text(
                    new Intl.NumberFormat("vi-VN").format(data.totalDiscount)
                );
                $("#total-35").text(data.total35);
                $("#total-polime").text(data.totalPolime);
                $("#total-8").text(data.total8);
                $("#total-10").text(data.total10);
                $("#total-pack").text(data.totalPack);
                $(".loader-over").fadeOut();
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            popupNotificationSessionExpired();
        })
        .complete(function () {
            $(".loader-over").fadeOut();
        });
    }
);

$(document).on("click", ".btn-clear-filter", function () {
    $(".loader-over").fadeIn();
    getListAccountant();
    $(".clear-filter").addClass("hidden");
});

$(document).on("click", ".completeAccount", function () {
    var order_id = $(this).data("id");
    var data = getValues(order_id);
    data.push({ name: "order_id", value: order_id });
    $(".loader-over").fadeIn();
    $.ajax({
        url: url_complete_accountant,
        method: "Patch",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: data,
    })
        .done(function (data) {
            $(".status_id_" + order_id).html(
                '<span style="color: #0071e3;">Đã xử lý</span>'
            );
            $(".update-account-" + order_id).html("");
            $(".loader-over").fadeOut();
            successMsg(data.success);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            popupNotificationSessionExpired();
        })
        .complete(function () {
            $(".loader-over").fadeOut();
        });
});

$(document).on("change", "input[type=text], .select-update", function () {
    var target = $(this).attr("name").split("_");
    var order_id = target.pop();
    var data = getValues(order_id);
    data.push(
        { name: "order_id", value: order_id },
        { name: "currentChange", value: target.join("_") }
    );
    $.ajax({
        url: url_update_accountant,
        method: "Patch",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: data,
    })
        .done(function (data) {
            if (typeof data.html != "undefined" && data.html !== null) {
                console.log(data.multi);
                if (data.multi) {
                    $("." + data.className).html(data.html);
                    $("." + data.subClassName).html(data.subHtml);
                } else {
                    $("." + data.className).html(data.html);
                }
            }
            $(".status_id_" + order_id).html(
                '<span style="color: #00d0e3;">Đã cập nhật doanh thu</span>'
            );
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            popupNotificationSessionExpired();
        })
        .complete(function () {
            $(".loader-over").fadeOut();
        });
});
