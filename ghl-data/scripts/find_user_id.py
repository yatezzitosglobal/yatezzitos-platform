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
            email = "yatezzitosmexico@gmail.com"
            
            print(f"--- Searching for user with email: {email} ---")
            
            # 1. Search Contacts
            print("Checking contacts...")
            try:
                res = await session.call_tool('search_contacts', {
                    'locationId': location_id,
                    'query': email
                })
                print("Contacts result:", json.dumps([c.dict() for c in res.content], indent=2))
            except Exception as e:
                print("Contact search failed:", e)

            # 2. Search Conversations
            print("\nChecking conversations...")
            try:
                res = await session.call_tool('search_conversations', {
                    'locationId': location_id,
                    'query': email
                })
                print("Conversations result:", json.dumps([c.dict() for c in res.content], indent=2))
            except Exception as e:
                print("Conversation search failed:", e)

            # 3. Search Opportunities
            print("\nChecking opportunities...")
            try:
                res = await session.call_tool('search_opportunities', {
                    'locationId': location_id,
                    'limit': 20
                })
                # Check if any opportunity is assigned to someone
                data = json.loads(res.content[0].text) if res.content else {}
                opps = data.get('opportunities', [])
                assigned = [o for o in opps if o.get('assignedTo')]
                if assigned:
                    print("Found assigned opportunities:", json.dumps(assigned, indent=2))
                else:
                    print("No assigned opportunities found in the last 20.")
            except Exception as e:
                print("Opportunity search failed:", e)

if __name__ == "__main__":
    asyncio.run(main())
