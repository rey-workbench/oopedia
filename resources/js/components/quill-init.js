import Quill from 'quill';
import 'quill/dist/quill.snow.css';

/**
 * Initialize Quill Editor
 * @param {string} selector - CSS selector for the editor container
 * @param {string} inputSelector - CSS selector for the hidden input to sync with
 */
export function initQuill(selector = '#editor', inputSelector = '#content') {
    const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!element) return;

    // Default Toolbar Options
    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ 'header': [1, 2, false] }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link', 'image', 'video'],
        ['clean']
    ];

    const quill = new Quill(selector, {
        theme: 'snow',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    video: function (value) {
                        if (value) {
                            const href = prompt('Enter Video/YouTube URL:');
                            if (href) {
                                let videoUrl = href;
                                // Convert regular youtube links to embed links if necessary
                                if (href.includes('youtube.com/watch?v=')) {
                                    videoUrl = href.replace('watch?v=', 'embed/');
                                    if (videoUrl.includes('&')) videoUrl = videoUrl.split('&')[0];
                                } else if (href.includes('youtu.be/')) {
                                    const id = href.split('/').pop().split('?')[0];
                                    videoUrl = `https://www.youtube.com/embed/${id}`;
                                } else if (href.includes('youtube.com/embed/')) {
                                    videoUrl = href;
                                }

                                const range = this.quill.getSelection(true);
                                this.quill.insertEmbed(range.index, 'video', videoUrl);
                                this.quill.setSelection(range.index + 1);
                            }
                        }
                    }
                }
            },
            clipboard: {
                matchers: [
                    // Match linked URLs
                    ['A', (node, delta) => {
                        const href = node.getAttribute('href');
                        if (href) {
                            let isVideo = false;
                            let videoUrl = href;

                            if (href.includes('youtube.com/') || href.includes('youtu.be/')) {
                                isVideo = true;
                                if (href.includes('watch?v=')) {
                                    videoUrl = href.replace('watch?v=', 'embed/');
                                    if (videoUrl.includes('&')) videoUrl = videoUrl.split('&')[0];
                                } else if (href.includes('youtu.be/')) {
                                    const id = href.split('/').pop().split('?')[0];
                                    videoUrl = `https://www.youtube.com/embed/${id}`;
                                }
                            } else if (href.includes('embed')) {
                                isVideo = true;
                            }

                            if (isVideo) {
                                return new (Quill.import('delta'))().insert({ video: videoUrl });
                            }
                        }
                        return delta;
                    }],
                    // Match plain text URLs
                    [Node.TEXT_NODE, (node, delta) => {
                        const urlRegex = /(https?:\/\/[^\s]+)/g;
                        if (typeof node.data === 'string' && node.data.match(urlRegex)) {
                            const matches = node.data.match(urlRegex);
                            const Delta = Quill.import('delta');
                            let newDelta = new Delta();
                            let lastIndex = 0;

                            matches.forEach(match => {
                                let isVideo = false;
                                let videoUrl = match;

                                if (match.includes('youtube.com/watch?v=') || match.includes('youtu.be/') || match.includes('youtube.com/embed/')) {
                                    isVideo = true;
                                    if (match.includes('watch?v=')) {
                                        videoUrl = match.replace('watch?v=', 'embed/');
                                        if (videoUrl.includes('&')) videoUrl = videoUrl.split('&')[0];
                                    } else if (match.includes('youtu.be/')) {
                                        const id = match.split('/').pop().split('?')[0];
                                        videoUrl = `https://www.youtube.com/embed/${id}`;
                                    }
                                } else if (match.includes('embed')) {
                                    isVideo = true;
                                }

                                if (isVideo) {
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
                    }]
                ]
            }
        }
    });

    // Validasi form data sync
    const input = document.querySelector(inputSelector);
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

// Auto-init for specific classes if found
document.addEventListener('DOMContentLoaded', () => {
    // Cari elemen dengan class .quill-editor
    const editors = document.querySelectorAll('.quill-editor');
    editors.forEach((editor, index) => {
        // Asumsi ada hidden input dengan ID yang sesuai atau atribut data-input
        const inputId = editor.dataset.input || editor.nextElementSibling?.id;

        if (inputId) {
            initQuill(editor, `#${inputId}`);
        } else {
            console.warn('Quill editor found but no input target specified', editor);
            initQuill(editor); // init anyway without sync
        }
    });
});
