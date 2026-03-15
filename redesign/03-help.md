# 03 — Página de Ayuda / Preguntas Frecuentes (FAQ)

Rediseño completo con 5 secciones. Cada sección contiene su propio HTML + CSS auto-contenido para facilitar su inserción en Elementor mediante widgets HTML individuales.

Todo el contenido está en español para SEO. Los textos de la sección 5 se mantienen en inglés como en el diseño de Figma original.

---

## SECCIÓN 1 — Hero con Buscador

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 1: HERO CON BUSCADOR
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── S1: Variables Globales ── */
:root {
  --yz-primary: #0087a3;
  --yz-primary-dark: #006075;
  --yz-secondary: #002236;
  --yz-gold: #c3a152;
  --yz-gold-light: #e6c875;
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
  --yz-shadow-md: 0 4px 20px rgba(0,0,0,0.06);
  --yz-shadow-lg: 0 15px 40px rgba(0,0,0,0.12);
}

/* ── S1: Hero ── */
.yz-help-hero {
  position: relative;
  width: 100%;
  min-height: 520px;
  background-image: linear-gradient(180deg, rgba(0,34,54,0.55) 0%, rgba(0,34,54,0.80) 100%),
    url('https://yatezzitos.com/wp-content/uploads/2026/03/imagen-de-fondo-primera-seccion.png');
  background-size: cover;
  background-position: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 80px 20px 100px;
  box-sizing: border-box;
}

.yz-help-hero-content {
  max-width: 720px;
  animation: yzFadeUp 0.8s ease backwards;
}

.yz-help-hero-content h1 {
  font-family: 'DM Serif Display', serif;
  color: var(--yz-white);
  font-size: 3.2rem;
  font-weight: 400;
  margin: 0 0 12px;
  letter-spacing: 0.5px;
}

.yz-help-hero-content p {
  font-family: 'Inter', sans-serif;
  color: rgba(255,255,255,0.8);
  font-size: 1.15rem;
  font-weight: 300;
  margin: 0 0 40px;
  line-height: 1.6;
}

/* ── S1: Buscador ── */
.yz-help-search {
  display: flex;
  align-items: center;
  background: var(--yz-white);
  border-radius: var(--yz-radius-full);
  padding: 6px 6px 6px 24px;
  max-width: 580px;
  width: 100%;
  margin: 0 auto;
  box-shadow: 0 8px 30px rgba(0,0,0,0.18);
  animation: yzFadeUp 1s ease backwards 0.2s;
}

.yz-help-search input {
  flex: 1;
  border: none;
  outline: none;
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  color: var(--yz-gray-800);
  background: transparent;
  padding: 14px 0;
}

.yz-help-search input::placeholder {
  color: #94a3b8;
}

.yz-help-search button {
  background: var(--yz-primary);
  border: none;
  border-radius: var(--yz-radius-full);
  padding: 14px 28px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--yz-white);
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  transition: var(--yz-transition);
}

.yz-help-search button:hover {
  background: var(--yz-primary-dark);
  transform: scale(1.03);
}

.yz-help-search button svg {
  width: 18px;
  height: 18px;
  fill: currentColor;
}

