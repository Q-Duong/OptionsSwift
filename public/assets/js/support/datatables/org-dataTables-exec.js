$(".example thead tr").clone(true).appendTo(".example thead");

    // add a text input filter to each column of the new row
    $(".example thead tr:eq(1) th").each(function(i) {
        var title = $(this).text();
        $(this).html(
            '<input type="text" id="' +
            i +
            '" class="form-control search_target' +
            i +
            '" placeholder="' +
            title +
            '"/>'
        );
        $("input", this).on("keyup change", function() {
            if ($(".example").DataTable().column(i).search() !== $(this).val()) {
                $(".example").DataTable().column(i).search($(this).val()).draw();
            }
        });
    });

    $('#myTableScroll').DataTable({
        "scrollX": true,
        "orderCellsTop": true,
        // "processing": true,
        "deferRender": true,
        columns: [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            // {
            //     orderDataType: 'dom-text-numeric',
            //     type: "numeric"
            // },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text-numeric',
                type: "numeric"
            },
            {
                orderDataType: 'dom-text',
                type: 'string'
            },
            null,
            null,
            null,
            null,
        ],
    });