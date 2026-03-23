import asyncio
import json
import os
import requests
import argparse # Added for command-line arguments
from dotenv import load_dotenv
from mcp.client.stdio import stdio_client, StdioServerParameters
from mcp.client.session import ClientSession

# Directorios de la arquitectura IaC
DATA_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
EMAILS_DIR = os.path.join(DATA_DIR, 'emails')
PIPELINES_DIR = os.path.join(DATA_DIR, 'pipelines')
TAGS_DIR = os.path.join(DATA_DIR, 'tags')
CUSTOM_VALUES_DIR = os.path.join(DATA_DIR, 'custom_values')
CUSTOM_FIELDS_DIR = os.path.join(DATA_DIR, 'custom_fields')
WORKFLOWS_DIR = os.path.join(DATA_DIR, 'workflows') # Added for workflows
SNIPPETS_DIR = os.path.join(DATA_DIR, 'snippets')
SOCIAL_DIR = os.path.join(DATA_DIR, 'social')

# Archivos de salida específicos
CUSTOM_VALUES_FILE = os.path.join(CUSTOM_VALUES_DIR, 'custom_values.json')
CUSTOM_FIELDS_FILE = os.path.join(CUSTOM_FIELDS_DIR, 'custom_fields.json')
WORKFLOWS_FILE = os.path.join(WORKFLOWS_DIR, 'workflows.json')
SNIPPETS_FILE = os.path.join(SNIPPETS_DIR, 'snippets.json')
SOCIAL_ACCOUNTS_FILE = os.path.join(SOCIAL_DIR, 'accounts.json')
SOCIAL_POSTS_FILE = os.path.join(SOCIAL_DIR, 'posts.json')

def setup_directories():
    """Ensure data directories exist"""
    dirs = [
        EMAILS_DIR,
        PIPELINES_DIR,
        TAGS_DIR,
        CUSTOM_VALUES_DIR,
        CUSTOM_FIELDS_DIR,
        WORKFLOWS_DIR, # Added workflows directory
        SNIPPETS_DIR,
        SOCIAL_DIR
    ]
    for d in dirs:
        os.makedirs(d, exist_ok=True)

# Asegurar directorios
setup_directories() # Call the new setup function

# Parámetros del servidor MCP
ENV_FILE = "/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp"

load_dotenv(ENV_FILE)
location_id = os.environ.get("GHL_LOCATION_ID")

SERVER_PARAMS = StdioServerParameters(
    command="docker",
    args=["run", "-i", "--rm", "--env-file", ENV_FILE, "ghl-mcp-server", "node", "dist/server.js"]
)

