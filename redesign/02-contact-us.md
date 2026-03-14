# Código Rediseño: Contact Us (Contacto)

Este documento contiene la maqueta en HTML y el CSS exactos y actualizados con las imágenes y gráficos de tu biblioteca para construir la nueva página de Contacto en WordPress. 

Además, el código incluye media queries y `clamp()` para asegurar un diseño **Pixel-Perfect y Responsive** en PC, tabletas y móviles móviles.

---

## 1. Código HTML

Copia este bloque en tu widget de **HTML Personalizado** en Elementor:

```html
<!-- HERO SECTION -->
<div class="yz-contact-hero">
  <div class="yz-contact-hero-content">
    <h1>Contact Us:</h1>
    <h2>We're Here to Help</h2>
  </div>
</div>

<!-- MAIN CONTACT SECTION -->
<div class="yz-contact-main">
  <!-- Decorative Elements -->
  <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Estrella-de-mar.png" alt="Estrella de mar" class="yz-decor yz-decor-starfish">
  <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Caracolas.png" alt="Caracolas" class="yz-decor yz-decor-shells">

  <div class="yz-contact-container">
    
    <!-- LEFT COLUMN: FORM -->
    <div class="yz-card yz-contact-form-card">
      <h3>We'll get back to you in under <span class="yz-badge">24</span> hours</h3>
      <p class="yz-subtitle">Feel free to contact us any time. We will get back to you as soon as we can!</p>
      
      <!-- NOTA: Si utilizas un shortcode como Contact Form 7 o el form de GHL, reemplaza esta etiqueta <form> por el shortcode, asegurando conservar las clases visuales de los inputs si los puedes editar o aplicar CSS similar -->
      <form class="yz-form">
        <div class="yz-form-row">
          <div class="yz-input-group">
            <label>First Name*</label>
            <input type="text" placeholder="Enter first name" required>
          </div>
          <div class="yz-input-group">
            <label>Last Name*</label>
            <input type="text" placeholder="Enter last name" required>
          </div>
        </div>
        <div class="yz-form-row">
          <div class="yz-input-group">
            <label>Email*</label>
            <input type="email" placeholder="Enter your email" required>
          </div>
          <div class="yz-input-group">
            <label>Phone Number*</label>
            <input type="tel" placeholder="Enter phone number" required>
          </div>
        </div>
        <div class="yz-form-row">
          <div class="yz-input-group">
            <label>Message</label>
            <textarea placeholder="Write your message here..." rows="4"></textarea>
          </div>
        </div>
        <button type="submit" class="yz-btn-submit">Send Message <span class="yz-btn-icon">➤</span></button>
      </form>
    </div>

    <!-- RIGHT COLUMN: INFO -->
    <div class="yz-card yz-contact-info-card">
      <h3>Prefer Chat? Reach us on Social Media</h3>
      <p class="yz-subtitle">We're just a message away. Feel free to reach out through any of the platforms below—we typically respond within a few hours.</p>
      
      <div class="yz-social-contact">
        <!-- WhatsApp -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
            <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Vector-Whatsapp.svg" alt="WhatsApp Icon">
          </div>
          <div class="yz-social-text">
            <h4>WhatsApp</h4>
            <p>Chat with our support team instantly</p>
            <a href="https://wa.me/526691324073" target="_blank">+52 (669) 132 4073</a> 
          </div>
        </div>
        
        <!-- Email -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
            <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Email.svg" alt="Email Icon">
          </div>
          <div class="yz-social-text">
            <h4>Email</h4>
            <p>Send us your queries anytime at</p>
            <a href="mailto:help@yatezzitos.com">help@yatezzitos.com</a>
          </div>
        </div>

        <!-- Instagram -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
            <img src="https://yatezzitos.com/wp-content/uploads/2026/03/Frame-Instagram.svg" alt="Instagram Icon">
          </div>
          <div class="yz-social-text">
            <h4>Instagram</h4>
            <p>DM us for quick help & updates</p>
            <a href="https://instagram.com/Yatezzitos" target="_blank">@Yatezzitos</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

---

## 2. Código CSS (Pixel-Perfect y Responsive)

Inserta este código en **Pestaña Avanzado > Custom CSS** de Elementor, o en tu personalizador de Yellow Pencil global.

```css
/* ==========================================================
   YATEZZITOS REDESIGN - CONTACT US PAGE (PIXEL-PERFECT)
   ========================================================== */

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
  text-shadow: 0px 4px 10px rgba(0, 0, 0, 0.4); /* Refuerza el texto p/ accesibilidad sobre imagen */
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
  /* Fondo base limpio para sobreponer decoración */
  background-color: #E8F7FB; 
  padding: clamp(60px, 8vw, 100px) 5% clamp(100px, 12vw, 160px) 5%;
  position: relative;
  overflow: hidden; /* Asegura que la decoración extra no amplíe la pantalla(scroll horizontal) */
}

