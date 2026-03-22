/**
 * Gemini AI Agent — GitHub Issues → Código automático
 *
 * Flujo: WhatsApp → GHL → Webhook → GitHub Issue (ai-task) → este script → PR
 *
 * El agente:
 * 1. Lee el issue y carga contexto relevante del proyecto
 * 2. Llama a la API de Gemini con todo el contexto
 * 3. Parsea la respuesta estructurada
 * 4. Crea una rama, escribe los archivos y abre un PR
 * 5. Comenta en el issue con el resultado
 *
 * Secrets requeridos:
 * - GEMINI_API_KEY: clave de Google AI Studio (aistudio.google.com)
 * - COPILOT_PAT: GitHub Personal Access Token con permisos de repo
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

// ============================================================
// CONFIG DESDE ENVIRONMENT
// ============================================================

const GEMINI_API_KEY = process.env.GEMINI_API_KEY;
const ISSUE_NUMBER   = process.env.ISSUE_NUMBER;
const ISSUE_TITLE    = process.env.ISSUE_TITLE || '';
const ISSUE_BODY     = process.env.ISSUE_BODY  || '';
const ISSUE_LABELS   = JSON.parse(process.env.ISSUE_LABELS || '[]');
const REPO           = process.env.REPO;
const GEMINI_MODEL   = 'gemini-1.5-flash';

// ============================================================
// HELPERS
// ============================================================

function readFileSafe(filePath) {
  try {
    return fs.readFileSync(filePath, 'utf-8');
  } catch {
    return '';
  }
}

function run(cmd, opts = {}) {
  try {
    return execSync(cmd, { encoding: 'utf-8', stdio: 'pipe', ...opts }).trim();
  } catch (e) {
    throw new Error(e.stderr || e.message);
  }
}

/** Escribe contenido en un archivo temporal y retorna la ruta — evita problemas de escaping en shell */
function writeTempFile(content) {
  const tmpFile = path.join(os.tmpdir(), `gemini-agent-${Date.now()}.txt`);
  fs.writeFileSync(tmpFile, content, 'utf-8');
  return tmpFile;
}

function commentOnIssue(message) {
  const tmpFile = writeTempFile(message);
  try {
    run(`gh issue comment ${ISSUE_NUMBER} --repo ${REPO} --body-file "${tmpFile}"`);
  } finally {
    fs.unlinkSync(tmpFile);
  }
}

// ============================================================
// CARGA DE CONTEXTO
// ============================================================

function loadContext() {
  const titleAndBody = (ISSUE_TITLE + ' ' + ISSUE_BODY).toLowerCase();
  const labelStr     = ISSUE_LABELS.join(' ').toLowerCase();
  let context        = '';

  // Contexto base — siempre se carga
  const coreInstructions = readFileSafe('.github/copilot-instructions.md');
  if (coreInstructions) {
    context += `\n\n## INSTRUCCIONES DEL PROYECTO\n${coreInstructions.slice(0, 8000)}`;
  }

  // SEO / blog / contenido
  const isSeo = labelStr.includes('seo') ||
    ['seo', 'blog', 'meta', 'keyword', 'post', 'contenido', 'descripcion', 'cancun',
     'cabos', 'vallarta', 'mazatlan', 'huatulco', 'ixtapa', 'acapulco', 'nayarit',
     'playa', 'lapaz'].some(w => titleAndBody.includes(w));

  if (isSeo) {
    const seoWorkflow = readFileSafe('.agents/workflows/seo-blog-posts.md');
    if (seoWorkflow) {
      context += `\n\n## REGLAS SEO (OBLIGATORIAS)\n${seoWorkflow.slice(0, 5000)}`;
    }
  }

  // CRM / GoHighLevel
  if (['crm', 'ghl', 'gohighlevel', 'pipeline', 'lead', 'automatiz'].some(w => titleAndBody.includes(w))) {
    const crmSkills = readFileSafe('docs/ai/ghl_crm_skills.md');
    if (crmSkills) context += `\n\n## CONTEXTO CRM\n${crmSkills.slice(0, 3000)}`;
  }

  // Frontend / CSS / diseño
  if (['css', 'diseño', 'figma', 'estilo', 'color', 'fuente', 'responsive'].some(w => titleAndBody.includes(w))) {
    const figmaSkills = readFileSafe('docs/ai/figma_frontend_skills.md');
    if (figmaSkills) context += `\n\n## CONTEXTO FRONTEND\n${figmaSkills.slice(0, 3000)}`;
  }

  // Yates / embarcaciones
  if (['yate', 'embarcacion', 'barco', 'ficha', 'flota', 'houzez'].some(w => titleAndBody.includes(w))) {
    const fleetSkills = readFileSafe('docs/ai/houzez_fleet_skills.md');
    if (fleetSkills) context += `\n\n## CONTEXTO FLOTA\n${fleetSkills.slice(0, 3000)}`;
  }

  return context;
}

