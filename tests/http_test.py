#!/usr/bin/env python3
"""Real HTTP regression checks. Only temporary databases and sessions are used."""
import http.cookiejar
import json
import os
from pathlib import Path
import re
import socket
import sqlite3
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
PHP = os.environ.get('PHP_BIN', 'php')
PASSWORD = 'temporary-test-password-only'
checks = 0

def check(ok, message):
    global checks
    assert ok, message
    checks += 1

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *args):
        return None

def run(base):
    with tempfile.TemporaryDirectory(prefix='toku-http-') as temp:
        with socket.socket() as sock:
            sock.bind(('127.0.0.1', 0))
            port = sock.getsockname()[1]
        origin = f'http://127.0.0.1:{port}'
        hashed = subprocess.check_output([PHP, '-r', 'echo password_hash($argv[1],PASSWORD_DEFAULT);', PASSWORD], text=True)
        env = {**os.environ, 'TOKU_DB': f'{temp}/toku.db', 'TOKU_PASSWORD_HASH': hashed, 'TOKU_BASE_PATH': base, 'TOKU_HTTPS': '0'}
        jar = http.cookiejar.CookieJar()
        client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar), NoRedirect())

        def request(path, data=None, method=None, headers=None):
            req = urllib.request.Request(origin + base + path, data=data, method=method, headers=headers or {})
            try:
                response = client.open(req, timeout=10)
            except urllib.error.HTTPError as e:
                response = e
            return response.code, response.headers, response.read().decode()

        def form(path, data):
            return request(path, urllib.parse.urlencode(data).encode(), headers={'Content-Type': 'application/x-www-form-urlencoded'})

        def api(data, token=None):
            return request('/api/watch', json.dumps(data).encode(), headers={'Content-Type': 'application/json', 'X-CSRF-Token': token or ''})

        def token(html):
            return re.search(r'name="csrf" value="([a-f0-9]+)"', html)[1]

        with open(f'{temp}/server.log', 'w+') as log:
            server = subprocess.Popen([PHP, '-d', f'session.save_path={temp}', '-S', f'127.0.0.1:{port}', '-t', 'public', 'public/router.php'], cwd=ROOT, env=env, stdout=log, stderr=log)
            try:
                for _ in range(100):
                    try:
                        status, headers, body = request('/login')
                        break
                    except urllib.error.URLError:
                        time.sleep(.05)
                else:
                    raise RuntimeError('Server failed to start')
                check(status == 200, 'Login renders')
                csrf = token(body)
                check('unsafe-inline' not in headers['Content-Security-Policy'], 'Strict CSP')
                check('HttpOnly' in headers['Set-Cookie'] and 'SameSite=Strict' in headers['Set-Cookie'], 'Secure session attributes')
                check(request('/')[0] == 303, 'Dashboard requires login')
                check(api({'series_id': 1, 'episode': 1, 'action': 'watch'})[0] == 401, 'API requires login')
                check(form('/login', {'password': PASSWORD})[0] == 403, 'Login requires CSRF')
                check(form('/login', {'password': 'wrong-password', 'csrf': csrf})[0] == 401, 'Wrong password rejected')
                status, headers, body = form('/login', {'password': PASSWORD, 'csrf': csrf})
                check(status == 303 and headers['Location'] == base + '/', 'Successful login')
                for path in ['/', '/series', '/stats', '/series-detail?id=1', '/watch?id=1', '/search?q=Heisei']:
                    status, _, body = request(path)
                    check(status == 200 and 'Warning:' not in body, 'Page renders: ' + path)
                csrf = token(request('/watch?id=1')[2])
                for path in ['/data/toku.db', '/config.local.php', '/.git/config', '/api/../app/Store', '/api/nope', '/index.php/extra', '/app/Store.php']:
                    check(request(path)[0] == 404, 'Private/unknown path blocked: ' + path)
                check(request('/assets/app.css')[0] == 200, 'CSS served')
                check(request('/api/watch')[0] == 405, 'API rejects GET')
                check(api({'series_id': 1, 'episode': 1, 'action': 'watch'})[0] == 403, 'Mutation requires CSRF')
                for data in [[], 1, {'series_id': [], 'episode': 1, 'action': 'watch'}, {'series_id': '1abc', 'episode': 1, 'action': 'watch'}, {'series_id': 1, 'episode': 0, 'action': 'watch'}, {'series_id': 1, 'episode': 9999, 'action': 'watch'}, {'series_id': 1, 'episode': 1, 'action': 'delete'}]:
                    check(api(data, csrf)[0] == 400, 'Invalid API input rejected')
                check(api({'series_id': 999999, 'episode': 1, 'action': 'watch'}, csrf)[0] == 404, 'Unknown series rejected')
                status, _, body = api({'series_id': 1, 'episode': 1, 'action': 'watch'}, csrf)
                check(status == 200 and json.loads(body)['success'], 'Watch saves')
                check(json.loads(body)['redirect'].startswith(base + '/watch?'), 'Redirect preserves subdirectory')
                check(len(json.loads(request('/export')[2])['watched']) == 1, 'Export reflects save')
                with sqlite3.connect(f'{temp}/toku.db') as db:
                    db.execute("CREATE TRIGGER fail_write BEFORE INSERT ON watched BEGIN SELECT RAISE(ABORT,'test write failure'); END")
                status, _, body = api({'series_id': 1, 'episode': 2, 'action': 'watch'}, csrf)
                check(status == 500 and json.loads(body)['success'] is False, 'Write failure produces HTTP 500')
                check('test write failure' not in body, 'Database details not exposed')
                with sqlite3.connect(f'{temp}/toku.db') as db:
                    check(db.execute('SELECT COUNT(*) FROM watched').fetchone()[0] == 1, 'Failed write leaves history intact')
                    db.execute('DROP TRIGGER fail_write')
                for query in ['era=%3Cscript%3Ealert(1)%3C/script%3E', 'q=%3Cscript%3Ealert(1)%3C/script%3E']:
                    status, _, body = request('/series?' + query)
                    check(status == 200 and '<script>alert(1)</script>' not in body, 'Reflected XSS regression')
                check(request('/watch?id=1&episode=999999')[0] == 404, 'Invalid watch-page episode')
                check(request('/series?q[]=bad')[0] == 400, 'Array parameter rejected')
                check(form('/api/watch', {'csrf': csrf, 'series_id': 1, 'episode': 1, 'action': 'unwatch'})[0] == 303, 'No-JavaScript form works')
                check(form('/logout', {'csrf': csrf})[0] == 303, 'Logout works')
                check(request('/')[0] == 303, 'Logout ends access')
            finally:
                server.terminate()
                server.wait(timeout=10)

run('')
run('/toku')
print(f'{checks} HTTP checks passed (root and /toku deployments).')
