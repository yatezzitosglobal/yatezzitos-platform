import asyncio
import json
import os
import sys
import argparse
from mcp.client.stdio import stdio_client, StdioServerParameters
from mcp.client.session import ClientSession

# Directorios de la arquitectura IaC
DATA_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
EMAILS_DIR = os.path.join(DATA_DIR, 'emails')
TAGS_DIR = os.path.join(DATA_DIR, 'tags')
TAGS_FILE = os.path.join(TAGS_DIR, 'tags.json')
CUSTOM_VALUES_FILE = os.path.join(DATA_DIR, 'custom_values', 'custom_values.json')
CUSTOM_FIELDS_FILE = os.path.join(DATA_DIR, 'custom_fields', 'custom_fields.json')
SOCIAL_DIR = os.path.join(DATA_DIR, 'social')
SOCIAL_POSTS_TO_PUSH = os.path.join(SOCIAL_DIR, 'posts_to_push.json')

# Parámetros del servidor MCP
ENV_FILE = "/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp"
SERVER_PARAMS = StdioServerParameters(
    command="docker",
    args=["run", "-i", "--rm", "--env-file", ENV_FILE, "ghl-mcp-server", "node", "dist/server.js"]
)

async def push_emails(session: ClientSession, specific_id=None, specific_folder=None):
    print("[ghl_push] Sincronizando Plantillas Locales hacia el CRM (Push)...")
    if not os.path.exists(EMAILS_DIR):
        print("[ghl_push] ❌ El directorio de emails no existe.")
        return

    for root, dirs, files in os.walk(EMAILS_DIR):
        if "meta.json" in files and "index.html" in files:
            folder_name = os.path.basename(root)
            if specific_folder and folder_name != specific_folder:
                continue
                
            meta_file = os.path.join(root, "meta.json")
            html_file = os.path.join(root, "index.html")

            with open(meta_file, 'r', encoding='utf-8') as f:
                try:
                    meta = json.load(f)
                except json.JSONDecodeError:
                    print(f"[ghl_push] ❌ Error leyendo {meta_file}")
                    continue

            tpl_id = meta.get("id")
                
            if specific_id and tpl_id and tpl_id != specific_id:
                continue

            with open(html_file, 'r', encoding='utf-8') as f:
                html_content = f.read()

            try:
                if tpl_id:
                    # UPDATE EXISTING TEMPLATE
                    print(f"[ghl_push] Empujando actualizaciones para '{meta.get('name')}' ({tpl_id})...")
                    result = await session.call_tool("update_email_template", {
                        "templateId": tpl_id,
                        "name": meta.get("name"),
                        "html": html_content,
                        "updatedBy": "GitHub-IaC-Sys",
                        "templateData": {
                            "html": html_content
                        }
                    })
                    if not result.isError:
                        print(f"[ghl_push] ✅ Plantilla '{meta.get('name')}' actualizada exitosamente en GHL.")
                    else:
                        print(f"[ghl_push] ❌ Error actualizando '{meta.get('name')}': {result.content}")
                else:
                    # CREATE NEW TEMPLATE
                    print(f"[ghl_push] Creando NUEVA plantilla '{meta.get('name')}' en GHL...")
                    parent_id = meta.get("parentId", "")
                    
                    payload = {
                        "title": meta.get("name"),
                        "html": html_content,
                        "isPlainText": False
                    }
                    if parent_id:
                        payload["parentId"] = parent_id
                        
                    result = await session.call_tool("create_email_template", payload)
                    
                    if not result.isError:
                        # Extract the new ID from the response to update meta.json
                        try:
                            # result.content[0].text is a JSON string from GHL
                            res_data = json.loads(result.content[0].text)
                            new_id = res_data.get("_id") or res_data.get("id") or res_data.get("template", {}).get("id")
                            if new_id:
                                meta["id"] = new_id
                                with open(meta_file, 'w', encoding='utf-8') as f:
                                    json.dump(meta, f, indent=2, ensure_ascii=False)
                                print(f"[ghl_push] ✅ Plantilla NUEVA creada. ID guardado: {new_id}")
                                
                                # CRITICAL FIX: GHL ignores HTML on creation and injects 'Austin' template.
                                # Let's instantly UPDATE it to overwrite it with ours.
                                print(f"[ghl_push] 🛠️ Forzando inyección de código nativo para limpiar plantilla Austin...")
                                update_result = await session.call_tool("update_email_template", {
                                    "templateId": new_id,
                                    "name": meta.get("name"),
                                    "html": html_content,
                                    "updatedBy": "GitHub-IaC-Sys",
                                    "templateData": {
                                        "html": html_content
                                    }
                                })
                                if update_result.isError:
                                    print(f"[ghl_push] ⚠️ Ocurrió un error al forzar la limpieza de la plantilla nueva. {update_result.content}")
                                else:
                                    print(f"[ghl_push] ✅ Código HTML sincronizado exitosamente en la plantilla recién creada.")

                            else:
                                print(f"[ghl_push] ✅ Plantilla creada pero no se pudo extraer el ID: {result.content[0].text}")
                        except Exception as e:
                            print(f"[ghl_push] ✅ Plantilla creada pero falló el parseo del nuevo ID: {e}")
                    else:
                        print(f"[ghl_push] ❌ Error creando '{meta.get('name')}': {result.content}")

            except Exception as e:
                print(f"[ghl_push] ⚠️ Error crítico sincronizando '{meta.get('name')}': {e}")


