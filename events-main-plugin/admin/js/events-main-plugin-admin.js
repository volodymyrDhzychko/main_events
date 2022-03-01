// eslint:ignoreFile for File
function openEditor(evt, tabName) {

    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName('tabcontent');
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName('tablinks');
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(' active', '');
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = 'block';
    evt.currentTarget.className += ' active';
}

function nextClick(tabName) {

    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName('tabcontent');
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName('tablinks');
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(' active', '');
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = 'block';
    //  var element = document.getElementsByClassName(tabName);
    var element = document.getElementsByClassName(tabName);

    // Iterate through the retrieved elements and add the necessary class names.
    for (var i = 0; i < element.length; i++) {
        element[i].classList.add('active');
    }

}

function openSettings(evt, tabName) {

    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName('tabcontent');
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName('tablinks');
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(' active', '');
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = 'block';
    evt.currentTarget.className += ' active';
}

var $ = jQuery;

jQuery(document).ready(function () {

    jQuery('.taxonomy-add-new-cat').click(function () {
        jQuery('#events_categories-add').toggleClass('wp-hidden-child');
    });

    jQuery('.taxonomy-add-new-tags').click(function () {
        jQuery('#events_tags-add').toggleClass('wp-hidden-child');
    });

    jQuery('.taxonomy-add-new-arabic-cat').click(function () {
        jQuery('#events_arabic_categories-add').toggleClass('wp-hidden-child');
    });

    jQuery('.taxonomy-add-new-arabic-tags').click(function () {
        jQuery('#events_arabic_tags-add').toggleClass('wp-hidden-child');
    });

    jQuery('.dffmain-category-add-submit').click(function () {
        var newevents_categories = jQuery('#newevents_categories').val();
        var newevents_categories_parent = jQuery('#newevents_categories_parent').val();
        var post_id = jQuery('.post_id').val();

        if ('' !== newevents_categories) {
            jQuery.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'category_add_submit',
                    newevents_categories: newevents_categories,
                    newevents_categories_parent: newevents_categories_parent,
                    post_id: post_id,
                },
                success: function (data) {
                    if ("-1" === newevents_categories_parent) {
                        var eventsEnglishCategoriesChecklist = document.getElementById("events_categorieschecklist");
                        eventsEnglishCategoriesChecklist.innerHTML += data;
                    } else {
                        var newId = 'event_child_' + newevents_categories_parent;
                        console.log('ID', newId);
                        var newEventsCategoriesParent = document.getElementById(newId);
                        console.log('node', newEventsCategoriesParent);
                        newEventsCategoriesParent.innerHTML += data;
                    }
                }
            });
        }
    });

    jQuery('.dffmain-tags-add-submit').click(function () {
        var newevents_tags = jQuery('#newevents_tags').val();
        var post_id = jQuery('.post_id').val();

        if ('' !== newevents_tags) {
            jQuery.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'tags_add_submit',
                    newevents_tags: newevents_tags,
                    post_id: post_id,
                },
                success: function (data) {
                    var eventsEnglishTagssChecklist = document.getElementById("events_tagsschecklist");
                    eventsEnglishTagssChecklist.innerHTML += data;
                }
            });
        }
    });

    $('body').on('click', '.upload_event_detail_image_button', function (e) {
        e.preventDefault();

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function () { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                var uploadEventDetailImageButton = document.getElementsByClassName("upload_event_detail_image_button")[0];
                if (uploadEventDetailImageButton) { uploadEventDetailImageButton.innerHTML = '<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />'; }
                $(button).removeClass('button').next().val(attachment.id).next().show();
            })
                .open();
    });

    $('body').on('click', '.remove_event_detail_image', function () {
        $(this).hide().prev().val('');
        var uploadEventDetailImageButton = document.getElementsByClassName("upload_event_detail_image_button")[0];
        if (uploadEventDetailImageButton) { uploadEventDetailImageButton.innerHTML = 'Set event detail image'; }
        return false;
    });

    jQuery('.add_sites_button').on('click', function () {
        var add_sites_field = jQuery('.add_sites_field').val();

        if ('' === add_sites_field) {
            jQuery('.add_sites_field').css('border', '1px solid red');
        }

        if ('' !== add_sites_field) {
            jQuery.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'add_child_sites_action',
                    add_sites_field: add_sites_field,
                },
                success: function (data) {
                    var addSiteTable = document.getElementsByClassName("add_site_table")[0];
                    addSiteTable.innerHTML += data;
                    jQuery('.add_sites_field').val('').css('border', '1px solid #7e8993');

                }
            });
        }

    });

    jQuery(document).on('click', '.delete_site_button', function (event) {

        var retVal = confirm("Are you sure you wish to delete this site?");

        if (retVal == true) {

            var delete_site_button = jQuery(this).parent().siblings('.siteurl').text();
            var tr = jQuery(this).closest('tr');

            jQuery.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_sites_action',
                    delete_site_button: delete_site_button,
                },
                success: function (data) {
                    tr.fadeOut('normal', function () {
                        $(this).remove();
                    });
                }
            });
        } else {
            return false;
        }

    });

    jQuery('#email_history').DataTable({
        deferRender: true,
        'searching': false,
        'paging': true,
        "lengthChange": false,
        'autoWidth': false,
        "language": {
            "emptyTable": "Not Found"
        },
        'columns': [
            { 'width': '14%' },
            { 'width': '33%' },
            { 'width': '33%' },
            { 'width': '20%' }
        ]
    });

    /* Email History Accordian */
    jQuery(document).on('click', '.accordian_email_history > .accordian-main > .accordian-title, .registration-form-wrap .accordian-main .accordian-title', function () {
        $(this).next('.accordian-body').slideToggle().parent('.accordian-main').toggleClass('accordian-open');
    });

    jQuery(document).on('click', '.save_next', function () {

        var postID = $('#post_ID').val();
        var next_step_id = $(this).next().val();
        var dffmain_post_title = $('input[name=dffmain_post_title]').val();
        var events_overview = jQuery('#events_overview_ifr').contents().find('#tinymce').html();
        var dffmain_events_agenda = jQuery('#dffmain_events_agenda_ifr').contents().find('#tinymce').html();
        var dffmain_event_location = jQuery('.dffmain_event_location').val();

        var emp_category = [];
        var emp_tags = [];

        $.each($('input[name=\'emp_category[]\']:checked'), function () {
            emp_category.push($(this).val());
        });
        $.each($('input[name=\'emp_tags[]\']:checked'), function () {
            emp_tags.push($(this).val());
        });

        jQuery.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'dff_save_next_click_ajax',
                postID: postID,

                dffmain_post_title: dffmain_post_title,
                events_overview: events_overview,
                dffmain_events_agenda: dffmain_events_agenda,
                dffmain_event_location: dffmain_event_location,

                emp_category: emp_category,
                emp_tags: emp_tags
            },
            success: function (data) {
                nextClick(next_step_id);
            },
            beforeSend: function () {
                jQuery('body').addClass("is-loading");
            },
            complete: function () {
                jQuery('body').removeClass("is-loading");
            },
        });

    });

    // Send Spesial Email
    jQuery('.btn-send-email').click(function () {

        var dffmain_curr_post_id = jQuery('.curr_post_id').val();
        var dffmain_curr_site_id = jQuery('.curr_site_id').val();
        // var dffmain_curr_site_lang = jQuery('.curr_site_lang').val();

        var dffmainEventSpecialEmailContentIds = document.querySelectorAll('[id^=event_send_special_email_]');

        var dffmain_event_content_email = [];
        dffmainEventSpecialEmailContentIds.forEach(function(element) {
            if ('IFRAME' == element.nodeName){
                var dffmain_each_event_id = '#'+element.id;

                var dffmain_each_event_content = jQuery(dffmain_each_event_id).contents().find('#tinymce').html();

                var dffmain_each_event_language = dffmain_each_event_id.replace("#event_send_special_email_", "");
                dffmain_each_event_language = dffmain_each_event_language.replace("_ifr", "");

                dffmain_event_content_email.push({'language': dffmain_each_event_language, 'content': dffmain_each_event_content});
            }
        });

        var dffmainEventSpecialEmailSubjectIds = document.querySelectorAll('[id^=dffmain_special_mail_subject_]');
        var dffmain_event_subject_email = [];
        dffmainEventSpecialEmailSubjectIds.forEach(function(element) {
            var dffmain_each_event_subject = jQuery('#'+element.id).val();
            var dffmain_each_event_subject_language = jQuery(element).data('lang');
            
            dffmain_event_subject_email.push({'language': dffmain_each_event_subject_language, 'subject': dffmain_each_event_subject});
        });

        var dffmain_event_special_mail_data = [];

        dffmain_event_content_email.forEach(function(element){
            dffmain_event_special_mail_data.push({
                language: element.language,
                content: element.content,
                subject:(dffmain_event_subject_email.find(e=>e.language === element.language)).subject
            });  
        });

        var check_dffmain_event_subject = dffmain_event_subject_email.every(function(element){
            return element.subject.length > 2;
        })
        var check_dffmain_event_content = dffmain_event_content_email.every(function(element){
            return element.content.length > 2 && element.content != '<p><br data-mce-bogus="1"></p>';
        })

        if (check_dffmain_event_subject && check_dffmain_event_content) {

            jQuery(".event_send_email .notice-error").remove();

            jQuery.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    'action': 'event_send_special_single_email',
                    'post_id': dffmain_curr_post_id,
                    'site_id': dffmain_curr_site_id,
                    // 'site_language': dffmain_curr_site_lang,
                    // 'dffmain_event_content': dffmain_event_content,
                    // 'dffmain_event_subject': dffmain_event_subject,
                    'dffmain_event_special_mail_data': dffmain_event_special_mail_data
                },
                success: function (data) {

                    console.log('data', data);

                    jQuery('.email_history').html(data);

                    jQuery('#email_history').DataTable({
                        deferRender: true,
                        'searching': false,
                        'paging': true,
                        "lengthChange": false,
                        'autoWidth': false,
                        "destroy": true,
                        'columns': [
                            { 'width': '14%' },
                            { 'width': '33%' },
                            { 'width': '33%' },
                            { 'width': '20%' }
                        ]
                    });

                    alert("Email sent!");
                    hide_pagination();

                },
                beforeSend: function () {
                    jQuery('body').addClass("is-loading");
                },
                complete: function () {
                    jQuery('body').removeClass("is-loading");
                },

            });

        }else{
            jQuery(".event_send_email .notice-error").remove();
            jQuery(".event_send_email").prepend('<div class="notice notice-error"><p>Need to fill in all emails</p></div>');
        }


    });

    // Email history popup
    jQuery(document).on("click", '.view_history_action', function (event) {
        jQuery(this).next(".email_history_popup").addClass('open-popup');
        var OpenAccordian = jQuery(this).next(".email_history_popup").find(".accordian-main:first-of-type:last-child").hasClass("accordian-open");
        if (!OpenAccordian) {
            jQuery(this).next(".email_history_popup").find(".accordian-main:first-of-type").addClass("accordian-open").children(".accordian-body").css('display', 'block');
        }
    });

    jQuery(document).on('click', '.email_history_popup .accordian-title', function (e) {
        $(this).next('.accordian-body').slideDown().parent('.accordian-main').addClass('accordian-open').siblings().removeClass('accordian-open').children('.accordian-body').slideUp();
    });

    jQuery(document).on('click', '.email_history_popup .close_popup', function () {
        jQuery(this).parents('.email_history_popup').removeClass('open-popup');
    });


    jQuery(document).on('click', '#cancel_event_button', function () {
        jQuery('#cancel-event-modal').addClass('open-popup');
    });

    jQuery(document).on('click', '#cancel_event_now', function () {

        const post_id = jQuery('.post_id').val();

        jQuery.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'cancel_event_ajax',
                post_id: post_id,
            },
            success: function () {
                location.reload();
            },
            beforeSend: function () {
                jQuery('body').addClass("is-loading");
            },
            complete: function () {
                jQuery('body').removeClass("is-loading");
            },
        });
    });

    jQuery(document).on('click', '#cancel-event-modal .event-modal-close', function () {
        jQuery('#cancel-event-modal').removeClass('open-popup');
    });

    jQuery(document).on('click', '#post-preview', function (event) {
        event.preventDefault();
        var href = $(this).attr('href');
        window.open(href, '_blank');
    });

    jQuery(document).on('click', '.save-post-status', function () {

        var post_status = jQuery('#post_status').val();

        if ("cancelled" === post_status) {
            jQuery('#cancel-event-modal').addClass('open-popup');
        }

    });
    jQuery(document).on('click', '.post-type-dffmain-events #delete-action, .post-type-dffmain-events .row-actions .trash', function (e) {
        var trashConfirm = confirm("Are you sure you wish to trash this event?");

        if (!trashConfirm) {
            return false;
        }
    });


    /**
     * Check in label ajax call click.
     */
    $('.column-check_in label input').change(function () {

        var attendee_id = $(this).attr('id');
        var checked = '';
        $(this).parents('.column-check_in').find('.checkin-label').text('Loading...');
        $(this).parents('.column-check_in').find('.checkin-label').css('color', '#00a0d2');
        if (this.checked) {
            checked = 'true';
        } else {
            checked = 'false';
        }
        var currentElement = $(this);
        jQuery.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'dff_checkin_ajax',
                checked: checked,
                attendee_id: attendee_id,
            },
            success: function (data) {

            },
            beforeSend: function () {
                jQuery('body').addClass("is-loading");
            },
            complete: function () {
                jQuery('body').removeClass("is-loading");
                if ('true' === checked) {
                    currentElement.parents('.column-check_in').find('.checkin-label').text('Checked-in');
                    currentElement.parents('.column-check_in').find('.checkin-label').css('color', 'green');
                } else {
                    currentElement.parents('.column-check_in').find('.checkin-label').text('');
                    currentElement.parents('.column-check_in').find('.checkin-label').css('color', '#555');
                }
            },
        });

    });

    hide_pagination();
});

function hide_pagination() {
    if ($('#email_history tbody tr').length > 10) {
        $('#email_history_info').show();
        $('#email_history_paginate').show();
    } else {
        $('#email_history_info').hide();
        $('#email_history_paginate').hide();
    }

}