$(function () {
    /* Execute function to copy URL of the bookmark to the user's clipboard when the button is clicked. */
    $('[bookmark-copyurl]').click(function () {
        copyLink($(this).attr('bookmark-copyurl'));
    });

    /* Copy share link. */
    $('#copyBookmarksShareLink').click(function () {
        if (copyLink($('#bookmarksShareLink').val())) {
            $(this).closest('.modal-body').find('.alert-container').append(formAlert('success', 'Link copied to your clipboard!'));
        } else {
            $(this).closest('.modal-body').find('.alert-container').append(formAlert('error', 'Unable to copy link to clipboard. Do it manually.'));
        }
        setTimeout(() => {
            $(this).closest('.modal-body').find('.alert-container').empty();
        }, 1000);
    });

    /* Copies the bookmark ID to a hidden input in the form. */
    $(document).on('click', '[data-bs-target="#deleteBookmarkModal"]', function () {
        let bookmark_id = $(this).data('id');
        $('#deleteBookmarkModal input[name="bookmark_id"]').val(bookmark_id);
    });

    /* Sends data from the form to edit the bookmark. */
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
                var bookmark = JSON.parse(out);
                $('#bookmark_edit_title').val(bookmark.title);
                $('#bookmark_edit_url').val(bookmark.url);
            }
        });
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
                if (JSON.parse(out)['status'] === 'success') {
                    (value) ? btn.prop('checked', true) : btn.prop('checked', false);
                }
                else {
                    alert('Something went wrong. Try again later.');
                }
            }
        });
    });

    /* 
     * Handling forms in modal windows.
     */
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
                switch (res.status) {
                    case 'success':
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
    return `<div class="alert alert-` + type + `" role="alert">
        `+ message + `
    </div>`;
}

/* Function that copies link to user's clipboard */
function copyLink(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url);
        return true;
    }
    return false;
}
