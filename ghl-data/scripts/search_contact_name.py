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
            query = "Luis Velazquez"
            
            print(f"--- Searching for contact: {query} ---")
            
            try:
                res = await session.call_tool('search_contacts', {
                    'locationId': location_id,
                    'query': query
                })
                print(json.dumps([c.dict() for c in res.content], indent=2))
            except Exception as e:
                print("Search failed:", e)

if __name__ == "__main__":
    asyncio.run(main())
