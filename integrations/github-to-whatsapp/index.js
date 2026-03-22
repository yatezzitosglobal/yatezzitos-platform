/**
 * Webhook: GitHub → GHL → WhatsApp (notificación de tarea completada)
 * 
 * Cloudflare Worker que recibe eventos de GitHub y reenvía
 * notificaciones a GHL para que se envíen por WhatsApp.
 * 
 * Eventos soportados:
 * - Issue cerrado (con label "from-whatsapp")
 * - PR abierto que referencia un issue de WhatsApp
 * 
 * Variables de entorno requeridas:
 * - GITHUB_WEBHOOK_SECRET: Secret del webhook de GitHub
 * - GHL_INBOUND_WEBHOOK_URL: URL del Inbound Webhook de GHL
 */

// ============================================================
// HANDLER PRINCIPAL
// ============================================================

export default {
  async fetch(request, env) {
    if (request.method !== 'POST') {
      return json(405, { error: 'Method not allowed' });
    }

    try {
      const event = request.headers.get('X-GitHub-Event');
      const body = await request.json();

      // --- Validar que es un evento relevante ---
      const notification = parseGitHubEvent(event, body);
      
      if (!notification) {
        return json(200, { status: 'ignored', reason: 'Event not relevant' });
      }

      // --- Enviar a GHL Inbound Webhook ---
      const ghlUrl = env.GHL_INBOUND_WEBHOOK_URL;
      if (!ghlUrl) {
        return json(500, { error: 'GHL_INBOUND_WEBHOOK_URL not configured' });
      }

      const ghlResponse = await fetch(ghlUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(notification),
      });

      if (!ghlResponse.ok) {
        const errText = await ghlResponse.text();
        console.error(`GHL error ${ghlResponse.status}: ${errText}`);
        return json(502, { error: 'Failed to notify GHL' });
      }

      console.log(`✅ Notificación enviada: ${notification.type} — ${notification.title}`);
      return json(200, { status: 'notified', type: notification.type, title: notification.title });

    } catch (error) {
      console.error('❌ Error:', error.message);
      return json(500, { error: error.message });
    }
  }
};

// ============================================================
// PARSER DE EVENTOS DE GITHUB
// ============================================================

function parseGitHubEvent(event, body) {
  // --- Issue cerrado ---
  if (event === 'issues' && body.action === 'closed') {
    const issue = body.issue;
    const labels = (issue.labels || []).map(l => l.name);
    
    // Solo issues creados desde WhatsApp
    if (!labels.includes('from-whatsapp')) return null;

    return {
      type: 'issue_closed',
      title: issue.title,
      number: issue.number,
      url: issue.html_url,
      closed_by: body.sender?.login || 'Sistema',
      message: `✅ *Tarea completada*\n\n📋 #${issue.number} — ${issue.title}\n🔗 ${issue.html_url}\n👤 Cerrada por: ${body.sender?.login || 'Sistema'}\n\n_Tu solicitud desde WhatsApp ha sido procesada._`,
    };
  }

  // --- PR abierto que menciona un issue de WhatsApp ---
  if (event === 'pull_request' && (body.action === 'opened' || body.action === 'ready_for_review')) {
    const pr = body.pull_request;
    const prBody = pr.body || '';
    
    // Buscar referencias a issues (#XX) en el cuerpo del PR
    const issueRefs = prBody.match(/#(\d+)/g);
    if (!issueRefs) return null;

    // Verificar si menciona labels de WhatsApp (simple check)
    const hasWhatsAppRef = prBody.toLowerCase().includes('whatsapp') || 
                           prBody.toLowerCase().includes('from-whatsapp');
    
    // Siempre notificar PRs que referencian issues (pueden ser de WhatsApp)
    return {
      type: 'pr_opened',
      title: pr.title,
      number: pr.number,
      url: pr.html_url,
      author: pr.user?.login || 'Copilot',
      referenced_issues: issueRefs.map(r => r.slice(1)),
      message: `🔄 *Pull Request creado*\n\n📝 #${pr.number} — ${pr.title}\n🔗 ${pr.html_url}\n👤 Autor: ${pr.user?.login || 'Copilot'}\n📋 Issues relacionados: ${issueRefs.join(', ')}\n\n_Se han preparado cambios para tu solicitud. Pendiente de revisión._`,
    };
  }

  // --- Issue con comentario (para actualizaciones de progreso) ---
  if (event === 'issue_comment' && body.action === 'created') {
    const issue = body.issue;
    const labels = (issue.labels || []).map(l => l.name);
    
    if (!labels.includes('from-whatsapp')) return null;

    // Solo notificar comentarios de bots (Copilot)
    const commenter = body.comment?.user?.login || '';
    if (!commenter.includes('bot') && !commenter.includes('copilot') && commenter !== 'github-actions') {
      return null;
    }

    return {
      type: 'progress_update',
      title: issue.title,
      number: issue.number,
      url: issue.html_url,
      comment_by: commenter,
      comment_preview: (body.comment?.body || '').slice(0, 200),
      message: `📊 *Actualización de tarea*\n\n📋 #${issue.number} — ${issue.title}\n💬 ${commenter}: ${(body.comment?.body || '').slice(0, 150)}...\n🔗 ${issue.html_url}`,
    };
  }

  return null;
}

// ============================================================
// UTILIDAD
// ============================================================

function json(status, body) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { 'Content-Type': 'application/json' },
  });
}
