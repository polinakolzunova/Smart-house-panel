$(() => {

    let token = docCookies.hasItem('token') ? docCookies.getItem('token') : "";
    let $app = $("#app");
    if (token) loadPanel();
    else loadAuth();

    function loadAuth() {

        $app.load("/assets/html/auth.html", afterLoadAuth);

        function afterLoadAuth() {
            $("#authform").submit(ajaxLogin);
        }

        function ajaxLogin(e) {
            let login = $("#login").val();
            let password = $("#password").val();
            e.preventDefault();
            $.ajax({
                url: "/assets/php/auth.php",
                type: "post",
                data: {login: login, password: password},
                success(data) {
                    data = JSON.parse(data);
                    if (data.token) {
                        docCookies.setItem('token', data.token);
                        document.location.reload();
                    } else if (data.message) {
                        $(".centering p").text(data.message);
                        $(".centering p").addClass('error');
                    } else {
                        console.log(data);
                    }
                },
                error(data) {
                    console.error(data);
                }
            });
        }

    }

    function loadPanel() {

        $app.load("/assets/html/panel.html", afterLoadPanel);

        function afterLoadPanel() {
            $("#logout").click(ajaxLogout);
            $("#fire-off").click(offFireSignal);
            $("input[type=range]").change(changeRange);
            // $("input[type=range]").mouseover(showRange);
            $("input[type=range]").mousemove(showRange);
            $("input[type=checkbox]").change(changeCheckbox);
            ajaxGetData();
            setInterval(ajaxGetData, 1000);
        }

        function ajaxGetData() {
            $.ajax({
                url: "/assets/php/getdata.php",
                type: "post",
                data: {token: token},
                success(data) {
                    let id;
                    data = JSON.parse(data);
                    for (id in data) {
                        //console.log(id, data[id]);
                        setValue(id, data[id]);
                    }
                    if (data.message) {
                        clearCookies();
                        document.location.reload();
                    }
                },
                error(data) {
                    console.error(data);
                }
            });
        }

        function ajaxLogout() {
            $.ajax({
                url: "/assets/php/logout.php",
                data: {token: token},
                type: "post",
                success(data) {
                    clearCookies();
                    document.location.reload();
                },
                error(data) {
                    console.error(data);
                }
            });
        }

        function ajaxSendValue(id, value) {
            setValue(id, value);
            $.ajax({
                url: "/assets/php/setdata.php",
                data: {token: token, id: id, value: value},
                type: "post",
                success(data) {
                    data = JSON.parse(data);
                    //console.log(data);
                },
                error(data) {
                    console.error(data);
                }
            });
        }

        function offFireSignal() {
            ajaxSendValue("11", "0");
        }

        function setValue(id, value) {
            let list = $(`*[data-id=${id}]`);

            list.each(function () {
                let elval;
                let $el = $(this);
                let tag = $el.prop("tagName");
                let type = $el.attr('type');
                //console.log(id,value,tag,type);

                switch (tag.toLowerCase()) {
                    case "span":
                        $el.text(value);
                        break;
                    case "div":
                        if ($el.hasClass('icon')) {
                            let hb = $el.hasClass('has-btn');
                            if (value == "1") {
                                if (hb) $("#fire-off").addClass('active');
                                $el.addClass('active');
                            } else {
                                if (hb) $("#fire-off").removeClass('active');
                                $el.removeClass('active');
                            }
                        }
                        break;
                    case "input":
                        if (type == 'checkbox') {

                            value = (parseInt(value) == "1") ? true : false;
                            elval = $el.prop('checked');
                            if (elval != value) {
                                console.log(id, value, elval);
                                $el.prop('checked', value);
                            }

                        } else if (type == 'range') {

                            elval = $(this).attr("data-v");
                            if (elval != value) {
                                $el.val(value);
                                $(this).attr("data-v", value);
                                $(`span[for=d${id}]`).text(value);
                            }

                        } else {
                            console.log('some type error');
                        }
                        break;
                    default:
                        console.log('some tag error');
                }
            });
        }

        function showRange(e) {
            let id = $(this).attr("data-id");
            let val = $(this).val();
            $(`span[for=d${id}]`).text(val);
        }

        function changeRange(e) {
            let id = $(this).attr("data-id");
            let val = $(this).val();
            $(this).attr("data-v", val);
            $(`span[for=d${id}]`).text(val);
            ajaxSendValue(id, val);
        }

        function changeCheckbox(e) {
            let $el = $(this);
            let id = $el.attr("data-id");
            let value = $el.prop("checked");
            // console.log(id, value);
            ajaxSendValue(id, value);
        }

    }

});

function clearCookies() {
    docCookies.removeItem('token');
    console.log('ok');
}