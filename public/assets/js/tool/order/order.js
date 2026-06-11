
$(".order_cost").on({
    keyup: function () {
        formatCurrency($(this));
    },
    blur: function () {
        formatCurrency($(this), "blur");
    },
    input: function () {
        var order_quantity = $(".order_quantity").val();
        var order_cost = $(this).val();
        if (order_quantity != "" && order_cost != "") {
            var order_cost_format = order_cost
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, "");
            var total = parseInt(order_quantity) * parseInt(order_cost_format);
            var order_price = new Intl.NumberFormat("vi-VN").format(total);
            $(".order_price").val(order_price);
        } else {
            $(".order_price").val("");
        }
    },
});

$(".order_price").on({
    keyup: function () {
        formatCurrency($(this));
    },
    blur: function () {
        formatCurrency($(this), "blur");
    },
    input: function () {
        var order_price = $(this).val();
        var order_price_format = order_price
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, "");
    },
});

$(".order_all_in_one").click(function () {
    var value = $(this).val();
    if (value == 0) {
        $(".block-order-cost").removeClass("hidden");
    } else {
        $(".block-order-cost").addClass("hidden");
    }
});

$(document).on("change", ".unit-id", function () {
    var value = $(this).val();
    if (value == 5) {
        $(".suggest").removeClass("hidden");
    } else{
        $(".suggest").addClass("hidden");
    }
});