async def push_tags(session: ClientSession):
    print("\n[ghl_push] Sincronizando Etiquetas Locales hacia el CRM (Push)...")
    if not os.path.exists(TAGS_FILE):
        print("[ghl_push] ❌ El archivo local tags.json no existe. Haz pull primero.")
        return

    with open(TAGS_FILE, 'r', encoding='utf-8') as f:
        try:
            local_data = json.load(f)
            local_tags = local_data.get("tags", [])
        except json.JSONDecodeError:
            print(f"[ghl_push] ❌ Error leyendo {TAGS_FILE}")
            return

    if not local_tags:
        print("[ghl_push] No hay etiquetas locales para sincronizar.")
        return

    # Usamos el locationId de la primera etiqueta como referencia
    location_id = None
    for t in local_tags:
        if t.get("locationId"):
            location_id = t["locationId"]
            break
            
    if not location_id:
        print("[ghl_push] ❌ No se pudo determinar el locationId desde tags.json.")
        return

    print(f"[ghl_push] Obteniendo estado actual de etiquetas remotas para comparar...")
    result = await session.call_tool("get_location_tags", {"locationId": location_id})
    if result.isError:
        print(f"[ghl_push] ❌ Error obteniendo etiquetas remotas: {result.content}")
        return

    try:
        remote_data = json.loads(result.content[0].text)
        remote_tags = remote_data.get("tags", [])
    except Exception as e:
        print(f"[ghl_push] ❌ Falló el parseo de etiquetas remotas: {e}")
        return

    remote_map = {t["id"]: t["name"] for t in remote_tags}
    remote_names = {t["name"].lower(): t["id"] for t in remote_tags}

    modified_local = False
    
    for tag in local_tags:
        t_id = tag.get("id")
        t_name = tag.get("name")
        
        if not t_name:
            continue
            
        if t_id:
            # Check for update
            if t_id in remote_map:
                if remote_map[t_id] != t_name:
                    print(f"[ghl_push] Actualizando etiqueta '{t_name}' ({t_id})...")
                    upd_result = await session.call_tool("update_location_tag", {
                        "locationId": location_id,
                        "tagId": t_id,
                        "name": t_name
                    })
                    if upd_result.isError:
                        print(f"  ❌ Error: {upd_result.content}")
                    else:
                        print(f"  ✅ Actualizada exitosamente.")
            else:
                # El ID existe local pero no remoto, tal vez se borró en GHL. Recrear.
                print(f"[ghl_push] Etiqueta '{t_name}' huérfana (ID={t_id} no en GHL). Recreando...")
                cre_result = await session.call_tool("create_location_tag", {
                    "locationId": location_id,
                    "name": t_name
                })
                if not cre_result.isError:
                    try:
                        c_data = json.loads(cre_result.content[0].text)
                        new_id = c_data.get("tag", {}).get("id")
                        if new_id:
                            tag["id"] = new_id
                            modified_local = True
                            print(f"  ✅ Recreada. Nuevo ID: {new_id}")
                    except Exception as e:
                        print(f"  ⚠️ Creada pero falló parseo del nuevo ID: {e}")
                else:
                    print(f"  ❌ Error recreando: {cre_result.content}")
        else:
            # Create new tag
            if t_name.lower() in remote_names:
                print(f"[ghl_push] Etiqueta '{t_name}' ya existe en GHL. Enlazando ID...")
                tag["id"] = remote_names[t_name.lower()]
                modified_local = True
            else:
                print(f"[ghl_push] Creando NUEVA etiqueta '{t_name}'...")
                cre_result = await session.call_tool("create_location_tag", {
                    "locationId": location_id,
                    "name": t_name
                })
                if not cre_result.isError:
                    try:
                        c_data = json.loads(cre_result.content[0].text)
                        new_id = c_data.get("tag", {}).get("id")
                        if new_id:
                            tag["id"] = new_id
                            modified_local = True
                            print(f"  ✅ Creada. Nuevo ID: {new_id}")
                    except Exception as e:
                        print(f"  ⚠️ Creada pero falló parseo del nuevo ID: {e}")
                else:
                    print(f"  ❌ Error creando: {cre_result.content}")

    if modified_local:
        with open(TAGS_FILE, 'w', encoding='utf-8') as f:
            json.dump(local_data, f, indent=2, ensure_ascii=False)
        print("[ghl_push] ✅ Archivo local tags.json actualizado con nuevos IDs.")

