# Solución de Desbordamiento de WhatsApp (Pantalla de Yates y Productos)

Este archivo documenta el código CSS necesario para solucionar el desbordamiento horizontal en dispositivos móviles causado por los números de contacto y el enlace de WhatsApp en las pantallas de productos/yates.

## Problema

La caja de contacto contenía un `d-flex` que, al encogerse la pantalla, envolvía los elementos de forma irregular (ej. el icono en una línea y el texto en otra), o los textos largos como el enlace de WhatsApp empujaban el ancho de la pantalla generando scroll horizontal (`overflow-x`).

## Solución (CSS Grid)

Para asegurar una visualización *Premium* y profesional en dispositivos móviles menores a 768px, se transformó el contenedor de contactos en un Layout de Grid de 2 columnas. La primera columna contiene los iconos alineados verticalmente, y la segunda columna maneja los textos forzando un `word-break` cuando el ancho exceda la pantalla.

Código a incluir en "CSS Adicional" de WordPress o en la hoja de estilos global:

```css
/* =========================================================================
   UX PREMIUM: Contacto del Agente (Bloqueo de desbordamiento horizontal)
   ========================================================================= */

/* --- MEDIDA DE SEGURIDAD ESTRUCTURAL --- */
html, body {
    max-width: 100vw;
    overflow-x: hidden !important;
}

/* --- COMPORTAMIENTO PARA ESCRITORIO --- */
.property-contact-agent-wrap .agent-information .agent-phone-wrap {
    flex-wrap: wrap !important;
}

/* --- DISEÑO MÓVIL MAGISTRAL (Dispositivos hasta 768px) --- */
@media (max-width: 768px) {
    /* 1. Grid de 2 columnas infalible para emparejar permanentemente Icono + Texto */
    .property-contact-agent-wrap .agent-information .agent-phone-wrap {
        display: grid !important;
        /* Col 1 fija en 24px (Iconos). Col 2 toma el resto, pero permitiendo achicarse hasta 0 (evita muros invisibles) */
        grid-template-columns: 24px minmax(0, 1fr) !important; 
        gap: 12px 10px !important; /* 12px de separación vertical, 10px de separación horizontal */
        align-items: center;
        width: 100%;
        padding-right: 15px; /* Margen de respiro lateral */
    }

    /* 2. Alineación inmaculada para los iconos (Primera columna) */
    .property-contact-agent-wrap .agent-information .agent-phone-wrap i {
        margin: 0 !important; /* Limpiamos márgenes nativos que ensucian la alineación */
        display: flex;
        justify-content: center;
        font-size: 16px; 
    }

    /* 3. Comportamiento líquido inteligente para los textos y enlaces (Segunda columna) */
    .property-contact-agent-wrap .agent-information .agent-phone-wrap span,
    .property-contact-agent-wrap .agent-information .agent-phone-wrap a {
        margin: 0 !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        display: block; 
        max-width: 100%;
        line-height: 1.4;
    }
}
```
