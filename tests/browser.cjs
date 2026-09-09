const {chromium} = require(process.env.PLAYWRIGHT_MODULE || 'playwright-core');
const {spawn, spawnSync} = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const assert = require('node:assert/strict');

(async () => {
const root=path.resolve(__dirname,'..');
const php=process.env.PHP_BIN || 'php';
const dir=fs.mkdtempSync(path.join(os.tmpdir(),'toku-ui-'));
const password='temporary-browser-password';
const hash=spawnSync(php,['-r','echo password_hash($argv[1],PASSWORD_DEFAULT);',password],{encoding:'utf8'}).stdout;
const server=spawn(php,['-d','session.save_path='+dir,'-S','127.0.0.1:18764','-t','public','public/router.php'],{cwd:root,env:{...process.env,TOKU_DB:dir+'/test.db',TOKU_PASSWORD_HASH:hash,TOKU_HTTPS:'0',TOKU_BASE_PATH:''}});
let browser;
let logs='';
server.stderr.on('data', data=>logs+=data);
try {
for(let i=0;i<100;i++){try{await fetch('http://127.0.0.1:18764/login');break;}catch{await new Promise(r=>setTimeout(r,50));}}
browser=await chromium.launch({executablePath:process.env.CHROMIUM_BIN || '/usr/bin/chromium',headless:true,args:['--no-sandbox']});
const page=await browser.newPage({viewport:{width:1440,height:1100}});
const errors=[];page.on('pageerror',error=>errors.push(error.message));
page.on('console',msg=>{if(msg.type()==='error'&&!msg.text().includes('500')) errors.push(msg.text());});
await page.goto('http://127.0.0.1:18764/login');
await page.getByLabel('Password',{exact:true}).fill(password);
await page.getByRole('button',{name:'Open my tracker'}).click();
await page.waitForURL('http://127.0.0.1:18764/');
assert(await page.getByRole('heading',{name:'Your next transformation.'}).isVisible());
await page.getByRole('link',{name:'Library',exact:true}).click();
await page.getByLabel('Search',{exact:true}).fill('Kamen Rider Kuuga');
await page.getByRole('button',{name:'Find series'}).click();
assert.equal(await page.locator('.card').count(),1);
await page.locator('.card').click();
await page.getByRole('link',{name:/Start watching/}).click();
await page.getByRole('button',{name:/Mark watched/}).click();
await page.waitForURL(/episode=2/);
assert(await page.locator('.episode.watched').count()===1);
const fail=spawnSync(php,['-r',"$db=new PDO('sqlite:'.$argv[1]);$db->exec(\"CREATE TRIGGER fail_write BEFORE INSERT ON watched BEGIN SELECT RAISE(ABORT,'test'); END\");",dir+'/test.db'],{encoding:'utf8'});
assert.equal(fail.status,0);
await page.getByRole('button',{name:/Mark watched/}).click();
await page.locator('#notice').waitFor({state:'visible'});
assert(page.url().includes('episode=2'));
assert(await page.getByRole('button',{name:/Mark watched/}).isEnabled());
spawnSync(php,['-r',"$db=new PDO('sqlite:'.$argv[1]);$db->exec('DROP TRIGGER fail_write');",dir+'/test.db']);
await page.getByRole('link',{name:'Dashboard',exact:true}).click();
fs.mkdirSync(root+'/review',{recursive:true});
await page.screenshot({path:root+'/review/desktop.png',fullPage:true});
await page.setViewportSize({width:390,height:844});
await page.screenshot({path:root+'/review/mobile.png',fullPage:true});
for(const route of ['/','/series','/series-detail?id=1','/watch?id=1','/stats','/search?q=Heisei']) {
 await page.goto('http://127.0.0.1:18764'+route);
 assert(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),'Mobile overflow: '+route);
}
assert.deepEqual(errors,[]);
console.log('Chromium checks passed: login, search, watch, failed-save recovery, six mobile pages; screenshots saved.');
} finally {if(browser)await browser.close();server.kill();fs.rmSync(dir,{recursive:true,force:true});}
})().catch(e=>{console.error(e);process.exitCode=1;});
