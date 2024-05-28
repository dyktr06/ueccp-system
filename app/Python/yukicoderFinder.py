import requests
from bs4 import BeautifulSoup
import re
import sys

w = dict()

words = []

def search(url):
    res = requests.get(url)
    soup = BeautifulSoup(res.text, "html.parser")

    elems = soup.find_all("td")
    testcase = False
    for elem in elems:
        if len(elem.contents) == 0:
          continue
        res = re.findall("AC|WA|RE|TLE|MLE|ジャッジサーバー", str(elem.contents[0]))
        for word in res:
            if word == "ジャッジサーバー":
                testcase = True
                continue
            if testcase:
                words.append(word)

# 提出URL
args = sys.argv
search(args[1])

for word in words:
    if word in w:
        w[word] += 1
    else:
        w[word] = 1
        # print(word)

w = sorted(w.items(), key = lambda a : a[1], reverse=True)

for x, y in w:
    print(x, y)
# print(w)
