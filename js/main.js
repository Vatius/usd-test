$('[data-toggle="datepicker"]').datepicker({
    language: 'ru-RU',
    format: 'dd/mm/yyyy',
    autoHide: true
});

$("button").click(function() {
    $.ajax({
        url: "php/some.php",
        data: "date="+$("#somedate").val(),
        success: function(data) {
            $("#result").html('1 USD = ' + data);
        },
        error: function() {
            alert("Возникла ошибка на сервере");
        }
    });
});