import urllib.request,urllib.parse,http.cookiejar,re,os,sqlite3,json,io
from pathlib import Path
BASE=os.getenv('BASE_URL','http://localhost:8080')
USER=os.getenv('TEST_USERNAME','oracle')
PASSWORD=os.environ['TEST_PASSWORD']
jar=http.cookiejar.CookieJar();client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar));checks=[]
def get(path):
 r=client.open(BASE+path);return r.read().decode(),r.url
def token(html):
 m=re.search(r'name="(csrf_\w+)" value="([^"]+)"',html);assert m,'Missing CSRF token';return {m[1]:m[2]}
def post(path,data,page=None):
 if page is not None:data={**token(page),**data}
 r=client.open(BASE+path,urllib.parse.urlencode(data).encode());return r.read().decode(),r.url
def check(name,result):
 assert result,name;checks.append(name);print('PASS',name)
html,url=get('/products');check('Management requires authentication',url.endswith('/login'))
html,url=post('/login',{'username':USER,'password':PASSWORD},html);check('Valid staff login',url.rstrip('/')==BASE)
for path in ['/','/products','/customers','/staff','/sales','/sales/new']:
 html,url=get(path);check('Page renders '+path,'Whoops!' not in html and '<title>' in html)
html,_=get('/products/new');html,_=post('/products/save',{'name':'Integration Product','price':'12.35','stock_quantity':'5'},html);check('Product create','Record saved.' in html)
pid=re.search(r'/products/(\d+)/edit[^>]*>Edit',html)[1]
html,_=get('/products/'+pid+'/edit');html,_=post('/products/save',{'id':pid,'name':'Integration Product','price':'12.35','stock_quantity':'6'},html);check('Product edit','6 units' in html)
html,_=get('/products/new');html,_=post('/products/save',{'name':'Bad Product','price':'-1','stock_quantity':'-3'},html);check('Invalid product rejected','notice error' in html)
html,_=get('/customers/new');html,_=post('/customers/save',{'full_name':'Integration Customer','email':'integration@example.org','phone':'+63 917 555 1234'},html);check('Customer create','Record saved.' in html)
cid=re.search(r'/customers/(\d+)/edit[^>]*>Edit',html)[1]
html,_=get('/customers/'+cid+'/edit');html,_=post('/customers/save',{'id':cid,'full_name':'Integration Customer Edited','email':'integration@example.org','phone':'09175551234'},html);check('Customer edit','Integration Customer Edited' in html)
html,_=get('/staff/new');html,_=post('/staff/save',{'username':'integration.staff','full_name':'Integration Staff','password':'IntegrationPass!729'},html);check('Staff create','Record saved.' in html)
sid=re.search(r'/staff/(\d+)/edit[^>]*>Edit',html)[1]
html,_=get('/staff/'+sid+'/edit');html,_=post('/staff/save',{'id':sid,'username':'integration.staff','full_name':'Integration Staff Edited','password':''},html);check('Staff edit and optional password','Integration Staff Edited' in html)
def sale(q,customer=''):
 html,_=get('/sales/new');sale_token=re.search(r'name="sale_token" value="([^"]+)"',html)[1]
 return post('/sales',{'product_id':pid,'customer_id':customer,'quantity':str(q),'sale_token':sale_token},html)
