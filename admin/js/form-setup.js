var $ = jQuery;
var postID = $('#post_ID').val();
var formObj = formObj;
var registrationForm = '';
var changeFlag = false;
jQuery(document).ready(function () {


    if( '' === jQuery('#event_end_date_select').val() ) {
        jQuery('#event_time_id').show();
    } else {
        jQuery('#event_time_id').hide();
    }

    $(document).on('change', '.registration-form-select .select-form', function () {
        var templateId = $(this).val();
        changeFlag = true;
        if (changeFlag) {
            $('.reset').removeAttr('disabled');
        }
        $('#templateId').val(templateId);
        if ('' !== templateId) {
            formSelectAjaxFun(postID, templateId);
        } else {
            registrationForm = document.getElementsByClassName("registration-form-wrap");
            registrationForm[0].innerHTML = '<div class="templete-not-selected">Please select a registration template.</div>';
        }
    });

    $(document).on('click', '.registration_form', function (e) {
        var templateId = $('#templateId').val();
        e.preventDefault();
        if ('' !== postID) {
            formSelectAjaxFun(postID, templateId);
        } else {
            registrationForm = document.getElementsByClassName("registration-form-wrap");
            registrationForm[0].innerHTML = '<div class="templete-not-selected">Please select a registration template.</div>';
        }
    });

    $(document).on('click', '.submitbox #publish, #save-action', function (e) {
        e.stopPropagation();

        $('.for_event').remove();

        errorMsgs = '';

        if (0 !== $('body.post-type-dffmain-events').length) {
            if (0 === $('#set-post-thumbnail img').length) {
                errorMsgs += '<p>Please set Featured Image.</p>';
            }
            if ('' === jQuery('#event_date_select').val()) {
                errorMsgs += '<p>Please select Event Date.</p>';
            }

            var start_date = Date.parse( jQuery('#event_date_select').val() );
            var end_date = Date.parse( jQuery('#event_end_date_select').val() );

            if ( end_date <= start_date ) {
                errorMsgs += "<p>‘End Date’ should be greater than the ‘Start Date’.</p>";
            }

            if( '' === jQuery('#event_end_date_select').val() ) {

                if ('' === $('.event_time_start_select').val()) {
                    errorMsgs += '<p>Please select Event Start Time.</p>';
                }
                if ('' === $('.event_time_end_select').val()) {
                    errorMsgs += '<p>Please select Event End Time.</p>';
                }

            }

            if ('' === $('.registration-form-select select').val()) {
                errorMsgs += '<p>Please select a registration template.</p>';
            }
            
        }

        if( '' !==  errorMsgs) {
            $('<div class="error notice for_event">'+errorMsgs+'</div>').insertAfter('.wp-header-end');
            return false;
        }
        var currentId = $(this).attr("id");
        $(this).toggleClass('saving');
        if ($(this).hasClass('saving')) {
            saveNextFunction(currentId);
        } else {
            return false;
        }

    });

    $(document).on('click', '#registration_form .save_next', function (e) {
        e.stopPropagation();
        saveNextFunction(false);
    });

    $(document).on('click', '#arabic .save_next', function (e) {
        e.stopPropagation();
        var templateId = $('#templateId').val();
       // e.preventDefault();
        if ('' !== postID) {
            formSelectAjaxFun(postID, templateId);
        } else {
            registrationForm = document.getElementsByClassName("registration-form-wrap");
            registrationForm[0].innerHTML = '<div class="templete-not-selected">Please select a registration template.</div>';
        }
    });

    $(document).on('click', '#registration_form .reset', function (e) {
        var templateId = $(this).attr('templateid');
        $('.registration-form-select .select-form').val(templateId);
        if ('' !== postID) {
            formSelectAjaxFun(postID, templateId);
        } else {
            registrationForm = document.getElementsByClassName("registration-form-wrap");
            registrationForm[0].innerHTML = '<div class="templete-not-selected">Please select a registration template.</div>';
        }
    });

    $(document).on('change', "#select_all", function() {
        changeFlag = true;
        if (changeFlag) {
            $('.reset').removeAttr('disabled');
        }
        if (this.checked) {
            $(".display-filed-checkbox input").each(function() {
                this.checked=true;
            });
        } else {
            $(".display-filed-checkbox input").each(function() {
                this.checked=false;
            });
        }
    });

    $(document).on('click', ".display-filed-checkbox input", function(){
        changeFlag = true;
        if (changeFlag) {
            $('.reset').removeAttr('disabled');
        }

        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".display-filed-checkbox input").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#select_all").prop("checked", true);
            }     
        }
        else {
            $("#select_all").prop("checked", false);
        }
    });

    $(document).on('click', '.close', function () {
        $('.modal-popup').remove();
    });

    $(document).keyup(function (e) {
        if (27 === e.keyCode) { // Esc
            $('.modal-popup').remove();
        }
    });

    $(document).on('click', '.view-detail', function () {
        var AttendeeId = $(this).attr('AttendeeId');
        $('.modal-popup').remove();
        $('body').addClass('is-loading');
        jQuery.ajax({
            url: formObj.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_attendee_details',
                AttendeeId: AttendeeId,
            },
            success: function (result) {
                if (result.status) {
                    var body = document.getElementsByTagName("body")[0];
                    body.innerHTML += '<div class="modal-popup">' + result.details_html + '</div>';
                }
                $('body').removeClass('is-loading');
            }
        });
    });

    // Security code Field enable/disable
    $('.security_code_checkbox').change(function () {
        var checkbox = $(this);
        if (checkbox.is(':checked')) {
            $('#security_code').show();
        } else {
            $('#security_code').hide();
        }
    });

    $('.post-type-registration-forms .total_associated_events').find('a').each(function () {
        var associatedEventCount = $(this).text();
        if ( '0' !== associatedEventCount ) {
            $(this).parents('tr').find('.row-actions .trash').remove();
            $(this).parents('tr').find('.row-actions .edit').contents().eq(1).remove();

        }
    });
});

