var show_modal = function (state, modal) {
    document.getElementById(modal).style.display = state;
    document.getElementById('filter').style.display = state;
};

$(document).ready (function () {
    $("button[name=delete]").click( function(event) {
        event.preventDefault();
        var val=document.activeElement.getAttribute('value');
        $.ajax ({
            url: "delete_consultation.php",
            type: "POST",
            data: ({value:val}),
            dataType:"html",
            success: function funcSuccess(data) {
                show_modal('block', 'deleteForm');
                $("#deletionDIV").html(data);
            }
        });
    });

    $("button[name=delete_OK]").click( function(event) {
        event.preventDefault();
        var val=document.activeElement.getAttribute('value');
        $.ajax ({
            url: "delete_consultation.php",
            type: "POST",
            data: ({value:val, delete:'ok'}),
            dataType:"html",
            success: function funcSuccessOk(data) {
                $("#deletionDIV").html(data);
            }
        });
    });
});

$(document).ready (function () {
    $("button[name=add]").click( function(event) {
        event.preventDefault();
        $.ajax ({
            url: "delete_consultation.php",
            type: "POST",
            success: function funcSuccess() {
                show_modal('block', 'addForm');
            }
        });
    });
});


