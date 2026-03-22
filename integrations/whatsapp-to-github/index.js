/**
 * Webhook: WhatsApp (GHL) → GitHub Issue
 * 
 * Serverless function compatible con:
 * - Cloudflare Workers (recomendado, gratis 100K req/día)
 * - Google Cloud Functions
 * - AWS Lambda
 * - Cualquier entorno Node.js
 * 
 * Recibe mensajes de WhatsApp vía GoHighLevel y crea GitHub Issues
 * que Copilot coding agent puede procesar automáticamente.
 * 
 * Variables de entorno requeridas:
 * - GHL_WEBHOOK_SECRET: Token secreto para validar requests de GHL
 * - GITHUB_TOKEN: Personal Access Token con permisos de `repo`
 * - GITHUB_OWNER: Dueño del repositorio (ej: YatezzitosMexico)
 * - GITHUB_REPO: Nombre del repositorio (ej: yatezzitos-platform)
 * - ALLOWED_CONTACTS: IDs de contactos autorizados (comma-separated)
 */

// ============================================================
// CONFIGURACIÓN
// ============================================================

const CONFIG = {
  github: {
    apiUrl: 'https://api.github.com',
    owner: '', // Se llena desde env
    repo: '',  // Se llena desde env
    token: '', // Se llena desde env
  },
  ghl: {
    webhookSecret: '', // Se llena desde env
  },
  allowedContacts: [], // Se llena desde env
  maxIssuesPerHour: 10,
  defaultLabels: ['from-whatsapp', 'ai-task'],
  taskPrefix: '/tarea:',
};

// Rate limiting simple (en memoria, se resetea con cada deploy)
const rateLimitMap = new Map();

// ============================================================
// HANDLER PRINCIPAL
// ============================================================

/**
 * Handler principal — compatible con múltiples plataformas.
 * Para Cloudflare Workers: env se pasa como 2do argumento del fetch handler.
 * Para GCF: se usa process.env.
 */
async function handleRequest(request, env) {
  // --- Cargar config desde env (Workers pasa env como 2do arg) ---
  loadConfig(env);

  // --- Solo aceptar POST ---
  if (request.method !== 'POST') {
    return jsonResponse(405, { error: 'Method not allowed' });
  }

  try {
    const body = await request.json();

    // --- Validar token de autenticación ---
    const token = request.headers.get('X-GHL-Token') || 
                  request.headers.get('x-ghl-token') ||
                  body.token;
    
    if (!token || token !== CONFIG.ghl.webhookSecret) {
      console.error('⛔ Token inválido o ausente');
      return jsonResponse(401, { error: 'Unauthorized' });
    }

    // --- Validar contacto autorizado ---
    const contactId = body.contact_id || body.contactId || body.sender_id;
    if (CONFIG.allowedContacts.length > 0 && !CONFIG.allowedContacts.includes(contactId)) {
      console.warn(`⚠️ Contacto no autorizado: ${contactId}`);
      return jsonResponse(403, { error: 'Contact not authorized' });
    }

    // --- Rate limiting ---
    if (!checkRateLimit(contactId)) {
      return jsonResponse(429, { error: 'Rate limit exceeded. Max 10 issues/hour.' });
    }

    // --- Extraer y parsear mensaje ---
    const message = body.message || body.text || body.body || '';
    
    if (!message.toLowerCase().startsWith(CONFIG.taskPrefix)) {
      return jsonResponse(200, { 
        status: 'ignored', 
        reason: `Message does not start with "${CONFIG.taskPrefix}"` 
      });
    }

    const parsed = parseTaskMessage(message);
    if (!parsed.title) {
      return jsonResponse(400, { error: 'Could not parse task title from message' });
    }

    // --- Crear GitHub Issue ---
    const issue = await createGitHubIssue(parsed, body);

    console.log(`✅ Issue creado: #${issue.number} — ${issue.title}`);

    return jsonResponse(201, {
      status: 'created',
      issue_number: issue.number,
      issue_url: issue.html_url,
      title: issue.title,
    });

  } catch (error) {
    console.error('❌ Error:', error.message);
    return jsonResponse(500, { error: 'Internal server error', details: error.message });
  }
}

// ============================================================
// FUNCIONES DE PARSEO
// ============================================================

/**
 * Parsea un mensaje de WhatsApp con formato:
 * /tarea: [título]
 * ---
 * [descripción]
 * 
 * También soporta etiquetas opcionales:
 * #seo #urgente #cancun
 */