function HandleBrowseClick(fileinput) {
    var browse = $('#' + fileinput).parents('.field-group').find('.form-control').attr('id');
    var fileinputDom = document.getElementById(browse);
    fileinputDom.click();
}

function Handlechange(fileinput) {
    var textinput = $('#' + fileinput).parents('.file-upload-wrap').find('.filename').attr('id');
    var textinputDom = document.getElementById(textinput);
    var fileinputDom = document.getElementById(fileinput);

    textinputDom.value = fileinputDom.value;
}

function saveNextFunction(updateButton) {
    var templateID = $('.registration-form-select .select-form').val();
    var fieldPreference = [];
    $('.display-filed-checkbox').find('input').each(function () {
        var currentId = $(this).attr('id');
        var fieldName = currentId.replace('checkbox_', '');
        if (true === $(this).prop('checked')) {
            fieldPreference.push({'fieldName': fieldName, 'preference': 'true'});
        } else {
            fieldPreference.push({'fieldName': fieldName, 'preference': 'false'});
        }
    });
    if ('' !== postID) {
        $('body').addClass('is-loading');
        jQuery.ajax({
            url: formObj.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'save_registration_form_for_event',
                postID: postID,
                templateID: templateID,
                fieldPreference: JSON.stringify(fieldPreference)
            },
            success: function (result) {
                if (updateButton) {
                    if('publish' === updateButton ){
                        $('#publish').trigger('click');
                    }else if( 'save-post' === updateButton  ){
                        $('#save-post').trigger('click');
                    }
                }
                $('body').removeClass('is-loading');
            }
        });
    }
}

function formSelectAjaxFun(postID, templateId) {
    $('body').addClass('is-loading');
    jQuery.ajax({
        url: formObj.ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'select_registration_form_for_event',
            postID: postID,
            templateID: templateId
        },
        success: function (result) {
            if (result.status) {
                registrationForm = document.getElementsByClassName("registration-form-wrap");
                registrationForm[0].innerHTML = result.formHtml;
                
                // Input checkbox for "Select All" on load
                var isAllChecked = 0;
                var checkCnt = 0;
                $(".display-filed-checkbox input").each(function() {
                    if (!this.checked){
                        isAllChecked = 1;
                    }

                    if( checkCnt ===  $(".display-filed-checkbox input").length - 1 ){
                        console.log(isAllChecked);
                        if (isAllChecked == 0) {
                            $("#select_all").prop("checked", true);
                        }else {
                            $("#select_all").prop("checked", false);
                        }  
                    }
                    checkCnt++;
                });
            } else {
                registrationForm = document.getElementsByClassName("registration-form-wrap");
                registrationForm[0].innerHTML = '<div class="templete-not-selected">Please select a registration template.</div>';
            }
            $('body').removeClass('is-loading');
        }
    });
}


function handler(e){

    var event_end_date = e.target.value;

    if( '' === event_end_date ) {
        jQuery('#event_time_id').show();
    } else {
        jQuery('#event_time_id').hide();
    }

}