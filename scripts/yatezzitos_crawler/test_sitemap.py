import requests
from bs4 import BeautifulSoup

url = "https://yatezzitos.com/es/"
res = requests.get(url)
soup = BeautifulSoup(res.text, 'lxml')

# Find all links that look like destinations
links = soup.find_all('a', href=True)
destinations = set()
for a in links:
    href = a['href']
    if '/ciudad/' in href:
        destinations.add(href)

for d in destinations:
    print(d)
