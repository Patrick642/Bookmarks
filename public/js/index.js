$(function () {

    /* Initialize tooltips */
    $('[data-bs-toggle="tooltip"]').tooltip();

    /* Execute function to copy URL of the bookmark to the user's clipboard when the button is clicked. */
    $('[bookmark-copyurl]').click(function () {
        copyLink($(this).attr('bookmark-copyurl'));
    });

    /* Copy share link. */
    $('#copyBookmarksShareLink').click(function () {
        if (copyLink($('#bookmarksShareLink').val()))
            $(this).closest('.modal-body').find('.alert-container').append(formAlert('success', 'Link copied to your clipboard!'));
        else
            $(this).closest('.modal-body').find('.alert-container').append(formAlert('error', 'Unable to copy link to clipboard. Do it manually.'));

        setTimeout(() => {
            $(this).closest('.modal-body').find('.alert-container').empty();
        }, 1000);
    });

    /* Copies the bookmark ID to a hidden input in the form. */
    $(document).on('click', '[data-bs-target="#deleteBookmarkModal"]', function () {
        let bookmark_id = $(this).data('id');
        $('#deleteBookmarkModal input[name="bookmark_id"]').val(bookmark_id);
    });

    /* Get data of a bookmark and append it to edit bookmark form. */
    $(document).on('click', '[data-bs-target="#editBookmarkModal"]', function () {
        let bookmark_id = $(this).data('id');
        $('#editBookmarkModal input[name="bookmark_id"]').val(bookmark_id);

        $.ajax({
            type: 'GET',
            url: '/dashboard/get_bookmark',
            data: {
                bookmark_id: bookmark_id
            },
            success: function (out) {
                let result = JSON.parse(out);

                if (result.success) {
                    $('#bookmark_edit_label').val(result.label);
                    $('#bookmark_edit_url').val(result.url);
                    $('.edit-form-loading-overlay').addClass('d-none');
                }
            }
        });
    });

    /* Clear edit form fields on modal window close. */
    $("#editBookmarkModal").on("hide.bs.modal", function () {
        $('#bookmark_edit_label').val('');
        $('#bookmark_edit_url').val('');
        $('.edit-form-loading-overlay').removeClass('d-none');
    });

    /* Sending a form to change the visibility of all user bookmarks. (public/private) */
    $("#bookmarksPrivacySwitch").click(function (e) {
        e.preventDefault();
        let btn = $(this);
        let value = btn.is(':checked');

        $.ajax({
            type: "POST",
            url: "dashboard/change_bookmarks_privacy",
            data: {
                bool: value
            },
            success: function (out) {
                let result = JSON.parse(out);

                if (result.success)
                    (value) ? btn.prop('checked', true) : btn.prop('checked', false);
                else
                    alert('Something went wrong. Try again later.');
            },
            error: function () {
                alert('Something went wrong. Try again later.');
            }
        });
    });

    /* Load more bookmarks and append it */
    $("#infiniteScrollButton").click(function (e) {
        $('#infiniteScrollButton').prop('disabled', true);
        $('#infiniteScrollButton .spinner-border').removeClass('d-none');
        let offset = $('.bookmark-card').length;

        $.ajax({
            type: 'GET',
            url: '/get_more_bookmarks',
            data: {
                user_id: $('#infiniteScrollButton').data('id'),
                offset: offset
            },
            cache: false,
            success: function (out) {

                let result = JSON.parse(out);
                if (result.success) {
                    $('.col.bookmarks').append(result.render);
                    if (result.isLast)
                        $('#infiniteScrollButton').closest('.col').remove();
                    else {
                        $('#infiniteScrollButton .spinner-border').addClass('d-none');
                        $('#infiniteScrollButton').prop('disabled', false);
                    }
                }
            }
        });
    });

    /* Handling forms in modal windows. */
    $(document).on('submit', '.modal form', function (e) {
        e.preventDefault();
        $('button').prop('disabled', true);
        let form = $(this);
        form.find('.alert').remove();

        $.ajax({
            type: $(this).attr('method'),
            url: $(document.activeElement).attr('formaction') || $(this).attr('action'),
            data: $(this).serialize(),
            success: function (out) {
                let res = JSON.parse(out);
                switch (res.success) {
                    case true:
                        form.find('.alert-container').append(formAlert('success', 'Success!'));
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                        break;
                    default:
                        $('button').prop('disabled', false);
                        form.find('.alert-container').append(formAlert('danger', res.message));
                        break;
                }
            },
            error: function () {
                $('button').prop('disabled', false);
                form.find('.alert-container').append(formAlert('danger', 'An error occured.'));
            }
        });
    });
});

/* A function that returns the DOM element of an alert, used to display modal form processing messages to the user. */
function formAlert(type, message) {
    let types = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];

    if (!types.includes(type)) {
        console.warn('Wrong alert type. Possible types are: ' + types.join(', '));
        return;
    }

    return '<div class="alert alert-' + type + ' mb-0" role="alert">' + message + '</div>';
}

/* Function that copies link to user's clipboard */
function copyLink(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url);
        return true;
    }

    return false;
}