// ============================================================
// GEMINI API
// ============================================================

async function callGemini(prompt) {
  const url = `https://generativelanguage.googleapis.com/v1/models/${GEMINI_MODEL}:generateContent?key=${GEMINI_API_KEY}`;

  const response = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      contents: [{ parts: [{ text: prompt }] }],
      generationConfig: {
        temperature: 0.3,
        maxOutputTokens: 8192,
      },
    }),
  });

  if (!response.ok) {
    const errorText = await response.text();
    throw new Error(`Gemini API ${response.status}: ${errorText}`);
  }

  const data = await response.json();
  return data.candidates[0].content.parts[0].text;
}

function parseGeminiJSON(text) {
  // Eliminar bloques de código markdown si Gemini los incluyó
  const cleaned = text
    .replace(/^```json\s*/m, '')
    .replace(/^```\s*/m, '')
    .replace(/```\s*$/m, '')
    .trim();
  return JSON.parse(cleaned);
}

// ============================================================
// MAIN
// ============================================================

async function main() {
  console.log(`🤖 Gemini Agent iniciando — Issue #${ISSUE_NUMBER}: ${ISSUE_TITLE}`);

  if (!GEMINI_API_KEY) {
    commentOnIssue('❌ **Gemini Agent — Error de configuración**\n\nNo se encontró el secret `GEMINI_API_KEY`. Por favor configúralo en Settings → Secrets → Actions.');
    process.exit(1);
  }

  // Comentario inicial
  commentOnIssue(`🤖 **Gemini Agent activado**\n\nAnalizando la tarea y generando cambios... esto tomará unos segundos.\n\n> _Workflow: WhatsApp → GHL → GitHub Issue → Gemini Agent_`);

  // Cargar contexto del proyecto
  const context = loadContext();

  // Construir prompt
  const prompt = `Eres un agente de desarrollo autónomo trabajando en Yatezzitos Global — plataforma de turismo náutico de lujo en México (yatezzitos.com). Tienes 8 años de historial operativo y trabajas en 10+ destinos.
${context}

---

## TAREA A COMPLETAR (Issue #${ISSUE_NUMBER})

**Título:** ${ISSUE_TITLE}

**Descripción:**
${ISSUE_BODY || 'Sin descripción adicional.'}

**Labels:** ${ISSUE_LABELS.join(', ')}

---

## INSTRUCCIONES DE RESPUESTA

Analiza la tarea y genera los archivos necesarios para completarla dentro del repositorio.

Reglas estrictas:
- Trabaja SOLO con archivos del repositorio (docs, seo, configs, código, scripts)
- NUNCA inventes precios, disponibilidad o capacidad de embarcaciones
- Para contenido SEO: HTML puro, keywords naturales, reglas Yoast, enlaces internos y externos
- Si la tarea es ambigua, haz lo más razonable y documéntalo en el PR
- El branch_name debe seguir: fix/, feat/, docs/, seo/, ai/ seguido de descripción en kebab-case

Responde ÚNICAMENTE con JSON válido en este formato (sin texto adicional, sin markdown):

{
  "analysis": "qué hace esta tarea y cómo la resolví",
  "branch_name": "seo/descripcion-corta",
  "pr_title": "título descriptivo del Pull Request",
  "pr_body": "descripción de cambios realizados, archivos modificados y por qué",
  "issue_comment": "mensaje de confirmación para comentar en el issue",
  "changes": [
    {
      "file_path": "ruta/relativa/al/archivo.md",
      "action": "create",
      "content": "contenido completo del archivo"
    }
  ]
}`;

  // Llamar a Gemini
  let parsed;
  try {
    console.log('📡 Llamando a Gemini API...');
    const rawResponse = await callGemini(prompt);
    console.log('✅ Respuesta recibida');
    console.log('Preview:', rawResponse.slice(0, 300));

    parsed = parseGeminiJSON(rawResponse);
    console.log(`📋 Análisis: ${parsed.analysis}`);
    console.log(`🌿 Rama: ${parsed.branch_name}`);
    console.log(`📁 Cambios: ${parsed.changes?.length ?? 0} archivos`);
  } catch (error) {
    console.error('❌ Error Gemini:', error.message);
    commentOnIssue(`❌ **Gemini Agent — Error**\n\nNo se pudo procesar la tarea:\n\`\`\`\n${error.message}\n\`\`\`\n\nRevisa los logs del workflow en Actions o asigna manualmente.`);
    process.exit(1);
  }

  // Validar que hay cambios
  if (!parsed.changes || parsed.changes.length === 0) {
    commentOnIssue(`⚠️ **Gemini Agent** — La tarea fue analizada pero no generó cambios de archivos.\n\n**Análisis:** ${parsed.analysis}\n\nPuede requerir revisión manual o la tarea está fuera del alcance del agente.`);
    process.exit(0);
  }

  // Configurar git
  run('git config user.name "gemini-agent[bot]"');
  run('git config user.email "gemini-agent[bot]@users.noreply.github.com"');

  // Crear rama
  const branchName = `${parsed.branch_name}-issue-${ISSUE_NUMBER}`;
  try {
    run(`git checkout -b "${branchName}"`);
    console.log(`🌿 Rama creada: ${branchName}`);
  } catch (error) {
    commentOnIssue(`❌ **Error al crear rama** \`${branchName}\`:\n\`${error.message}\``);
    process.exit(1);
  }

  // Aplicar cambios de archivos
  let filesChanged = 0;
  const filesModified = [];

  for (const change of parsed.changes) {
    try {
      const dir = path.dirname(change.file_path);
      if (dir && dir !== '.') {
        fs.mkdirSync(dir, { recursive: true });
      }
      fs.writeFileSync(change.file_path, change.content, 'utf-8');
      run(`git add "${change.file_path}"`);
      filesChanged++;
      filesModified.push(change.file_path);
      console.log(`✅ ${change.action === 'create' ? 'Creado' : 'Actualizado'}: ${change.file_path}`);
    } catch (error) {
      console.error(`⚠️ Error con ${change.file_path}:`, error.message);
    }
  }

  if (filesChanged === 0) {
    commentOnIssue(`⚠️ **Gemini Agent** — No se pudieron escribir los archivos. Revisa los logs del workflow.`);
    process.exit(1);
  }

  // Commit
  const commitMsg = `${parsed.branch_name.split('/')[0]}: ${parsed.pr_title}\n\nResolves #${ISSUE_NUMBER}\n\nGenerated by Gemini Agent (${GEMINI_MODEL})`;
  const commitTmpFile = writeTempFile(commitMsg);
  try {
    run(`git commit -F "${commitTmpFile}"`);
    console.log('📝 Commit creado');
  } finally {
    fs.unlinkSync(commitTmpFile);
  }

  // Push
  run(`git push origin "${branchName}"`);
  console.log('🚀 Push completado');

  // Crear PR
  const prBody = `${parsed.pr_body}

---

**Archivos modificados:**
${filesModified.map(f => `- \`${f}\``).join('\n')}

