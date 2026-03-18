<?php
/**
 * YZZ | Leer más / Leer menos + botón descargar imágenes (v8)
 *
 * IMPORTANTE — Fix crítico WordPress (agregar en snippet separado o aquí mismo):
 * WordPress por defecto elimina el HTML de las descripciones de taxonomías (categorías/ciudades).
 * Los dos filtros de abajo lo corrigen. Sin ellos, todo el HTML que pegues en la descripción
 * de la categoría se guardará como texto plano y perderá todo el formato.
 *
 * Cambios v8 respecto a v7:
 * - FIX CRÍTICO: Añadidos filtros PHP para preservar HTML en taxonomías
 * - FIX JS: removeEmptyTaxNodes ya no elimina <ul>, <ol>, <table> con hijos
 * - FIX JS: La transición "Leer más" en taxonomías de un solo bloque
 *   corta el texto dentro del bloque sin eliminar elementos hijos
 * - FIX JS: Se mantienen todos los estilos HTML al expandir/contraer
 */

// =============================================================================
// FIX CRÍTICO: Preservar HTML en descripciones de taxonomías (ciudades)
// Sin estos filtros, WordPress guarda el HTML como texto plano al editar
// una categoría y el frontend muestra solo texto sin formato.
// =============================================================================
remove_filter( 'pre_term_description', 'wp_filter_kses' );
add_filter( 'pre_term_description', 'wp_filter_post_kses' );
add_filter( 'term_description', 'wp_specialchars_decode' );

