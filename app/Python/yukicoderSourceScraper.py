import requests
from bs4 import BeautifulSoup
import sys

def search(url):
    res = requests.get(url + "/source")
    print(res.text)

# 提出URL
args = sys.argv
search(args[1])
