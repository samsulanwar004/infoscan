$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

function generateMessage(arrM) {
    var stringConstructor = "test".constructor;
    var arrayConstructor = [].constructor;
    var objectConstructor = {}.constructor;

    var message = '<ul>';
    if (arrM.constructor === stringConstructor) {
        message += '<li>' + arrM + '</li>';
    } else {
        $.each(arrM, function (index, value) {
            message += '<li>' + value + '</li>';
        })
    }

    message += '</ul>';

    return message;
}

function inputErrorMessage(arrM) {
    $.each(arrM, function (index, value) {
        $("." + index).addClass('has-error');
        $("." + index).append('<p class="help-block text-red error-text">'+value+'</p>');
    })
}

function removeInputErrorMessage() {
    $(".has-error").removeClass('has-error');
    $(".error-text").remove();
}

function createPutInput() {
    return '<input type="hidden" name="_method" value="PUT">';
}

function menuFocus() {
    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
    path = decodeURIComponent(path);

    $(".left-menu > li.treeview").removeClass('active');
    $(".left-menu > li.treeview > a").removeClass('v-link-active v-link-active-exact');

    if (0 === path.length || path == '/') {
        $("li.dashboard").addClass('active');
        $('a.link-dashboard').addClass('v-link-active v-link-active-exact');
    } else {
        $(".left-menu a").each(function () {

            var href = $(this).attr('href');
            var objReg = RegExp(href, 'gi');
            objReg = href.match(path);//path.match(objReg);
            //console.log(href, objReg);
            // must re-check for matching value.
            if (path === href || null !== objReg && !$(this).hasClass('dashboard')) {
                $(this).closest('li.treeview').addClass('active');

                $(this).closest('li.treeview > ul').addClass('menu-open');
                //$(this).closest('li').addClass('active');
                //$(this).closest('li.treeview > a').addClass('v-link-active v-link-active-exact');
            }
        });
    }
}

function redirectIfUnauthorizedInAdmin(xhr) {
    if (xhr.status == 401) {

        /*REBEL.showErrorMessage('<ul><li>You are not loggedin or your session is expired <br>' +
            'You will redirecting to the Login Page.</li></ul>', 'app-layout', true);*/

        REBEL.smallNotifTemplate('Your session is expired. We will redirect you to login page.', 'body', 'error');

        setTimeout(function () {
            window.location = '/admin/login';
        }, 3000);
    }
}

function getTokenValue() {
    return document.querySelector('#token').getAttribute('content');
}

function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

