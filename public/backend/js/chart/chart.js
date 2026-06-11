"use strict";

(function ($) {
    $(document).ready(function() {
        var chart = new Morris.Bar({
            element: 'chart',
            //option chart
            lineColors: ['#819C79', '#fc8710', '#FF6541', '#A4ADD3', '#6932a8'],
            parseTime: false,
            stacked: true,
            axes: true,
            grid: true,
            hideHover: 'auto',
            xkey: 'period',
            ykeys: ['order', 'sales', 'quantity'],
            labels: ['Đơn hàng', 'Doanh số', 'Số lượng'],
            resize: true
        });

        revenueStatisticsForTheMonth();

        function revenueStatisticsForTheMonth() {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: url_revenue_statistics_for_the_month,
                method: "POST",
                dataType: "JSON",
                data: {
                    _token: _token
                },
                success: function(data) {
                    if (data.success) {
                        chart.setData(data.chart);
                        $('.total').text(new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        }).format(data.total));
                        $('.total_orders').text(data.total_orders + " đơn");
                        $('.filter-text').text('Tháng ' + data.month);
                        $('.chart-text').text('Tháng ' + data.month);
                    } else {
                        errorMsg('Không có dữ liệu');
                    }
                }
            });
        }

        $('.optional-revenue').change(function() {
            var optional_revenue = $(this).val();
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: url_optional_revenue_statistics,
                method: "POST",
                dataType: "JSON",
                data: {
                    optional_revenue: optional_revenue,
                    _token: _token
                },
                success: function(data) {
                    if (data.success) {
                        chart.setData(data.chart);
                        $('.total').text(new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        }).format(data.total));
                        $('.total_orders').text(data.total_orders + " đơn");
                        $('.filter-text').text(data.option);
                        $('.chart-text').text(data.option);
                    } else {
                        errorMsg('Không có dữ liệu');
                    }
                }
            });
        });

        $('.btn-revenue-by-date').click(function() {
            var _token = $('input[name="_token"]').val();
            var from_date = $('input[name="from_date"]').val();
            var to_date = $('input[name="to_date"]').val();
            $.ajax({
                url: url_revenue_statistics_by_date,
                method: "POST",
                dataType: "JSON",
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    _token: _token
                },
                success: function(data) {
                    if (data.success) {
                        chart.setData(data.chart);
                        $('.total').text(new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        }).format(data.total));
                        $('.total_orders').text(data.total_orders + " đơn");
                        $('.filter-text').text('Từ ngày ' + data.from_date +
                            ' --- Đến ngày ' + data.to_date);
                        $('.chart-text').text('Từ ngày ' + data.from_date +
                            ' --- Đến ngày ' + data.to_date);
                    } else {
                        errorMsg('Không có dữ liệu');
                    }
                }
            });
        });


        $('.btn-unit-revenue').click(function() {
            
            var _token = $('input[name="_token"]').val();
            var unit_id = $('.unit_id').val();
            var unit_name = $(".unit_id option:selected").text();
            $.ajax({
                url: url_revenue_statistics_by_unit,
                method: "POST",
                dataType: "JSON",
                data: {
                    unit_id: unit_id,
                    _token: _token
                },
                success: function(data) {
                    if (data.success) {
                        chart.setData(data.chart);
                        $('.total').text(new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        }).format(data.total));
                        $('.total_orders').text(data.total_orders + " đơn");
                        $('.filter-text').text('Đơn vị ' + unit_name);
                        $('.chart-text').text('Đơn vị ' + unit_name);
                    } else {
                        errorMsg('Không có dữ liệu');
                    }
                }
            });
        });
    });
})(jQuery);
