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
            
            conversation_id = "3h99FVsU6wzeJHJILgNR"
            print(f"--- Fetching messages for conversation: {conversation_id} ---")
            
            try:
                # get_recent_messages is a tool I saw in the list
                res = await session.call_tool('get_recent_messages', {
                    'conversationId': conversation_id,
                    'limit': 10
                })
                print(json.dumps([c.dict() for c in res.content], indent=2))
            except Exception as e:
                print("Failed to get messages:", e)

if __name__ == "__main__":
    asyncio.run(main())