/* ── S1: Animaciones ── */
@keyframes yzFadeUp {
  0% { opacity: 0; transform: translateY(25px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* ── S1: Responsive ── */
@media (max-width: 768px) {
  .yz-help-hero {
    min-height: 420px;
    padding: 60px 16px 80px;
  }
  .yz-help-hero-content h1 {
    font-size: 2.2rem;
  }
  .yz-help-hero-content p {
    font-size: 1rem;
  }
  .yz-help-search {
    flex-direction: column;
    border-radius: var(--yz-radius-md);
    padding: 8px;
    gap: 8px;
  }
  .yz-help-search input {
    padding: 12px 16px;
    width: 100%;
    box-sizing: border-box;
    text-align: center;
  }
  .yz-help-search button {
    width: 100%;
    justify-content: center;
    padding: 14px;
    border-radius: 10px;
  }
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
</div>
```

---

## SECCIÓN 2 — Botones de Categorías

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 2: BOTONES DE CATEGORÍAS
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── S2: Categorías Container ── */
.yz-help-categories {
  position: relative;
  background-image: linear-gradient(180deg, rgba(248,250,252,0.92) 0%, rgba(248,250,252,0.96) 100%),
    url('https://yatezzitos.com/wp-content/uploads/2026/03/Fondo-de-perfil-segunda-seccion-ayuda.png');
  background-size: cover;
  background-position: center;
  padding: 70px 20px;
  text-align: center;
}

.yz-help-categories-inner {
  max-width: 900px;
  margin: 0 auto;
}

.yz-help-categories-inner h2 {
  font-family: 'DM Serif Display', serif;
  color: var(--yz-secondary);
  font-size: 2rem;
  font-weight: 400;
  margin: 0 0 10px;
}

.yz-help-categories-inner .yz-cat-subtitle {
  font-family: 'Inter', sans-serif;
  color: var(--yz-gray-500);
  font-size: 1.05rem;
  margin: 0 0 40px;
}

/* ── S2: Grid de Botones ── */
.yz-cat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.yz-cat-btn {
  background: var(--yz-white);
  border: 2px solid var(--yz-gray-200);
  border-radius: var(--yz-radius-lg);
  padding: 35px 20px 30px;
  cursor: pointer;
  transition: var(--yz-transition);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  text-decoration: none;
  position: relative;
  overflow: hidden;
}

.yz-cat-btn::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: var(--yz-primary);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.yz-cat-btn:hover,
.yz-cat-btn.active {
  border-color: var(--yz-primary);
  box-shadow: var(--yz-shadow-lg);
  transform: translateY(-4px);
}

.yz-cat-btn:hover::after,
.yz-cat-btn.active::after {
  transform: scaleX(1);
}

.yz-cat-btn img {
  width: 52px;
  height: 52px;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.yz-cat-btn:hover img {
  transform: scale(1.1);
}

.yz-cat-btn span {
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--yz-secondary);
  line-height: 1.3;
}

/* ── S2: Responsive ── */
@media (max-width: 768px) {
  .yz-cat-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
  .yz-cat-btn {
    padding: 25px 14px 22px;
  }
  .yz-cat-btn img {
    width: 42px;
    height: 42px;
  }
  .yz-cat-btn span {
    font-size: 0.85rem;
  }
  .yz-help-categories {
    padding: 50px 16px;
  }
}
</style>

<div class="yz-help-categories">
  <div class="yz-help-categories-inner">
    <h2>Explora por Categoría</h2>
    <p class="yz-cat-subtitle">Selecciona un tema para ver las preguntas más relevantes</p>
    <div class="yz-cat-grid">
      <button class="yz-cat-btn active" onclick="yzFilterFaq('bookings')" data-cat="bookings">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-booking-ayuda.svg" alt="Reservaciones y Pagos">
        <span>Reservaciones<br>y Pagos</span>
      </button>
      <button class="yz-cat-btn" onclick="yzFilterFaq('cancellations')" data-cat="cancellations">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Cancelaciones-Ayuda.svg" alt="Cancelaciones y Reembolsos">
        <span>Cancelaciones<br>y Reembolsos</span>
      </button>
      <button class="yz-cat-btn" onclick="yzFilterFaq('safety')" data-cat="safety">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Safeti-Ayuda.svg" alt="Seguridad">
        <span>Seguridad<br>y Protección</span>
      </button>
      <button class="yz-cat-btn" onclick="yzFilterFaq('vessel')" data-cat="vessel">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-Vessel-Ayuda.svg" alt="Embarcación">
        <span>Embarcación<br>y Equipamiento</span>
      </button>
    </div>
  </div>
</div>
```

---

## SECCIÓN 3 — Acordeón de Preguntas Frecuentes

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 3: ACORDEÓN FAQ (filtrable por categoría)
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── S3: Container ── */
.yz-faq-section {
  background: var(--yz-gray-100);
  padding: 70px 20px 80px;
}

.yz-faq-inner {
  max-width: 800px;
  margin: 0 auto;
}

.yz-faq-inner h2 {
  font-family: 'DM Serif Display', serif;
  color: var(--yz-secondary);
  font-size: 2rem;
  text-align: center;
  margin: 0 0 40px;
}

/* ── S3: Grupo de categoría ── */
.yz-faq-group {
  display: none;
}
.yz-faq-group.yz-faq-active {
  display: block;
  animation: yzFadeUp 0.5s ease;
}

.yz-faq-group-title {
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--yz-primary);
  margin: 0 0 20px;
  padding-left: 4px;
}

/* ── S3: Accordion Items ── */
.yz-faq-item {
  background: var(--yz-white);
  border: 1px solid var(--yz-gray-200);
  border-radius: var(--yz-radius-md);
  margin-bottom: 14px;
  overflow: hidden;
  transition: var(--yz-transition);
}

.yz-faq-item:hover {
  border-color: var(--yz-primary);
  box-shadow: var(--yz-shadow-sm);
}

/* Ocultar flecha nativa */
.yz-faq-item summary::-webkit-details-marker { display: none; }
.yz-faq-item summary { list-style: none; }

.yz-faq-item summary {
  padding: 20px 24px;
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 1.05rem;
  color: var(--yz-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  transition: var(--yz-transition);
}

.yz-faq-item summary:hover {
  color: var(--yz-primary);
}

/* Icono +/- */
.yz-faq-item summary::after {
  content: "+";
  font-family: 'Inter', sans-serif;
  font-weight: 300;
  font-size: 1.6rem;
  color: var(--yz-primary);
  width: 36px;
  height: 36px;
  min-width: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: var(--yz-gray-100);
  transition: var(--yz-transition);
}

.yz-faq-item[open] {
  border-color: var(--yz-primary);
  box-shadow: 0 4px 12px rgba(0,135,163,0.1);
}

.yz-faq-item[open] summary {
  color: var(--yz-primary);
  border-bottom: 1px solid var(--yz-gray-100);
}

.yz-faq-item[open] summary::after {
  content: "−";
  background: var(--yz-primary);
  color: var(--yz-white);
}

.yz-faq-answer {
  padding: 20px 24px 24px;
  font-family: 'Inter', sans-serif;
  color: var(--yz-gray-500);
  font-size: 1rem;
  line-height: 1.75;
  animation: yzSlideDown 0.35s ease;
}

.yz-faq-answer p {
  margin: 0 0 10px;
}
.yz-faq-answer p:last-child { margin-bottom: 0; }
.yz-faq-answer ul {
  padding-left: 20px;
  margin: 8px 0 0;
}
.yz-faq-answer li { margin-bottom: 6px; }
.yz-faq-answer strong { color: var(--yz-secondary); }

@keyframes yzSlideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ── S3: Responsive ── */
@media (max-width: 768px) {
  .yz-faq-section { padding: 50px 16px 60px; }
  .yz-faq-item summary { font-size: 0.95rem; padding: 16px 18px; }
  .yz-faq-answer { padding: 16px 18px 20px; font-size: 0.95rem; }
  .yz-faq-item summary::after { width: 30px; height: 30px; min-width: 30px; font-size: 1.3rem; }
}
</style>

<div class="yz-faq-section" id="yz-faq-section">
  <div class="yz-faq-inner">
    <h2>Preguntas Frecuentes</h2>

    <!-- ── GRUPO: BOOKINGS ── -->
    <div class="yz-faq-group yz-faq-active" data-group="bookings">
      <p class="yz-faq-group-title">Reservaciones y Pagos</p>

      <details class="yz-faq-item">
        <summary>¿Cómo es el proceso para reservar un yate?</summary>
        <div class="yz-faq-answer">
          <p>Selecciona tu destino favorito en <strong>yatezzitos.com</strong> (Mazatlán, Vallarta, Cancún, La Paz, Los Cabos, Acapulco, Playa del Carmen, Huatulco o Ixtapa Zihuatanejo), elige la embarcación de tu preferencia y haz clic en <strong>"Solicitar Cotización"</strong>. Llena el formulario con la fecha, tipo de viaje y horario. Nuestro equipo te enviará una cotización detallada en menos de 48 horas. Una vez aceptada, confirma tu reserva realizando un anticipo del 50%.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Cuáles son los métodos de pago aceptados?</summary>
        <div class="yz-faq-answer">
          <p>Aceptamos <strong>transferencias bancarias y efectivo</strong> (depósito directo o en el muelle) sin comisiones adicionales. También puedes pagar con tarjetas de crédito/débito (Visa, Mastercard, Amex), PayPal y Criptomonedas (Bitcoin, USDT) con un cargo adicional del 5%.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se requiere un depósito o anticipo para apartar la fecha?</summary>
        <div class="yz-faq-answer">
          <p>Sí, es indispensable un <strong>anticipo del 50%</strong> del costo total para poder bloquear la fecha y el horario en la embarcación seleccionada.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Con cuánta anticipación necesito reservar?</summary>
        <div class="yz-faq-answer">
          <p>Recomendamos ampliamente reservar con <strong>más de 30 días de anticipación</strong>, especialmente para temporadas altas (Semana Santa, Año Nuevo, puentes vacacionales). Sin embargo, en algunas ocasiones contamos con disponibilidad inmediata; consúltalo con nuestros asesores.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Existe un mínimo de horas para el alquiler?</summary>
        <div class="yz-faq-answer">
          <p>Sí, el mínimo varía por destino:</p>
          <ul>
            <li><strong>Los Cabos:</strong> Desde 2 horas</li>
            <li><strong>Mazatlán y Playa del Carmen:</strong> Desde 3 horas</li>
            <li><strong>Vallarta y Cancún:</strong> Desde 4 horas</li>
            <li><strong>Acapulco:</strong> Desde 5 horas</li>
            <li><strong>Ixtapa y Huatulco:</strong> Desde 7 horas</li>
            <li><strong>La Paz:</strong> Desde 8 horas</li>
          </ul>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué incluyen los paquetes todo incluido?</summary>
        <div class="yz-faq-answer">
          <p>Los paquetes todo incluido (disponibles en destinos selectos como Cancún y Playa del Carmen) incluyen <strong>barra de bebidas nacional, snacks, hielo, aguas, refrescos, equipo de snorkel y flotadores</strong>. Algunos yates premium también ofrecen el servicio de Chef Privado a Bordo y mixología con costo adicional.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo extender mi aventura reservando más horas una vez a bordo?</summary>
        <div class="yz-faq-answer">
          <p>¡Sí! Sujeto a disponibilidad de la embarcación y la tripulación, puedes solicitar directamente al capitán una <strong>extensión de horas</strong>. El costo adicional se acuerda en ese instante y se paga antes de continuar la navegación.</p>
        </div>
      </details>
    </div>

    <!-- ── GRUPO: CANCELLATIONS ── -->
    <div class="yz-faq-group" data-group="cancellations">
      <p class="yz-faq-group-title">Cancelaciones y Reembolsos</p>

      <details class="yz-faq-item">
        <summary>¿Qué pasa si necesito cancelar mi reservación?</summary>
        <div class="yz-faq-answer">
          <p>No ofrecemos reembolsos por cancelaciones realizadas por el cliente con <strong>menos de 30 días de anticipación</strong>. Si cancelas con la debida anticipación, es posible dejar "congelado" el anticipo para un futuro viaje, sujeto a disponibilidad.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué sucede si hay mal clima o fallas mecánicas?</summary>
        <div class="yz-faq-answer">
          <p>La navegación está sujeta a la Capitanía de Puerto local. Si colocan <strong>bandera roja</strong> (puerto cerrado) o si ocurre una falla mecánica, te ofreceremos el <strong>reembolso total</strong> de tu anticipo o la opción de cambiar la fecha de tu viaje.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo modificar la fecha o destino de mi reservación?</summary>
        <div class="yz-faq-answer">
          <p>Sí, puedes modificar la fecha de tu viaje <strong>con al menos 15 días de anticipación</strong>, sujeto a disponibilidad de la embarcación. El cambio de destino también es posible pero requiere coordinación con nuestro equipo ya que cada puerto tiene flotas distintas. Contáctanos para reprogramar.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué pasa si recibo un mal servicio?</summary>
        <div class="yz-faq-answer">
          <p>Tu satisfacción es nuestra prioridad. Si consideras que la experiencia no cumplió con lo acordado, <strong>contáctanos en las siguientes 48 horas</strong> con una descripción de la situación. Nuestro equipo evaluará tu caso y buscará la mejor solución, que puede incluir un descuento en tu próxima reservación o una compensación parcial.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo congelar mi anticipo para una fecha futura?</summary>
        <div class="yz-faq-answer">
          <p>Sí, si cancelas con <strong>más de 30 días de anticipación</strong>, tu anticipo puede quedar congelado para reprogramar tu viaje dentro de los siguientes 6 meses, sujeto a disponibilidad y destino.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué sucede si accidentalmente se daña la embarcación?</summary>
        <div class="yz-faq-answer">
          <p>Todas nuestras embarcaciones cuentan con <strong>seguro de daños</strong>. Sin embargo, los daños causados por negligencia, uso indebido o bajo la influencia de sustancias son responsabilidad del arrendatario. El capitán documentará cualquier incidente y se evaluará caso por caso.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Cómo se manejan las emergencias médicas a bordo?</summary>
        <div class="yz-faq-answer">
          <p>Todas las embarcaciones cuentan con <strong>botiquín de primeros auxilios</strong> y radios VHF de comunicación con la Marina. El capitán está capacitado para manejar situaciones de emergencia y coordinar con los servicios de rescate locales. Si tienes alguna condición médica, infórmalo antes de abordar.</p>
        </div>
      </details>
    </div>

    <!-- ── GRUPO: SAFETY ── -->
    <div class="yz-faq-group" data-group="safety">
      <p class="yz-faq-group-title">Seguridad y Protección</p>

      <details class="yz-faq-item">
        <summary>¿Se requieren chalecos salvavidas para todos?</summary>
        <div class="yz-faq-answer">
          <p>Sí, todas las embarcaciones cuentan con <strong>chalecos salvavidas certificados</strong> para todos los pasajeros. Si viajas con niños menores, avísanos con antelación para asegurar la talla correcta.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Las embarcaciones cuentan con seguro de daños?</summary>
        <div class="yz-faq-answer">
          <p>Sí, todas nuestras embarcaciones cuentan con <strong>seguro vigente contra daños</strong>, además de botiquín de primeros auxilios y equipos de comunicación certificados por las autoridades marítimas mexicanas.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo contratar el servicio si soy menor de edad?</summary>
        <div class="yz-faq-answer">
          <p>No, el contrato de alquiler debe ser firmado por un <strong>adulto mayor de 18 años</strong>. Los menores de edad pueden abordar sin problema, siempre acompañados de un adulto responsable y con chalecos salvavidas de su talla.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Existen áreas de navegación restringidas?</summary>
        <div class="yz-faq-answer">
          <p>Sí, existen restricciones por destino para proteger el ecosistema. Por ejemplo, en <strong>Cozumel y La Paz</strong> hay arrecifes y zonas de ballenas protegidas, y la <strong>Playa del Amor en Vallarta</strong> (Islas Marietas) no permite desembarque directo desde yates privados. Tu capitán respetará todas las normativas de la Marina de México.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se puede fumar a bordo del yate?</summary>
        <div class="yz-faq-answer">
          <p>Sí, puedes fumar pero <strong>únicamente en las áreas exteriores</strong> indicadas por la tripulación. Solicita un cenicero para evitar quemaduras en la tapicería. Las sustancias ilícitas están estrictamente prohibidas y son motivo de cancelación inmediata sin reembolso.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué protocolos de limpieza y sanidad se siguen?</summary>
        <div class="yz-faq-answer">
          <p>Cada embarcación es <strong>sanitizada y limpiada profesionalmente</strong> antes y después de cada viaje. Se revisan todas las áreas comunes, el equipo de snorkel, flotadores, baños y cocina. Cumplimos con los estándares de higiene establecidos por las autoridades marítimas locales.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se dejan propinas a la tripulación?</summary>
        <div class="yz-faq-answer">
          <p>Los precios no incluyen propina. Recomendamos llevar <strong>efectivo mexicano</strong> y considerar entre un <strong>5% y 10%</strong> del total del alquiler para gratificar el servicio del Capitán y Marinero.</p>
        </div>
      </details>
    </div>

    <!-- ── GRUPO: VESSEL ── -->
    <div class="yz-faq-group" data-group="vessel">
      <p class="yz-faq-group-title">Embarcación y Equipamiento</p>

      <details class="yz-faq-item">
        <summary>¿Cuántas personas pueden ir a bordo?</summary>
        <div class="yz-faq-answer">
          <p>Cada embarcación tiene una <strong>capacidad máxima</strong> fijada por las autoridades navales. Contamos con lanchas desde <strong>6 personas</strong> hasta mega-yates y catamaranes para <strong>más de 40 invitados</strong>.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo llevar mi propia comida y bebidas a bordo?</summary>
        <div class="yz-faq-answer">
          <p>¡Sí! Eres bienvenido a subir tus alimentos y bebidas. La mayoría de nuestras embarcaciones ya incluyen <strong>hielera, hielo comestible y aguas/refrescos</strong>. Para una experiencia premium ofrecemos paquetes "Todo Incluido" o servicio de Chef Privado con costo adicional.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Los yates incluyen tripulación?</summary>
        <div class="yz-faq-answer">
          <p>Sí, todas las rentas incluyen un <strong>Capitán y Marinero</strong> capacitados con amplia experiencia para garantizar tu seguridad y brindarte un servicio excelente durante todo el itinerario.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué equipo para deportes acuáticos se proporciona?</summary>
        <div class="yz-faq-answer">
          <p>Dependiendo del yate, ofrecemos equipo de <strong>snorkel, tapetes acuáticos (islas flotantes), kayaks, paddle boards</strong> y equipamiento de pesca. Las motos acuáticas (jet skis) usualmente tienen un costo adicional independiente.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo celebrar un evento privado a bordo?</summary>
        <div class="yz-faq-answer">
          <p>¡Por supuesto! Ofrecemos el escenario perfecto para <strong>despedidas de soltera/o, bodas, cumpleaños y eventos corporativos</strong>. Contáctanos con anticipación para gestionar alimentos, decoración, DJ o agrupaciones musicales (banda, mariachi, norteño).</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se ofrecen opciones de catering o chef a bordo?</summary>
        <div class="yz-faq-answer">
          <p>En destinos selectos como <strong>Cancún y Playa del Carmen</strong>, ofrecemos servicio de Chef Privado a Bordo y barra de mixología preparada. También hay menús personalizado para eventos especiales. Consulta con nuestros asesores para conocer la disponibilidad y costos en tu destino.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Hay un código de vestimenta o qué recomiendan llevar?</summary>
        <div class="yz-faq-answer">
          <p>Trae ropa de playa, gorras y gafas. Es obligatorio llevar <strong>bloqueador solar</strong> y toallas. Si no tienes mucha experiencia en altamar, te recomendamos <strong>pastillas para mareos</strong>. Lleva efectivo (pesos mexicanos) para propinas y actividades extras en la playa.</p>
        </div>
      </details>
    </div>

  </div>
</div>

<!-- ── S3: JavaScript para filtrar categorías ── -->
<script>
function yzFilterFaq(cat) {
  // Actualizar botones activos
  document.querySelectorAll('.yz-cat-btn').forEach(function(btn) {
    btn.classList.remove('active');
    if (btn.getAttribute('data-cat') === cat) btn.classList.add('active');
  });
  // Mostrar/ocultar grupos
  document.querySelectorAll('.yz-faq-group').forEach(function(g) {
    g.classList.remove('yz-faq-active');
    if (g.getAttribute('data-group') === cat) g.classList.add('yz-faq-active');
  });
  // Cerrar todos los details abiertos
  document.querySelectorAll('.yz-faq-item[open]').forEach(function(d) { d.removeAttribute('open'); });
  // Scroll suave a la sección
  document.getElementById('yz-faq-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>
```

---

## SECCIÓN 4 — CTA con Equipo

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 4: CTA CON FOTO DEL EQUIPO
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── S4: CTA Section ── */
.yz-help-cta {
  background: var(--yz-secondary);
  padding: 80px 20px;
  position: relative;
  overflow: hidden;
}

/* Decoración sutil */
.yz-help-cta::before {
  content: '';
  position: absolute;
  top: -120px; right: -80px;
  width: 350px; height: 350px;
  background: radial-gradient(circle, rgba(195,161,82,0.12) 0%, transparent 70%);
  border-radius: 50%;
}

.yz-help-cta-inner {
  max-width: 1000px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 60px;
}

/* Imagen del equipo */
.yz-help-cta-img {
  flex: 0 0 340px;
  animation: yzFadeUp 0.8s ease backwards;
}

.yz-help-cta-img img {
  width: 100%;
  border-radius: var(--yz-radius-lg);
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
}

/* Texto */
.yz-help-cta-text {
  flex: 1;
  animation: yzFadeUp 0.8s ease backwards 0.15s;
}

.yz-help-cta-text h2 {
  font-family: 'DM Serif Display', serif;
  color: var(--yz-white);
  font-size: 2.4rem;
  font-weight: 400;
  margin: 0 0 16px;
  line-height: 1.2;
}

.yz-help-cta-text p {
  font-family: 'Inter', sans-serif;
  color: #94a3b8;
  font-size: 1.1rem;
  line-height: 1.7;
  margin: 0 0 32px;
}

/* Botón CTA */
.yz-cta-button {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  background: var(--yz-primary);
  color: var(--yz-white);
  padding: 16px 36px;
  border-radius: var(--yz-radius-full);
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 1.05rem;
  text-decoration: none;
  transition: var(--yz-transition);
  box-shadow: 0 6px 20px rgba(0,135,163,0.35);
}

.yz-cta-button img {
  width: 26px;
  height: 26px;
}

.yz-cta-button:hover {
  background: var(--yz-primary-dark);
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0,135,163,0.45);
  color: white;
}

/* ── S4: Responsive ── */
@media (max-width: 768px) {
  .yz-help-cta { padding: 50px 16px; }
  .yz-help-cta-inner {
    flex-direction: column;
    text-align: center;
    gap: 30px;
  }
  .yz-help-cta-img { flex: none; max-width: 280px; margin: 0 auto; }
  .yz-help-cta-text h2 { font-size: 1.8rem; }
  .yz-help-cta-text p { font-size: 1rem; }
  .yz-cta-button { width: 100%; justify-content: center; }
}
</style>

<div class="yz-help-cta">
  <div class="yz-help-cta-inner">
    <div class="yz-help-cta-img">
      <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-equipo-ayuda-seccion-.png" alt="Equipo Yatezzitos - Expertos en renta de yates">
    </div>
    <div class="yz-help-cta-text">
      <h2>¿No encuentras lo que buscas?</h2>
      <p>Nuestro equipo de expertos en turismo náutico está listo para ayudarte a planear las vacaciones de tus sueños en el mar. Responderemos tu mensaje en menos de 24 horas.</p>
      <a href="https://api.whatsapp.com/send?phone=526691324073" class="yz-cta-button" target="_blank" rel="noopener">
        <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Icono-boton-cta-pag-ayuda.svg" alt="Salvavidas">
        Contáctanos ahora
      </a>
    </div>
  </div>
</div>
```

---

## SECCIÓN 5 — Bloques Informativos

```html
<!-- ═══════════════════════════════════════════════════════════
     SECCIÓN 5: BLOQUES INFORMATIVOS (3 tarjetas)
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── S5: Info Blocks ── */
.yz-help-info {
  background: var(--yz-white);
  padding: 80px 20px;
}

.yz-help-info-inner {
  max-width: 1000px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.yz-info-card {
  background: var(--yz-gray-100);
  border-radius: var(--yz-radius-lg);
  padding: 40px 30px;
  text-align: center;
  transition: var(--yz-transition);
  border: 1px solid transparent;
}

.yz-info-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--yz-shadow-lg);
  border-color: var(--yz-gray-200);
}

.yz-info-card-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 22px;
  display: block;
  object-fit: contain;
}

.yz-info-card h3 {
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--yz-secondary);
  margin: 0 0 10px;
  line-height: 1.4;
}

.yz-info-card p {
  font-family: 'Inter', sans-serif;
  font-size: 0.92rem;
  color: var(--yz-gray-500);
  line-height: 1.6;
  margin: 0;
}

/* ── S5: Responsive ── */
@media (max-width: 768px) {
  .yz-help-info { padding: 50px 16px; }
  .yz-help-info-inner {
    grid-template-columns: 1fr;
    gap: 20px;
  }
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