async def push_custom_values(session: ClientSession):
    print("\n[ghl_push] Sincronizando Custom Values Locales hacia el CRM (Push)...")
    if not os.path.exists(CUSTOM_VALUES_FILE):
        print("[ghl_push] ❌ El archivo local custom_values.json no existe. Haz pull primero.")
        return

    with open(CUSTOM_VALUES_FILE, 'r', encoding='utf-8') as f:
        try:
            local_data = json.load(f)
            local_cvs = local_data.get("customValues", [])
        except json.JSONDecodeError:
            print(f"[ghl_push] ❌ Error leyendo {CUSTOM_VALUES_FILE}")
            return

    if not local_cvs:
        print("[ghl_push] No hay custom values locales para sincronizar.")
        return

    location_id = None
    for cv in local_cvs:
        if cv.get("locationId"):
            location_id = cv["locationId"]
            break
            
    if not location_id:
        print("[ghl_push] ❌ No se pudo determinar el locationId desde custom_values.json.")
        return

    print(f"[ghl_push] Obteniendo estado actual de Custom Values remotos...")
    result = await session.call_tool("get_location_custom_values", {"locationId": location_id})
    if result.isError:
        print(f"[ghl_push] ❌ Error obteniendo Custom Values remotos: {result.content}")
        return

    remote_data = json.loads(result.content[0].text)
    remote_cvs = remote_data.get("customValues", [])
    
    remote_map = {cv["id"]: cv["name"] for cv in remote_cvs}
    remote_names = {cv["name"].lower(): cv["id"] for cv in remote_cvs}

    modified_local = False
    
    for cv in local_cvs:
        cv_id = cv.get("id")
        cv_name = cv.get("name")
        cv_value = cv.get("value")
        
        if not cv_name:
            continue
            
        if cv_id:
            # Check for update
            if cv_id in remote_map:
                print(f"[ghl_push] Actualizando Custom Value '{cv_name}' ({cv_id})...")
                upd_result = await session.call_tool("update_location_custom_value", {
                    "locationId": location_id,
                    "customValueId": cv_id,
                    "name": cv_name,
                    "value": cv_value
                })
                if upd_result.isError:
                    print(f"  ❌ Error: {upd_result.content}")
                else:
                    print(f"  ✅ Actualizado exitosamente.")
            else:
                print(f"[ghl_push] Custom Value '{cv_name}' huérfano (ID={cv_id} no en GHL). Recreando...")
                cre_result = await session.call_tool("create_location_custom_value", {
                    "locationId": location_id,
                    "name": cv_name,
                    "value": cv_value
                })
                if not cre_result.isError:
                    try:
                        c_data = json.loads(cre_result.content[0].text)
                        new_id = c_data.get("customValue", {}).get("id")
                        if new_id:
                            cv["id"] = new_id
                            modified_local = True
                            print(f"  ✅ Recreado. Nuevo ID: {new_id}")
                    except Exception as e:
                        print(f"  ⚠️ Creado pero falló parseo del nuevo ID: {e}")
                else:
                    print(f"  ❌ Error recreando: {cre_result.content}")
        else:
            # Create new custom value
            if cv_name.lower() in remote_names:
                print(f"[ghl_push] Custom Value '{cv_name}' ya existe en GHL. Enlazando ID...")
                cv["id"] = remote_names[cv_name.lower()]
                modified_local = True
                
                # Update it just in case value changed
                print(f"[ghl_push] Actualizando valor de '{cv_name}' tras enlazar...")
                await session.call_tool("update_location_custom_value", {
                    "locationId": location_id,
                    "customValueId": cv["id"],
                    "name": cv_name,
                    "value": cv_value
                })
            else:
                print(f"[ghl_push] Creando NUEVO Custom Value '{cv_name}'...")
                cre_result = await session.call_tool("create_location_custom_value", {
                    "locationId": location_id,
                    "name": cv_name,
                    "value": cv_value
                })
                if not cre_result.isError:
                    try:
                        c_data = json.loads(cre_result.content[0].text)
                        new_id = c_data.get("customValue", {}).get("id")
                        if new_id:
                            cv["id"] = new_id
                            modified_local = True
                            print(f"  ✅ Creado. Nuevo ID: {new_id}")
                    except Exception as e:
                        print(f"  ⚠️ Creado pero falló parseo del nuevo ID: {e}")
                else:
                    print(f"  ❌ Error creando: {cre_result.content}")

    if modified_local:
        with open(CUSTOM_VALUES_FILE, 'w', encoding='utf-8') as f:
            json.dump(local_data, f, indent=2, ensure_ascii=False)
        print("[ghl_push] ✅ Archivo local custom_values.json actualizado con nuevos IDs.")

