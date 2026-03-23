import asyncio
import json
import os
from dotenv import load_dotenv
from mcp.client.stdio import stdio_client, StdioServerParameters
from mcp.client.session import ClientSession

ENV_FILE = "/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp"
load_dotenv(ENV_FILE)

async def main():
    params = StdioServerParameters(
        command="docker",
        args=["run", "-i", "--rm", "--env-file", ENV_FILE, "ghl-mcp-server", "node", "dist/server.js"]
    )
    
    async with stdio_client(params) as (read, write):
        async with ClientSession(read, write) as session:
            await session.initialize()
            
            location_id = os.environ.get("GHL_LOCATION_ID")
            print(f"--- Fetching workflows for location: {location_id} ---")
            
            try:
                res = await session.call_tool('get_location_workflows', {
                    'locationId': location_id
                })
                # Check the first few workflows for author info
                data = json.loads(res.content[0].text) if res.content else {}
                workflows = data.get('workflows', [])
                if workflows:
                    print(f"Found {len(workflows)} workflows. Checking first 5 for user IDs...")
                    for wf in workflows[:5]:
                        print(f"Workflow: {wf.get('name')}, ID: {wf.get('id')}")
                        # If there's an author/userId field, print it
                        # We don't know the field name, so print the whole object
                        # print(json.dumps(wf, indent=2)) 
                else:
                    print("No workflows found.")
                
                # Try search_workflows if it exists
                # No, search_workflows is not in the list.
            except Exception as e:
                print("Failed to get workflows:", e)

if __name__ == "__main__":
    asyncio.run(main())
