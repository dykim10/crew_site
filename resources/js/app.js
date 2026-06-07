import './bootstrap';

import Alpine from 'alpinejs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
// Underline은 StarterKit v3에 내장 — 별도 import 시 중복 등록으로 "mismatched transaction" 발생
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';

window.Alpine = Alpine;

Alpine.data('tiptap', (opts = {}) => ({
    editor:    null,
    content:   opts.content ?? '',
    formats: {
        bold: false, italic: false, underline: false, strike: false,
        h2: false, h3: false,
        bulletList: false, orderedList: false, blockquote: false,
    },
    charCount:  0,
    images:     [],
    maxImages:  opts.maxImages ?? 0,
    uploadUrl:  opts.uploadUrl ?? '/boards/images/upload',

    init() {
        const self = this;
        this.editor = new Editor({
            element: this.$refs.editorEl,
            extensions: [
                StarterKit,
                Image.configure({ inline: false }),
                Placeholder.configure({ placeholder: opts.placeholder ?? '내용을 입력하세요...' }),
            ],
            content: opts.content ?? '',
            onUpdate({ editor }) {
                self.content   = editor.getHTML();
                self.charCount = editor.getText().length;
                self._syncFormats(editor);
            },
            onSelectionUpdate({ editor }) {
                self._syncFormats(editor);
            },
        });
        this.charCount = this.editor.getText().length;
        window.addEventListener('beforeunload', () => this.editor?.destroy(), { once: true });
    },

    destroy() { this.editor?.destroy(); },

    _syncFormats(editor) {
        this.formats = {
            bold:        editor.isActive('bold'),
            italic:      editor.isActive('italic'),
            underline:   editor.isActive('underline'),
            strike:      editor.isActive('strike'),
            h2:          editor.isActive('heading', { level: 2 }),
            h3:          editor.isActive('heading', { level: 3 }),
            bulletList:  editor.isActive('bulletList'),
            orderedList: editor.isActive('orderedList'),
            blockquote:  editor.isActive('blockquote'),
        };
    },

    toggleBold()        { this.editor?.chain().focus().toggleBold().run(); },
    toggleItalic()      { this.editor?.chain().focus().toggleItalic().run(); },
    toggleUnderline()   { this.editor?.chain().focus().toggleUnderline().run(); },
    toggleStrike()      { this.editor?.chain().focus().toggleStrike().run(); },
    toggleH2()          { this.editor?.chain().focus().toggleHeading({ level: 2 }).run(); },
    toggleH3()          { this.editor?.chain().focus().toggleHeading({ level: 3 }).run(); },
    toggleBulletList()  { this.editor?.chain().focus().toggleBulletList().run(); },
    toggleOrderedList() { this.editor?.chain().focus().toggleOrderedList().run(); },
    toggleBlockquote()  { this.editor?.chain().focus().toggleBlockquote().run(); },
    insertHR()          { this.editor?.chain().focus().setHorizontalRule().run(); },
    insertImageUrl(url) { this.editor?.chain().focus().setImage({ src: url }).run(); },

    // ── 이미지 슬롯 관리 ─────────────────────────────────────────
    addImages(files) {
        Array.from(files).forEach(file => {
            if (this.images.length >= this.maxImages) return;
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.images.push({ file, previewUrl: e.target.result, s3Url: null, uploading: false });
            };
            reader.readAsDataURL(file);
        });
    },

    removeImage(idx) {
        this.images.splice(idx, 1);
    },

    async insertImageToEditor(idx) {
        const img = this.images[idx];
        if (!img) return;
        if (img.s3Url) { this.insertImageUrl(img.s3Url); return; }

        img.uploading = true;
        try {
            const fd    = new FormData();
            fd.append('image', img.file);
            const token = document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const res   = await fetch(this.uploadUrl, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': token },
                body:    fd,
            });
            if (!res.ok) throw new Error();
            const { url } = await res.json();
            img.s3Url = url;
            this.insertImageUrl(url);
        } catch {
            alert('이미지 업로드에 실패했습니다. 다시 시도해주세요.');
        } finally {
            img.uploading = false;
        }
    },
}));

Alpine.start();
