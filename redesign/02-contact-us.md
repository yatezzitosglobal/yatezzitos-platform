# Código Rediseño: Contact Us (Contacto)

Este documento contiene la maqueta en HTML y el CSS para construir la página de Contacto en WordPress. Se han implementado los iconos en los inputs, correcciones de encuadre en versión móvil, z-index de las decoraciones, enlaces en color azul, y un copywriting en español ajustado al SEO de Yatezzitos (priorizando "renta de yates").

---

## 1. Código HTML

Copia este bloque en tu widget de **HTML Personalizado** en Elementor:

```html
<!-- HERO SECTION -->
<div class="yz-contact-hero">
  <div class="yz-contact-hero-content">
    <h1>Contáctanos:</h1>
    <h2>Renta de Yates Hoy</h2>
  </div>
</div>

<!-- MAIN CONTACT SECTION -->
<div class="yz-contact-main">
  <!-- Decorative Elements -->
  <!-- Estrellas de mar fijadas a la izquierda -->
  <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Estrella-de-mar.png" alt="Decoración Estrella de mar" class="yz-decor yz-decor-starfish">
  <!-- Caracolas fijadas abajo a la derecha -->
  <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Caracolas.png" alt="Decoración Caracolas" class="yz-decor yz-decor-shells">

  <div class="yz-contact-container">
    
    <!-- LEFT COLUMN: FORM -->
    <div class="yz-card yz-contact-form-card">
      <h3>Te responderemos en menos de <span class="yz-badge">24</span> horas</h3>
      <p class="yz-subtitle">Escríbenos para tu renta de yates, te responderemos lo más pronto posible.</p>
      
      <!-- Si usas Elementor Forms o Contact Form 7, tendrás que forzar las clases CSS. De lo contrario, aquí tienes la estructura base pura. -->
      <form class="yz-form">
        <div class="yz-form-row">
          <div class="yz-input-group">
            <label>Nombre*</label>
            <div class="yz-input-wrapper">
              <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Nombre-de-usuario-Form.svg" class="yz-input-icon" alt="Icono usuario">
              <input type="text" placeholder="Ingresa tu nombre" required>
            </div>
          </div>
          <div class="yz-input-group">
            <label>Apellido*</label>
            <div class="yz-input-wrapper">
              <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Nombre-de-usuario-Form.svg" class="yz-input-icon" alt="Icono apellido">
              <input type="text" placeholder="Ingresa tu apellido" required>
            </div>
          </div>
        </div>
        <div class="yz-form-row">
          <div class="yz-input-group">
            <label>Correo*</label>
            <div class="yz-input-wrapper">
              <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Email.svg" class="yz-input-icon" alt="Icono correo">
              <input type="email" placeholder="Ingresa tu correo" required>
            </div>
          </div>
          <div class="yz-input-group">
            <label>Teléfono*</label>
            <div class="yz-input-wrapper">
              <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-telefono-llamadas.svg" class="yz-input-icon" alt="Icono teléfono">
              <input type="tel" placeholder="Ingresa tu teléfono" required>
            </div>
          </div>
        </div>
        <div class="yz-form-row">
          <div class="yz-input-group yz-full-width">
            <label>Mensaje</label>
            <div class="yz-input-wrapper">
              <!-- El campo mensaje suele no llevar icono para aprovechar el espacio, según me comentas, pero conservamos la envoltura estructural -->
              <textarea placeholder="Escribe tu duda o detalles de la renta aquí..." rows="4"></textarea>
            </div>
          </div>
        </div>
        <!-- Botón con SVG provisto -->
        <button type="submit" class="yz-btn-submit">
          Enviar Mensaje 
          <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-cta-boton.svg" alt="Mandar CTA" class="yz-btn-cta-icon">
        </button>
      </form>
    </div>

    <!-- RIGHT COLUMN: INFO -->
    <div class="yz-card yz-contact-info-card">
      <h3>¿Prefieres Chat? Háblanos en Redes</h3>
      <p class="yz-subtitle">Estamos a un clic de distancia. Escríbenos por cualquiera de las plataformas abajo, solemos responder en un par de horas.</p>
      
      <div class="yz-social-contact">
        <!-- WhatsApp -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
            <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Vector-Whatsapp.svg" alt="WhatsApp Icon">
          </div>
          <div class="yz-social-text">
            <h4>WhatsApp</h4>
            <p>Chatea con nuestro equipo al instante</p>
            <a href="https://wa.me/526691324073" target="_blank" class="yz-social-link">+52 (669) 132 4073</a> 
          </div>
        </div>
        
        <!-- Email -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
            <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Email.svg" alt="Email Icon">
          </div>
          <div class="yz-social-text">
            <h4>Correo Electrónico</h4>
            <p>Envíanos tus consultas cuando gustes</p>
            <a href="mailto:help@yatezzitos.com" class="yz-social-link">help@yatezzitos.com</a>
          </div>
        </div>

        <!-- Instagram -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
            <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Instagram.svg" alt="Instagram Icon">
          </div>
          <div class="yz-social-text">
            <h4>Instagram</h4>
            <p>Envía un DM para recibir ayuda e info</p>
            <a href="https://instagram.com/Yatezzitos" target="_blank" class="yz-social-link">@Yatezzitos</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

---

## 2. Código CSS (Pixel-Perfect y Responsive)

Inserta este código en **Pestaña Avanzado > Custom CSS** de Elementor o donde inyectas el diseño. 

```css
/* ==========================================================
   YATEZZITOS REDESIGN - CONTACT US PAGE (PIXEL-PERFECT)
   ========================================================== */

