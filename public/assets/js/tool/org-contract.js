var typingTimer;
var doneTypingInterval = 600;

function getValues(id) {
    return [
        {
            name: "accountant_number",
            value: $('input[name="accountant_number_' + id + '"]').val(),
        },
        {
            name: "liquidation_number",
            value: $('input[name="liquidation_number_' + id + '"]').val(),
        },
        {
            name: "contract_type",
            value: $(".contract_type_" + id).val(),
        },
        {
            name: "contract_date",
            value: $('input[name="contract_date_' + id + '"]').val(),
        },
        {
            name: "contract_status",
            value: $(".contract_status_" + id).val(),
        },
    ];
}
function getValuesFilter() {
    return [
        {
            name: "unit_name",
            value: $(".unit-name").val(),
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
            name: "liquidation_number",
            value: $(".liquidation-number").val(),
        },
        {
            name: "contract_type",
            value: $(".contract-type").val(),
        },
        {
            name: "contract_date",
            value: $(".contract-date").val(),
        },
        {
            name: "contract_status",
            value: $(".contract-status").val(),
        },
    ];
}
function getListContract(year) {
    $.ajax({
        url: url_get_contract,
        method: "POST",
        async: true,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            year: year,
        },
        beforeSend: function () {},
    })
        .done(function (data) {
            $(".table-content").html(data.html);
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
        getListContract();
    }, 1000);
});

$(".year-filter").on("change", function () {
    $(".loader-over").fadeIn();
    getListContract($(".year-filter").val());
});

$(document).on("change", ".contract_status", function () {
    var status = $(this).val();
    $(this).removeClass(
        "contract-status-1 contract-status-2 contract-status-3"
    );
    if (status == 1) {
        $(this).addClass("contract-status-1");
    } else if (status == 2) {
        $(this).addClass("contract-status-2");
    } else if (status == 3) {
        $(this).addClass("contract-status-3");
    }
});

$(document).on(
    "change",
    ".unit-name, .accountant-number, .accountant-date, .liquidation-number, .contract-type, .contract-date, .contract-status",
    function () {
        var data = getValuesFilter();
        $(".loader-over").fadeIn();
        $.ajax({
            url: url_filter_contract,
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
    getListContract();
    $(".clear-filter").addClass("hidden");
});

$(document).on("change", "input[type=text], .select-update", function () {
    var target = $(this).attr("name").split("_");
    var id = target.pop();
    var data = getValues(id);
    data.push(
        { name: "id", value: id },
        { name: "currentChange", value: target.join("_") }
    );
    $.ajax({
        url: url_update_contract,
        method: "Patch",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: data,
    })
        .done(function (data) {
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            popupNotificationSessionExpired();
        })
        .complete(function () {
            $(".loader-over").fadeOut();
        });
});
