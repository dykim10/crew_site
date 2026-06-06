import './bootstrap';

import Alpine from 'alpinejs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

window.Alpine = Alpine;

// TipTap 에디터 Alpine 컴포넌트
// Alpine v3: init() 은 자동 호출 (x-init 불필요)
Alpine.data('tiptap', (opts = {}) => ({
    editor: null,
    content: opts.content ?? '',

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
            },
        });

        // 페이지 이탈 시 에디터 정리
        window.addEventListener('beforeunload', () => this.editor?.destroy(), { once: true });
    },

    destroy() {
        this.editor?.destroy();
    },

    toggleBold()        { this.editor?.chain().focus().toggleBold().run(); },
    toggleItalic()      { this.editor?.chain().focus().toggleItalic().run(); },
    toggleStrike()      { this.editor?.chain().focus().toggleStrike().run(); },
    toggleBulletList()  { this.editor?.chain().focus().toggleBulletList().run(); },
    toggleOrderedList() { this.editor?.chain().focus().toggleOrderedList().run(); },
    toggleBlockquote()  { this.editor?.chain().focus().toggleBlockquote().run(); },

    isActive(type) {
        return this.editor?.isActive(type) ?? false;
    },
}));

Alpine.start();
