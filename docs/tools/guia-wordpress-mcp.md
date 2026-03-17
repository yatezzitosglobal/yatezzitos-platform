# Guía: Conectar WordPress a Claude Code via MCP

---

## Opción Recomendada: `wp-mcp-server` (190+ herramientas)

**Repo:** https://github.com/c-sakel/wp-mcp-server
**Por qué:** Incluye herramientas de SEO con Yoast, gestión de contenido, Gutenberg y más. Perfecto para crear entradas optimizadas y editar fichas de yates.

### Herramientas clave que usaremos:

| Herramienta | Para qué |
|---|---|
| `wp_create_post` | Crear entradas de blog SEO |
| `wp_update_post` | Editar fichas de yates existentes |
| `wp_list_posts` | Ver todos los posts |
| `wp_list_pages` | Ver todas las páginas |
| `seo_get_yoast_meta` | Leer meta SEO actual de cualquier página |
| `seo_update_yoast_meta` | Modificar título, meta descripción, focus keyword |
| `seo_analyze_keywords` | Analizar uso de keywords en una página |
| `seo_content_score` | Calcular score SEO de contenido |
| `seo_bulk_analyze` | Analizar SEO de múltiples posts de golpe |
| `gutenberg_generate_block` | Generar bloques Gutenberg |

---

## Paso 1: Prerequisitos

```bash
# Verificar que Node.js está instalado
node --version   # Debe ser v18+
npm --version
```

---

## Paso 2: Crear Application Password en WordPress

1. Entra a `yatezzitos.com/wp-admin/`
2. Ve a **Usuarios → Tu Perfil**
3. Baja hasta la sección **"Contraseñas de aplicación"** (Application Passwords)
4. En el campo "Nuevo nombre de contraseña de aplicación" escribe: `Antigravity MCP`
5. Haz clic en **"Añadir nueva contraseña de aplicación"**
6. **Copia la contraseña generada** (se muestra solo una vez, tiene formato `xxxx xxxx xxxx xxxx xxxx xxxx`)
7. Guarda tu **usuario** y esta **contraseña** en un lugar seguro

> ⚠️ Esta contraseña de aplicación es diferente a tu contraseña normal de WordPress. Es específica para APIs.

---

## Paso 3: Instalar el MCP Server

```bash
# Clonar el repositorio
git clone https://github.com/c-sakel/wp-mcp-server.git
cd wp-mcp-server

# Instalar dependencias
npm install

# Configuración interactiva (te pedirá URL, usuario y contraseña)
npm run init

# Cuando pregunte:
# - WordPress URL: https://yatezzitos.com
# - Username: (tu usuario de WP)
# - Application Password: (la que copiaste en Paso 2)
```

---

## Paso 4: Configurar en Antigravity

Agrega la configuración del MCP server. La configuración típica es:

```json
{
  "mcpServers": {
    "wordpress": {
      "command": "node",
      "args": ["/ruta/completa/a/wp-mcp-server/dist/index.js"],
      "env": {
        "WP_URL": "https://yatezzitos.com",
        "WP_USERNAME": "tu_usuario",
        "WP_APP_PASSWORD": "xxxx xxxx xxxx xxxx xxxx xxxx"
      }
    }
  }
}
```

---

## Paso 5: Verificar conexión

Una vez configurado, en la próxima conversación con Antigravity deberías poder usar herramientas como:
- "Lista todos los posts de yatezzitos.com"
- "Muéstrame el SEO meta del post de Puerto Vallarta"
- "Crea una entrada de blog sobre renta de yates en Cancún"

---

## Alternativa Simple: `wordpress-mcp` (NPX)

Si prefieres empezar más rápido sin clonar repos:

**Repo:** https://github.com/Utsav-Ladani/WordPress-MCP

```json
{
  "mcpServers": {
    "wordpress": {
      "command": "npx",
      "args": ["-y", "wordpress-mcp"],
      "env": {
        "WORDPRESS_HOST_URL": "https://yatezzitos.com",
        "WORDPRESS_API_USERNAME": "tu_usuario",
        "WORDPRESS_API_PASSWORD": "xxxx xxxx xxxx xxxx xxxx xxxx",
        "WORDPRESS_POST_AUTHOR_ID": "1"
      }
    }
  }
}
```

Solo tiene 5 herramientas (crear, editar, buscar, obtener posts + block types), pero funciona con `npx` sin instalación.

---

## ¿Cuál elegir?

| Criterio | wp-mcp-server (190+) | wordpress-mcp (NPX) |
|---|---|---|
| Instalación | Clonar + npm install | Solo agregar config |
| Herramientas SEO/Yoast | ✅ Sí | ❌ No |
| Editar meta SEO | ✅ Sí | ❌ No |
| Crear posts | ✅ Sí | ✅ Sí |
| Editar posts | ✅ Sí | ✅ Sí |
| Gestión de media | ✅ Sí | ❌ No |
| Gutenberg blocks | ✅ Sí | ✅ Sí (schema) |
| Páginas | ✅ Sí | ❌ No |
| Categorías/Tags | ✅ Sí | ❌ No |

**Recomendación:** Empezar con la opción NPX (rápida, en 2 minutos) y después migrar a wp-mcp-server cuando necesites las herramientas SEO de Yoast.
