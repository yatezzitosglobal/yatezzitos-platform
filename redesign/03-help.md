# 03 - Help / FAQ Page (Centro de Ayuda)

Este documento contiene la maqueta HTML responsiva y el código CSS (Pixel-perfect) para la nueva página de Ayuda (Preguntas Frecuentes). Está lista para ser pegada en Elementor usando los widgets de "HTML" o insertada a través de un Shortcode/Bloque personalizado en WordPress. 

Se empleó el método de acordiones (Details/Summary) nativos y optimizados con CSS para garantizar el mejor rendimiento SEO y accesibilidad sin necesidad de JavaScript pesado.

El contenido ha sido destilado y unificado para representar de forma magistral todas las reglas y destinos.

---

## 1. Código HTML

```html
<!-- HERO SECTION - CENTRO DE AYUDA -->
<div class="yz-help-hero">
  <div class="yz-help-hero-content">
    <h1>Centro de Ayuda</h1>
    <h2>Preguntas Frecuentes (FAQ)</h2>
    <p>Todo lo que necesitas saber antes, durante y después de tu aventura en yate.</p>
  </div>
</div>

<!-- MAIN HELP SECTION -->
<div class="yz-help-main">
  <!-- Decorative Elements -->
  <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Estrella-de-mar.png" alt="Estrella de mar decorativa" class="yz-decor yz-help-decor-starfish">
  
  <div class="yz-help-container">
    
    <!-- CATEGORÍA 1: RESERVACIONES Y PAGOS -->
    <div class="yz-faq-category">
      <div class="yz-faq-category-header">
        <span class="yz-faq-icon">💳</span>
        <h3>1. Reservaciones y Pagos</h3>
      </div>
      
      <details class="yz-faq-item">
        <summary>¿Cómo es el proceso para reservar un yate?</summary>
        <div class="yz-faq-content">
          <p>Puedes reservar fácilmente desde nuestro sitio web (yatezzitos.com). Selecciona tu destino (Mazatlán, Vallarta, Cancún, La Paz, Los Cabos, Acapulco, Playa del Carmen, Huatulco o Ixtapa Zihuatanejo), elige la embarcación de tu preferencia y haz clic en "Solicitar Cotización". Llena el formulario con la fecha, tipo de viaje (por hora, día o semana) y horario. Nuestro equipo te enviará una cotización detallada en menos de 48 horas. Una vez aceptada, confirma tu reserva realizando un anticipo del 50%.</p>
        </div>
      </details>
      
      <details class="yz-faq-item">
        <summary>¿Cuáles son los métodos de pago aceptados?</summary>
        <div class="yz-faq-content">
          <p>Aceptamos transferencias bancarias y efectivo (depósito directo o en el muelle) sin comisiones adicionales. También puedes pagar con tarjetas de crédito/débito (Visa, Mastercard, Amex), PayPal y Criptomonedas (Bitcoin, USDT) con un cargo adicional del 5%.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se requiere un depósito o anticipo para apartar la fecha?</summary>
        <div class="yz-faq-content">
          <p>Sí, es indispensable un anticipo del 50% del costo total para poder bloquear la fecha y el horario en la embarcación seleccionada.</p>
        </div>
      </details>
      
      <details class="yz-faq-item">
        <summary>¿Con cuánta anticipación necesito reservar?</summary>
        <div class="yz-faq-content">
          <p>Recomendamos ampliamente reservar con más de 30 días de anticipación, especialmente para temporadas altas (Semana Santa, Año Nuevo, puentes vacacionales). Sin embargo, en algunas ocasiones contamos con disponibilidad inmediata o de un día para otro; consúltalo con nuestros asesores.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Existe un mínimo de horas para el alquiler?</summary>
        <div class="yz-faq-content">
          <p>Sí, el mínimo de horas varía por destino:</p>
          <ul>
            <li><strong>Los Cabos:</strong> Desde 2 horas.</li>
            <li><strong>Mazatlán y Playa del Carmen:</strong> Desde 3 horas.</li>
            <li><strong>Vallarta y Cancún:</strong> Desde 4 horas.</li>
            <li><strong>Acapulco:</strong> Desde 5 horas.</li>
            <li><strong>Ixtapa y Huatulco:</strong> Desde 7 horas.</li>
            <li><strong>La Paz:</strong> Desde 8 horas.</li>
          </ul>
        </div>
      </details>
    </div>

    <!-- CATEGORÍA 2: POLÍTICAS Y REEMBOLSO -->
    <div class="yz-faq-category">
      <div class="yz-faq-category-header">
        <span class="yz-faq-icon">🛡️</span>
        <h3>2. Políticas de Cancelación y Reembolso</h3>
      </div>
      
      <details class="yz-faq-item">
        <summary>¿Qué pasa si necesito cancelar mi reservación?</summary>
        <div class="yz-faq-content">
          <p>No ofrecemos reembolsos por cancelaciones realizadas por el cliente con menos de 30 días de anticipación a la fecha reservada. Si cancelas con la debida anticipación, es posible dejar "congelado" el anticipo para un futuro viaje, sujeto a disponibilidad y destino.</p>
        </div>
      </details>
      
      <details class="yz-faq-item">
        <summary>¿Qué sucede si hay mal clima o fallas mecánicas?</summary>
        <div class="yz-faq-content">
          <p>La navegación está sujeta a las indicaciones de la Capitanía de Puerto local. Si colocan bandera roja (puerto cerrado) por clima adverso o si ocurre alguna falla mecánica de último minuto en el yate, te ofreceremos de inmediato el reembolso total de tu anticipo o la opción de cambiar tu viaje de fecha.</p>
        </div>
      </details>
    </div>

    <!-- CATEGORÍA 3: DURANTE EL VIAJE -->
    <div class="yz-faq-category">
      <div class="yz-faq-category-header">
        <span class="yz-faq-icon">🛥️</span>
        <h3>3. Durante el Viaje</h3>
      </div>
      
      <details class="yz-faq-item">
        <summary>¿Cuántas personas pueden ir a bordo?</summary>
        <div class="yz-faq-content">
          <p>Cada embarcación tiene una capacidad máxima fijada por las autoridades navales que debe respetarse. Tenemos lanchas para grupos desde 6 personas hasta mega-yates o catamaranes para más de 40 invitados. Las tarifas por invitado adicional dependen del yate.</p>
        </div>
      </details>
      
      <details class="yz-faq-item">
        <summary>¿Puedo llevar mi propia comida y bebidas a bordo?</summary>
        <div class="yz-faq-content">
          <p>¡Sí! Eres bienvenido a subir tus propios alimentos y bebidas. Además, la mayoría de nuestras embarcaciones ya incluyen hielera, hielo comestible y aguas/refrescos. Para una experiencia premium, en ciertos destinos (como Cancún y Playa del Carmen) ofrecemos paquetes "Todo Incluido" o el servicio de Chef Privado con costo adicional.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Los yates incluyen tripulación?</summary>
        <div class="yz-faq-content">
          <p>Sí, la renta de todas nuestras embarcaciones incluye un Capitán y Marinero capacitados y con amplia experiencia para garantizar tu seguridad y servirte de forma excelente en todo el itinerario. Conocen perfectamente la navegación marítima local.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Puedo celebrar un evento privado a bordo?</summary>
        <div class="yz-faq-content">
          <p>¡Por supuesto! Nos enorgullece ofrecer el escenario perfecto para despedidas de soltera/o, bodas, cumpleaños y eventos corporativos. Contáctanos con anticipación para gestionar alimentos, decoración, DJ o agrupaciones musicales (banda, norteño, mariachi).</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Qué equipo para deportes acuáticos se proporciona?</summary>
        <div class="yz-faq-content">
          <p>Dependiendo del yate, ofrecemos equipo para snorkel y flotadores (tapetes acuáticos / islas flotantes). Algunos incluyen kayaks, paddle boards, equipamiento de pesca o inflables. Las motos acuáticas (jet skis) de forma independiente usualmente tienen un costo adicional.</p>
        </div>
      </details>
    </div>

    <!-- CATEGORÍA 4: REGLAS Y RESTRICCIONES -->
    <div class="yz-faq-category">
      <div class="yz-faq-category-header">
        <span class="yz-faq-icon">🚦</span>
        <h3>4. Reglas, Seguridad y Restricciones</h3>
      </div>
      
      <details class="yz-faq-item">
        <summary>¿Se puede fumar a bordo del yate?</summary>
        <div class="yz-faq-content">
          <p>Sí, puedes fumar pero <strong>únicamente con precaución y en las áreas exteriores indicadas</strong> por la tripulación. Pide un cenicero para evitar multas por quemaduras en tapicería interior. Las sustancias ilícitas están estrictamente prohibidas y son motivo de cancelación sin reembolso.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Hay un código de vestimenta?</summary>
        <div class="yz-faq-content">
          <p>Trae tu ropa favorita de playa, gorras, gafas e indispensablemente <strong>bloqueador solar</strong>, toallas y pastillas para mareos si no tienes mucha experiencia en altamar.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se requieren chalecos salvavidas para todos?</summary>
        <div class="yz-faq-content">
          <p>Sí, todas las embarcaciones cuentan con seguro de daños, botiquín de primeros auxilios, radios VHF y chalecos salvavidas certificados. Si viajas con niños menores, avísanos con antelación para asegurar que llevemos la talla correcta.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Se dejan propinas a la tripulación?</summary>
        <div class="yz-faq-content">
          <p>Los precios no incluyen la propina del equipo a bordo. Recomendamos llevar efectivo mexicano y considerar entre un 5% y un 10% del total de alquiler para gratificar el excelente servicio del Capitán y Marinero.</p>
        </div>
      </details>

      <details class="yz-faq-item">
        <summary>¿Existen áreas de navegación o playas restringidas?</summary>
        <div class="yz-faq-content">
          <p>En pro de proteger el ecosistema, existen restricciones por destino. Por ejemplo, en Cozumel y La Paz hay arrecifes o zonas de ballenas protegidas; y la Playa del Amor en Vallarta no permite el desembarque masivo desde yates estandar. Tu capitán respetará en todo momento las normativas de la Marina de México para un turismo ecológico.</p>
        </div>
      </details>
    </div>

    <!-- CALL TO ACTION (Aún tienes dudas) -->
    <div class="yz-help-cta">
      <h3>¿Aún tienes dudas sobre tu viaje?</h3>
      <p>Nuestros expertos en turismo náutico están listos para ayudarte a planear las vacaciones de tus sueños en el mar.</p>
      <div class="yz-help-cta-buttons">
        <a href="https://api.whatsapp.com/send?phone=526691324073" class="yz-btn yz-btn-primary">
          <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.82 9.82 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
          Escribir por WhatsApp
        </a>
        <a href="https://yatezzitos.com/contacto/" class="yz-btn yz-btn-secondary">
          <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
          Ir a Contacto
        </a>
      </div>
    </div>
    
  </div>
</div>
```

