import './bootstrap';

import Alpine from 'alpinejs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';

window.Alpine = Alpine;

/**
 * TipTap 에디터 Alpine 컴포넌트
 *
 * 핵심 설계 원칙:
 * - Editor 인스턴스를 클로저 변수(_ed)에 보관 — Alpine Proxy 밖에 둬야 함
 *   Alpine v3은 data 객체를 Proxy로 감싸므로, editor를 this.editor에 넣으면
 *   ProseMirror 내부 dispatch 시 "Applying a mismatched transaction" 발생
 * - StarterKit v3은 Underline 내장 — 별도 import 금지(중복 등록 에러)
 * - 툴바 버튼은 @mousedown.prevent 필수 — @click 시 포커스 이탈 → 상태 불일치
 */
Alpine.data('tiptap', (opts = {}) => {
    // ── 클로저 변수: Alpine Proxy 밖에 보관 ──────────────────
    let _ed = null;

    return {
        content:  opts.content  ?? '',
        formats: {
            bold: false, italic: false, underline: false, strike: false,
            h2: false, h3: false,
            bulletList: false, orderedList: false, blockquote: false,
        },
        charCount: 0,
        images:    [],
        maxImages: opts.maxImages ?? 0,
        uploadUrl: opts.uploadUrl ?? '/boards/images/upload',

        init() {
            const self = this;

            _ed = new Editor({
                element: this.$refs.editorEl,
                extensions: [
                    StarterKit,
                    // StarterKit v3 내장: Bold, Italic, Underline, Strike,
                    //   Heading, BulletList, OrderedList, Blockquote, HorizontalRule ...
                    Image.configure({ inline: false }),
                    Placeholder.configure({ placeholder: opts.placeholder ?? '내용을 입력하세요...' }),
                ],
                content: opts.content ?? '',
                onUpdate({ editor }) {
                    self.content   = editor.getHTML();
                    self.charCount = editor.getText().length;
                    self._sync(editor);
                },
                onSelectionUpdate({ editor }) {
                    self._sync(editor);
                },
            });

            this.charCount = _ed.getText().length;

            // 페이지 이탈 시 정리
            window.addEventListener('beforeunload', () => _ed?.destroy(), { once: true });
        },

        destroy() { _ed?.destroy(); },

        _sync(editor) {
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

        // ── 툴바 커맨드 (@mousedown.prevent 로 호출) ─────────
        toggleBold()        { _ed?.chain().focus().toggleBold().run(); },
        toggleItalic()      { _ed?.chain().focus().toggleItalic().run(); },
        toggleUnderline()   { _ed?.chain().focus().toggleUnderline().run(); },
        toggleStrike()      { _ed?.chain().focus().toggleStrike().run(); },
        toggleH2()          { _ed?.chain().focus().toggleHeading({ level: 2 }).run(); },
        toggleH3()          { _ed?.chain().focus().toggleHeading({ level: 3 }).run(); },
        toggleBulletList()  { _ed?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { _ed?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote()  { _ed?.chain().focus().toggleBlockquote().run(); },
        insertHR()          { _ed?.chain().focus().setHorizontalRule().run(); },

        _insertUrl(url) {
            _ed?.chain().focus().setImage({ src: url }).run();
        },

        // ── 이미지 슬롯 ──────────────────────────────────────
        addImages(files) {
            Array.from(files).forEach(file => {
                if (this.images.length >= this.maxImages) return;
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e =>
                    this.images.push({ file, previewUrl: e.target.result, s3Url: null, uploading: false });
                reader.readAsDataURL(file);
            });
        },

        removeImage(idx) {
            this.images.splice(idx, 1);
        },

        async insertImageToEditor(idx) {
            const img = this.images[idx];
            if (!img) return;
            if (img.s3Url) { this._insertUrl(img.s3Url); return; }

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
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                if (!data.url) throw new Error('응답에 URL 없음');
                img.s3Url = data.url;
                this._insertUrl(data.url);
            } catch (e) {
                console.error('[TipTap] 이미지 업로드 실패:', e.message);
                alert('이미지 업로드에 실패했습니다. 다시 시도해주세요.');
            } finally {
                img.uploading = false;
            }
        },
    };
});

Alpine.start();
