# 03 — Página de Ayuda / Preguntas Frecuentes (FAQ)

Rediseño completo con 4 secciones (las categorías están fusionadas con el Hero). Cada sección contiene su HTML + CSS auto-contenido para Elementor.

Los nombres de las categorías se mantienen en inglés como en el diseño de Figma. Los textos de la sección de info blocks también.

---

## SECCIÓN 1 — Hero + Buscador + Categorías (Pills)

Los botones de categoría son **pills horizontales con glassmorphism** dentro del Hero. En móvil hacen **scroll horizontal** nativo.

<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 1: HERO + BUSCADOR + CATEGORÍAS
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── Variables Globales ── */
:root {
  --yz-primary: #0087a3;
  --yz-primary-dark: #006075;
  --yz-secondary: #002236;
  --yz-gold: #c3a152;
  --yz-white: #ffffff;
  --yz-gray-100: #f8fafc;
  --yz-gray-200: #e2e8f0;
  --yz-gray-500: #64748b;
  --yz-gray-800: #1e293b;
  --yz-radius-md: 12px;
  --yz-radius-lg: 24px;
  --yz-radius-full: 50px;
  --yz-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --yz-shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
  --yz-shadow-lg: 0 15px 40px rgba(0,0,0,0.12);
}

/* ── Hero ── */
.yz-help-hero {
  position: relative;
  width: 100%;
  min-height: 560px;
  background-image: linear-gradient(180deg, rgba(0,34,54,0.55) 0%, rgba(0,34,54,0.80) 100%),
    url('https://yatezzitos.com/wp-content/uploads/2026/03/imagen-de-fondo-primera-seccion.png');
  background-size: cover;
  background-position: right center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 70px 20px 40px;
}
.yz-help-hero-content { max-width: 720px; animation: yzFadeUp 0.8s ease backwards; }
.yz-help-hero-content h1 {
  font-family: 'DM Serif Display', serif;
  color: var(--yz-white); font-size: 3.2rem; font-weight: 400; margin: 0 0 12px;
}
.yz-help-hero-content p {
  font-family: 'Inter', sans-serif;
  color: rgba(255,255,255,0.8); font-size: 1.15rem; font-weight: 300;
  margin: 0 0 36px; line-height: 1.6;
}