html,_=sale(2,cid);check('Sale recorded with exact decimal total','Sale #' in html and '24.70' in html)
html,_=sale(99);check('Overselling rejected','quantity exceeds available stock' in html)
html,_=get('/products');check('Rejected sale preserves stock','4 units' in html)
html,_=sale(1);check('Optional customer supports walk-in','Walk-in' in html and '12.35' in html)
html,_=get('/sales/new');st=re.search(r'name="sale_token" value="([^"]+)"',html)[1]
html,_=post('/sales',{'product_id':pid,'quantity':'1','sale_token':st},html)
html,_=post('/sales',{'product_id':pid,'quantity':'1','sale_token':st},html);check('Duplicate sale submission rejected','already submitted' in html)
# Upload real PNG bytes, exercising validation, GD processing and private delivery.
def upload(path,fields,filename,content,mime,resource="products",field="image"):
 html,_=get(path);fields={**token(html),**fields};boundary='SubaanTestBoundary729'
 parts=[]
 for k,v in fields.items():parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="{k}"\r\n\r\n{v}\r\n'.encode())
 parts.extend([f'--{boundary}\r\nContent-Disposition: form-data; name="{field}"; filename="{filename}"\r\nContent-Type: {mime}\r\n\r\n'.encode(),content,f'\r\n--{boundary}--\r\n'.encode()])
 req=urllib.request.Request(BASE+'/'+resource+'/save',data=b''.join(parts),headers={'Content-Type':f'multipart/form-data; boundary={boundary}'})
 return client.open(req).read().decode()
from PIL import Image
im=Image.new('RGB',(64,32),'green');b=io.BytesIO();im.save(b,format='PNG')
html=upload('/products/'+pid+'/edit',{'id':pid,'name':'Integration Product','price':'12.35','stock_quantity':'2'},'test.png',b.getvalue(),'image/png');check('Image upload and resize','Record saved.' in html)
media=re.search(r'/media/([a-f0-9]{40}\.jpg)',html)[1];resp=client.open(BASE+'/media/'+media);check('Authenticated image delivery',resp.headers.get_content_type()=='image/jpeg' and resp.read()[:2]==b'\xff\xd8')
html=upload('/products/'+pid+'/edit',{'id':pid,'name':'Integration Product','price':'12.35','stock_quantity':'2'},'bad.php',b'<?php echo 1;','application/x-php');check('Executable upload rejected','Only JPEG, PNG and WebP' in html)
html=upload('/staff/'+sid+'/edit',{'id':sid,'username':'integration.staff','full_name':'Integration Staff Edited','password':''},'avatar.png',b.getvalue(),'image/png','staff','avatar');check('Staff avatar upload','Record saved.' in html)
avatar=re.search(r'/media/([a-f0-9]{40}\.jpg)',html)[1]
avatar_bytes=client.open(BASE+'/media/'+avatar).read();check('Avatar prepared at 256 square',Image.open(io.BytesIO(avatar_bytes)).size==(256,256))
html,_=sale(0);check('Zero quantity rejected','notice error' in html)
html,_=get('/products/'+pid+'/edit');html,_=post('/products/save',{'id':pid,'name':'Integration Product','price':'99999999.99','stock_quantity':'2'},html)
html,_=sale(2);check('Sale total overflow rejected','sale total exceeds the supported limit' in html)
html,_=get('/products');check('Overflow rejection rolls back stock','2 units' in html)
for resource,rid in [('products',pid),('customers',cid),('staff',sid)]:
 html,_=get('/'+resource);html,_=post('/'+resource+'/'+rid+'/delete',{},html);check(resource+' delete or archive',('Record deleted.' in html or 'Product archived.' in html))
html,_=get('/sales');check('History retains related names after deletion','Integration Customer Edited' in html and 'Integration Product' in html)
html,_=get('/staff');oid=re.search(r'/staff/(\d+)/edit[^>]*>Edit',html)[1];html,_=post('/staff/'+oid+'/delete',{},html);check('Own account deletion blocked','cannot delete your own' in html)
html,_=get('/products');html,_=post('/logout',{},html);check('Logout returns to sign in','Sign in to your store' in html)
html,url=get('/media/'+media);check('Images require authentication',url.endswith('/login'))
html,_=post('/login',{'username':USER,'password':'wrong'},html);check('Invalid password rejected','username or password is incorrect' in html)
html,_=get('/login');html,url=post('/login',{'username':USER,'password':PASSWORD});check('CSRF-free login rejected',url.endswith('/login') and 'Overview' not in html)
Path('test-results.json').write_text(json.dumps({'passed':len(checks),'checks':checks},indent=2))
print(f'{len(checks)} checks passed')
