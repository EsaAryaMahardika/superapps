$(document).ready(function () {
    $(document).on("click", "#print", function () {
        var element = $("#content")[0];
        var filename = "Data Pendaftar PSB An-Nur II.pdf";
        html2pdf(element, {
            margin: 20,
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        });
    });
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
    
    $(document).on("change", "#s_prov", function () {
        var prov_id = $(this).val();
        if (prov_id) {
            $.ajax({
                url: BASE_URL + "/s_kab/" + prov_id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#s_kab").empty();
                    $.each(data, function (key, value) {
                        $("#s_kab").append(
                            '<option value="' + key + '">' + value + "</option>"
                        );
                    });
                },
                error: function (xhr, status, error) {
                alert("Terjadi kesalahan saat memuat data kabupaten: " + error);
            },
            });
        } else {
            $("#s_kab").empty();
            alert("Terjadi kesalahan saat memuat data kabupaten: " + error);
        }
    });
    $(document).on("change", "#s_kab", function () {
        var kab_id = $(this).val();
        if (kab_id) {
            $.ajax({
                url: BASE_URL + "/s_kec/" + kab_id,
                type: "GET",
                success: function (data) {
                    $("#s_kec").empty();
                    $.each(data, function (key, value) {
                        $("#s_kec").append(
                            '<option value="' + key + '">' + value + "</option>"
                        );
                    });
                },
            });
        } else {
            $("#s_kec").empty();
        }
    });
    $(".close").click(function () {
        $(this).parent(".alert").fadeOut();
    });
}),

// Pindah halaman signin ke signup dan sebaliknya
$('a[href="#"]').on("click", function (e) {
    e.preventDefault();
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

$(function () {
    $("#form-total").steps({
        headerTag: "h2",
        bodyTag: "section",
        transitionEffect: "fade",
        enableAllSteps: false,
        autoFocus: true,
        transitionEffectSpeed: 500,
        titleTemplate: '<span class="title">#title#</span>',
        labels: {
            previous: "Prev",
            next: "Next",
            finish: "Confirm",
            current: "",
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            var asrama = $("#asrama").find("option:selected").text();
            var nama = $("#nama").val();
            var nik = $("#nik").val();
            var nisn = $("#nisn").val();
            var akte = $("#akte").val();
            var kk = $("#kk").val();
            var anakke = $("#anakke").val();
            var tempat = $("#tempat").val();
            var tl = $("#tl").val();
            var kelamin = $("#kelamin").find("option:selected").text();
            var alamat = $("#alamat").val();
            var prov_id = $("#prov").find("option:selected").text();
            var kab_id = $("#kab").find("option:selected").text();
            var kec_id = $("#kec").find("option:selected").text();
            var des_id = $("#des").find("option:selected").text();
            var rt = $("#rt").val();
            var rw = $("#rw").val();
            var saudara = $("#saudara").val();
            var ayah = $("#ayah").val();
            var a_nik = $("#a_nik").val();
            var a_tl = $("#a_tl").val();
            var a_pend = $("#a_pend").find("option:selected").text();
            var a_telp = $("#a_telp").val();
            var a_ker = $("#a_ker").find("option:selected").text();
            var a_has = $("#a_has").find("option:selected").text();
            var ibu = $("#ibu").val();
            var i_nik = $("#i_nik").val();
            var i_tl = $("#i_tl").val();
            var i_pend = $("#i_pend").find("option:selected").text();
            var i_telp = $("#i_telp").val();
            var i_ker = $("#i_ker").find("option:selected").text();
            var i_has = $("#i_has").find("option:selected").text();
            var wali = $("#wali").val();
            var w_telp = $("#w_telp").val();
            var w_ker = $("#w_ker").find("option:selected").text();
            var w_has = $("#w_has").find("option:selected").text();
            var s_asal = $("#s_asal").val();
            var s_alamat = $("#s_alamat").val();
            var s_prov = $("#s_prov").find("option:selected").text();
            var s_kab = $("#s_kab").find("option:selected").text();
            var s_kec = $("#s_kec").find("option:selected").text();
            var lulus = $("#lulus").val();
            $("#asrama-val").text(asrama);
            $("#nama-val").text(nama);
            $("#nik-val").text(nik);
            $("#nisn-val").text(nisn);
            $("#akte-val").text(akte);
            $("#kk-val").text(kk);
            $("#anakke-val").text(anakke);
            $("#tempat-val").text(tempat);
            $("#tl-val").text(tl);
            $("#kelamin-val").text(kelamin);
            $("#alamat-val").text(alamat);
            $("#prov-val").text(prov_id);
            $("#kab-val").text(kab_id);
            $("#kec-val").text(kec_id);
            $("#des-val").text(des_id);
            $("#rt-val").text(rt);
            $("#rw-val").text(rw);
            $("#saudara-val").text(saudara);
            $("#ayah-val").text(ayah);
            $("#a_nik-val").text(a_nik);
            $("#a_tl-val").text(a_tl);
            $("#a_pend-val").text(a_pend);
            $("#a_telp-val").text(a_telp);
            $("#a_ker-val").text(a_ker);
            $("#a_has-val").text(a_has);
            $("#ibu-val").text(ibu);
            $("#i_nik-val").text(i_nik);
            $("#i_tl-val").text(i_tl);
            $("#i_pend-val").text(i_pend);
            $("#i_telp-val").text(i_telp);
            $("#i_ker-val").text(i_ker);
            $("#i_has-val").text(i_has);
            $("#wali-val").text(wali);
            $("#w_telp-val").text(w_telp);
            $("#w_ker-val").text(w_ker);
            $("#w_has-val").text(w_has);
            $("#s_asal-val").text(s_asal);
            $("#s_alamat-val").text(s_alamat);
            $("#s_prov-val").text(s_prov);
            $("#s_kab-val").text(s_kab);
            $("#s_kec-val").text(s_kec);
            $("#lulus-val").text(lulus);
            return true;
        },
        onFinishing: function () {
            var data = $("#pendaftar").serialize();
            $.ajax({
                url: "/form-pendaftar",
                type: "POST",
                data: data,
                success: function (response) {
                    window.location.href = '/form-lengkap'
                },
                error: function (error) {
                    let errorMessage = error.responseJSON?.message || "Terjadi kesalahan tidak diketahui.";
                    let errorDetail = error.responseJSON?.error || "";
                    alert(`${errorMessage}\n\nDetail: ${errorDetail}`);
                },
            });
        },
    });
});