---

## 2. Código CSS

```css
/* ==========================================================================
   YATEZZITOS - HELP / FAQ PAGE STYLES
   ========================================================================== */

/* --- 1. Topografia y Variables Globales --- */
:root {
  --yz-primary: #0087a3;
  --yz-primary-dark: #006075;
  --yz-secondary: #002236;
  --yz-gold: #c3a152;
  --yz-gold-light: #e6c875;
  --yz-gray-100: #f8fafc;
  --yz-gray-200: #e2e8f0;
  --yz-gray-500: #64748b;
  --yz-gray-800: #1e293b;
  --yz-white: #ffffff;
  
  --yz-shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
  --yz-shadow-md: 0 4px 15px rgba(0,0,0,0.05);
  --yz-shadow-hover: 0 10px 25px rgba(0,135,163,0.15);
  
  --yz-radius-md: 12px;
  --yz-radius-lg: 24px;
  
  --yz-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Resetear estilos internos si es necesario */
.yz-help-main p, .yz-help-main h1, .yz-help-main h2, .yz-help-main h3 {
  margin: 0;
  padding: 0;
}

/* --- 2. Hero Section (Similar a Contacto pero adaptado a Help) --- */
.yz-help-hero {
  position: relative;
  width: 100%;
  height: 380px;
  background-image: linear-gradient(rgba(0, 34, 54, 0.75), rgba(0, 34, 54, 0.85)), url('https://yatezzitos.com/wp-content/uploads/2026/03/contact-bg-placeholder.jpg'); /* Reemplazar con imagen real si existe */
  background-color: var(--yz-secondary);
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  margin-bottom: -60px; /* Para que el contenido principal monte sobre el Hero */
  z-index: 1;
}

.yz-help-hero-content {
  animation: yzFadeInUp 0.8s ease backwards;
  max-width: 800px;
  padding: 0 20px;
}

.yz-help-hero-content h1 {
  color: var(--yz-gold);
  font-family: 'DM Serif Display', serif;
  font-size: 2.5rem;
  margin-bottom: 5px;
  font-weight: 400;
  letter-spacing: 1px;
}

.yz-help-hero-content h2 {
  color: var(--yz-white);
  font-family: 'Inter', sans-serif;
  font-size: 3rem;
  font-weight: 700;
  letter-spacing: -0.5px;
  margin-bottom: 20px;
}

.yz-help-hero-content p {
  color: #e2e8f0;
  font-family: 'Inter', sans-serif;
  font-size: 1.15rem;
  font-weight: 300;
}

/* --- 3. Main Container y Layout --- */
.yz-help-main {
  position: relative;
  background-color: var(--yz-gray-100);
  padding: 60px 20px 100px;
  z-index: 2;
  overflow: hidden; /* Evitar scroll x de elementos decorativos */
}

/* Decoraciones (Starfish/Shells) */
.yz-decor {
  position: absolute;
  z-index: -1;
  opacity: 0.15;
  pointer-events: none;
}
.yz-help-decor-starfish {
  top: 15%;
  left: -5%;
  width: 250px;
  transform: rotate(15deg);
}

.yz-help-container {
  max-width: 900px;
  margin: 0 auto;
  position: relative;
  z-index: 10;
}

/* --- 4. Categorías de FAQ --- */
.yz-faq-category {
  background: var(--yz-white);
  border-radius: var(--yz-radius-lg);
  padding: 40px;
  margin-bottom: 40px;
  box-shadow: var(--yz-shadow-md);
  animation: yzFadeInUp 0.8s ease backwards;
}

/* Cascada de animación por cada bloque */
.yz-faq-category:nth-child(1) { animation-delay: 0.1s; }
.yz-faq-category:nth-child(2) { animation-delay: 0.2s; }
.yz-faq-category:nth-child(3) { animation-delay: 0.3s; }
.yz-faq-category:nth-child(4) { animation-delay: 0.4s; }

.yz-faq-category-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 30px;
  border-bottom: 2px solid var(--yz-gray-100);
  padding-bottom: 15px;
}

.yz-faq-icon {
  font-size: 2rem;
  /* Efecto moderno */
  filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
}

.yz-faq-category-header h3 {
  color: var(--yz-secondary);
  font-family: 'DM Serif Display', serif;
  font-size: 1.8rem;
  font-weight: 400;
  line-margin: 0;
}

/* --- 5. Accordion (Detalles Nativo) --- */
.yz-faq-item {
  margin-bottom: 15px;
  border: 1px solid var(--yz-gray-200);
  border-radius: var(--yz-radius-md);
  background-color: var(--yz-white);
  transition: var(--yz-transition);
  overflow: hidden;
}

.yz-faq-item:hover {
  border-color: var(--yz-primary);
  box-shadow: var(--yz-shadow-sm);
}

/* Ocultar flecha nativa de Safari/Chrome */
.yz-faq-item summary::-webkit-details-marker {
  display: none;
}
.yz-faq-item summary {
  list-style: none; /* Firefox */
}

/* Título de la pregunta */
.yz-faq-item summary {
  padding: 20px 25px;
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 1.1rem;
  color: var(--yz-secondary);
  cursor: pointer;
  position: relative;
  transition: var(--yz-transition);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.yz-faq-item summary:hover {
  color: var(--yz-primary);
  background-color: rgba(0, 135, 163, 0.02);
}

/* Icono Plus / Minus personalizado via ::after */
.yz-faq-item summary::after {
  content: "+";
  font-family: monospace; /* Para centro geométrico */
  font-weight: 400;
  font-size: 1.8rem;
  color: var(--yz-primary);
  line-height: 1;
  transition: transform 0.3s ease;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: var(--yz-gray-100);
}

/* Estado abierto */
.yz-faq-item[open] {
  border-color: var(--yz-primary);
  box-shadow: 0 4px 10px rgba(0, 135, 163, 0.08);
}

.yz-faq-item[open] summary {
  color: var(--yz-primary);
  border-bottom: 1px solid var(--yz-gray-100);
}

.yz-faq-item[open] summary::after {
  content: "−"; /* Menos */
  background-color: var(--yz-primary);
  color: var(--yz-white);
  transform: rotate(180deg);
}

/* Contenido de la respuesta (Dropdown) */
.yz-faq-content {
  padding: 20px 25px 25px;
  font-family: 'Inter', sans-serif;
  color: var(--yz-gray-500);
  font-size: 1rem;
  line-height: 1.7;
  animation: yzDropdown 0.4s ease forwards;
}

.yz-faq-content p {
  margin-bottom: 12px;
}
.yz-faq-content p:last-child {
  margin-bottom: 0;
}
.yz-faq-content ul {
  padding-left: 20px;
  margin-top: 10px;
  margin-bottom: 0;
}
.yz-faq-content li {
  margin-bottom: 8px;
}
.yz-faq-content strong {
  color: var(--yz-secondary);
}

/* --- 6. Call to Action (Aún tienes dudas) --- */
.yz-help-cta {
  background: var(--yz-secondary);
  border-radius: var(--yz-radius-lg);
  padding: 50px 40px;
  text-align: center;
  margin-top: 60px;
  box-shadow: 0 20px 40px rgba(0,34,54,0.2);
  position: relative;
  overflow: hidden;
  animation: yzFadeInUp 1s ease backwards;
}

/* Fondo decorativo CSS sutil */
.yz-help-cta::before {
  content: '';
  position: absolute;
  top: -50%; right: -10%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--yz-gold-light) 0%, transparent 70%);
  opacity: 0.1;
  border-radius: 50%;
}

.yz-help-cta h3 {
  font-family: 'DM Serif Display', serif;
  font-size: 2.2rem;
  color: var(--yz-white);
  margin-bottom: 15px;
}

.yz-help-cta p {
  color: #a0aec0;
  font-family: 'Inter', sans-serif;
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto 30px;
}

.yz-help-cta-buttons {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap; /* Para móviles */
}

/* Botones reutilizables (Mismo estilo que contacto) */
.yz-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px 32px;
  border-radius: 50px;
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 1rem;
  text-decoration: none;
  cursor: pointer;
  transition: var(--yz-transition);
  border: none;
}

.yz-btn svg {
  transition: transform 0.3s ease;
}

.yz-btn:hover svg {
  transform: scale(1.1);
}

.yz-btn-primary {
  background-color: var(--yz-primary);
  color: var(--yz-white);
  box-shadow: 0 4px 15px rgba(0, 135, 163, 0.3);
}

.yz-btn-primary:hover {
  background-color: var(--yz-primary-dark);
  box-shadow: 0 6px 20px rgba(0, 135, 163, 0.4);
  transform: translateY(-2px);
  color: white;
}

.yz-btn-secondary {
  background-color: transparent;
  color: var(--yz-white);
  border: 2px solid var(--yz-gold);
}

.yz-btn-secondary:hover {
  background-color: var(--yz-gold);
  color: var(--yz-secondary);
  transform: translateY(-2px);
}

/* --- 7. Animaciones --- */
@keyframes yzFadeInUp {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes yzDropdown {
  0% { opacity: 0; transform: translateY(-10px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* --- 8. Responsive / Mobile --- */
@media (max-width: 992px) {
  .yz-help-hero {
    height: 320px;
  }
  .yz-help-hero-content h1 {
    font-size: 2rem;
  }
  .yz-help-hero-content h2 {
    font-size: 2.4rem;
  }
  .yz-faq-category {
    padding: 30px 20px;
  }
  .yz-help-cta {
    padding: 40px 20px;
  }
}

@media (max-width: 768px) {
  .yz-help-hero {
    height: 300px;
    margin-bottom: -40px;
  }
  .yz-help-hero-content h1 {
    font-size: 1.8rem;
  }
  .yz-help-hero-content h2 {
    font-size: 2rem;
  }
  .yz-faq-item summary {
    font-size: 1rem;
    padding: 15px 20px;
  }
  .yz-faq-category-header h3 {
    font-size: 1.5rem;
  }
  .yz-btn {
    width: 100%; /* Botones bloque 100% en celular */
  }
  .yz-faq-content {
    padding: 15px 20px 20px;
  }
}
```
