function myFunction() {

    var e =

        ((document.body.scrollTop || document.documentElement.scrollTop) /

            (document.documentElement.scrollHeight -

                document.documentElement.clientHeight)) *

        100;

    document.getElementById("myBar").style.width = e + "%";

}

function skinChanger() {

    $(".choose-skin li").on("click", function () {

        var e = $("body"),

            t = $(this),

            n = $(".choose-skin li.active").data("theme");

        $(".choose-skin li").removeClass("active"),

            e.removeClass("theme-" + n),

            t.addClass("active");

        $(".choose-skin li.active").data("theme");

        e.addClass("theme-" + t.data("theme"));

    });

}

    (window.onscroll = function () {

        myFunction();

    }),

    $(document).ready(function () {

        $("#main-menu").metisMenu(),

            $("#left-sidebar .sidebar-scroll").slimScroll({

                height: "calc(100vh - 65px)",

                wheelStep: 10,

                touchScrollStep: 50,

                color: "#1c222c",

                size: "2px",

                borderRadius: "3px",

                alwaysVisible: !1,

                position: "right",

            }),

            $(".btn-toggle-fullwidth").on("click", function () {

                $("body").hasClass("layout-fullwidth")

                    ? $("body").removeClass("layout-fullwidth")

                    : $("body").addClass("layout-fullwidth"),

                    $(this)

                        .find(".fa")

                        .toggleClass("fa-arrow-left fa-arrow-right");

            }),

            $(".btn-toggle-offcanvas").on("click", function () {

                $("body").toggleClass("offcanvas-active");

            }),

            $("#main-content").on("click", function () {

                $("body").removeClass("offcanvas-active");

            }),

            $(".right_toggle, .overlay").on("click", function () {

                $("#rightbar").toggleClass("open"),

                    $(".overlay").toggleClass("open");

            }),

            $(".themesetting .theme_btn").on("click", function () {

                $(".themesetting").toggleClass("open");

            }),

            $(".search_toggle").on("click", function () {

                $(".search_div").toggleClass("open");

            }),

            $(".megamenu_toggle").on("click", function () {

                $("#megamenu").toggleClass("open");

            }),

            $(".rightbar .right_chat li a, .rightbar .back_btn").on(

                "click",

                function () {

                    $("#rightbar").toggleClass("detail");

                }

            ),

            0 < $('[data-toggle="tooltip"]').length &&

                $('[data-toggle="tooltip"]').tooltip(),

            0 < $('[data-toggle="popover"]').length &&

                $('[data-toggle="popover"]').popover(),

            $(window).on("load", function () {

                $("#main-content").height() < $("#left-sidebar").height() &&

                    $("#main-content").css(

                        "min-height",

                        $("#left-sidebar").innerHeight() -

                            $("footer").innerHeight()

                    );

            }),

            $(".full-screen").on("click", function () {

                $(this).parents(".card").toggleClass("fullscreen");

            }),

            $(".progress .progress-bar").progressbar({ display_text: "none" }),

            $(".header-dropdown .dropdown-toggle").on("click", function () {

                $(".header-dropdown li .dropdown-menu").toggleClass(

                    "vivify fadeIn"

                );

            }),

            $(".check-all").on("click", function () {

                this.checked

                    ? $(this)

                          .parents(".check-all-parent")

                          .find(".checkbox-tick")

                          .each(function () {

                              this.checked = !0;

                          })

                    : $(this)

                          .parents(".check-all-parent")

                          .find(".checkbox-tick")

                          .each(function () {

                              this.checked = !1;

                          });

            }),

            $(".checkbox-tick").on("click", function () {

                $(this)

                    .parents(".check-all-parent")

                    .find(".checkbox-tick:checked").length ==

                $(this).parents(".check-all-parent").find(".checkbox-tick")

                    .length

                    ? $(this)

                          .parents(".check-all-parent")

                          .find(".check-all")

                          .prop("checked", !0)

                    : $(this)

                          .parents(".check-all-parent")

                          .find(".check-all")

                          .prop("checked", !1);

            }),

            $("a.mail-star").on("click", function () {

                $(this).toggleClass("active");

            }),

            $(".todo_list .todo-delete").on("click", function () {

                $(this).parents("li:first").toggleClass("delete");

            }),

            $(".font_setting input:radio").click(function () {

                var e = $("[name='" + this.name + "']")

                    .map(function () {

                        return this.value;

                    })

                    .get()

                    .join(" ");

                console.log(e), $("body").removeClass(e).addClass(this.value);

            }),

            $(".setting_switch .mini-sidebar-btn").on("change", function () {

                this.checked

                    ? ($("body").addClass("mini_sidebar"),

                      $("#left-sidebar").addClass("mini_sidebar_on"),

                      $(".hmenu-btn").attr("disabled", "disabled"))

                    : ($("body").removeClass("mini_sidebar"),

                      $("#left-sidebar").removeClass("mini_sidebar_on"),

                      $(".hmenu-btn").removeAttr("disabled", "disabled"));

            }),

            $("#left-sidebar").hover(function () {

                $("body").toggleClass("mini_hover"),

                    $("#left-sidebar").toggleClass("mini_sidebar_on");

            }),

            $(".setting_switch .hmenu-btn").on("change", function () {

                this.checked

                    ? ($("body").addClass("h-menu"),

                      $(".mini-sidebar-btn").attr("disabled", "disabled"))

                    : ($("body").removeClass("h-menu"),

                      $(".mini-sidebar-btn").removeAttr(

                          "disabled",

                          "disabled"

                      ));

            }),

            $(".setting_switch .rtl-btn").on("change", function () {

                this.checked

                    ? $("body").addClass("rtl")

                    : $("body").removeClass("rtl");

            });

            $('#mode').on('click', function() {

                let icon = $('#icon');

                if (icon.hasClass('fa-sun')) {

                    $('body').removeClass('light_version');

                    icon.removeClass('fa fa-2x fa-sun').addClass('fa fa-2x fa-moon');

                } else {

                    $('body').addClass('light_version');

                    icon.removeClass('fa fa-2x fa-moon').addClass('fa fa-2x fa-sun');

                }

            });
            // DataTables berdasarkan tanggal
            var today = new Date();
            var tabel = $('.tabel').DataTable();
            $('#tanggal').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true
            })
            .datepicker('setDate', today);
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex) {
                    let row = oSettings.aoData[iDataIndex].nTr;
                    let tanggalStr = $(row).data('tanggal');
                    if (!tanggalStr) return false;
                    let selectedDate = $('#tanggal').val();
                    return tanggalStr === selectedDate;
                }
            );
            $('#tanggal').on('change', function() {
                tabel.draw();
            });
            tabel.draw();

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
            const htmlElement = $("html");
            const switchElement = $("#darkModeSwitch");
            const prefersDarkScheme = window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches;
            const currentTheme =
                localStorage.getItem("bsTheme") || (prefersDarkScheme ? "dark" : "light");
            htmlElement.attr("data-bs-theme", currentTheme);
            switchElement.prop("checked", currentTheme === "dark");
            switchElement.on("change", function () {
                const newTheme = this.checked ? "dark" : "light";
                htmlElement.attr("data-bs-theme", newTheme);
                localStorage.setItem("bsTheme", newTheme);
            });

            $('[data-bs-toggle="tooltip"]').each(function () {
                new bootstrap.Tooltip(this);
            });

            // Untuk input hanya angka
            $(document).on("keypress", ".number", function () {
                return event.which >= 48 && event.which <= 57;
            });
            // Pencarian Data Santri dan Pengurus
            $('.santri, .pengurus, .kepkam').select2({
                dropdownParent: $('.modal'),
                placeholder: 'Cari nama',
                width: '100%',
                ajax: {
                    url: function () {
                        if ($(this).hasClass('santri')) {
                            return BASE_URL + '/search-santri';
                        } else if ($(this).hasClass('pengurus')) {
                            return BASE_URL + '/search-pengurus';
                        } else if ($(this).hasClass('kepkam')) {
                            return BASE_URL + '/search-kepkam';
                        }
                        return BASE_URL;
                    },
                    delay: 250,
                    cache: true,
                    processResults: function({data}){
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id:item.nis,
                                    text: item.nama
                                }
                            })
                        }
                    },
                }
            });
    }),

    ($.fn.clickToggle = function (t, n) {

        return this.each(function () {

            var e = !1;

            $(this).bind("click", function () {

                return e

                    ? ((e = !1), n.apply(this, arguments))

                    : ((e = !0), t.apply(this, arguments));

            });

        });

    })