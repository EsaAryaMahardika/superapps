$(document).ready(function () {
    $(document).on("change", "#a_sta", function () {
        var status = $(this).val();
        if (status === "M") {
            $(".ayah").prop("disabled", true);
        } else {
            $(".ayah").prop("disabled", false);
        }
    });
    $(document).on("change", "#i_sta", function () {
        var status = $(this).val();
        if (status === "M") {
            $(".ibu").prop("disabled", true);
        } else {
            $(".ibu").prop("disabled", false);
        }
    });
    $(document).on("change", "#w_sta", function () {
        var status = $(this).val();
        if (status === "tidak") {
            $(".wali").prop("disabled", true);
        } else {
            $(".wali").prop("disabled", false);
        }
    });
    $(document).on("change", "#prov", function () {
        var prov_id = $(this).val();
        if (prov_id) {
            $.ajax({
                url: BASE_URL + "/kab/" + prov_id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#kab").empty();
                    $.each(data, function (key, value) {
                        $("#kab").append(
                            '<option value="' + key + '">' + value + "</option>"
                        );
                    });
                },
            });
        } else {
            $("#kab").empty();
        }
    });
    $(document).on("change", "#kab", function () {
        var kab_id = $(this).val();
        if (kab_id) {
            $.ajax({
                url: BASE_URL + "/kec/" + kab_id,
                type: "GET",
                success: function (data) {
                    $("#kec").empty();
                    $.each(data, function (key, value) {
                        $("#kec").append(
                            '<option value="' + key + '">' + value + "</option>"
                        );
                    });
                },
            });
        } else {
            $("#kec").empty();
        }
    });
    $(document).on("change", "#kec", function () {
        var kec_id = $(this).val();
        if (kec_id) {
            $.ajax({
                url: BASE_URL + "/kel/" + kec_id,
                type: "GET",
                success: function (data) {
                    $("#des").empty();
                    $.each(data, function (key, value) {
                        $("#des").append(
                            '<option value="' + key + '">' + value + "</option>"
                        );
                    });
                },
            });
        } else {
            $("#des").empty();
        }
    });
    $(".close").click(function () {
        $(this).parent(".alert").fadeOut();
    });

    // Dark Mode
    const $htmlElement = $("html");
    const $switchElement = $("#darkModeSwitch");
    const prefersDarkScheme = window.matchMedia(
        "(prefers-color-scheme: dark)"
    ).matches;
    const currentTheme =
        localStorage.getItem("bsTheme") || (prefersDarkScheme ? "dark" : "light");
    $htmlElement.attr("data-bs-theme", currentTheme);
    $switchElement.prop("checked", currentTheme === "dark");
    $switchElement.on("change", function () {
        const newTheme = this.checked ? "dark" : "light";
        $htmlElement.attr("data-bs-theme", newTheme);
        localStorage.setItem("bsTheme", newTheme);
    });
    $('[data-bs-toggle="tooltip"]').each(function () {
        new bootstrap.Tooltip(this);
    });

    // Untuk input hanya angka
    $(document).on("keypress", ".number", function () {
        return event.which >= 48 && event.which <= 57;
    });
    $('.santri, .pengurus').select2({
        dropdownParent: $('.modal'),
        width: '100%',
        ajax: {
            url: function() {
                return $(this).hasClass('santri') ? '/santri' : '/pengurus';
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults:data=>{   
                return {
                    results:data.map(res=>{
                        return {text:res.nama,id:res.nis}
                    })
                }
            },
            cache: true
        }
    });
});