var REBEL = REBEL || {};
REBEL = (function () {
    return {
        initialize: function () {
            getModalContent();
            menuFocus();
            //REBEL.setIcheck();
        },

        inputSlugify: function(elem, result) {
            var _originInput = elem;

            if(_originInput.length) {
                var _originText = _originInput.val(),
                    _slugText = slugify(_originText);

                //console.log(_originText, _slugText);

                $(result).val(_slugText);
            }
        },

        /*setIcheck: function () {
            $('input.icheck').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
            });
        },*/

        smallNotifTemplate: function (message, places, type) {

            places = typeof places === 'undefined' ? 'body' : places;
            type = typeof type === 'undefined' ? 'success' : type;

            var bgColor = '#f9edbe',
                borderColor = '#f0c36d',
                textColor = '#222';

            if(type === 'error') {
                bgColor = '#f36150';
                textColor = '#fff';
                borderColor = 'rgb(107, 32, 29)';
            }

            var template = '<div class="small-notif" style="padding:2px 10px;border:1px solid transparent; font-weight:700;border-color: '+borderColor+';background-color:'+bgColor+';color:'+textColor+';text-align: center;position: fixed;left: 39%;width: 20%;border-radius: 0px 0px 6px 6px;font-size: 12px;min-width: 180px; z-index:1031;" > ' + message + ' </div>';

            $(places).prepend(template);

            return false;
        },

        smallErrorNotif: function (places) {
            return REBEL.smallNotifTemplate('Error process!', places, 'error');
        },
        smallSuccessNotif: function (places) {
            return REBEL.smallNotifTemplate('Success.', places, 'success');
        },


        messageTemplate: function (message, alertType, title, icon, fixed) {
            alertType = typeof alertType !== 'undefined' ? alertType : 'info';

            if (fixed == false || fixed == 'undefined') {
                return '<div class="alert alert-' + alertType + ' alert-dismissable"' +
                    'style="">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                    '<h4 class="small-type"><i class="fa fa-' + icon + '"></i> ' + title + '!</h4>' +
                    message + '</div>';
            }

            return '<div class="alert alert-' + alertType + ' alert-dismissable"' +
                'style="position: fixed; width: 50%;z-index: 8889; margin-left: 25%; margin-top: 5px;">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h4 class="small-type"><i class="fa fa-' + icon + '"></i> ' + title + '!</h4>' +
                message + '</div>';
        },
        scrollToTop: function (element) {
            $(element).scrollTop(0);
        },

        /*showLoading: function (container) {
         var $background = 'background-color: #f3ce85; -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80); filter: alpha(opacity=80); -moz-opacity: 0.7; -khtml-opacity: 0.7; opacity: 0.7; ',
         $loading = '<div id="loading" style="' + $background +
         'z-index: 999; margin: -15px; width: 100%; height: 100%; position: absolute; overflow: hidden;">' +
         ' <img style="position: absolute; top: 39%; left: 37%; " src="/img/loading.gif"></div>';
         $('#' + container).prepend($loading);

         return false;
         },*/

        hideLoading: function () {
            $("#loading").remove();

            return false;
        },

        removeAllMessageAlert: function () {
            $(".alert").remove();
            $(".small-notif").remove();
            return false;
        },

        showErrorMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';

            $("." + tagIdToAdd).prepend(REBEL.messageTemplate(message, 'danger', 'Whoops!!! Something wrong with your process.', 'ban', fixed));
        },

        showInfoMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';

            $("." + tagIdToAdd).prepend(REBEL.messageTemplate(message, 'info', 'Info', 'bullhorn', fixed));
        },

        showWarningMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';

            $("." + tagIdToAdd).prepend(REBEL.messageTemplate(message, 'warning', 'Warning', 'warning', fixed));
        },

        showSuccessMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';
            var ele = $("." + tagIdToAdd);
            ele.prepend(REBEL.messageTemplate(message, 'success', 'YayyY... Process successed!', 'thumbs-up', fixed));
        },
        isJson: function (jsonString) {
            try {
                var o = JSON.parse(jsonString);
                if (o && typeof o === "object" && o !== null) {
                    return o;
                }
            }
            catch (e) {
            }

            return false;
        },
        disableElement: function (form) {
            form.find('input, textarea, button, select').attr('disabled', 'disabled');
        },
        enableElement: function (form) {
            setTimeout(function () {
                form.find('input, textarea, button, select').removeAttr('disabled');
            }, 500);
        },
        onSubmit: function (form, callback, closeModal) {
            var _form = form,
                _action = _form.attr('action'),
                _method = $("#action_method").length ? $("#action_method").val() : _form.attr('method'),
                _inputData = _form.serializeJSON();

            //noinspection JSDuplicatedDeclaration
            var closeModal = !!(typeof closeModal == 'undefined' || closeModal != false);

            REBEL.disableElement(_form);
            REBEL.removeAllMessageAlert();
            removeInputErrorMessage();
            $.ajax({
                url: _action,
                type: _method,
                dataType: 'JSON',
                data: _inputData,
                success: function (data, textStatus, requestObject) {
                    if (REBEL.isJson(data)) {
                        data = $.parseJSON(data)
                    }

                    // redirect to login page if authentication session expired.
                    if(requestObject.status === 401) {
                        return redirectIfUnauthorizedInAdmin(requestObject);
                    }

                    // check status of response payloads.
                    if (data.status == 'ok') {
                        //REBEL.showSuccessMessage(generateMessage(data.messages), "modal-body", false)
                        $('.modal-content').length ? REBEL.smallSuccessNotif('.modal-content') : REBEL.smallSuccessNotif('.body');

                        if (callback && typeof(callback) === "function") {
                            REBEL.enableElement(_form);
                            var responseData = data;
                            callback(responseData);
                        }

                        // close modal
                        if (closeModal) {
                            setTimeout(function () {
                                $(".btn-modal-close").trigger("click");
                                $("#modalContent").on('hide.bs.modal', function (e) {
                                    $(this).remove()
                                });
                            }, 2500);
                        } else {
                            setTimeout(function () {
                                REBEL.removeAllMessageAlert();
                            }, 1500);
                        }
                    } else {
                        // do something for error handler.
                        //REBEL.showSuccessMessage(generateMessage(data.messages), "modal-body", false)
                        REBEL.smallErrorNotif('.modal-content');
                    }

                    REBEL.enableElement(_form);
                },
                error: function (requestObject, error, errorThrown) {
                    if(requestObject.status === 401) {
                        return redirectIfUnauthorizedInAdmin(requestObject);
                    }

                    REBEL.enableElement(_form);
                    var _output = JSON.parse(requestObject.responseText);
                    //_output = generateMessage(_output);

                    inputErrorMessage(_output);

                    setTimeout(function () {
                        //REBEL.smallErrorNotif('.modal-content');REBEL.showErrorMessage(_output, "modal-body", false)
                        REBEL.smallErrorNotif('.modal-content');
                    }, 200);
                }
            }).done(function () {
                REBEL.scrollToTop('#modalContent')
            })
        },
        onChangeStatus: function (element, callback) {
            var url = element.attr('href');
            alert(url);

            $.post(url, function (data, status) {
                alert("Data: " + data + "\nStatus: " + status);
            })
        }
    }
})();

