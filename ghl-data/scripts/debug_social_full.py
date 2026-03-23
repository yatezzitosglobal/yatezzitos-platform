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
            print(f"--- Debugging Social Media for Location: {location_id} ---")
            
            # 1. get_social_accounts
            print("\nTool: get_social_accounts")
            try:
                res = await session.call_tool('get_social_accounts', {
                    'locationId': location_id
                })
                print(res.content[0].text if res.content else "No content")
            except Exception as e:
                print("Failed:", e)

            # 2. get_platform_accounts (Test a few)
            for platform in ["Facebook", "Instagram", "TikTok", "Google"]:
                print(f"\nTool: get_platform_accounts ({platform})")
                try:
                    res = await session.call_tool('get_platform_accounts', {
                        'locationId': location_id,
                        'platform': platform
                    })
                    print(res.content[0].text if res.content else "No content")
                except Exception as e:
                    print("Failed:", e)

            # 3. search_social_posts
            print("\nTool: search_social_posts")
            try:
                res = await session.call_tool('search_social_posts', {
                    'locationId': location_id,
                    'limit': 10
                })
                print(res.content[0].text if res.content else "No content")
            except Exception as e:
                print("Failed:", e)

            # 4. get_social_categories
            print("\nTool: get_social_categories")
            try:
                res = await session.call_tool('get_social_categories', {
                    'locationId': location_id
                })
                print(res.content[0].text if res.content else "No content")
            except Exception as e:
                print("Failed:", e)

if __name__ == "__main__":
    asyncio.run(main())