// =============================================================================
// FUNCIÓN PRINCIPAL: Read More + Drive Download
// =============================================================================
function yzz_seo_readmore_and_drive_download_master_fix() {
    $is_property = is_singular( 'property' );
    $is_tax_like = is_tax() || is_post_type_archive( 'property' );

    if ( ! $is_property && ! $is_tax_like ) {
        return;
    }
    ?>
    <style>
        /* ── Taxonomía móvil overflow fix ── */
        article.taxonomy-description.yzz-tax-collapsible {
            overflow: hidden !important;
        }

        .yzz-tax-single-collapsible {
            overflow: hidden !important;
            transition: max-height .30s ease;
            will-change: max-height;
        }

        .yzz-readmore-scope { position: relative; }

        .yzz-readmore-toggle {
            appearance: none;
            -webkit-appearance: none;
            border: 0;
            background: transparent;
            color: #007BFF;
            text-decoration: underline;
            cursor: pointer;
            display: inline-block;
            margin-top: 12px;
            padding: 0;
            font-weight: 600;
            font-size: 14px;
            line-height: 1.3;
        }

        .yzz-readmore-toggle:hover { opacity: .9; }

        .yzz-readmore-control { margin-top: 12px; }

        .yzz-property-readmore-legacy { display: none !important; }

        article.taxonomy-description {
            position: relative !important;
            z-index: 2 !important;
            overflow: visible !important;
            height: auto !important;
            margin-bottom: 28px !important;
        }

        /* Conservar estilos HTML dentro del bloque colapsado/expandido */
        article.taxonomy-description h1,
        article.taxonomy-description h2,
        article.taxonomy-description h3,
        article.taxonomy-description h4,
        article.taxonomy-description h5,
        article.taxonomy-description h6,
        .yzz-hidden-blocks h1,
        .yzz-hidden-blocks h2,
        .yzz-hidden-blocks h3,
        .yzz-hidden-blocks h4,
        .yzz-tax-single-collapsible h1,
        .yzz-tax-single-collapsible h2,
        .yzz-tax-single-collapsible h3 {
            display: block !important;
        }

        article.taxonomy-description ul,
        article.taxonomy-description ol,
        article.taxonomy-description table,
        .yzz-hidden-blocks ul,
        .yzz-hidden-blocks ol,
        .yzz-hidden-blocks table,
        .yzz-tax-single-collapsible ul,
        .yzz-tax-single-collapsible ol,
        .yzz-tax-single-collapsible table {
            display: block !important;
        }

        article.taxonomy-description li,
        .yzz-hidden-blocks li,
        .yzz-tax-single-collapsible li {
            display: list-item !important;
        }

        .listing-wrap,
        .listing-wrap .container,
        .listing-wrap .row,
        .listing-wrap .col-lg-12,
        .listing-wrap .col-md-12,
        .listing-wrap .col-sm-12 {
            overflow: visible !important;
        }

        .yzz-hidden-blocks { display: none !important; }
        .yzz-hidden-blocks.yzz-visible { display: block !important; }

        .yzz-tax-collapsible {
            overflow: hidden !important;
            transition: max-height .30s ease;
        }

        .yzz-prop-desc-content { display: block; }
        .yzz-prop-desc-content.yzz-property-collapsible {
            overflow: hidden !important;
            transition: max-height .34s ease;
            will-change: max-height;
        }

        .yzz-drive-overview-hidden { display: none !important; }
        .property-overview-data li[class*="url-de-las-imagen"] { display: none !important; }

        .yzz-download-tool {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .yzz-download-tool a {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
            border-radius: 6px;
            background: #fff;
            color: #000;
            text-decoration: none;
            transition: all .18s ease;
            box-sizing: border-box;
        }

        .yzz-download-tool a:hover { transform: translateY(-1px); opacity: .95; }

        .yzz-download-tool svg {
            width: 18px;
            height: 18px;
            display: block;
            fill: currentColor;
        }

        .yzz-download-mobile { list-style: none; margin: 0; padding: 0; }

        .yzz-download-mobile a {
            min-width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #0c2d38;
            border: 1px solid #0c2d38;
            color: #fff;
            padding: 0 8px;
            text-decoration: none;
            box-sizing: border-box;
        }

        .yzz-download-mobile svg {
            width: 20px;
            height: 20px;
            display: block;
            fill: currentColor;
        }

        @media (max-width: 767px) {
            .yzz-readmore-toggle { font-size: 13px; }
        }
    </style>

    <script>
    (function () {
        'use strict';

        if (window.__YZZ_MASTER_FIX_V8__) return;
        window.__YZZ_MASTER_FIX_V8__ = true;

        const IS_PROPERTY = <?php echo $is_property ? 'true' : 'false'; ?>;
        const IS_TAX      = <?php echo $is_tax_like  ? 'true' : 'false'; ?>;

        let driveUrlCache = '';

        /* ── Utilidades básicas ── */
        function each(list, fn) {
            Array.prototype.forEach.call(list || [], fn);
        }

        function addUnique(arr, el) {
            if (el && arr.indexOf(el) === -1) arr.push(el);
        }

        function text(input) {
            if (!input) return '';
            const raw = (typeof input === 'string') ? input : (input.textContent || '');
            return raw.replace(/\s+/g, ' ').trim();
        }

        function normalize(input) {
            let t = text(input);
            if (t.normalize) t = t.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            return t.toUpperCase();
        }

        function isHeading(el) {
            return !!(el && /^H[1-6]$/.test(el.tagName));
        }

        function isVisible(el) {
            if (!el) return false;
            const st = window.getComputedStyle(el);
            if (st.display === 'none' || st.visibility === 'hidden') return false;
            return el.getClientRects().length > 0;
        }

        function hasMeaningfulContent(el) {
            if (!el) return false;
            const tag = (el.tagName || '').toUpperCase();
            if (tag === 'HR') return true;
            if (text(el).length > 0) return true;
            if (el.querySelector('img,svg,iframe,video,picture,ul,ol,table,blockquote,hr')) return true;
            return false;
        }

        function shouldIgnoreNode(el) {
            if (!el) return true;
            const tag = (el.tagName || '').toUpperCase();
            if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'BR') return true;
            if (el.classList.contains('yzz-readmore-toggle'))           return true;
            if (el.classList.contains('yzz-readmore-control'))          return true;
            if (el.classList.contains('yzz-hidden-blocks'))             return true;
            if (el.classList.contains('yzz-property-readmore-legacy'))  return true;
            return false;
        }

        function hideLegacyReadMore(scope) {
            if (!scope) return;
            const selectors = ['span.read-more-btn','a.read-more','.read-more','.more-link','span.read-more-button'];
            each(scope.querySelectorAll(selectors.join(',')), function (el) {
                el.classList.add('yzz-property-readmore-legacy');
                el.setAttribute('aria-hidden', 'true');
            });
            each(scope.querySelectorAll('a,span,button,p,div,strong,em'), function (el) {
                if (el.classList.contains('yzz-readmore-toggle')) return;
                const token = normalize(el.textContent);
                if (token === 'READ MORE' || token === 'LEER MAS' || token === 'LEER MENOS') {
                    el.classList.add('yzz-property-readmore-legacy');
                    el.setAttribute('aria-hidden', 'true');
                }
            });
        }

        function createToggleButton() {
            const btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'yzz-readmore-toggle';
            btn.textContent = 'Leer más';
            return btn;
        }

        function getDirectChild(parent, node) {
            let cur = node;
            while (cur && cur.parentElement !== parent) cur = cur.parentElement;
            return (cur && cur.parentElement === parent) ? cur : null;
        }

        /* =============================================================================
           TAXONOMÍAS / CIUDADES
           ============================================================================= */

        /**
         * FIX v8: Solo elimina nodos que son HOJA (sin hijos) y sin texto.
         * Antes eliminaba <ul>, <ol> y <table> que lógicamente "parecían vacíos"
         * desde textContent pero tenían hijos (<li>, <tr>, etc.).
         */
        function removeEmptyTaxNodes(container) {
            if (!container) return;
            each(Array.prototype.slice.call(container.querySelectorAll('*')), function (el) {
                if (!el || !el.parentNode) return;
                const tag = (el.tagName || '').toUpperCase();

                // Solo eliminar <br> sueltos
                if (tag === 'BR') { el.remove(); return; }

                // Nunca tocar elementos que agrupan otros (<ul>, <ol>, <table>, etc.)
                if (tag === 'HR' || tag === 'UL' || tag === 'OL' || tag === 'TABLE' ||
                    tag === 'TBODY' || tag === 'THEAD' || tag === 'TR') return;

                // Solo eliminar si es hoja (sin hijos de elemento) y sin texto
                if (el.children.length === 0 && !text(el)) el.remove();
            });
        }

        function getUsefulChildren(container) {
            const out = [];
            if (!container) return out;
            each(container.children, function (child) {
                if (!child || shouldIgnoreNode(child)) return;
                if (hasMeaningfulContent(child)) out.push(child);
            });
            return out;
        }

        function applyTaxReadMore(article) {
            if (!article || article.dataset.yzzTaxReady === '1') return;

            hideLegacyReadMore(article);
            removeEmptyTaxNodes(article);

            const blocks = getUsefulChildren(article);
            if (!blocks.length) {
                article.dataset.yzzTaxReady = '1';
                return;
            }

            article.classList.add('yzz-readmore-scope');

            // Mostrar: primer título (H1/H2/H3) + primer párrafo = 2 bloques
            // Si el bloque 0 no es heading, mostrar solo el primer bloque
            const keepCount = isHeading(blocks[0]) ? 2 : 1;
            const hiddenBlocks = blocks.slice(keepCount);

            if (hiddenBlocks.length) {
                const hiddenWrap = document.createElement('div');
                hiddenWrap.className = 'yzz-hidden-blocks';

                hiddenBlocks.forEach(function (node) {
                    hiddenWrap.appendChild(node);
                });

                article.appendChild(hiddenWrap);

                const btn = createToggleButton();
                btn.addEventListener('click', function () {
                    const expanded = article.classList.toggle('yzz-expanded');
                    hiddenWrap.classList.toggle('yzz-visible', expanded);
                    btn.textContent = expanded ? 'Leer menos' : 'Leer más';
                    if (!expanded) {
                        article.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });

                article.appendChild(btn);
                article.dataset.yzzTaxReady = '1';
                return;
            }

            // Caso: un único bloque largo → colapsar por altura
            // Nota: altura reducida intencionalmente para cortar ~15% del contenido visible
            const oneText = text(blocks[0]);
            if (blocks.length === 1 && oneText.length > 420) {
                const contentBlock  = blocks[0];
                const collapsedH    = window.matchMedia('(max-width: 479px)').matches ? 90 : 110;

                contentBlock.classList.add('yzz-tax-single-collapsible');
                contentBlock.style.maxHeight = collapsedH + 'px';

                const btnSingle = createToggleButton();
                btnSingle.addEventListener('click', function () {
                    const expanded = contentBlock.classList.contains('yzz-expanded');

                    if (expanded) {
                        contentBlock.classList.remove('yzz-expanded');
                        contentBlock.style.maxHeight = collapsedH + 'px';
                        btnSingle.textContent = 'Leer más';
                        article.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        contentBlock.classList.add('yzz-expanded');
                        contentBlock.style.maxHeight = contentBlock.scrollHeight + 'px';
                        btnSingle.textContent = 'Leer menos';
                        setTimeout(function () {
                            if (contentBlock.classList.contains('yzz-expanded')) {
                                contentBlock.style.maxHeight = 'none';
                            }
                        }, 360);
                    }
                });

                article.appendChild(btnSingle);
            }

            article.dataset.yzzTaxReady = '1';
        }

        function initTaxReadMore() {
            each(document.querySelectorAll('article.taxonomy-description'), function (article) {
                applyTaxReadMore(article);
            });
        }

        /* =============================================================================
           PROPERTY READ MORE
           ============================================================================= */
        let yzzPropCardSeq = 0;

        function yzzEnsureCardId(card) {
            if (card.id) return card.id;
            yzzPropCardSeq += 1;
            card.id = 'yzz-prop-card-' + yzzPropCardSeq;
            return card.id;
        }

        function yzzFindDescriptionHeading(card) {
            let heading = null;
            each(card.querySelectorAll('.block-title, .block-title-wrap h1, .block-title-wrap h2, .block-title-wrap h3, h1, h2, h3, h4, h5'), function (h) {
                if (heading) return;
                const t = normalize(h.textContent || '');
                if (t.indexOf('DESCRIPCION') === -1 && t.indexOf('DESCRIPTION') === -1) return;
                if (t.indexOf('CARACTER') !== -1 || t.indexOf('FEATURE') !== -1 || t.indexOf('AMENIDAD') !== -1) return;
                heading = h;
            });
            return heading;
        }

        function yzzGetDescriptionCandidates() {
            const out = [];
            each(document.querySelectorAll('.block-title, .block-title-wrap h1, .block-title-wrap h2, .block-title-wrap h3, h1, h2, h3, h4, h5'), function (h) {
                const t = normalize(h.textContent || '');
                if (t.indexOf('DESCRIPCION') === -1 && t.indexOf('DESCRIPTION') === -1) return;
                if (t.indexOf('CARACTER') !== -1 || t.indexOf('FEATURE') !== -1 || t.indexOf('AMENIDAD') !== -1) return;
                let card = h.closest('.block-wrap');
                if (!card) card = h.closest('[class*="block-wrap"]');
                if (!card) return;
                if (!isVisible(card)) return;
                addUnique(out, card);
            });
            return out;
        }

        function yzzPickDescriptionCard() {
            const candidates = yzzGetDescriptionCandidates();
            let best = null, bestScore = -Infinity;
            candidates.forEach(function (card) {
                const t = normalize(card.textContent || '');
                let score = 0;
                if (t.indexOf('COPIAR DESCRIPCION') !== -1) score += 300;
                if (card.querySelector('[class*="copy"]'))   score += 120;
                if (t.indexOf('CARACTERISTICA') !== -1)      score -= 500;
                score += Math.min(400, text(card).length / 8);
                score -= card.querySelectorAll('*').length * 0.35;
                if (score > bestScore) { bestScore = score; best = card; }
            });
            return best;
        }

        function yzzFindDirectChildByClass(parent, cls) {
            let out = null;
            each(parent.children, function (ch) {
                if (ch.classList && ch.classList.contains(cls)) out = ch;
            });
            return out;
        }

        function yzzEnsureDescriptionContent(card) {
            let existing = yzzFindDirectChildByClass(card, 'yzz-prop-desc-content');
            if (existing) return existing;

            let explicit = null;
            each(card.children, function (ch) {
                if (explicit || !ch || !ch.classList) return;
                if (ch.classList.contains('block-content-wrap') || ch.classList.contains('block-content') || ch.classList.contains('entry-content')) {
                    explicit = ch;
                }
            });

            if (explicit) {
                explicit.classList.add('yzz-prop-desc-content');
                return explicit;
            }

            const heading = yzzFindDescriptionHeading(card);
            const headerWrap = heading ? (heading.closest('.block-title-wrap, .block-title-wrap-v2, .title-wrap, .d-flex') || heading) : null;

            const nodes = [];
            each(card.children, function (ch) {
                if (!ch) return;
                const tag = (ch.tagName || '').toUpperCase();
                if (tag === 'SCRIPT' || tag === 'STYLE') return;
                if (ch.classList.contains('yzz-readmore-control')) return;
                if (headerWrap && ch === headerWrap) return;
                nodes.push(ch);
            });

            if (!nodes.length) return null;

            const wrap = document.createElement('div');
            wrap.className = 'yzz-prop-desc-content';
            card.appendChild(wrap);
            nodes.forEach(function (n) { wrap.appendChild(n); });
            return wrap;
        }

        function yzzUnlockAncestors(node) {
            let cur = node, hops = 0;
            while (cur && cur !== document.body && hops < 10) {
                const cls = ((cur.className || '') + '').toLowerCase();
                if (hops < 4 || /(property|block|content|wrap|detail|section|description)/.test(cls)) {
                    cur.style.overflow = 'visible';
                    cur.style.maxHeight = 'none';
                    if (cur.style.height && cur.style.height !== 'auto') cur.style.height = 'auto';
                }
                cur = cur.parentElement;
                hops += 1;
            }
        }

        function yzzRevealNestedHiddenContent(scope) {
            if (!scope) return;
            hideLegacyReadMore(scope);
            each(scope.querySelectorAll('*'), function (el) {
                if (!el || el.classList.contains('yzz-property-readmore-legacy')) return;
                const raw = (el.getAttribute('style') || '').toLowerCase();
                if (!raw) return;
                const t = normalize(el.textContent || '');
                if (t === 'READ MORE' || t === 'LEER MAS' || t === 'LEER MENOS') return;
                const hasReal = text(el).length > 60 || el.querySelector('p,li,ul,ol,hr,h2,h3,h4,table,blockquote');
                if (!hasReal) return;
                if (/display\s*:\s*none/.test(raw) || /visibility\s*:\s*hidden/.test(raw) || /opacity\s*:\s*0/.test(raw)) {
                    const tag = (el.tagName || '').toUpperCase();
                    if (tag === 'SPAN') el.style.display = 'inline';
                    else if (tag === 'LI') el.style.display = 'list-item';
                    else el.style.display = 'block';
                    el.style.visibility = 'visible';
                    el.style.opacity = '1';
                }
                if (/max-height\s*:/.test(raw) || /overflow\s*:\s*hidden/.test(raw) || /height\s*:\s*\d+px/.test(raw)) {
                    el.style.maxHeight = 'none';
                    el.style.overflow  = 'visible';
                    el.style.height    = 'auto';
                }
            });
        }

        function yzzMeasureAutoHeight(el) {
            const oldMax = el.style.maxHeight, oldOverflow = el.style.overflow, oldHeight = el.style.height;
            el.style.maxHeight = 'none'; el.style.overflow = 'visible'; el.style.height = 'auto';
            const h = Math.ceil(Math.max(el.scrollHeight, el.getBoundingClientRect().height));
            el.style.maxHeight = oldMax; el.style.overflow = oldOverflow; el.style.height = oldHeight;
            return h;
        }

        function yzzGetCollapsedHeight(content) {
            const mobile = window.matchMedia('(max-width: 767px)').matches;
            let base = mobile ? 190 : 255;
            const rich = content.querySelectorAll('ul,ol,li,hr,h2,h3,h4,table,blockquote,p').length;
            if (rich > 8) base += mobile ? 30 : 55;
            return base;
        }

        function yzzGetPropertyControl(card) {
            let out = null;
            each(card.children, function (ch) {
                if (ch.classList && ch.classList.contains('yzz-readmore-control') && ch.classList.contains('yzz-property-control')) {
                    out = ch;
                }
            });
            return out;
        }

        function yzzRemovePropertyControl(card) {
            const c = yzzGetPropertyControl(card);
            if (c) c.remove();
        }

        function yzzCleanupPropertyControls(validCardId) {
            each(document.querySelectorAll('.yzz-readmore-control.yzz-property-control'), function (control) {
                const cid = control.getAttribute('data-yzz-card-id') || '';
                if (!cid || !document.getElementById(cid) || (validCardId && cid !== validCardId)) {
                    control.remove();
                }
            });
        }

        function yzzEnsurePropertyControl(card, content) {
            const cardId = yzzEnsureCardId(card);
            let control = yzzGetPropertyControl(card);
            if (!control) {
                control = document.createElement('div');
                control.className = 'yzz-readmore-control yzz-property-control';
                control.setAttribute('data-yzz-card-id', cardId);
                if (content.parentElement === card) content.insertAdjacentElement('afterend', control);
                else card.appendChild(control);
            }
            let btn = control.querySelector('.yzz-readmore-toggle');
            if (!btn) { btn = createToggleButton(); control.appendChild(btn); }
            if (!btn.dataset.yzzBound) {
                btn.dataset.yzzBound = '1';
                btn.addEventListener('click', function () { yzzTogglePropertyCard(card); });
            }
            return btn;
        }

        function yzzTogglePropertyCard(card) {
            const content  = card.querySelector('.yzz-prop-desc-content');
            const btn      = card.querySelector('.yzz-property-control .yzz-readmore-toggle');
            if (!content || !btn) return;

            const collapsed = parseInt(content.dataset.yzzCollapsedHeight || '255', 10);

            if (content.classList.contains('yzz-property-expanded')) {
                if (content.style.maxHeight === 'none') {
                    content.style.maxHeight = Math.ceil(content.scrollHeight) + 'px';
                    content.offsetHeight;
                }
                content.classList.remove('yzz-property-expanded');
                content.style.overflow = 'hidden';
                content.style.maxHeight = collapsed + 'px';
                btn.textContent = 'Leer más';
                setTimeout(function () { card.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 80);
                return;
            }

            yzzRevealNestedHiddenContent(content);
            yzzUnlockAncestors(card);

            const full = yzzMeasureAutoHeight(content);
            content.classList.add('yzz-property-expanded');
            content.style.overflow = 'hidden';
            content.style.maxHeight = Math.max(full + 40, collapsed + 90) + 'px';
            btn.textContent = 'Leer menos';

            clearTimeout(content.__yzzExpandTimer1);
            clearTimeout(content.__yzzExpandTimer2);

            content.__yzzExpandTimer1 = setTimeout(function () {
                if (!content.classList.contains('yzz-property-expanded')) return;
                yzzRevealNestedHiddenContent(content);
                const late = yzzMeasureAutoHeight(content);
                content.style.maxHeight = Math.max(late + 40, collapsed + 90) + 'px';
            }, 150);

            content.__yzzExpandTimer2 = setTimeout(function () {
                if (!content.classList.contains('yzz-property-expanded')) return;
                content.style.maxHeight = 'none';
                content.style.overflow  = 'visible';
                yzzUnlockAncestors(card);
            }, 560);
        }

        function yzzApplyPropertyReadMore(card) {
            if (!card || !isVisible(card)) return;
            const cardId  = yzzEnsureCardId(card);
            const content = yzzEnsureDescriptionContent(card);
            if (!content) { yzzRemovePropertyControl(card); yzzCleanupPropertyControls(cardId); return; }

            hideLegacyReadMore(content);
            yzzRevealNestedHiddenContent(content);
            yzzUnlockAncestors(card);

            const plain = text(content).replace(/copiar descripción/ig, '').replace(/copiar descripcion/ig, '').trim();
            const richNodes = content.querySelectorAll('p,li,ul,ol,hr,h2,h3,h4,table,blockquote').length;
            const hasRealDescription = plain.length >= 180 || richNodes >= 4;

            if (!hasRealDescription) {
                content.classList.remove('yzz-property-collapsible', 'yzz-property-expanded');
                content.style.maxHeight = 'none'; content.style.overflow = 'visible';
                yzzRemovePropertyControl(card); yzzCleanupPropertyControls(cardId);
                return;
            }

            const collapsed = yzzGetCollapsedHeight(content);
            const full      = yzzMeasureAutoHeight(content);
            const shouldCollapse = full > (collapsed + 32) && (plain.length > 420 || richNodes > 10);

            if (!shouldCollapse) {
                content.classList.remove('yzz-property-collapsible', 'yzz-property-expanded');
                content.style.maxHeight = 'none'; content.style.overflow = 'visible';
                yzzRemovePropertyControl(card); yzzCleanupPropertyControls(cardId);
                return;
            }

            card.classList.add('yzz-readmore-scope');
            content.classList.add('yzz-property-collapsible');
            content.dataset.yzzCollapsedHeight = String(collapsed);

            if (!content.classList.contains('yzz-property-expanded')) {
                content.style.overflow = 'hidden';
                content.style.maxHeight = collapsed + 'px';
            }

            const btn = yzzEnsurePropertyControl(card, content);
            btn.textContent = content.classList.contains('yzz-property-expanded') ? 'Leer menos' : 'Leer más';
            yzzCleanupPropertyControls(cardId);
        }

        function initPropertyReadMore() {
            const card = yzzPickDescriptionCard();
            if (!card) { yzzCleanupPropertyControls(''); return; }
            yzzApplyPropertyReadMore(card);
        }

        /* =============================================================================
           DRIVE DOWNLOAD
           ============================================================================= */
        function normalizeDriveUrl(url) {
            if (!url) return '';
            const clean = url.trim().replace(/[)\],.;]+$/g, '');
            const folder = clean.match(/drive\.google\.com\/drive\/folders\/([A-Za-z0-9_-]+)/i);
            if (folder && folder[1]) return 'https://drive.google.com/drive/folders/' + folder[1];
            const file = clean.match(/drive\.google\.com\/file\/d\/([A-Za-z0-9_-]+)/i);
            if (file && file[1]) return 'https://drive.google.com/drive/folders/' + file[1];
            const openId = clean.match(/[?&]id=([A-Za-z0-9_-]+)/i);
            if (openId && openId[1]) return 'https://drive.google.com/drive/folders/' + openId[1];
            return clean;
        }

        function findDesktopTools() {
            return document.querySelector('ul.property-item-tools.list-unstyled.d-flex.gap-1.m-0.p-0') ||
                   document.querySelector('ul.property-item-tools');
        }

        function findOverviewRoots() {
            const roots = [];
            each(document.querySelectorAll('.property-overview-data, .property-overview-wrap, .property-overview, .d-flex.property-overview-data'), function (el) {
                addUnique(roots, el);
            });
            each(document.querySelectorAll('li[class*="url-de-las-imagen"], [class*="url-de-las-imagen"]'), function (el) {
                addUnique(roots, el.closest('.property-overview-data') || el.closest('.property-overview-wrap') || el.closest('ul') || el.parentElement);
            });
            const tools = findDesktopTools();
            if (tools) addUnique(roots, tools.closest('.page-title-wrap') || tools.closest('.property-wrap'));
            return roots.filter(Boolean);
        }

        function extractDriveUrlFromOverview() {
            const roots = findOverviewRoots();
            let found = '';
            for (let i = 0; i < roots.length && !found; i++) {
                const root = roots[i];
                const anchors = root.querySelectorAll('a[href*="drive.google.com"]');
                for (let j = 0; j < anchors.length; j++) {
                    const href = anchors[j].getAttribute('href') || '';
                    if (/drive\.google\.com/i.test(href)) { found = normalizeDriveUrl(href); break; }
                }
                if (found) break;
                const nodes = root.querySelectorAll('li,div,p,span,strong');
                for (let k = 0; k < nodes.length && !found; k++) {
                    const raw = nodes[k].textContent || '';
                    if (/drive\.google\.com/i.test(raw) || /url\s*de\s*las\s*imagenes?/i.test(raw)) {
                        const m = raw.match(/https?:\/\/drive\.google\.com\/[^\s"'<>]+/i);
                        if (m && m[0]) found = normalizeDriveUrl(m[0]);
                    }
                }
                if (found) break;
                const allText = root.textContent || '';
                const m2 = allText.match(/https?:\/\/drive\.google\.com\/[^\s"'<>]+/i);
                if (m2 && m2[0]) found = normalizeDriveUrl(m2[0]);
            }
            return found;
        }

        function hideDriveFieldFromOverview() {
            each(findOverviewRoots(), function (root) {
                each(root.querySelectorAll('[class*="url-de-las-imagen"]'), function (el) {
                    el.classList.add('yzz-drive-overview-hidden');
                });
                each(root.querySelectorAll('li,div,p,span,strong,a'), function (el) {
                    if (el.closest('.yzz-download-tool, .yzz-download-mobile')) return;
                    const raw = el.textContent || '';
                    if (/url\s*de\s*las\s*imagenes?/i.test(raw) || /https?:\/\/drive\.google\.com\/[^\s"'<>]+/i.test(raw)) {
                        const host = el.closest('li') || el;
                        host.classList.add('yzz-drive-overview-hidden');
                    }
                });
                const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
                const textNodes = [];
                while (walker.nextNode()) {
                    const value = walker.currentNode.nodeValue || '';
                    if (/https?:\/\/drive\.google\.com\/[^\s"'<>]+/i.test(value) || /url\s*de\s*las\s*imagenes?/i.test(value)) {
                        textNodes.push(walker.currentNode);
                    }
                }
                textNodes.forEach(function (node) {
                    node.nodeValue = node.nodeValue
                        .replace(/https?:\/\/drive\.google\.com\/[^\s"'<>]+/ig, '')
                        .replace(/url\s*de\s*las\s*imagenes?/ig, '');
                });
            });
        }

        function createDownloadIcon() {
            const ns = 'http://www.w3.org/2000/svg';
            const svg = document.createElementNS(ns, 'svg');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('aria-hidden', 'true');
            const path = document.createElementNS(ns, 'path');
            path.setAttribute('d', 'M12 3a1 1 0 011 1v8.59l2.3-2.3 1.4 1.42L12 16.41 7.3 11.7l1.4-1.41 2.3 2.3V4a1 1 0 011-1zm-7 14a1 1 0 011 1v1h12v-1a1 1 0 112 0v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2a1 1 0 011-1z');
            svg.appendChild(path);
            return svg;
        }

        function createDownloadItem(url, mobile) {
            const li = document.createElement('li');
            li.className = mobile ? 'nav-item yzz-download-mobile' : 'item-tool yzz-download-tool';
            const a = document.createElement('a');
            a.href = url; a.target = '_blank'; a.rel = 'noopener noreferrer';
            a.setAttribute('aria-label', 'Descargar imágenes');
            a.setAttribute('title',      'Descargar imágenes');
            a.appendChild(createDownloadIcon());
            li.appendChild(a);
            return li;
        }

        function initDriveDownloadButton() {
            if (!driveUrlCache) driveUrlCache = extractDriveUrlFromOverview();
            if (driveUrlCache) {
                const desktopTools = findDesktopTools();
                if (desktopTools && !desktopTools.querySelector('.yzz-download-tool')) {
                    desktopTools.appendChild(createDownloadItem(driveUrlCache, false));
                }
                each(document.querySelectorAll('.mobile-property-tools ul.nav-pills, .mobile-property-tools ul, #pills-tab'), function (target) {
                    if (!target.querySelector('.yzz-download-mobile')) {
                        target.appendChild(createDownloadItem(driveUrlCache, true));
                    }
                });
            }
            hideDriveFieldFromOverview();
        }

        /* =============================================================================
           INIT
           ============================================================================= */
        function debounce(fn, wait) {
            let t;
            return function () { clearTimeout(t); t = setTimeout(fn, wait); };
        }

        function runAll() {
            if (IS_TAX)      initTaxReadMore();
            if (IS_PROPERTY) { initPropertyReadMore(); initDriveDownloadButton(); }
        }

        function boot() {
            runAll();
            [350, 900, 1800, 3200, 5200, 8000].forEach(function (ms) {
                setTimeout(runAll, ms);
            });
            if (IS_PROPERTY && window.MutationObserver) {
                const observer = new MutationObserver(debounce(runAll, 180));
                observer.observe(document.body, { childList: true, subtree: true });
                setTimeout(function () { observer.disconnect(); }, 25000);
            }
        }

        document.addEventListener('DOMContentLoaded', boot);
        window.addEventListener('load', runAll);
        window.addEventListener('resize', debounce(function () {
            if (IS_PROPERTY) runAll();
        }, 240));
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'yzz_seo_readmore_and_drive_download_master_fix', 99 );
