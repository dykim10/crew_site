import './bootstrap';

import Alpine from 'alpinejs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

window.Alpine = Alpine;

// TipTap 에디터 Alpine 컴포넌트
Alpine.data('tiptap', (opts = {}) => ({
    editor:  null,
    content: opts.content ?? '',

    // 툴바 active 상태 — 반응형 객체로 추적 (isActive() 메서드 스코프 문제 방지)
    formats: {
        bold: false, italic: false, strike: false,
        bulletList: false, orderedList: false, blockquote: false,
    },

    init() {
        const self = this;
        this.editor = new Editor({
            element: this.$refs.editorEl,
            extensions: [
                StarterKit,
                Placeholder.configure({
                    placeholder: opts.placeholder ?? '내용을 입력하세요...',
                }),
            ],
            content: opts.content ?? '',
            onUpdate({ editor }) {
                self.content = editor.getHTML();
                self._syncFormats(editor);
            },
            onSelectionUpdate({ editor }) {
                self._syncFormats(editor);
            },
        });

        window.addEventListener('beforeunload', () => this.editor?.destroy(), { once: true });
    },

    destroy() {
        this.editor?.destroy();
    },

    _syncFormats(editor) {
        this.formats = {
            bold:         editor.isActive('bold'),
            italic:       editor.isActive('italic'),
            strike:       editor.isActive('strike'),
            bulletList:   editor.isActive('bulletList'),
            orderedList:  editor.isActive('orderedList'),
            blockquote:   editor.isActive('blockquote'),
        };
    },

    toggleBold()        { this.editor?.chain().focus().toggleBold().run(); },
    toggleItalic()      { this.editor?.chain().focus().toggleItalic().run(); },
    toggleStrike()      { this.editor?.chain().focus().toggleStrike().run(); },
    toggleBulletList()  { this.editor?.chain().focus().toggleBulletList().run(); },
    toggleOrderedList() { this.editor?.chain().focus().toggleOrderedList().run(); },
    toggleBlockquote()  { this.editor?.chain().focus().toggleBlockquote().run(); },
}));

Alpine.start();
