$(document).ready(function () {
    $('#form').validate({ // initialize the plugin
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            number: {
                required: true,
                digits: true
                
            },
            minlength: {
                required: true,
                minlength: 5
                
            },
            maxlength: {
                required: true,
                maxlength: 8
                
            },
            minvalue: {
                required: true,
                min: 1
                
            },
            maxvalue: {
                required: true,
                max: 100
                
            },
            range: {
                required: true,
                range: [20, 40]
                
            },
            url: {
            required: true,
            url: true
            },
            filename: {
                required: true,
                extension: "jpeg|png"
            },
        }
    });
});