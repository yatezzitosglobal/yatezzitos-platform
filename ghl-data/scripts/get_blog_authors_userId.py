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
            print(f"--- Fetching blog authors for location: {location_id} ---")
            
            try:
                res = await session.call_tool('get_blog_authors', {
                    'locationId': location_id
                })
                print(json.dumps([c.dict() for c in res.content], indent=2))
            except Exception as e:
                print("Failed to get blog authors:", e)

if __name__ == "__main__":
    asyncio.run(main())
