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
            
            # Using the Facebook Page ID from location details
            account_id = "117293112294927"
            user_id = "65ffdc74bd0f8171a4bc8e75"
            location_id = os.environ.get("GHL_LOCATION_ID")
            
            print(f"--- Attempting test post for Facebook: {account_id} ---")
            
            post_data = {
                "accountIds": [account_id],
                "userId": user_id,
                "type": "post",
                "summary": "Prueba de publicación desde API (Facebook) ⚓️",
                "media": [
                    {
                        "url": "https://yatezzitos.com/wp-content/uploads/2024/09/yate-de-lujo-en-el-mar.jpg",
                        "type": "image" # Small letters as a first guess
                    }
                ]
            }
            
            try:
                res = await session.call_tool('create_social_post', post_data)
                print("Success:", json.dumps([c.dict() for c in res.content], indent=2))
            except Exception as e:
                print("Failed (Attempt 1 - image):", e)
                
                # Attempt 2: Try IMAGE in uppercase
                print("\n--- Attempting with type: IMAGE ---")
                post_data["media"][0]["type"] = "IMAGE"
                try:
                    res = await session.call_tool('create_social_post', post_data)
                    print("Success:", json.dumps([c.dict() for c in res.content], indent=2))
                except Exception as e2:
                    print("Failed (Attempt 2 - IMAGE):", e2)

                    # Attempt 3: No media
                    print("\n--- Attempting with NO media ---")
                    post_data_no_media = post_data.copy()
                    del post_data_no_media["media"]
                    try:
                        res = await session.call_tool('create_social_post', post_data_no_media)
                        print("Success:", json.dumps([c.dict() for c in res.content], indent=2))
                    except Exception as e3:
                        print("Failed (Attempt 3 - No media):", e3)

if __name__ == "__main__":
    asyncio.run(main())
