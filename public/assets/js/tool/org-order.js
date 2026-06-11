
(function ($) {
    $(document).on('click', '.delete_file', function() {
        var path = $(this).data("path");
        var token = $('input[name="_token"]').val();
        var order_detail_id = $('input[name="order_detail_id"]').val();
        url_delete_file_order = url_delete_file_order.replace(':path', path);
        if (confirm('Bạn muốn xoá file này không?')) {
            $(".loader").fadeIn();
            $("#preloder").fadeIn("slow");
            $.ajax({
                url: url_delete_file_order,
                method: "DELETE",
                data: {
                    "path": path,
                    "_token": token,
                    "order_detail_id": order_detail_id
                },
                success: function(data) {
                    $('.file_name').hide('');
                    $('.fied_file').show();
                    $('.dowload_file').hide('');
                    $('.delete_file').hide();
                    $(".loader").fadeOut();
                    $("#preloder").fadeOut("slow");
                }
            });
        }
    });

    $('.order_quantity').on({
        keyup: function() {
            formatQuantity($(this));
        },
        input: function() {
            var order_quantity = $(this).val();
            var order_cost = $('.order_cost').val();
            if (order_quantity != '' && order_cost != '') {
                var order_cost_format = order_cost.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var total = parseInt(order_quantity) * parseInt(order_cost_format);
                var order_price = new Intl.NumberFormat('vi-VN').format(total);
                $('.order_price').val(order_price);
            } else {
                $('.order_price').val('');
            }
        }
    });

    $('.order_cost').on({
        keyup: function() {
            formatCurrency($(this));
        },
        blur: function() {
            formatCurrency($(this), "blur");
        },
        input: function() {
            var order_quantity = $('.order_quantity').val();
            var order_cost = $(this).val();
            if (order_quantity != '' && order_cost != '') {
                var order_cost_format = order_cost.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "");
                var total = parseInt(order_quantity) * parseInt(order_cost_format);
                var order_price = new Intl.NumberFormat('vi-VN').format(total);
                $('.order_price').val(order_price);
            } else {
                $('.order_price').val('');
            }
        }
    });

    $('.order_price').on({
        keyup: function() {
            formatCurrency($(this));
        },
        blur: function() {
            formatCurrency($(this), "blur");
        },
        input: function() {
            var order_price = $(this).val();
            var order_price_format = order_price.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "");
        }
    });

    $('.order_all_in_one').click(function() {
        var value = $(this).val();
        if (value == 0) {
            $('.block-order-cost').removeClass('hidden');
        } else {
            $('.block-order-cost').addClass('hidden');
        }
    });
})(jQuery);
