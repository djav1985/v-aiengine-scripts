<script>
document.addEventListener('DOMContentLoaded', function () {

    const youtubeSelector =
        'a[href*="youtube.com/watch"], a[href*="youtu.be/"]';

    function getYouTubeId(url) {
        try {
            const parsed = new URL(url);

            if (
                parsed.hostname === 'youtube.com' ||
                parsed.hostname === 'www.youtube.com'
            ) {
                return parsed.searchParams.get('v');
            }

            if (
                parsed.hostname === 'youtu.be' ||
                parsed.hostname === 'www.youtu.be'
            ) {
                return parsed.pathname.split('/')[1];
            }

        } catch (e) {
            return null;
        }

        return null;
    }

    function replaceYouTubeLink(link) {

        const videoId = getYouTubeId(link.href);

        if (!videoId || !/^[A-Za-z0-9_-]{11}$/.test(videoId)) {
            return;
        }

        const iframe = document.createElement('iframe');

        iframe.src = 'https://www.youtube.com/embed/' + videoId;
        iframe.width = '350';
        iframe.height = '197';
        iframe.loading = 'lazy';
        iframe.frameBorder = '0';

        iframe.setAttribute(
            'allow',
            'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
        );

        iframe.setAttribute('allowfullscreen', '');

        iframe.setAttribute(
            'referrerpolicy',
            'strict-origin-when-cross-origin'
        );

        link.replaceWith(iframe);
    }

    function processChatBox(chatBox) {

        chatBox.querySelectorAll(youtubeSelector).forEach(function (link) {
            replaceYouTubeLink(link);
        });

    }

    function initializeChatBox(chatBox) {

        if (chatBox.dataset.youtubeObserver === 'true') {
            return;
        }

        chatBox.dataset.youtubeObserver = 'true';

        // Process links already in the chatbot
        processChatBox(chatBox);

        // Watch this chatbot for new messages
        const chatObserver = new MutationObserver(function () {
            processChatBox(chatBox);
        });

        chatObserver.observe(chatBox, {
            childList: true,
            subtree: true
        });
    }

    function findChatBoxes() {

        document.querySelectorAll('.mwai-window-box').forEach(function (chatBox) {
            initializeChatBox(chatBox);
        });

    }

    // Find any chatbot already present
    findChatBoxes();

    // Watch for AI Engine creating the chatbot later
    const pageObserver = new MutationObserver(function () {
        findChatBoxes();
    });

    pageObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

});
</script>