function getModalContent() {
    $('a[data-toggle|="modal"]').on('click', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var url = $(this).attr('href');
        var modalSize = $(this).attr('modal-size');
        modalSize = typeof modalSize != 'undefined' ? modalSize : '';

        if (!url.indexOf('#') == 0) {
            var modal, modalDialog, modalContent;
            modal = $('<div id="modalContent" class="modal fade" role="dialog" data-backdrop="static"/>');
            modalDialog = $('<div id="modalContent" class="modal-dialog ' + modalSize + '" />');
            modalContent = $('<div class="modal-content"><div class="modal-loading"></div></div>');

            modal.modal('hide')
                .append(modalDialog)
                .on('hidden.bs.modal', function () {
                    $(this).remove();
                })
                .appendTo('body');

            modalDialog.append(modalContent);
            modal.modal('show');

            // check for delete dialog
            var deleteAttr = $(this).attr('for-delete');
            if (typeof deleteAttr !== typeof undefined && deleteAttr !== false) {
                var dataMessage = $(this).attr('data-message');
                var token = getTokenValue();
                var deleteHtml = '<form method="POST" action="' + url + '">';
                deleteHtml += '<div class="modal-header"><a class="close" data-dismiss="modal">&times;</a><h4><i class="fa fa-tag fa-btn"></i> Delete Confirmation</h4></div>';
                deleteHtml += '<div class="modal-body">'
                deleteHtml += '<input type="hidden" name="_token" value="' + token + '">';
                deleteHtml += '<input type="hidden" id="action_method" class="action_method" name="_method" value="DELETE">';
                deleteHtml += '<p>' + dataMessage + '</p>';
                deleteHtml += '</div>';
                deleteHtml += '<div class="modal-footer"><i class="fa fa-floppy-ofa-btn"></i><button class="btn btn-danger"><i class="fa fa-trash fa-btn"></i> <span class="ladda-label">Delete!</span></button><a class="btn btn-modal-close" data-dismiss="modal">Close</a></div>';
                deleteHtml += '</form>';

                modalContent.html(deleteHtml);

            } else {
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (data) {
                        modalContent.html(data);
                    },
                    error: function (requestObject, error, errorThrown) {
                        var modal = $("#modalContent");
                        modal.modal('hide');
                        modal.on('hide.bs.modal', function (e) {
                            $(this).remove()
                        });
                        //console.log(requestObject, error, errorThrown);

                        if(requestObject.status === 401) {
                            return redirectIfUnauthorizedInAdmin(requestObject);
                        }

                        var _output = JSON.parse(requestObject.responseText);
                        _output = generateMessage(_output);

                        REBEL.showErrorMessage(_output, 'body', true);
                    }
                });
            }
        }
    });
}

$(document).ready(function () {
    REBEL.initialize();
});