function parseTaskMessage(message) {
  // Remover el prefijo /tarea:
  const withoutPrefix = message.replace(/^\/tarea:\s*/i, '').trim();
  
  // Separar título de descripción por ---
  const parts = withoutPrefix.split(/\n---\n/);
  const titleLine = parts[0].trim();
  const description = parts.length > 1 ? parts.slice(1).join('\n---\n').trim() : '';
  
  // Extraer hashtags del título o descripción
  const fullText = titleLine + ' ' + description;
  const hashtags = (fullText.match(/#(\w+)/g) || []).map(h => h.slice(1).toLowerCase());
  
  // Limpiar hashtags del título
  const cleanTitle = titleLine.replace(/#\w+/g, '').trim();
  
  // Mapear hashtags a labels de GitHub
  const extraLabels = [];
  const labelMap = {
    'seo': 'seo',
    'urgente': 'priority-high',
    'bug': 'bug',
    'feature': 'enhancement',
    'docs': 'documentation',
    'contenido': 'content',
    'cancun': 'dest-cancun',
    'mazatlan': 'dest-mazatlan',
    'vallarta': 'dest-puerto-vallarta',
    'cabos': 'dest-los-cabos',
    'lapaz': 'dest-la-paz',
    'huatulco': 'dest-huatulco',
    'ixtapa': 'dest-ixtapa',
    'acapulco': 'dest-acapulco',
    'playa': 'dest-playa-del-carmen',
    'nayarit': 'dest-nuevo-vallarta',
  };
  
  for (const tag of hashtags) {
    if (labelMap[tag]) {
      extraLabels.push(labelMap[tag]);
    }
  }
  
  return {
    title: cleanTitle,
    description: description,
    labels: [...CONFIG.defaultLabels, ...extraLabels],
    hashtags: hashtags,
  };
}

// ============================================================
// GITHUB API
// ============================================================

/**
 * Crea un GitHub Issue con el contenido parseado.
 */
async function createGitHubIssue(parsed, webhookBody) {
  const timestamp = new Date().toISOString();
  const senderName = webhookBody.contact_name || webhookBody.sender_name || 'WhatsApp';
  
  const issueBody = [
    parsed.description || '_Sin descripción adicional._',
    '',
    '---',
    '',
    `📱 **Creado desde WhatsApp** por ${senderName}`,
    `🕐 **Fecha:** ${timestamp}`,
    parsed.hashtags.length > 0 ? `🏷️ **Tags:** ${parsed.hashtags.map(h => `\`#${h}\``).join(' ')}` : '',
    '',
    '> _Este issue fue creado automáticamente desde un mensaje de WhatsApp vía GoHighLevel._',
    '> _Workflow: WhatsApp → GHL → Webhook → GitHub Issue_',
  ].filter(Boolean).join('\n');

  const response = await fetch(
    `${CONFIG.github.apiUrl}/repos/${CONFIG.github.owner}/${CONFIG.github.repo}/issues`,
    {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${CONFIG.github.token}`,
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
        'Content-Type': 'application/json',
        'User-Agent': 'Yatezzitos-WhatsApp-Bot/1.0',
      },
      body: JSON.stringify({
        title: parsed.title,
        body: issueBody,
        labels: parsed.labels,
      }),
    }
  );

  if (!response.ok) {
    const errorText = await response.text();
    throw new Error(`GitHub API error ${response.status}: ${errorText}`);
  }

  return await response.json();
}

// ============================================================
// UTILIDADES
// ============================================================

function loadConfig(workerEnv) {
  // Cloudflare Workers pasa env como argumento; GCF usa process.env
  const env = workerEnv || (typeof process !== 'undefined' ? process.env : {});
  
  CONFIG.github.owner = env.GITHUB_OWNER || 'YatezzitosMexico';
  CONFIG.github.repo = env.GITHUB_REPO || 'yatezzitos-platform';
  CONFIG.github.token = env.GITHUB_TOKEN || '';
  CONFIG.ghl.webhookSecret = env.GHL_WEBHOOK_SECRET || '';
  CONFIG.allowedContacts = (env.ALLOWED_CONTACTS || '').split(',').filter(Boolean);
}

function checkRateLimit(contactId) {
  const now = Date.now();
  const hourAgo = now - 3600000;
  const key = contactId || 'default';
  
  if (!rateLimitMap.has(key)) {
    rateLimitMap.set(key, []);
  }
  
  const timestamps = rateLimitMap.get(key).filter(t => t > hourAgo);
  
  if (timestamps.length >= CONFIG.maxIssuesPerHour) {
    return false;
  }
  
  timestamps.push(now);
  rateLimitMap.set(key, timestamps);
  return true;
}

function jsonResponse(status, body) {
  return new Response(JSON.stringify(body), {
    status: status,
    headers: { 'Content-Type': 'application/json' },
  });
}

// ============================================================
// EXPORTS — Compatibilidad multi-plataforma
// ============================================================

// Cloudflare Workers
export default {
  fetch: handleRequest,
};

// Google Cloud Functions (descomentar si se usa GCF)
// const functions = require('@google-cloud/functions-framework');
// functions.http('whatsappToGithub', async (req, res) => {
//   const request = new Request(req.url, {
//     method: req.method,
//     headers: req.headers,
//     body: JSON.stringify(req.body),
//   });
//   const response = await handleRequest(request);
//   const body = await response.json();
//   res.status(response.status).json(body);
// });