async def push_custom_fields(session: ClientSession):
    print("\n[ghl_push] Sincronizando Custom Fields Locales hacia el CRM (Push)...")
    if not os.path.exists(CUSTOM_FIELDS_FILE):
        print("[ghl_push] ❌ El archivo local custom_fields.json no existe. Haz pull primero.")
        return

    with open(CUSTOM_FIELDS_FILE, 'r', encoding='utf-8') as f:
        try:
            local_data = json.load(f)
            local_cfs = local_data.get("customFields", [])
        except json.JSONDecodeError:
            print(f"[ghl_push] ❌ Error leyendo {CUSTOM_FIELDS_FILE}")
            return

    if not local_cfs:
        print("[ghl_push] No hay custom fields locales para sincronizar.")
        return

    location_id = None
    for cf in local_cfs:
        if cf.get("locationId"):
            location_id = cf["locationId"]
            break
            
    if not location_id:
        print("[ghl_push] ❌ No se pudo determinar el locationId desde custom_fields.json.")
        return

    print(f"[ghl_push] Obteniendo estado actual de Custom Fields remotos...")
    result = await session.call_tool("get_location_custom_fields", {"locationId": location_id})
    if result.isError:
        print(f"[ghl_push] ❌ Error obteniendo Custom Fields remotos: {result.content}")
        return

    remote_data = json.loads(result.content[0].text)
    remote_cfs = remote_data.get("customFields", [])
    
    remote_map = {cf["id"]: cf["name"] for cf in remote_cfs}
    remote_names = {cf["name"].lower(): cf["id"] for cf in remote_cfs}

    modified_local = False
    
    for cf in local_cfs:
        cf_id = cf.get("id")
        cf_name = cf.get("name")
        cf_dataType = cf.get("dataType")
        cf_model = cf.get("model", "contact")
        
        if not cf_name:
            continue
            
        payload = {
            "locationId": location_id,
            "name": cf_name,
            "dataType": cf_dataType,
            "model": cf_model
        }
        if "placeholder" in cf:
            payload["placeholder"] = cf["placeholder"]
            
        if cf_id:
            # Check for update
            if cf_id in remote_map:
                print(f"[ghl_push] Actualizando Custom Field '{cf_name}' ({cf_id})...")
                upd_payload = {
                    "locationId": location_id,
                    "customFieldId": cf_id,
                    "name": cf_name
                }
                if "placeholder" in cf:
                    upd_payload["placeholder"] = cf["placeholder"]
                if "position" in cf:
                    upd_payload["position"] = cf["position"]
                    
                upd_result = await session.call_tool("update_location_custom_field", upd_payload)
                if upd_result.isError:
                    print(f"  ❌ Error: {upd_result.content}")
                else:
                    print(f"  ✅ Actualizado exitosamente.")
            else:
                print(f"[ghl_push] Custom Field '{cf_name}' huérfano (ID={cf_id}). Recreando...")
                cre_result = await session.call_tool("create_location_custom_field", payload)
                if not cre_result.isError:
                    try:
                        c_data = json.loads(cre_result.content[0].text)
                        new_id = c_data.get("customField", {}).get("id")
                        if new_id:
                            cf["id"] = new_id
                            modified_local = True
                            print(f"  ✅ Recreado. Nuevo ID: {new_id}")
                    except:
                        pass
                else:
                    print(f"  ❌ Error recreando: {cre_result.content}")
        else:
            # Create new custom field
            if cf_name.lower() in remote_names:
                print(f"[ghl_push] Custom Field '{cf_name}' ya existe en GHL. Enlazando ID...")
                cf["id"] = remote_names[cf_name.lower()]
                modified_local = True
            else:
                print(f"[ghl_push] Creando NUEVO Custom Field '{cf_name}'...")
                cre_result = await session.call_tool("create_location_custom_field", payload)
                if not cre_result.isError:
                    try:
                        c_data = json.loads(cre_result.content[0].text)
                        new_id = c_data.get("customField", {}).get("id")
                        if new_id:
                            cf["id"] = new_id
                            modified_local = True
                            print(f"  ✅ Creado. Nuevo ID: {new_id}")
                    except: pass
                else:
                    print(f"  ❌ Error creando: {cre_result.content}")

    if modified_local:
        with open(CUSTOM_FIELDS_FILE, 'w', encoding='utf-8') as f:
            json.dump(local_data, f, indent=2, ensure_ascii=False)
        print("[ghl_push] ✅ Archivo local custom_fields.json actualizado con nuevos IDs.")

