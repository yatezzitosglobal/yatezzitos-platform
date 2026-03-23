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
                # Based on search_code findings in earlier parts of the conversation (simulated)
                # or just guessing the correct tool name from the list.
                # Actually, let's list all tools first to be SURE.
                res_tools = await session.list_tools()
                for tool in res_tools.tools:
                    if "workflow" in tool.name.lower():
                        print(f"Workflow tool found: {tool.name}")
                
                # Let's try get_workflows
                res = await session.call_tool('get_workflows', {
                    'locationId': location_id
                })
                # Check the output
                if res.content:
                    print(res.content[0].text[:2000]) # Print first 2k chars
            except Exception as e:
                print("Failed to get workflows:", e)

if __name__ == "__main__":
    asyncio.run(main())
