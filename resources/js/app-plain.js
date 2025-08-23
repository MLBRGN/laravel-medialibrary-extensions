// noinspection ES6UnusedImports

import '@/js/shared/general';
import '@/js/shared/image-fallback';
import '@/js/shared/image-editor-listener'
import '@/js/plain/media-carousel';
import '@/js/plain/modal-core'
import '@/js/plain/modal-media'
import '@/js/plain/modal-image-editor'
import '@/css/app-plain.scss';

// ImageEditor custom element
import ImageEditor from "@evertjanmlbrgn/imageeditor";// imported for side effects
ImageEditor.translationsPath = '/js/vendor/image-editor/lang';
