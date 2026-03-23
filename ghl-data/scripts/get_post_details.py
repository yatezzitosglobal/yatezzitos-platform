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
            
            post_id = '69c0b5d39e1f107cac07a471'
            location_id = os.environ.get("GHL_LOCATION_ID")
            
            print(f"--- Fetching details for post: {post_id} at location: {location_id} ---")
            res = await session.call_tool('get_social_post', {
                'locationId': location_id,
                'postId': post_id
            })
            print(json.dumps([c.dict() for c in res.content], indent=2))

if __name__ == "__main__":
    asyncio.run(main())
