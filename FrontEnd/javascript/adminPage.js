$(document).ready(function() {
    $(".toggleBox").click(function() {
        $(this).toggleClass('active');
        $(".VerticalNav").toggleClass('active');
    });

    function hideAllSections() {
        $('.fullInfo, .ADDEvent, .expireEvent, .UpcomingEvent, .comment-section, .reply-form , .Chart' ).slideUp(400).addClass('enactive');
    }

    $('.addEventButton-btn').click(function() {
        hideAllSections();
        $('.ADDEvent').slideDown(400).removeClass('enactive');
    });

    $('.home').click(function() {
        hideAllSections();
        $('.fullInfo').slideDown(400).removeClass('enactive');
    });

    $('.summary').click(function(){
        console.log("Summary button clicked");
        hideAllSections();
        $('.Chart').slideDown(400).removeClass('enactive');
    });
    
    $('.comment').click(function() {
        console.log("Comment button clicked");
        hideAllSections();
        $('.comment-section, .reply-form').slideDown(400).removeClass('enactive');
    });

    $('.Expire').click(function() {
        hideAllSections();
        $('.expireEvent').slideDown(400).removeClass('enactive');
    });

    $('.upcoming').click(function() {
        hideAllSections();
        $('.UpcomingEvent').slideDown(400).removeClass('enactive');
    });

    $('.form-container').submit(function(event) {
        let valid = true;
        let errorMessage = '';

        // Validate Event Name
        const eventName = $('#eventName').val().trim();
        if (eventName === '') {
            valid = false;
            errorMessage += 'Event Name is required.\n';
        }

        // Validate Event Description
        const eventDescription = $('#eventDescription').val().trim();
        if (eventDescription === '') {
            valid = false;
            errorMessage += 'Event Description is required.\n';
        }

        // Validate Event Date and Time
        const eventDateTime = $('#eventDateTime').val().trim();
        if (eventDateTime === '') {
            valid = false;
            errorMessage += 'Event Date and Time is required.\n';
        }

        // Validate Location
        const location = $('#location').val().trim();
        if (location === '') {
            valid = false;
            errorMessage += 'Location is required.\n';
        }

        // Validate Category
        const category = $('#category').val().trim();
        if (category === '') {
            valid = false;
            errorMessage += 'Category is required.\n';
        }

        // Validate Writer Username
        const writerUsername = $('#writerUsername').val().trim();
        if (writerUsername === '') {
            valid = false;
            errorMessage += 'Writer Username is required.\n';
        }
        // If the form is not valid, prevent submission and show error message
        if (!valid) {
            alert(`Please correct the following errors:\n\n${errorMessage}`);
            event.preventDefault();
        } else {
            hideAllSections();
            $('.fullInfo').slideDown(400).removeClass('enactive');
        }
    });

    $('a.delete').on('click', function() {
        var eventId = $(this).data('id');
        if (confirm('Are you sure you want to delete this event?')) {
            $.ajax({
                url: 'delete_event.php',
                type: 'POST',
                data: { eventId: eventId },
                success: function(response) {
                    if (response.trim() === 'success') {
                        alert('Event deleted successfully');
                        location.reload(); // Reload the page to reflect the changes
                    } else {
                        alert('Error deleting event: ' + response);
                    }
                },
                error: function() {
                    alert('Error deleting event');
                }
            });
        }
    });

    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'edit_event.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Event edited successfully');
                $('.modal').fadeOut(400);
                location.reload();
            },
            error: function() {
                alert('Error editing event');
            }
        });
    });

    $('a.edit').on('click', function() {
        var eventId = $(this).data('id');
        var eventName = $(this).data('name');
        var eventDescription = $(this).data('description');
        var eventDateTime = $(this).data('datetime');
        var location = $(this).data('location');
        var category = $(this).data('category');
        var status = $(this).data('status');
        var writerUsername = $(this).data('writer');

        $('#editEventId').val(eventId);
        $('#editEventName').val(eventName);
        $('#editEventDescription').val(eventDescription);
        $('#editEventDateTime').val(eventDateTime);
        $('#editLocation').val(location);
        $('#editCategory').val(category);
        $('#editStatus').val(status);
        $('#editWriterUsername').val(writerUsername);

        $('#editEventModal').fadeIn(400);
    });

    $('.close').on('click', function() {
        $('.modal').fadeOut(400);
    });

    $(window).on('click', function(event) {
        if ($(event.target).hasClass('modal')) {
            $('.modal').fadeOut(400);
        }
    });
});