/* ── Buscador ── */
.yz-help-search {
  display: flex; align-items: center; background: var(--yz-white);
  border-radius: var(--yz-radius-full); padding: 6px 6px 6px 24px;
  max-width: 580px; width: 100%; margin: 0 auto 44px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.18); animation: yzFadeUp 1s ease backwards 0.2s;
}
.yz-help-search input {
  flex: 1; border: none; outline: none; font-family: 'Inter', sans-serif;
  font-size: 1rem; color: var(--yz-gray-800); background: transparent; padding: 14px 0;
}
.yz-help-search input::placeholder { color: #94a3b8; }
.yz-help-search button {
  background: #5BC5C2; border: none; border-radius: var(--yz-radius-full);
  padding: 14px 28px; cursor: pointer; display: flex; align-items: center; gap: 8px;
  color: var(--yz-secondary); font-family: 'Inter', sans-serif; font-weight: 600;
  font-size: 0.95rem; transition: var(--yz-transition);
}
.yz-help-search button:hover { background: #4ab5b2; transform: scale(1.03); }
.yz-help-search button svg { width: 18px; height: 18px; fill: currentColor; }

/* ── Category Pills (dentro del hero) ── */
.yz-cat-wrapper {
  position: relative; max-width: 740px; width: 100%; margin: 0 auto;
  animation: yzFadeUp 1.2s ease backwards 0.35s; overflow: hidden;
}
.yz-cat-bar {
  display: flex; align-items: center; gap: 14px;
  width: 100%; padding: 6px 4px 12px;
  overflow-x: auto; overflow-y: visible;
  -webkit-overflow-scrolling: touch; scrollbar-width: none;
}
.yz-cat-bar::-webkit-scrollbar { display: none; }

/* Fade gradient borde derecho (solo mobile) */
.yz-cat-fade {
  display: none; position: absolute; top: 0; right: 0;
  width: 60px; height: calc(100% - 20px);
  background: linear-gradient(90deg, transparent 0%, rgba(0,34,54,0.7) 100%);
  pointer-events: none; z-index: 2; border-radius: 0 50px 50px 0;
  transition: opacity 0.3s ease;
}
/* Scroll progress track + thumb */
.yz-cat-progress {
  display: none; height: 3px;
  background: rgba(255,255,255,0.15); border-radius: 3px;
  margin-top: 2px; overflow: hidden; position: relative;
}
.yz-cat-progress-thumb {
  height: 100%; background: var(--yz-white); border-radius: 3px;
  width: 30%; position: absolute; left: 0; top: 0;
  transition: left 0.1s ease-out;
}

.yz-cat-pill {
  flex: 0 0 auto; display: flex; align-items: center; gap: 10px;
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
  border: 1.5px solid rgba(255,255,255,0.25);
  border-radius: var(--yz-radius-full);
  padding: 12px 24px; cursor: pointer; transition: var(--yz-transition);
  white-space: nowrap; color: rgba(255,255,255,0.85);
  font-family: 'Inter', sans-serif; font-weight: 500; font-size: 0.92rem;
}
.yz-cat-pill img {
  width: 22px; height: 22px; object-fit: contain;
  filter: brightness(0) invert(1); transition: filter 0.3s ease;
}
.yz-cat-pill:hover {
  background: rgba(255,255,255,0.22); border-color: rgba(255,255,255,0.5);
  color: var(--yz-white);
}
.yz-cat-pill.active {
  background: var(--yz-white); border-color: var(--yz-white);
  color: var(--yz-secondary); box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.yz-cat-pill.active img { filter: none; }

/* ── Animaciones ── */
@keyframes yzFadeUp { 0% { opacity: 0; transform: translateY(25px); } 100% { opacity: 1; transform: translateY(0); } }

/* ── Responsive ── */
@media (max-width: 768px) {
  .yz-help-hero { min-height: 480px; padding: 50px 16px 30px; background-position: 65% center; }
  .yz-help-hero-content h1 { font-size: 2.2rem; }
  .yz-help-hero-content p { font-size: 1rem; margin-bottom: 28px; }
  .yz-help-search { flex-direction: column; border-radius: var(--yz-radius-md); padding: 8px; gap: 8px; margin-bottom: 30px; }
  .yz-help-search input { padding: 12px 16px; width: 100%; text-align: center; }
  .yz-help-search button { width: 100%; justify-content: center; padding: 14px; border-radius: 10px; }
  .yz-cat-wrapper { margin: 0 -16px; width: calc(100% + 32px); max-width: none; }
  .yz-cat-bar { gap: 10px; padding: 6px 16px 8px; }
  .yz-cat-pill { padding: 10px 18px; font-size: 0.82rem; }
  .yz-cat-pill img { width: 18px; height: 18px; }
  /* Mostrar indicadores solo en mobile */
  .yz-cat-fade { display: block; }
  .yz-cat-progress { display: block; margin: 0 16px; width: calc(100% - 32px); }
  /* Hint de scroll */
  @keyframes yzScrollHint {
    0%, 100% { transform: translateX(0); }
    30% { transform: translateX(-12px); }
    60% { transform: translateX(4px); }
  }
  .yz-cat-bar.yz-hint { animation: yzScrollHint 0.8s ease 1.5s 1; }
}
</style>

<div class="yz-help-hero">
  <div class="yz-help-hero-content">
    <h1>¿En qué podemos ayudarte?</h1>
    <p>Encuentra respuestas rápidas sobre reservaciones, pagos, seguridad y todo lo que necesitas saber para tu experiencia en yate.</p>
  </div>
  <div class="yz-help-search">
    <input type="text" placeholder="Buscar preguntas frecuentes..." id="yzFaqSearch">
    <button type="button" onclick="document.getElementById('yz-faq-section').scrollIntoView({behavior:'smooth'})">
      <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
      Buscar
    </button>
  </div>
  <!-- Categorías: pills horizontales con scroll indicator en mobile -->
  <div class="yz-cat-wrapper">
    <div class="yz-cat-bar yz-hint" id="yzCatBar">
      <button class="yz-cat-pill active" onclick="yzFilterFaq('bookings')" data-cat="bookings">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-booking-ayuda.svg" alt="Reservaciones">
        Reservas &amp; pagos
      </button>
      <button class="yz-cat-pill" onclick="yzFilterFaq('safety')" data-cat="safety">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Safeti-Ayuda.svg" alt="Seguridad">
        Seguridad &amp; seguros
      </button>
      <button class="yz-cat-pill" onclick="yzFilterFaq('vessel')" data-cat="vessel">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Vessel-Ayuda.svg" alt="Embarcación">
        Botes &amp; servicios
      </button>
      <button class="yz-cat-pill" onclick="yzFilterFaq('cancellations')" data-cat="cancellations">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Cancelaciones-Ayuda.svg" alt="Cancelaciones">
        Cambios &amp; cancelaciones
      </button>
    </div>
    <div class="yz-cat-fade" id="yzCatFade"></div>
    <div class="yz-cat-progress"><div class="yz-cat-progress-thumb" id="yzCatThumb"></div></div>
  </div>
</div>

---

## SECCIÓN 2 — FAQ + CTA Equipo (fusionadas con fondo de playa)

La imagen de fondo de playa abarca todo: preguntas frecuentes arriba y tarjeta CTA del equipo abajo.

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 2: FAQ + CTA EQUIPO (fusionadas, fondo de playa)
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── Contenedor general con fondo de playa ── */
.yz-faq-cta-section {
  position: relative;
  background-image: url('https://yatezzitos.com/wp-content/uploads/2026/03/Fondo-de-perfil-segunda-seccion-ayuda.png');
  background-size: cover;
  background-position: center bottom;
  background-repeat: no-repeat;
  padding: 70px 20px 0;
}

/* Estrella de mar y caracolas decorativas (esquina inferior izquierda) */
.yz-starfish-deco {
  position: absolute; bottom: 20px; left: 0;
  width: 160px; height: auto; z-index: 2;
  pointer-events: none;
}

/* ── FAQ accordion ── */
.yz-faq-inner { max-width: 800px; margin: 0 auto; padding-bottom: 50px; }
.yz-faq-group { display: none; }
.yz-faq-group.yz-faq-active { display: block; animation: yzFadeUp 0.5s ease; }
.yz-faq-group-title {
  font-family: 'DM Serif Display', serif; font-weight: 400; font-size: 1.6rem;
  color: var(--yz-secondary); margin: 0 0 24px;
}
.yz-faq-item {
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
  border: 1px solid rgba(255,255,255,0.6);
  border-radius: var(--yz-radius-md); margin-bottom: 14px; overflow: hidden;
  transition: var(--yz-transition);
}
.yz-faq-item:hover { border-color: var(--yz-primary); box-shadow: var(--yz-shadow-sm); }
.yz-faq-item summary::-webkit-details-marker { display: none; }
.yz-faq-item summary { list-style: none; }
.yz-faq-item summary {
  padding: 20px 24px; font-family: 'Inter', sans-serif; font-weight: 600;
  font-size: 1.05rem; color: var(--yz-secondary); cursor: pointer;
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  transition: var(--yz-transition);
}
.yz-faq-item summary:hover { color: var(--yz-primary); }
.yz-faq-item summary::after {
  content: "∨"; font-family: 'Inter', sans-serif; font-weight: 400; font-size: 1.2rem;
  color: var(--yz-gray-500); width: 32px; height: 32px; min-width: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%; transition: var(--yz-transition);
}
.yz-faq-item[open] { border-color: var(--yz-primary); box-shadow: 0 4px 12px rgba(0,135,163,0.1); }
.yz-faq-item[open] summary { color: var(--yz-primary); border-bottom: 1px solid rgba(0,0,0,0.06); }
.yz-faq-item[open] summary::after { content: "∧"; color: var(--yz-primary); }
.yz-faq-answer {
  padding: 20px 24px 24px; font-family: 'Inter', sans-serif; color: var(--yz-gray-500);
  font-size: 1rem; line-height: 1.75; animation: yzSlideDown 0.35s ease;
}
.yz-faq-answer p { margin: 0 0 10px; }
.yz-faq-answer p:last-child { margin-bottom: 0; }
.yz-faq-answer ul { padding-left: 20px; margin: 8px 0 0; }
.yz-faq-answer li { margin-bottom: 6px; }
.yz-faq-answer strong { color: var(--yz-secondary); }
@keyframes yzSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

/* ── CTA equipo (tarjeta con fondo de océano) ── */
.yz-cta-card {
  max-width: 960px; margin: 0 auto;
  background-image: url('https://yatezzitos.com/wp-content/uploads/2026/03/Rectangle-135-2.png');
  background-size: cover; background-position: center; background-repeat: no-repeat;
  border-radius: 24px;
  padding: 48px 48px 48px 48px;
  display: flex; align-items: flex-end; gap: 0;
  position: relative; overflow: visible;
  min-height: 220px;
}
.yz-cta-card-text { flex: 1; padding-right: 20px; }
.yz-cta-card-text h2 {
  font-family: 'DM Serif Display', serif; color: var(--yz-white);
  font-size: 2rem; font-weight: 400; font-style: italic;
  margin: 0 0 14px; line-height: 1.25;
}
.yz-cta-card-text p {
  font-family: 'Inter', sans-serif; color: rgba(255,255,255,0.75);
  font-size: 0.92rem; line-height: 1.65; margin: 0 0 24px;
}
.yz-cta-card-btn {
  display: inline-flex; align-items: center; gap: 10px;
  background: #5BC5C2; color: var(--yz-secondary); padding: 14px 32px;
  border-radius: var(--yz-radius-full); font-family: 'Inter', sans-serif;
  font-weight: 600; font-size: 0.95rem; text-decoration: none;
  transition: var(--yz-transition); border: none; cursor: pointer;
}
.yz-cta-card-btn img { width: 20px; height: 20px; }
.yz-cta-card-btn:hover {
  background: #4ab5b2; transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(91,197,194,0.35); color: var(--yz-secondary);
}
/* Imagen del equipo: en PC sobresale por arriba */
.yz-cta-card-img {
  flex: 0 0 380px; align-self: flex-end;
  margin-bottom: -48px; /* alineado al borde inferior */
}
.yz-cta-card-img img {
  width: 100%; height: auto; display: block;
  margin-top: -60px; /* la cabeza sobresale por arriba del card */
}

/* ── Espacio inferior para que la playa respire debajo de la tarjeta ── */
.yz-faq-cta-bottom { padding: 50px 20px 60px; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .yz-faq-cta-section { padding: 50px 16px 0; }
  .yz-faq-inner { padding-bottom: 40px; }
  .yz-faq-group-title { font-size: 1.3rem; }
  .yz-faq-item summary { font-size: 0.95rem; padding: 16px 18px; }
  .yz-faq-answer { padding: 16px 18px 20px; font-size: 0.95rem; }
  .yz-faq-item summary::after { width: 28px; height: 28px; min-width: 28px; font-size: 1rem; }
  .yz-cta-card {
    flex-direction: column; text-align: center; gap: 0;
    padding: 36px 24px 0; overflow: hidden;
    align-items: center;
  }
  .yz-cta-card-text { padding-right: 0; order: 1; }
  .yz-cta-card-img {
    flex: none; max-width: 300px; margin: 0 auto;
    order: 2; margin-bottom: 0;
  }
  .yz-cta-card-img img { margin-top: 0; }
  .yz-cta-card-text h2 { font-size: 1.7rem; }
  .yz-cta-card-text p { font-size: 0.9rem; }
  .yz-cta-card-btn { width: 100%; justify-content: center; margin-bottom: 24px; }
  .yz-starfish-deco { width: 100px; bottom: 10px; }
  .yz-faq-cta-bottom { padding: 30px 16px 40px; }
}
</style>

<div class="yz-faq-cta-section" id="yz-faq-section">

  <!-- ── FAQ Accordion ── -->
  <div class="yz-faq-inner">

    <!-- ── GRUPO: BOOKINGS ── -->
    <div class="yz-faq-group yz-faq-active" data-group="bookings">
      <h2 class="yz-faq-group-title">Reservas &amp; pagos</h2>
      <details class="yz-faq-item"><summary>¿Cómo es el proceso para reservar un yate?</summary><div class="yz-faq-answer"><p>Selecciona tu destino favorito en <strong>yatezzitos.com</strong>, elige la embarcación de tu preferencia y haz clic en <strong>"Solicitar Cotización"</strong>. Llena el formulario con la fecha, tipo de viaje y horario. Nuestro equipo te enviará una cotización detallada en menos de 48 horas. Una vez aceptada, confirma tu reserva realizando un anticipo del 50%.</p></div></details>
      <details class="yz-faq-item"><summary>¿Cuáles son los métodos de pago aceptados?</summary><div class="yz-faq-answer"><p>Aceptamos <strong>transferencias bancarias y efectivo</strong> sin comisiones adicionales. También tarjetas de crédito/débito (Visa, Mastercard, Amex), PayPal y Criptomonedas (Bitcoin, USDT) con un cargo adicional del 5%.</p></div></details>
      <details class="yz-faq-item"><summary>¿Se requiere un depósito o anticipo para apartar la fecha?</summary><div class="yz-faq-answer"><p>Sí, es indispensable un <strong>anticipo del 50%</strong> del costo total para poder bloquear la fecha y el horario en la embarcación seleccionada.</p></div></details>
      <details class="yz-faq-item"><summary>¿Con cuánta anticipación necesito reservar?</summary><div class="yz-faq-answer"><p>Recomendamos reservar con <strong>más de 30 días de anticipación</strong>, especialmente para temporadas altas. Sin embargo, en algunas ocasiones contamos con disponibilidad inmediata; consúltalo con nuestros asesores.</p></div></details>
      <details class="yz-faq-item"><summary>¿Existe un mínimo de horas para el alquiler?</summary><div class="yz-faq-answer"><p>Sí, varía por destino:</p><ul><li><strong>Los Cabos:</strong> Desde 2 horas</li><li><strong>Mazatlán y Playa del Carmen:</strong> Desde 3 horas</li><li><strong>Vallarta y Cancún:</strong> Desde 4 horas</li><li><strong>Acapulco:</strong> Desde 5 horas</li><li><strong>Ixtapa y Huatulco:</strong> Desde 7 horas</li><li><strong>La Paz:</strong> Desde 8 horas</li></ul></div></details>
      <details class="yz-faq-item"><summary>¿Qué incluyen los paquetes todo incluido?</summary><div class="yz-faq-answer"><p>Incluyen <strong>barra de bebidas, snacks, hielo, aguas, refrescos, equipo de snorkel y flotadores</strong>. Algunos yates premium también ofrecen Chef Privado a Bordo y mixología con costo adicional.</p></div></details>
      <details class="yz-faq-item"><summary>¿Puedo extender mi aventura reservando más horas una vez a bordo?</summary><div class="yz-faq-answer"><p>¡Sí! Sujeto a disponibilidad de la embarcación y la tripulación, puedes solicitar una <strong>extensión de horas</strong> directamente al capitán.</p></div></details>
    </div>

    <!-- ── GRUPO: CANCELLATIONS ── -->
    <div class="yz-faq-group" data-group="cancellations">
      <h2 class="yz-faq-group-title">Cambios &amp; cancelaciones</h2>
      <details class="yz-faq-item"><summary>¿Qué pasa si necesito cancelar mi reservación?</summary><div class="yz-faq-answer"><p>No ofrecemos reembolsos por cancelaciones con <strong>menos de 30 días de anticipación</strong>. Si cancelas a tiempo, es posible "congelar" el anticipo para un futuro viaje.</p></div></details>
      <details class="yz-faq-item"><summary>¿Qué sucede si hay mal clima o fallas mecánicas?</summary><div class="yz-faq-answer"><p>Si se coloca <strong>bandera roja</strong> o hay una falla mecánica, te ofreceremos el <strong>reembolso total</strong> de tu anticipo o la opción de cambiar la fecha.</p></div></details>
      <details class="yz-faq-item"><summary>¿Puedo modificar la fecha o destino de mi reservación?</summary><div class="yz-faq-answer"><p>Sí, con al menos <strong>15 días de anticipación</strong>, sujeto a disponibilidad. Contáctanos para reprogramar.</p></div></details>
      <details class="yz-faq-item"><summary>¿Qué pasa si recibo un mal servicio?</summary><div class="yz-faq-answer"><p>Contáctanos en las siguientes <strong>48 horas</strong>. Nuestro equipo evaluará tu caso y buscará la mejor solución.</p></div></details>
      <details class="yz-faq-item"><summary>¿Puedo congelar mi anticipo para una fecha futura?</summary><div class="yz-faq-answer"><p>Sí, si cancelas con <strong>más de 30 días de anticipación</strong>, tu anticipo puede quedar congelado por 6 meses.</p></div></details>
      <details class="yz-faq-item"><summary>¿Qué sucede si accidentalmente se daña la embarcación?</summary><div class="yz-faq-answer"><p>Todas nuestras embarcaciones cuentan con <strong>seguro de daños</strong>. Los daños por negligencia son responsabilidad del arrendatario.</p></div></details>
      <details class="yz-faq-item"><summary>¿Cómo se manejan las emergencias médicas a bordo?</summary><div class="yz-faq-answer"><p>Todas las embarcaciones cuentan con <strong>botiquín de primeros auxilios</strong> y radios VHF. El capitán está capacitado para coordinar con servicios de rescate.</p></div></details>
    </div>

    <!-- ── GRUPO: SAFETY ── -->
    <div class="yz-faq-group" data-group="safety">
      <h2 class="yz-faq-group-title">Seguridad &amp; seguros</h2>
      <details class="yz-faq-item"><summary>¿Se requieren chalecos salvavidas para todos?</summary><div class="yz-faq-answer"><p>Sí, todas las embarcaciones cuentan con <strong>chalecos salvavidas certificados</strong>. Si viajas con niños, avísanos para la talla correcta.</p></div></details>
      <details class="yz-faq-item"><summary>¿Las embarcaciones cuentan con seguro de daños?</summary><div class="yz-faq-answer"><p>Sí, todas cuentan con <strong>seguro vigente</strong>, botiquín y equipos de comunicación certificados.</p></div></details>
      <details class="yz-faq-item"><summary>¿Puedo contratar el servicio si soy menor de edad?</summary><div class="yz-faq-answer"><p>No, el contrato debe ser firmado por un <strong>adulto mayor de 18 años</strong>. Los menores pueden abordar acompañados.</p></div></details>
      <details class="yz-faq-item"><summary>¿Existen áreas de navegación restringidas?</summary><div class="yz-faq-answer"><p>Sí, existen restricciones para proteger el ecosistema. Tu capitán respetará todas las normativas de la <strong>Marina de México</strong>.</p></div></details>
      <details class="yz-faq-item"><summary>¿Se puede fumar a bordo del yate?</summary><div class="yz-faq-answer"><p>Sí, pero <strong>únicamente en áreas exteriores</strong>. Las sustancias ilícitas están prohibidas y son motivo de cancelación sin reembolso.</p></div></details>
      <details class="yz-faq-item"><summary>¿Qué protocolos de limpieza y sanidad se siguen?</summary><div class="yz-faq-answer"><p>Cada embarcación es <strong>sanitizada profesionalmente</strong> antes y después de cada viaje.</p></div></details>
      <details class="yz-faq-item"><summary>¿Se dejan propinas a la tripulación?</summary><div class="yz-faq-answer"><p>Recomendamos entre un <strong>5% y 10%</strong> del total del alquiler en efectivo mexicano.</p></div></details>
    </div>

    <!-- ── GRUPO: VESSEL ── -->
    <div class="yz-faq-group" data-group="vessel">
      <h2 class="yz-faq-group-title">Botes &amp; servicios</h2>
      <details class="yz-faq-item"><summary>¿Cuántas personas pueden ir a bordo?</summary><div class="yz-faq-answer"><p>Desde <strong>6 personas</strong> en lanchas hasta <strong>más de 40 invitados</strong> en mega-yates y catamaranes.</p></div></details>
      <details class="yz-faq-item"><summary>¿Puedo llevar mi propia comida y bebidas a bordo?</summary><div class="yz-faq-answer"><p>¡Sí! La mayoría de embarcaciones incluyen <strong>hielera, hielo y aguas/refrescos</strong>. También ofrecemos paquetes premium.</p></div></details>
      <details class="yz-faq-item"><summary>¿Los yates incluyen tripulación?</summary><div class="yz-faq-answer"><p>Sí, todas las rentas incluyen un <strong>Capitán y Marinero</strong> capacitados.</p></div></details>
      <details class="yz-faq-item"><summary>¿Qué equipo para deportes acuáticos se proporciona?</summary><div class="yz-faq-answer"><p>Ofrecemos <strong>snorkel, tapetes acuáticos, kayaks, paddle boards</strong> y equipamiento de pesca según el yate.</p></div></details>
      <details class="yz-faq-item"><summary>¿Puedo celebrar un evento privado a bordo?</summary><div class="yz-faq-answer"><p>¡Por supuesto! Ideal para <strong>bodas, cumpleaños, despedidas y eventos corporativos</strong>.</p></div></details>
      <details class="yz-faq-item"><summary>¿Se ofrecen opciones de catering o chef a bordo?</summary><div class="yz-faq-answer"><p>En destinos selectos ofrecemos <strong>Chef Privado a Bordo</strong> y barra de mixología con costo adicional.</p></div></details>
      <details class="yz-faq-item"><summary>¿Hay un código de vestimenta?</summary><div class="yz-faq-answer"><p>Ropa de playa, <strong>bloqueador solar</strong> obligatorio, toallas y pastillas para mareos si eres principiante.</p></div></details>
    </div>

  </div>

  <!-- ── CTA Equipo (tarjeta oscura dentro de la playa) ── -->
  <div class="yz-cta-card">
    <div class="yz-cta-card-text">
      <h2>¿No encuentras lo que buscas?</h2>
      <p>Nuestro equipo de expertos en turismo náutico está listo para ayudarte a planear las vacaciones de tus sueños en el mar. Responderemos tu mensaje en menos de 24 horas.</p>
      <a href="https://api.whatsapp.com/send?phone=526691324073" class="yz-cta-card-btn" target="_blank" rel="noopener">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-boton-cta-pag-ayuda.svg" alt="Salvavidas">
        Contáctanos ahora
      </a>
    </div>
    <div class="yz-cta-card-img">
      <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-equipo-ayuda-seccion-.png" alt="Equipo Yatezzitos - Expertos en renta de yates">
    </div>
  </div>

  <!-- Estrella de mar decorativa -->
  <img class="yz-starfish-deco" src="https://yatezzitos.com/wp-content/uploads/2026/03/Estrella-de-mar-y-caracolas.png" alt="" aria-hidden="true">

  <!-- Espacio inferior playa -->
  <div class="yz-faq-cta-bottom"></div>

</div>

<!-- ── JavaScript: filtrar categorías + scroll indicator ── -->
<script>
function yzFilterFaq(cat) {
  document.querySelectorAll('.yz-cat-pill').forEach(function(btn) {
    btn.classList.remove('active');
    if (btn.getAttribute('data-cat') === cat) btn.classList.add('active');
  });
  document.querySelectorAll('.yz-faq-group').forEach(function(g) {
    g.classList.remove('yz-faq-active');
    if (g.getAttribute('data-group') === cat) g.classList.add('yz-faq-active');
  });
  document.querySelectorAll('.yz-faq-item[open]').forEach(function(d) { d.removeAttribute('open'); });
  document.getElementById('yz-faq-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── Scroll progress + fade para category pills ── */
(function() {
  var bar = document.getElementById('yzCatBar');
  var thumb = document.getElementById('yzCatThumb');
  var fade = document.getElementById('yzCatFade');
  if (!bar || !thumb || !fade) return;
  function updateScroll() {
    var scrollLeft = bar.scrollLeft;
    var maxScroll = bar.scrollWidth - bar.clientWidth;
    if (maxScroll <= 0) { fade.style.opacity = '0'; thumb.style.left = '0'; return; }
    var pct = scrollLeft / maxScroll;
    var trackWidth = thumb.parentElement.clientWidth;
    var thumbWidth = thumb.clientWidth;
    var maxLeft = trackWidth - thumbWidth;
    thumb.style.left = (pct * maxLeft) + 'px';
    fade.style.opacity = pct > 0.92 ? '0' : '1';
  }
  bar.addEventListener('scroll', updateScroll, { passive: true });
  window.addEventListener('resize', updateScroll);
  setTimeout(updateScroll, 100);
})();
</script>
```

---

## SECCIÓN 3 — Bloques Informativos

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 3: BLOQUES INFORMATIVOS (3 tarjetas)
     ═══════════════════════════════════════════════════════════ -->
<style>
.yz-help-info { background: var(--yz-white); padding: 80px 20px; }
.yz-help-info-inner { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.yz-info-card {
  background: var(--yz-gray-100); border-radius: var(--yz-radius-lg);
  padding: 40px 30px; text-align: center; transition: var(--yz-transition);
  border: 1px solid transparent;
}
.yz-info-card:hover { transform: translateY(-6px); box-shadow: var(--yz-shadow-lg); border-color: var(--yz-gray-200); }
.yz-info-card-icon { width: 72px; height: 72px; margin: 0 auto 22px; display: block; object-fit: contain; }
.yz-info-card h3 {
  font-family: 'Inter', sans-serif; font-weight: 700; font-size: 1.1rem;
  color: var(--yz-secondary); margin: 0 0 10px; line-height: 1.4;
}
.yz-info-card p {
  font-family: 'Inter', sans-serif; font-size: 0.92rem;
  color: var(--yz-gray-500); line-height: 1.6; margin: 0;
}

@media (max-width: 768px) {
  .yz-help-info { padding: 50px 16px; }
  .yz-help-info-inner { grid-template-columns: 1fr; gap: 20px; }
  .yz-info-card { padding: 30px 24px; }
  .yz-info-card-icon { width: 56px; height: 56px; }
}
</style>

<div class="yz-help-info">
  <div class="yz-help-info-inner">
    <div class="yz-info-card">
      <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Confirm-with-your-captain-24-hours-in-advance.png" alt="Confirma con tu capitán" class="yz-info-card-icon">
      <h3>Confirm with your captain 24 hours in advance</h3>
      <p>Confirma los detalles de tu viaje directamente con el capitán asignado al menos 24 horas antes de zarpar.</p>
    </div>
    <div class="yz-info-card">
      <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Your-payment-is-protected-by-Stripe.png" alt="Pago protegido por Stripe" class="yz-info-card-icon">
      <h3>Your payment is protected by Stripe</h3>
      <p>Tus datos de pago están 100% protegidos con la tecnología de encriptación de Stripe, el líder mundial en pagos seguros.</p>
    </div>
    <div class="yz-info-card">
      <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Book-more-than-30-days-in-advance-for-the-best-selection.png" alt="Reserva con 30 días de anticipación" class="yz-info-card-icon">
      <h3>Book more than 30 days in advance for the best selection</h3>
      <p>Reservar con anticipación te garantiza la mejor disponibilidad de embarcaciones y los mejores precios de temporada.</p>
    </div>
  </div>
</div>
```
