/**
 * Quill Editor Initialization Component
 * Rich text editor setup with YouTube video embed support
 */

import Quill from 'quill';
import 'quill/dist/quill.snow.css';

/**
 * Initialize Quill Editor
 * @param {string|Element} selector - CSS selector or element for the editor container
 * @param {string|Element} inputSelector - CSS selector or element for the hidden input to sync with
 * @param {Object} options - Additional Quill options
 * @returns {Quill|null} Quill instance or null if element not found
 */
export function initQuill(selector = '#editor', inputSelector = '#content', options = {}) {
    const element = typeof selector === 'string'
        ? document.querySelector(selector)
        : selector;

    if (!element) {
        console.warn('Quill editor element not found:', selector);
        return null;
    }

    // Default Toolbar Options
    const defaultToolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ 'header': [1, 2, false] }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link', 'image', 'video'],
        ['clean']
    ];

    const quillOptions = {
        theme: 'snow',
        modules: {
            toolbar: {
                container: options.toolbar || defaultToolbarOptions,
                handlers: {
                    video: videoHandler
                }
            },
            clipboard: {
                matchers: [
                    ['A', linkMatcher],
                    [Node.TEXT_NODE, textNodeMatcher]
                ]
            }
        },
        ...options
    };

    const quill = new Quill(selector, quillOptions);

    // Sync with hidden input
    const input = typeof inputSelector === 'string'
        ? document.querySelector(inputSelector)
        : inputSelector;

    if (input) {
        // Set initial content if any
        if (input.value) {
            quill.root.innerHTML = input.value;
        }

        // Listen for content changes
        quill.on('text-change', () => {
            input.value = quill.root.innerHTML;
        });
    }

    return quill;
}

/**
 * Video handler for Quill toolbar
 */
function videoHandler(value) {
    if (value) {
        const href = prompt('Enter Video/YouTube URL:');
        if (href) {
            const videoUrl = convertToEmbedUrl(href);
            const range = this.quill.getSelection(true);
            this.quill.insertEmbed(range.index, 'video', videoUrl);
            this.quill.setSelection(range.index + 1);
        }
    }
}

/**
 * Convert YouTube URL to embed format
 * @param {string} url - YouTube URL
 * @returns {string} Embed URL
 */
function convertToEmbedUrl(url) {
    let videoUrl = url;

    if (url.includes('youtube.com/watch?v=')) {
        videoUrl = url.replace('watch?v=', 'embed/');
        if (videoUrl.includes('&')) {
            videoUrl = videoUrl.split('&')[0];
        }
    } else if (url.includes('youtu.be/')) {
        const id = url.split('/').pop().split('?')[0];
        videoUrl = `https://www.youtube.com/embed/${id}`;
    }

    return videoUrl;
}

/**
 * Check if URL is a video URL
 * @param {string} url - URL to check
 * @returns {boolean} True if video URL
 */
function isVideoUrl(url) {
    return url.includes('youtube.com/') ||
        url.includes('youtu.be/') ||
        url.includes('embed');
}

/**
 * Link matcher for clipboard
 */
function linkMatcher(node, delta) {
    const href = node.getAttribute('href');
    if (href && isVideoUrl(href)) {
        const videoUrl = convertToEmbedUrl(href);
        return new (Quill.import('delta'))().insert({ video: videoUrl });
    }
    return delta;
}

/**
 * Text node matcher for clipboard
 */
function textNodeMatcher(node, delta) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;

    if (typeof node.data === 'string' && node.data.match(urlRegex)) {
        const matches = node.data.match(urlRegex);
        const Delta = Quill.import('delta');
        let newDelta = new Delta();
        let lastIndex = 0;

        matches.forEach(match => {
            if (isVideoUrl(match)) {
                const videoUrl = convertToEmbedUrl(match);
                const index = node.data.indexOf(match, lastIndex);
                newDelta.insert(node.data.substring(lastIndex, index));
                newDelta.insert({ video: videoUrl });
                lastIndex = index + match.length;
            }
        });

        if (lastIndex > 0) {
            newDelta.insert(node.data.substring(lastIndex));
            return newDelta;
        }
    }

    return delta;
}

/**
 * Auto-initialize Quill editors on DOM ready
 */
document.addEventListener('DOMContentLoaded', () => {
    const editors = document.querySelectorAll('.quill-editor');

    editors.forEach((editor) => {
        const inputId = editor.dataset.input || editor.nextElementSibling?.id;

        if (inputId) {
            initQuill(editor, `#${inputId}`);
        } else {
            console.warn('Quill editor found but no input target specified', editor);
            initQuill(editor); // init anyway without sync
        }
    });
});

// Make globally available
if (typeof window !== 'undefined') {
    window.initQuill = initQuill;
}
