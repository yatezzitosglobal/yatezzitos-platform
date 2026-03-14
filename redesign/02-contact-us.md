# Código Rediseño: Contact Us (Contacto)

Este documento contiene la maqueta en HTML y el CSS necesario para construir la nueva página de Contacto basada en los diseños de Figma en WordPress.

## Instrucciones de Implementación en Elementor
1. Abre la página "Contacto" o crea una nueva.
2. Añade un contenedor principal (sección) de **Ancho Completo** (Full Width).
3. Añade el widget **HTML Personalizado** dentro de ese contenedor.
4. Copia el bloque de "HTML" y pégalo.
5. Copia el bloque "CSS" y ponlo en `Apariencia > Personalizar > CSS Adicional` (o en la pestaña de "Avanzado > Custom CSS" del propio widget de Elementor).

---

## 1. Código HTML

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
  <div class="yz-contact-container">
    
    <!-- LEFT COLUMN: FORM -->
    <div class="yz-card yz-contact-form-card">
      <h3>We'll get back to you in under <span class="yz-badge">24</span> hours</h3>
      <p class="yz-subtitle">Feel free to contact us any time. We will get back to you as soon as we can!</p>
      
      <!-- NOTA: Aquí puedes reemplazar este código por el shortcode de tu plugin de formularios, por ejemplo: [contact-form-7 id="123"] o mantener este diseño si lo unes a un Webhook hacia GHL -->
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
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Reemplazar con el ícono SVG real de WhatsApp de FontAwesome/Elementor si es necesario -->
              <path d="M12 2C6.48 2 2 6.48 2 12C2 13.84 2.5 15.56 3.38 17.03L2.24 21.24L6.61 20.1C8.21 21.32 10.02 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM17.43 16.48C17.18 17.18 15.93 17.78 15.22 17.88C14.65 17.96 13.88 18.06 11.23 16.96C8.04 15.63 5.98 12.38 5.8 12.14C5.64 11.91 4.34 10.18 4.34 8.39C4.34 6.59 5.25 5.72 5.6 5.36C5.9 5.04 6.38 4.9 6.81 4.9C6.94 4.9 7.07 4.9 7.18 4.91C7.5 4.92 7.66 4.94 7.87 5.46C8.13 6.09 8.76 7.62 8.84 7.78C8.91 7.94 9 8.16 8.87 8.41C8.75 8.67 8.63 8.77 8.44 8.99C8.25 9.21 8.05 9.38 7.87 9.61C7.66 9.87 7.42 10.12 7.67 10.55C7.92 10.98 8.78 12.38 10.05 13.51C11.68 14.97 13.02 15.43 13.48 15.62C13.94 15.82 14.43 15.78 14.75 15.43C15.15 14.99 15.63 14.28 16.14 13.56C16.51 13.04 16.96 12.98 17.38 13.13C17.8 13.28 20.06 14.39 20.52 14.62C20.98 14.85 21.28 14.97 21.39 15.15C21.5 15.34 21.5 16.4 20.93 17.38" fill="#000"/>
            </svg>
          </div>
          <div class="yz-social-text">
            <h4>WhatsApp</h4>
            <p>Chat with our support team instantly</p>
            <a href="https://wa.me/526691324073" target="_blank">+52 (669) 132 4073</a> <!-- Usa el teléfono oficial -->
          </div>
        </div>
        
        <!-- Email -->
        <div class="yz-social-item">
          <div class="yz-social-icon-wrapper">
             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 4H4C2.9 4 2.01 4.9 2.01 6L2 18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM19.6 8.25L12.53 12.67C12.21 12.87 11.79 12.87 11.47 12.67L4.4 8.25C4.15 8.09 4 7.82 4 7.53C4 6.86 4.73 6.46 5.3 6.81L12 11L18.7 6.81C19.27 6.46 20 6.86 20 7.53C20 7.82 19.85 8.09 19.6 8.25Z" fill="#000"/>
            </svg>
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
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M7.8 2H16.2C19.4 2 22 4.6 22 7.8V16.2C22 19.4 19.4 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2ZM7.6 4C5.6 4 4 5.6 4 7.6V16.4C4 18.4 5.6 20 7.6 20H16.4C18.4 20 20 18.4 20 16.4V7.6C20 5.6 18.4 4 16.4 4H7.6ZM12 6.875C14.8305 6.875 17.125 9.16954 17.125 12C17.125 14.8305 14.8305 17.125 12 17.125C9.16954 17.125 6.875 14.8305 6.875 12C6.875 9.16954 9.16954 6.875 12 6.875ZM12 8.875C10.2741 8.875 8.875 10.2741 8.875 12C8.875 13.7259 10.2741 15.125 12 15.125C13.7259 15.125 15.125 13.7259 15.125 12C15.125 10.2741 13.7259 8.875 12 8.875ZM17.25 5.5C18.2165 5.5 19 6.2835 19 7.25C19 8.2165 18.2165 9 17.25 9C16.2835 9 15.5 8.2165 15.5 7.25C15.5 6.2835 16.2835 5.5 17.25 5.5Z" fill="#000"/>
            </svg>
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