async def pull_tags(session: ClientSession):
    print("[ghl_pull] Descargando Etiquetas (Tags)...")
    try:
        # Para tags de contactos, GoHighLevel pide locationId implícito pero para este route requería explícito
        result = await session.call_tool("get_location_tags", {"locationId": location_id})
        if not result.isError and result.content:
            text = result.content[0].text
            data = json.loads(text)
            out_file = os.path.join(TAGS_DIR, "tags.json")
            with open(out_file, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print(f"[ghl_pull] ✅ Etiquetas guardadas en {out_file}")
        else:
            print(f"[ghl_pull] ❌ Error descargando etiquetas")
    except Exception as e:
        print(f"[ghl_pull] ⚠️ No se pudieron obtener las etiquetas: {e}")

async def pull_pipelines(session: ClientSession):
    print("[ghl_pull] Descargando Pipelines de Oportunidades...")
    try:
        result = await session.call_tool("get_pipelines", {})
        if not result.isError and result.content:
            text = result.content[0].text
            data = json.loads(text)
            os.makedirs(PIPELINES_DIR, exist_ok=True)
            pipelines = data.get("pipelines", [])
            for pipeline in pipelines:
                # Reemplazar caracteres no válidos para nombres de archivo
                safe_name = "".join([c if c.isalnum() or c in " _-" else "_" for c in pipeline.get("name", "pipeline")])
                pipeline_file = os.path.join(PIPELINES_DIR, f"{safe_name}.json")
                with open(pipeline_file, "w") as f:
                    json.dump(pipeline, f, indent=2)
            print(f"[ghl_pull] ✅ {len(pipelines)} pipelines guardados individualmente en {PIPELINES_DIR}")
        else:
            print("[ghl_pull] ❌ Error al obtener pipelines:", result)
    except Exception as e:
        print("[ghl_pull] ⚠️ Excepción al descargar pipelines:", str(e))

async def crawl_and_download_templates(session: ClientSession, parent_id=None, path=""):
    args = {}
    if parent_id:
        args["parentId"] = parent_id
        
    result = await session.call_tool("get_email_templates", args)
    if result.isError or not result.content:
        return 0
        
    data = json.loads(result.content[0].text)
    builders = data.get("templates", {}).get("builders", [])
    
    count = 0
    for item in builders:
        safe_name = "".join([c if c.isalnum() or c in " _-" else "_" for c in item.get("name", "template")])
        item_path = os.path.join(path, safe_name) if path else safe_name
        
        if item.get("templateType") == "folder":
            print(f"[ghl_pull] Entrando a carpeta: {item_path}")
            count += await crawl_and_download_templates(session, item["id"], item_path)
        else:
            template_dir = os.path.join(EMAILS_DIR, item_path)
            os.makedirs(template_dir, exist_ok=True)
            
            # Guardamos el HTML
            preview_url = item.get("previewUrl")
            html_content = ""
            if preview_url:
                try:
                    response = requests.get(preview_url, timeout=10)
                    if response.status_code == 200:
                        response.encoding = 'utf-8'
                        html_content = response.text
                    else:
                        print(f"[ghl_pull] ⚠️ No se pudo descargar HTML para {item_path}: Status {response.status_code}")
                except Exception as e:
                    print(f"[ghl_pull] ⚠️ Error descargando HTML para {item_path}: {e}")
            else:
                 print(f"[ghl_pull] ⚠️ Plantilla {item_path} no tiene previewUrl.")
                 
            with open(os.path.join(template_dir, "index.html"), "w", encoding="utf-8") as f:
                f.write(html_content)
                
            # Guardamos metadatos omitiendo el HTML para no duplicar info
            meta = {k: v for k, v in item.items() if k != "html" and k != "templateData"}
            with open(os.path.join(template_dir, "meta.json"), "w", encoding="utf-8") as f:
                json.dump(meta, f, indent=2, ensure_ascii=False)
            
            print(f"[ghl_pull] ✅ Plantilla guardada: {item_path}")
            count += 1
            
    return count

async def pull_custom_values(session: ClientSession):
    print("[ghl_pull] Descargando Valores Personalizados (Custom Values)...")
    try:
        result = await session.call_tool("get_location_custom_values", {"locationId": location_id})
        if not result.isError and result.content:
            data = json.loads(result.content[0].text)
            with open(CUSTOM_VALUES_FILE, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print(f"[ghl_pull] ✅ Valores Personalizados guardados en {CUSTOM_VALUES_FILE}")
        else:
            print(f"[ghl_pull] ❌ Error descargando custom values: {result.content}")
    except Exception as e:
        print(f"[ghl_pull] ⚠️ No se pudieron obtener los custom values: {e}")

async def pull_custom_fields(session: ClientSession):
    print("[ghl_pull] Descargando Campos Personalizados (Custom Fields)...")
    try:
        result = await session.call_tool("get_location_custom_fields", {"locationId": location_id})
        if not result.isError and result.content:
            # Save data
            with open(CUSTOM_FIELDS_FILE, 'w', encoding='utf-8') as f:
                f.write(result.content[0].text)
                
            print(f"[ghl_pull] ✅ Se descargaron y actualizaron los Custom Fields en: {CUSTOM_FIELDS_FILE}")
        else:
            print(f"[ghl_pull] ❌ Error descargando custom fields: {result.content}")
    except Exception as e:
        print(f"[ghl_pull] ❌ Error extrayendo custom fields: {e}")

async def pull_workflows(session: ClientSession):
    print("\n[ghl_pull] Obteniendo la lista de Workflows...")
    
    try:
        location_id = os.getenv("GHL_LOCATION_ID")
        if not location_id:
            print("[ghl_pull] ❌ Error: Falta GHL_LOCATION_ID en el archivo .env")
            return
            
        print(f"[ghl_pull] Realizando petición al servidor MCP usando location_id: {location_id}")
        # Llama a la herramienta MCP para listar los workflows
        result = await session.call_tool("ghl_get_workflows", {
            "locationId": location_id
        })
        
        if result.isError:
            print(f"[ghl_pull] ❌ Error del servidor MCP: {result.content}")
            return
            
        # Parse el resultado y formatea el JSON
        parsed_data = json.loads(result.content[0].text)
        
        # Guarda la metadata de workflows
        with open(WORKFLOWS_FILE, 'w', encoding='utf-8') as f:
            json.dump(parsed_data, f, indent=2, ensure_ascii=False)
            
        num_workflows = len(parsed_data.get("workflows", []))
        print(f"[ghl_pull] ✅ Se descargaron y versionaron {num_workflows} Workflows en: {WORKFLOWS_FILE}")
        
    except Exception as e:
        print(f"[ghl_pull] ❌ Error extrayendo workflows: {e}")

async def pull_snippets(session: ClientSession):
    print("\n[ghl_pull] Descargando Plantillas SMS/WhatsApp (Snippets)...")
    try:
        result = await session.call_tool("get_location_templates", {"locationId": location_id})
        if not result.isError and result.content:
            data = json.loads(result.content[0].text)
            with open(SNIPPETS_FILE, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            num_snippets = len(data.get("templates", []))
            print(f"[ghl_pull] ✅ Se descargaron y versionaron {num_snippets} Snippets en: {SNIPPETS_FILE}")
        else:
            print(f"[ghl_pull] ❌ Error descargando snippets: {result.content}")
    except Exception as e:
        print(f"[ghl_pull] ❌ Error extrayendo snippets: {e}")

async def pull_social(session: ClientSession):
    print("\n[ghl_pull] Descargando Planificador de Redes Sociales...")
    try:
        # 1. Cuentas de Redes Sociales
        acc_result = await session.call_tool("get_social_accounts", {"locationId": location_id})
        if not acc_result.isError and acc_result.content:
            data = json.loads(acc_result.content[0].text)
            with open(SOCIAL_ACCOUNTS_FILE, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print(f"[ghl_pull] ✅ Cuentas de Redes Sociales guardadas: {SOCIAL_ACCOUNTS_FILE}")
        else:
            print(f"[ghl_pull] ❌ Error cuentas: {acc_result.content}")
            
        # 2. Posts Agendados/Publicados
        post_result = await session.call_tool("search_social_posts", {"locationId": location_id})
        if not post_result.isError and post_result.content:
            data = json.loads(post_result.content[0].text)
            with open(SOCIAL_POSTS_FILE, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print(f"[ghl_pull] ✅ Planners/Posts guardados: {SOCIAL_POSTS_FILE}")
        else:
            print(f"[ghl_pull] ❌ Error posts: {post_result.content}")
            
    except Exception as e:
        print(f"[ghl_pull] ❌ Error extrayendo social media: {e}")

async def pull_emails(session: ClientSession):
    print("[ghl_pull] Descargando TODAS las Plantillas de Email del CRM...")
    try:
        os.makedirs(EMAILS_DIR, exist_ok=True)
        total_synced = await crawl_and_download_templates(session)
        print(f"[ghl_pull] ✅ Se sincronizaron {total_synced} plantillas de correo en total.")
    except Exception as e:
        print("[ghl_pull] ⚠️ Excepción al descargar emails:", str(e))

async def main():
    print("🚀 Iniciando Arquitectura GHL Infra-as-Code [PULL]...")

    parser = argparse.ArgumentParser(description='Sincroniza recursos de GoHighLevel con el sistema de archivos local.')
    parser.add_argument('--emails', action='store_true', help='Extraer plantillas de email')
    parser.add_argument('--tags', action='store_true', help='Extraer etiquetas de contactos')
    parser.add_argument('--pipelines', action='store_true', help='Extraer pipelines y sus etapas')
    parser.add_argument('--custom-values', action='store_true', help='Extraer Valores Personalizados')
    parser.add_argument('--custom-fields', action='store_true', help='Extraer Campos Personalizados')
    parser.add_argument('--workflows', action='store_true', help='Extraer lista de Workflows activos e inactivos')
    parser.add_argument('--snippets', action='store_true', help='Extraer plantillas de SMS/WhatsApp')
    parser.add_argument('--social', action='store_true', help='Extraer el Planificador de Redes Sociales')
    parser.add_argument('--all', action='store_true', help='Ejecutar todas las acciones integradas')
    
    args = parser.parse_args()
    
    if not any([args.emails, args.tags, args.pipelines, args.custom_values, args.custom_fields, args.workflows, args.snippets, args.social, args.all]):
        parser.print_help()
        return
        
    # Set all to true if --all used
    if args.all:
        args.emails = True
        args.tags = True
        args.pipelines = True
        args.custom_values = True
        args.custom_fields = True
        args.workflows = True
        args.snippets = True
        args.social = True

    async with stdio_client(SERVER_PARAMS) as (read, write):
        async with ClientSession(read, write) as session:
            await session.initialize()
            
            print("🔗 Conectado con GoHighLevel MCP de forma segura.")
            
            if args.emails:
                await pull_emails(session)
            if args.tags:
                await pull_tags(session)
                
            if args.pipelines:
                await pull_pipelines(session)
                
            if args.custom_values:
                await pull_custom_values(session)

            if args.custom_fields:
                await pull_custom_fields(session)
                
            if args.workflows:
                await pull_workflows(session)
                
            if args.snippets:
                await pull_snippets(session)
                
            if args.social:
                await pull_social(session)
                
            print("\n🎉 Operación finalizada de forma segura. Revisa la carpeta ghl-data/")

if __name__ == "__main__":
    asyncio.run(main())