* {
  box-sizing: border-box;
}

/* --- 1. Hero Section --- */
.yz-contact-hero {
  /* Fondo utilizando la imagen provista */
  background-image: url('https://yatezzitos.com/wp-content/uploads/2026/03/Group-91.png');
  background-size: cover;
  background-position: center bottom;
  background-repeat: no-repeat;
  padding: clamp(80px, 10vw, 150px) 5%;
  min-height: clamp(350px, 40vh, 500px);
  display: flex;
  align-items: center;
}

.yz-contact-hero-content {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
}

.yz-contact-hero-content h1 {
  color: #ffffff !important;
  font-size: clamp(40px, 5vw, 64px);
  font-family: var(--e-global-typography-primary-font-family), "Georgia", "Inter", serif; 
  margin: 0 0 5px 0;
  font-weight: 600;
  line-height: 1.1;
  text-shadow: 0px 4px 10px rgba(0, 0, 0, 0.4);
}

.yz-contact-hero-content h2 {
  color: #ffffff !important;
  font-size: clamp(32px, 4vw, 56px);
  font-family: var(--e-global-typography-primary-font-family), "Georgia", "Inter", serif;
  margin: 0;
  font-weight: 500;
  line-height: 1.1;
  text-shadow: 0px 4px 10px rgba(0, 0, 0, 0.4);
}

/* --- 2. Main Layout --- */
.yz-contact-main {
  background-color: #E8F7FB; 
  padding: clamp(60px, 8vw, 100px) 5% clamp(100px, 12vw, 160px) 5%;
  position: relative;
  overflow: hidden; /* Frena que el contenedor se salga del viewport limitando scrolls fantasmas */
}

/* Decoraciones Absolutas */
.yz-decor {
  position: absolute;
  /* El z-index en 0 evita que sobrepongas al Header de WP o al form, manteniéndose pasivo detrás de los elementos útiles */
  z-index: 0; 
  pointer-events: none; 
}

.yz-decor-starfish {
  left: -20px;
  top: 50%;
  transform: translateY(-50%);
  width: clamp(150px, 20vw, 300px);
  opacity: 0.9;
}

.yz-decor-shells {
  right: -30px;
  bottom: 0px;
  width: clamp(200px, 25vw, 400px);
  opacity: 0.95;
}

.yz-contact-container {
  max-width: 1200px;
  width: 100%;
  margin: -100px auto 0 auto; 
  display: flex;
  flex-wrap: wrap;
  gap: clamp(20px, 3vw, 40px);
  justify-content: space-between;
  position: relative;
  z-index: 2; /* Arriba de las decoraciones */
}

/* --- 3. Cards Base Styles --- */
.yz-card {
  background: #ffffff;
  border-radius: 24px;
  padding: clamp(30px, 5vw, 50px);
  box-shadow: 0 16px 48px rgba(0, 41, 102, 0.08); 
  display: flex;
  flex-direction: column;
}

.yz-contact-form-card { flex: 1 1 55%; }
.yz-contact-info-card { flex: 1 1 35%; }

.yz-card h3 {
  font-size: clamp(24px, 2.5vw, 32px);
  font-weight: 600;
  color: #0B1928;
  margin-bottom: 20px;
  font-family: var(--e-global-typography-primary-font-family), "Georgia", serif;
  line-height: 1.25;
}

.yz-card .yz-subtitle {
  color: #4B5A6A;
  font-size: clamp(15px, 1.5vw, 17px);
  line-height: 1.6;
  margin-bottom: clamp(25px, 3vw, 40px);
}

.yz-badge {
  background: #A9F0D1; /* Corregido para verde de Yatezzitos válido */
  color: #06452D; 
  padding: 4px 14px;
  border-radius: 50px;
  display: inline-block;
  font-family: var(--e-global-typography-text-font-family), sans-serif;
  font-weight: 700;
  font-size: 0.9em;
}