/* Decoraciones Absolutas */
.yz-decor {
  position: absolute;
  z-index: 1; /* Debajo de los cards */
  pointer-events: none; /* No intervienen con clics */
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
  margin: -100px auto 0 auto; /* Overlap sutil sobre el Hero */
  display: flex;
  flex-wrap: wrap;
  gap: clamp(20px, 3vw, 40px);
  justify-content: space-between;
  position: relative;
  z-index: 5; /* Por encima de las decoraciones */
}

/* --- 3. Cards Base Styles --- */
.yz-card {
  background: #ffffff;
  border-radius: 24px;
  padding: clamp(30px, 5vw, 50px);
  box-shadow: 0 16px 48px rgba(0, 41, 102, 0.08); /* Sombra suave y sofisticada */
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
  background: #A9F0D1; 
  color: #06452D; /* Color del texto alineado a legibilidad */
  padding: 4px 14px;
  border-radius: 50px;
  display: inline-block;
  font-family: var(--e-global-typography-text-font-family), sans-serif;
  font-weight: 700;
  font-size: 0.9em;
}

/* --- 4. Form Styles --- */
.yz-form { display: flex; flex-direction: column; width: 100%; }

.yz-form-row {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 20px;
}

.yz-input-group {
  flex: 1 1 calc(50% - 10px); /* 2 columnas precisas */
  display: flex;
  flex-direction: column;
}

/* Si es full width (Textarea) */
.yz-form-row:last-of-type .yz-input-group {
  flex: 1 1 100%;
}

.yz-input-group label {
  font-size: 15px;
  font-weight: 600;
  color: #0B1928;
  margin-bottom: 10px;
}

.yz-input-group input,
.yz-input-group textarea {
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

.yz-input-group input::placeholder,
.yz-input-group textarea::placeholder {
  color: #A0B0C0;
}

.yz-input-group input:focus,
.yz-input-group textarea:focus {
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
  padding: 18px 45px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 15px;
  transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
  align-self: flex-start; /* Alineación a la izquierda */
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
  gap: clamp(20px, 3vh, 35px);
  margin-top: auto; /* Empuja el contenido si falta espacio */
}

.yz-social-item {
  display: flex;
  align-items: flex-start;
  gap: 18px;
  padding: 15px;
  border-radius: 16px;
  transition: background 0.3s ease;
}

.yz-social-item:hover {
  background: #F4F9FB; /* Soft hover azulito claro */
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
  font-size: clamp(18px, 2vw, 20px);
  font-weight: 700;
  color: #0B1928;
}

.yz-social-text p {
  margin: 0 0 10px 0;
  font-size: 15px;
  color: #4B5A6A;
  line-height: 1.4;
}

.yz-social-text a {
  color: #001F3F;
  font-weight: 700;
  font-size: 16px;
  text-decoration: none;
  border-bottom: 2px solid transparent;
  align-self: flex-start;
  padding-bottom: 2px;
  transition: border-color 0.3s ease;
}

.yz-social-text a:hover {
  border-color: #A9F0D1; 
  color: #003366;
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

  /* Las decoraciones cambian de tamaño/posición */
  .yz-decor-starfish {
    width: 140px;
    top: 5%;
  }
  
  .yz-decor-shells {
    width: 160px;
  }
}

/* Móviles Grandes y pequeños */
@media (max-width: 768px) {
  .yz-contact-hero {
    background-position: center center; 
  }

  .yz-contact-main {
    padding-top: 40px;
    padding-bottom: 80px;
  }

  .yz-contact-container {
    margin-top: -30px;
  }
  
  .yz-card {
    padding: 30px 20px; /* Reducimos el padding de los cards en móvil */
    border-radius: 20px;
  }

  .yz-form-row {
    flex-direction: column; /* Inputs se apilan */
    gap: 20px;
    margin-bottom: 20px;
  }

  .yz-input-group {
    flex: 1 1 100%;
  }

  .yz-btn-submit {
    width: 100%; /* Botón full-width en móvil */
    padding: 16px 20px;
  }
  
  /* Ajustamos las decoraciones para móvil que estorban menos */
  .yz-decor-starfish {
    width: 100px;
    left: -15px;
    top: -10px; /* Sube la estrella para no tapar contenido central */
  }

  .yz-decor-shells {
    width: 120px;
    right: -10px;
    bottom: 0px; 
    opacity: 0.6; /* Un poco menos intrusivos en pantallas muy pequeñas */
  }
  
  .yz-social-item {
    padding: 10px 5px; /* Menos borde en móvil */
  }
}
```
