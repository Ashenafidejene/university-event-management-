document.addEventListener('DOMContentLoaded', () => {
    const likeButton = document.getElementById('like-button');
    const dislikeButton = document.getElementById('dislike-button');

    likeButton.addEventListener('click', () => {
        handleLikeDislike('like', likeButton);
    });

    dislikeButton.addEventListener('click', () => {
        handleLikeDislike('dislike', dislikeButton);
    });

    function handleLikeDislike(action, button) {
        const eventId = button.dataset.eventId;
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'likeDislikeHandler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                console.log(response); // Log the response for debugging

                if (response.status === 'success') {
                    if (action === 'like') {
                        document.getElementById('like-count').textContent = parseInt(document.getElementById('like-count').textContent) + 1;
                        likeButton.style.backgroundColor = '#e0f7fa';
                        likeButton.style.color = "black"
                        dislikeButton.style.backgroundColor = '';
                    } else if (action === 'dislike') {
                        document.getElementById('dislike-count').textContent = parseInt(document.getElementById('dislike-count').textContent) + 1;
                        dislikeButton.style.backgroundColor = '#ffebee';
                        dislikeButton.style.color = "black"
                        likeButton.style.backgroundColor = '';
                    }
                    document.getElementById('error-message').textContent = '';
                } else {
                    document.getElementById('error-message').textContent = response.message;
                    document.getElementById('error-message').style.color = 'red';
                }
            } else {
                document.getElementById('error-message').textContent = 'An error occurred. Please try again.';
                document.getElementById('error-message').style.color = 'red';
            }
        };

        xhr.send(`action=${action}&event_id=${eventId}`);
    }
});


$(document).ready(function() {
    $('#show-comments-button').click(function() {
        $('#comment-form').toggle();
        $('#comments').toggle();
        if ($('#comment-form').is(':visible')) {
            $(this).text('Hide Comments');
        } else {
            $(this).text('Show Comments');
        }
    });
});

