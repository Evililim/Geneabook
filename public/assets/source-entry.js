(function () {
    'use strict';

    class DocumentViewer {
        constructor(root) {
            this.root = root;
            this.image = root.querySelector('[data-document-image]');
            this.scale = 1;
            this.translate = { x: 0, y: 0 };
            this.drag = { active: false, x: 0, y: 0 };

            this.bindEvents();
            this.reset();
        }

        bindEvents() {
            this.root.addEventListener('wheel', (event) => this.onWheel(event), { passive: false });
            this.root.addEventListener('mousedown', (event) => this.startPan(event));
            this.root.addEventListener('mousemove', (event) => this.pan(event));
            this.root.addEventListener('mouseup', () => this.stopPan());
            this.root.addEventListener('mouseleave', () => this.stopPan());
        }

        reset() {
            this.scale = 1;
            this.translate = { x: 0, y: 0 };
            this.applyTransform();
        }

        onWheel(event) {
            event.preventDefault();

            // Keep the point under the cursor visually stable while the scale changes.
            const rect = this.root.getBoundingClientRect();
            const cursorX = event.clientX - rect.left - rect.width / 2;
            const cursorY = event.clientY - rect.top - rect.height / 2;
            const previousScale = this.scale;
            const zoomFactor = event.deltaY < 0 ? 1.12 : 0.88;

            this.scale = this.clamp(this.scale * zoomFactor, 0.35, 5);

            const ratio = this.scale / previousScale;
            this.translate.x = cursorX - (cursorX - this.translate.x) * ratio;
            this.translate.y = cursorY - (cursorY - this.translate.y) * ratio;

            this.applyTransform();
        }

        startPan(event) {
            this.drag = {
                active: true,
                x: event.clientX,
                y: event.clientY,
            };
            this.root.classList.add('is-dragging');
        }

        pan(event) {
            if (!this.drag.active) {
                return;
            }

            this.translate.x += event.clientX - this.drag.x;
            this.translate.y += event.clientY - this.drag.y;
            this.drag.x = event.clientX;
            this.drag.y = event.clientY;

            this.applyTransform();
        }

        stopPan() {
            this.drag.active = false;
            this.root.classList.remove('is-dragging');
        }

        applyTransform() {
            this.image.style.transform = [
                'translate(-50%, -50%)',
                `translate(${this.translate.x}px, ${this.translate.y}px)`,
                `scale(${this.scale})`,
            ].join(' ');
        }

        clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        }
    }

    class AssertionsForm {
        constructor(list, template) {
            this.list = list;
            this.template = template;
            this.nextIndex = 0;
        }

        addRow(values = {}) {
            const fragment = this.template.content.cloneNode(true);
            const row = fragment.querySelector('[data-assertion-row]');
            const index = this.nextIndex;

            // Names are indexed for PHP's native multidimensional POST parsing.
            this.nextIndex += 1;
            row.dataset.index = String(index);
            row.querySelector('[data-assertion-title]').textContent = `Personne citee #${index + 1}`;

            row.querySelectorAll('[data-name]').forEach((field) => {
                const key = field.dataset.name;
                field.name = `assertions[${index}][${key}]`;

                if (Object.prototype.hasOwnProperty.call(values, key)) {
                    if (field.type === 'checkbox') {
                        field.checked = Boolean(values[key]);
                    } else {
                        field.value = values[key];
                    }
                }
            });

            row.querySelector('[data-remove-assertion]').addEventListener('click', () => {
                row.remove();
                this.refreshTitles();
            });

            this.list.appendChild(row);
            this.refreshTitles();
        }

        refreshTitles() {
            this.list.querySelectorAll('[data-assertion-row]').forEach((row, visibleIndex) => {
                row.querySelector('[data-assertion-title]').textContent = `Personne citee #${visibleIndex + 1}`;
            });
        }
    }

    function mockAiParser() {
        const textarea = document.querySelector('textarea[name="ai[raw_text]"]');
        console.log('AI parser placeholder:', textarea ? textarea.value : '');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const viewerRoot = document.querySelector('[data-document-viewer]');
        const assertionsList = document.querySelector('[data-assertions-list]');
        const assertionTemplate = document.getElementById('assertion-row-template');

        if (viewerRoot) {
            const viewer = new DocumentViewer(viewerRoot);
            document.querySelector('[data-reset-viewer]')?.addEventListener('click', () => viewer.reset());
        }

        if (assertionsList && assertionTemplate) {
            const assertionsForm = new AssertionsForm(assertionsList, assertionTemplate);
            assertionsForm.addRow({ role: 'Sujet' });

            document.querySelector('[data-add-assertion]')?.addEventListener('click', () => {
                assertionsForm.addRow();
            });
        }

        document.querySelector('[data-ai-parser]')?.addEventListener('click', mockAiParser);
    });
})();