## 2. Código CSS

Copia esto en WordPress para que los elementos obtengan la estética del archivo Figma.

```css
/* ==========================================================
   YATEZZITOS REDESIGN - CONTACT US PAGE
   ========================================================== */

/* --- 1. Hero Section --- */
.yz-contact-hero {
  /* Sustituye la URL con la imagen correspondiente del Hero de la mujer */
  background-image: linear-gradient(to right, rgba(0, 31, 63, 0.8) 0%, rgba(0, 31, 63, 0.4) 100%), url('/wp-content/uploads/2026/03/contact-hero-bg.jpg');
  background-size: cover;
  background-position: center;
  padding: 120px 5%;
  min-height: 400px;
  display: flex;
  align-items: center;
}

.yz-contact-hero-content {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
}

.yz-contact-hero-content h1 {
  color: #fff;
  font-size: 52px;
  font-family: "Georgia", "Times New Roman", serif; /* Ajustar según la tipografía premium definida */
  margin: 0 0 10px 0;
  font-weight: 500;
  line-height: 1.1;
}

.yz-contact-hero-content h2 {
  color: #fff;
  font-size: 52px;
  font-family: "Georgia", "Times New Roman", serif;
  margin: 0;
  font-weight: 500;
  line-height: 1.1;
}

/* --- 2. Main Layout --- */
.yz-contact-main {
  /* Fondo general estilo océano azul claro con objetos si es necesario */
  background: #E8F7FB; 
  /* Puedes añadir tu propio patrón de fondo de estrellas de mar o caracoles via imagen si gustas */
  /* background-image: url('...'); */
  padding: 80px 5% 120px 5%;
}

.yz-contact-container {
  max-width: 1200px;
  margin: -80px auto 0 auto; /* Para sobreponerse ligeramente si hace falta o mantenerlo alineado */
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: space-between;
}

/* --- 3. Cards Base Styles --- */
.yz-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 50px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.05);
}

.yz-contact-form-card {
  flex: 1 1 55%;
}

.yz-contact-info-card {
  flex: 1 1 35%;
}

.yz-card h3 {
  font-size: 28px;
  font-weight: 600;
  color: #000;
  margin-bottom: 15px;
  font-family: "Georgia", "Times New Roman", serif;
}

.yz-card .yz-subtitle {
  color: #555;
  font-size: 16px;
  line-height: 1.6;
  margin-bottom: 30px;
}

.yz-badge {
  background: #a9f0d1; /* Verde menta Yatezzitos */
  color: #000;
  padding: 0px 10px;
  border-radius: 50px;
  display: inline-block;
  font-family: sans-serif;
  font-weight: bold;
}

/* --- 4. Form Styles --- */
.yz-form-row {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.yz-input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.yz-input-group label {
  font-size: 14px;
  font-weight: 600;
  color: #111;
  margin-bottom: 8px;
}

.yz-input-group input,
.yz-input-group textarea {
  background: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  font-size: 15px;
  color: #333;
  width: 100%;
  transition: border-color 0.3s ease;
}

.yz-input-group input:focus,
.yz-input-group textarea:focus {
  outline: none;
  border-color: #001F3F;
}

.yz-btn-submit {
  background: #001F3F; /* Azul marino oscuro */
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 50px;
  padding: 15px 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: transform 0.2s ease, background 0.2s ease;
  margin-top: 10px;
}

.yz-btn-submit:hover {
  background: #003366;
  transform: translateY(-2px);
}

/* --- 5. Info/Social Section Styles --- */
.yz-social-contact {
  display: flex;
  flex-direction: column;
  gap: 30px;
  margin-top: 40px;
}

.yz-social-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
}

.yz-social-icon-wrapper {
  background: #f5f5f5;
  border-radius: 50%;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.yz-social-text h4 {
  margin: 0 0 5px 0;
  font-size: 18px;
  font-weight: 600;
  color: #000;
}

.yz-social-text p {
  margin: 0 0 8px 0;
  font-size: 14px;
  color: #555;
  line-height: 1.4;
}

.yz-social-text a {
  color: #0066cc;
  text-decoration: underline;
  font-weight: 500;
  font-size: 15px;
}

/* --- 6. Responsive Defaults --- */
@media (max-width: 900px) {
  .yz-contact-hero-content h1,
  .yz-contact-hero-content h2 {
    font-size: 38px;
  }
  
  .yz-form-row {
    flex-direction: column;
    gap: 20px;
  }
  
  .yz-card {
    padding: 30px;
  }
}

@media (max-width: 768px) {
  .yz-contact-form-card,
  .yz-contact-info-card {
    flex: 1 1 100%;
  }
  
  .yz-contact-hero {
    padding: 80px 5%;
  }
}
```
