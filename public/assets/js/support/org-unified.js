$(document).ready(function () {
    $("#pagination-input").on("input", function () {
        var value = $(this).val();
        $(this).val(value.replace(/[^0-9]/g, ""));
    });
    $("#pagination-input").keypress(function (event) {
        var inputPage = $(this).val();
        var url = window.location.href;
        if (event.which === 13 && inputPage != "" && inputPage <= lastPage) {
            if (inputPage == 1) {
                url = url.replace(/(\?page=)[^\&]+/, "");
            } else {
                if (url.indexOf("?page=") !== -1) {
                    url = url.replace(/(\?page=)[^\&]+/, "$1" + inputPage);
                } else {
                    url +=
                        (url.indexOf("?") !== -1 ? "&" : "?") +
                        "page=" +
                        inputPage;
                }
            }
            location.replace(url);
        }
    });
    $(".pagination-ctrl__btn--previous").click(function (event) {
        if (currentPage == 2) {
            event.preventDefault();
            location.replace(window.location.href.replace(/(\?page=)[^\&]+/, ""));
        }
    });
});
