---
description: Reglas SEO obligatorias para artículos de blog en yatezzitos.com
---

# Reglas SEO para Blog Posts — Yatezzitos

Estas reglas se aplican SIEMPRE que se cree o edite un artículo de blog.

## 1. Palabra clave de enfoque (Focus Keyword)

### Ubicación obligatoria (al INICIO)
La frase clave debe mencionarse **tal cual está escrita** y debe **empezar** en estos 3 lugares:
1. **Título SEO** — Empieza con la frase clave exacta.
2. **Meta descripción** — Empieza con la frase clave exacta.
3. **Slug (URL)** — Empieza con la frase clave exacta (formato slug con guiones).

### Ejemplo:
Si la frase clave es `yates los cabos`:
- ✅ Título SEO: `Yates Los Cabos: renta privada y lanchas de lujo`
- ✅ Meta descripción: `Yates Los Cabos para rentar. Catálogo completo...`
- ✅ Slug: `yates-los-cabos-renta-privada`
- ❌ Título SEO: `Renta de Yates Los Cabos` (keyword no está al inicio)

## 2. Densidad de keyword — Regla anti-sobreoptimización

### Máximos por longitud de texto
| Caracteres del texto | Menciones máximas (H1+H2+párrafos) |
|---|---|
| < 1,500 | 5 |
| 1,500 – 2,800 | 7 |
| 2,800 – 3,400 | 9 |
| 3,400 – 5,000 | 11 |
| > 5,000 | 11 (nunca exceder 11) |

> **Regla general:** ~1 mención por cada 400 caracteres, con un **máximo absoluto de 11** menciones totales (incluyendo H1, H2 y párrafos).

### Distribución equitativa
- Las menciones deben estar **repartidas de forma uniforme** a lo largo del texto.
- ❌ NO concentrar varias menciones en los primeros párrafos y dejar el resto sin keyword.
- ✅ Distribuir una mención cada 2-3 párrafos aprox.

## 3. Títulos H2

### Regla del 50% máximo
- **Menos del 50%** de los H2 deben contener la frase clave.
- **Mínimo 3 H2** deben contener la frase clave.

### Ejemplo con 7 H2:
- ✅ 3 de 7 H2 con keyword (43%) → Correcto
- ❌ 5 de 7 H2 con keyword (71%) → Sobreoptimizado

## 4. Legibilidad y Comprensión (Yoast SEO)

Para asegurar que el usuario tenga la mejor experiencia (y Google nos premie con tiempo en página), es vital cumplir las reglas de legibilidad de Yoast:

### Palabras de transición
- **Regla:** Mínimo **30%** de las oraciones deben contener palabras de transición.
- ✅ Utiliza palabras que conecten ideas: *Además, Por lo tanto, Sin embargo, Primero, Después, Por otro lado, De hecho, Así que, Entonces, Finalmente, También, Porque, Por consiguiente.*

### Complejidad de palabras
- **Regla:** Mantén las palabras complejas (muy largas o sumamente técnicas) por debajo del **10%** del texto total.
- ✅ Escribe en un tono conversacional, directo y fácil de comprender (nivel de lectura ágil).
- ✅ Reemplaza términos muy largos por sinónimos más cortos y familiares. Ejemplo: usa *barcos* en lugar de *embarcaciones*; *hermosos* en vez de *espectaculares*, *servicios* en vez de *amenidades o especificaciones*.

## 5. Formato de Entrega (HTML Puro)

Para acelerar la implementación en WordPress:
- **Regla:** Todas las descripciones largas de páginas de categorías y destinos DEBEN ser generadas SIEMPRE en **formato HTML puro** listas para copiar y pegar.
- ✅ Utilizar etiquetas estructurales (`<h1>`, `<h2>`, `<h3>`, `<p>`, `<ul>`, `<ol>`, `<li>`, `<strong>`, `<a>`) y tablas HTML (`<table>`, `<tr>`, `<th>`, `<td>`).
- ❌ NO generar los textos en Markdown sin etiquetas si el destino es el editor (pestaña HTML/Text) de WordPress.

## 6. Checklist antes de publicar

- [ ] Frase clave al INICIO del título SEO
- [ ] Frase clave al INICIO de la meta descripción
- [ ] Frase clave al INICIO del slug
- [ ] Menciones dentro del rango según tabla de caracteres (Anti-sobreoptimización)
- [ ] Menos del 50% de H2 con keyword
- [ ] Mínimo 3 H2 con keyword
- [ ] Distribución equitativa de menciones en el texto
- [ ] **Legibilidad:** Más del 30% de oraciones tienen palabras de transición
- [ ] **Legibilidad:** Menos del 10% de las palabras son rimbombantes/complejas
- [ ] **Formato:** El texto entregado es 100% código HTML puro listo para pegar en WP.
- [ ] Categoría correcta asignada según la ciudad
- [ ] 2-3 imágenes con atribución en comentarios HTML
- [ ] Enlaces internos cruzados a otros artículos
- [ ] CTA con WhatsApp, correo y catálogo al final