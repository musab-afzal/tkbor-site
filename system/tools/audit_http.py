"""Read-only local public-site crawl. No form submissions or external requests."""
import json, re, urllib.request, urllib.error
from urllib.parse import urljoin, urlparse
from html.parser import HTMLParser
from pathlib import Path
from concurrent.futures import ThreadPoolExecutor
BASE = "http://misiom-laravel.test/"
class Parser(HTMLParser):
 def __init__(self): super().__init__(); self.links=[]; self.assets=[]; self.images=0; self.missing_alt=0; self.h1=0
 def handle_starttag(self, tag, attrs):
  a=dict(attrs)
  if tag=="a" and a.get("href"): self.links.append(a["href"])
  if tag=="img": self.images+=1; self.missing_alt += "alt" not in a
  if tag=="h1": self.h1+=1
  if tag in ("img","script") and a.get("src"): self.assets.append(a["src"])
  if tag=="link" and a.get("href") and a.get("rel") in ("stylesheet","icon","manifest","apple-touch-icon"): self.assets.append(a["href"])
def get(url):
 try:
  with urllib.request.urlopen(url,timeout=25) as r: return r.status,r.read(),r.headers.get("Content-Type", "")
 except urllib.error.HTTPError as e: return e.code,b"", ""
 except Exception as e: return str(e),b"", ""
routes=re.findall(r"Route::(?:get|redirect|view)\('([^']+)'", Path("routes/web.php").read_text())
queue={urljoin(BASE,p) for p in routes if "{" not in p and "'.$" not in p}
queue.update([BASE+"search?q=web",BASE+"admin/login",BASE+"admin",BASE+"missing-audit-page"])
status,xml,_=get(BASE+"sitemap.xml")
queue.update(re.findall(r"<loc>(.*?)</loc>",xml.decode()))
seen=set(); results=[]; assets=set()
while queue:
 batch=sorted(queue-seen); queue=set()
 if not batch: break
 for url,(status,body,content_type) in zip(batch,map(get,batch)):
  seen.add(url); row={"url":url,"status":status,"bytes":len(body)}
  if "text/html" in content_type and status==200:
   text=body.decode(errors="replace"); p=Parser(); p.feed(text)
   row.update(h1=p.h1,images=p.images,missing_alt=p.missing_alt)
   for link in p.links:
    target=urljoin(url,link).split("#")[0]
    if urlparse(target).netloc==urlparse(BASE).netloc and not urlparse(target).path.startswith(("/admin", "/storage", "/assets")): queue.add(target)
   assets.update(urljoin(url,a) for a in p.assets)
   assets.update(urljoin(url,a) for a in re.findall(r"url\(['\"]?([^)'\"]+)",text))
  results.append(row)
local_assets=sorted(a for a in assets if urlparse(a).netloc==urlparse(BASE).netloc)
asset_results=[{"url":a,"status":r[0],"bytes":len(r[1])} for a,r in zip(local_assets,ThreadPoolExecutor(8).map(get,local_assets))]
report={"pages":results,"assets":asset_results}
Path("storage/app/audit-http.json").write_text(json.dumps(report,indent=2))
print(json.dumps({"pages":len(results),"assets":len(asset_results),"unexpected_pages":[p for p in results if p["status"]!=200],"broken_assets":[a for a in asset_results if a["status"]!=200],"missing_alt":[p for p in results if p.get("missing_alt")]},indent=2))