/* --- 4. Form Styles con Iconos --- */
.yz-form { 
  display: flex; 
  flex-direction: column; 
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

.yz-form-row {
  display: flex;
  flex-wrap: wrap; /* Soporte nativo para grid natural */
  gap: 20px;
  margin-bottom: 20px;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

.yz-input-group {
  flex: 1 1 calc(50% - 10px); 
  display: flex;
  flex-direction: column;
  min-width: 0; /* Previene overflows nativos de inputs */
  width: 100%;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

.yz-full-width {
  flex: 1 1 100% !important;
  max-width: 100% !important;
}

.yz-input-group label {
  font-size: 14px;
  font-weight: 700; /* Un poco mas destacado como manda el diseño */
  color: #0B1928;
  margin-bottom: 10px;
  text-transform: uppercase; /* Así venia en el diseño original "EMAIL*" */
  letter-spacing: 0.5px;
  box-sizing: border-box !important;
}

/* Wrappers para los iconos dentro inputs */
.yz-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

.yz-input-icon {
  position: absolute;
  left: 16px;
  width: 20px;
  height: auto;
  pointer-events: none; /* No rompe el selector del input */
  z-index: 2;
}

.yz-input-wrapper input {
  padding-left: 46px !important; /* Espacio exacto para que el icono no choque con el texto */
}

.yz-input-wrapper input,
.yz-input-wrapper textarea {
  box-sizing: border-box !important;
  background: #F9FBFD;
  border: 1px solid #D1DFE8;
  border-radius: 12px;
  padding: 16px 20px;
  font-size: 16px;
  color: #0B1928;
  width: 100%;
  transition: all 0.3s ease;
  font-family: var(--e-global-typography-text-font-family), sans-serif;
}

.yz-input-wrapper input::placeholder,
.yz-input-wrapper textarea::placeholder {
  color: #A0B0C0;
}

.yz-input-wrapper input:focus,
.yz-input-wrapper textarea:focus {
  outline: none;
  border-color: #001F3F;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(0, 31, 63, 0.1);
}

.yz-btn-submit {
  background: #001F3F; 
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 50px;
  padding: 16px 36px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 15px;
  transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
  align-self: flex-start; 
}

.yz-btn-cta-icon {
  width: 18px;
  height: auto;
}

.yz-btn-submit:hover {
  background: #003366;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 31, 63, 0.2);
}

/* --- 5. Social Info Styles --- */
.yz-social-contact {
  display: flex;
  flex-direction: column;
  gap: clamp(20px, 3.5vh, 35px);
  margin-top: auto; 
}

.yz-social-item {
  display: flex;
  align-items: flex-start;
  gap: 18px;
}

.yz-social-icon-wrapper {
  background: #E8F7FB; 
  border-radius: 50%;
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.yz-social-icon-wrapper img {
  width: 24px;
  height: auto;
}

.yz-social-text {
  display: flex;
  flex-direction: column;
}

.yz-social-text h4 {
  margin: 0 0 6px 0;
  font-size: clamp(16px, 1.8vw, 18px);
  font-weight: 700;
  color: #0B1928;
  text-transform: uppercase;
}

.yz-social-text p {
  margin: 0 0 8px 0;
  font-size: 15px;
  color: #4B5A6A;
  line-height: 1.4;
}

/* Azul brillante identico al "link blue" para los contactos */
.yz-social-link {
  color: #0056D2; 
  font-weight: 600;
  font-size: 16px;
  text-decoration: underline; 
  align-self: flex-start;
}

.yz-social-link:hover {
  color: #003B95;
}

/* --- 6. Media Queries para Responsive (Tablet y Móvil) --- */

/* Tablets Normales - IPads */
@media (max-width: 992px) {
  .yz-contact-container {
    gap: 25px;
    margin-top: -60px;
  }
  
  .yz-contact-form-card, 
  .yz-contact-info-card {
    flex: 1 1 100%;
  }
  
  /* FORZAR DESDE TABLET PARA EVITAR DESBORDAMIENTOS (SCROLL HORIZONTAL) */
  .yz-form-row {
    display: flex !important;
    flex-direction: column !important; 
    gap: 20px !important;
    width: 100% !important;
  }

  .yz-input-group {
    flex: 1 1 100% !important;
    width: 100% !important;
    max-width: 100% !important;
  }

  .yz-decor-starfish {
    width: 120px;
    top: 5%;
    left: -10px;
  }
  
  .yz-decor-shells {
    width: 160px;
  }
}

/* Móviles Grandes y pequeños */
@media (max-width: 768px) {
  .yz-contact-hero {
    /* Mueve la imagen a la derecha para obligar que salga la modelo 
       Si no aparece exacta, se puede ajustar a `85% center` */
    background-position: 80% center; 
  }

  .yz-contact-main {
    padding-top: 40px;
    padding-bottom: 80px;
  }

  .yz-contact-container {
    margin-top: -30px;
  }
  
  .yz-card {
    padding: 30px 20px; 
    border-radius: 20px;
    width: 100% !important;
    max-width: 100% !important;
  }

  .yz-btn-submit {
    width: 100% !important; 
    padding: 16px 20px;
  }
  
  /* Ajustamos las decoraciones para móvil (z-index y opacidad para que no tapen formularios) */
  .yz-decor-starfish {
    width: 80px;
    left: -10px;
    top: 0px; 
    opacity: 0.5;
    z-index: 0;
  }

  .yz-decor-shells {
    width: 100px;
    right: -10px;
    bottom: 0px; 
    opacity: 0.4; 
    z-index: 0;
  }
}
```