---

🤖 **Generado automáticamente por Gemini Agent** (\`${GEMINI_MODEL}\`)
Issue: #${ISSUE_NUMBER}
Workflow: WhatsApp → GHL → GitHub Issue → Gemini Agent → PR`;

  const prBodyFile = writeTempFile(prBody);
  let prUrl;
  try {
    prUrl = run(`gh pr create \
      --title "${parsed.pr_title.replace(/"/g, "'")}" \
      --body-file "${prBodyFile}" \
      --base main \
      --head "${branchName}" \
      --repo ${REPO}`);
    console.log(`🎉 PR creado: ${prUrl}`);
  } finally {
    fs.unlinkSync(prBodyFile);
  }

  // Comentario final en el issue
  const finalComment = `✅ **Gemini Agent — Tarea completada**

${parsed.issue_comment}

**Pull Request:** ${prUrl}
**Archivos generados:** ${filesChanged}
**Rama:** \`${branchName}\`

> Revisa el PR y haz merge si todo está correcto. El agente trabajó basándose en las instrucciones del proyecto en \`.github/copilot-instructions.md\`.`;

  commentOnIssue(finalComment);
  console.log('✅ Gemini Agent finalizado exitosamente');
}

main().catch(error => {
  console.error('💥 Error fatal:', error.message);
  process.exit(1);
});