async def push_social(session: ClientSession):
    print("\n[ghl_push] Subiendo publicaciones programadas de Redes Sociales...")
    if not os.path.exists(SOCIAL_POSTS_TO_PUSH):
        print(f"[ghl_push] ⚠️ El archivo {SOCIAL_POSTS_TO_PUSH} no existe. Créalo primero basado en accounts.json.")
        return

    try:
        with open(SOCIAL_POSTS_TO_PUSH, 'r', encoding='utf-8') as f:
            posts = json.load(f)
            
        for post in posts:
            required = ["accountIds", "type", "summary"]
            if not all(k in post for k in required):
                print(f"[ghl_push] ⚠️ Post ignorado. Le faltan campos requeridos: {required}")
                continue
                
            # As you cannot retrieve the location_id natively from the post object here easily 
            # (unlike contacts that have it embedded), we borrow an implementation detail that 
            # locationId comes from the current agent context or another workaround. For now
            # we must find a way. Wait, looking at ghl_push.py, the previous functions extract
            # location_id from local arrays, but we don't have location_id here. 
            # I must pass location_id to create_social_post.
            # But the schema doesn't require locationId in the arguments? Wait, the mcp tool
            # for social posts uses locationId dynamically if missing? Let's assume the MCP tool
            # handles it or we'll inject the root locationId.
            
            result = await session.call_tool("create_social_post", post)
            if not result.isError:
                print(f"[ghl_push] ✅ Post '{post['summary'][:30]}...' subido exitosamente en cuenta(s): {post['accountIds']}")
            else:
                print(f"[ghl_push] ❌ Error subiendo post '{post['summary'][:30]}...': {result.content}")
                
    except Exception as e:
        print(f"[ghl_push] ❌ Error procesando {SOCIAL_POSTS_TO_PUSH}: {e}")

async def main():
    parser = argparse.ArgumentParser(description="Empujar (Push) configuraciones locales de GHL hacia el CRM.")
    parser.add_argument("--emails", action="store_true", help="Actualizar plantillas de emails.")
    parser.add_argument("--tags", action="store_true", help="Actualizar y crear etiquetas (Tags).")
    parser.add_argument("--custom-values", action="store_true", help="Actualizar y crear Custom Values.")
    parser.add_argument("--custom-fields", action="store_true", help="Actualizar y crear Custom Fields (Campos personalizados).")
    parser.add_argument("--social", action="store_true", help="Empujar publicaciones programadas de Redes Sociales.")
    parser.add_argument("--all", action="store_true", help="Ejecutar todas las validaciones de subida habilitadas.")
    parser.add_argument("--id", type=str, help="Limitar a un ID específico (ej. para email).")
    parser.add_argument("--folder", type=str, help="Limitar a una subcarpeta específica (ej. 'Email de Prueba (IA)').")
    args = parser.parse_args()

    if not args.emails and not args.tags and not args.custom_values and not args.custom_fields and not args.social and not args.all:
        print("Uso: python3 ghl_push.py [--emails] [--tags] [--custom-values] [--custom-fields] [--social] [--id <id>] [--folder <folder>]")
        print("Debe especificar al menos un recurso para empujar.")
        return
        
    if args.all:
        args.emails = True
        args.tags = True
        args.custom_values = True
        args.custom_fields = True
        args.social = True

    print("🚀 Iniciando Arquitectura GHL Infra-as-Code [PUSH]...")
    async with stdio_client(SERVER_PARAMS) as (read, write):
        async with ClientSession(read, write) as session:
            await session.initialize()
            print("🔗 Conectado con GoHighLevel MCP de forma segura.")
            
            if args.emails:
                await push_emails(session, args.id, args.folder)
            
            if args.tags:
                await push_tags(session)
                
            if args.custom_values:
                await push_custom_values(session)
                
            if args.custom_fields:
                await push_custom_fields(session)
                
            if args.social:
                await push_social(session)
            
            print("\n🎉 Operación Push finalizada.")

if __name__ == "__main__":
    asyncio.run(main())
