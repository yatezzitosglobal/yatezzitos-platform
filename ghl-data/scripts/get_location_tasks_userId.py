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
            print(f"--- Fetching tasks for location: {location_id} ---")
            
            try:
                # search_location_tasks is in the tool list
                res = await session.call_tool('search_location_tasks', {
                    'locationId': location_id,
                    'limit': 50
                })
                # Check the output
                if res.content:
                    print(res.content[0].text[:10000]) # Print more chars to find assignedTo
            except Exception as e:
                print("Failed to get tasks:", e)

if __name__ == "__main__":
    asyncio.run(main())
