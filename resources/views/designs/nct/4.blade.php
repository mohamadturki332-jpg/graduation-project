<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · VTS Radar</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Bruno+Ace+SC&family=Cairo:wght@600&display=swap" rel="stylesheet">
<style>
  :root { --bg:#02100a; --bg2:#031c12; --crt:#36ff7e; --crt-d:#1a8c44; --warn:#ff9b3a; --alert:#ff4040; --grid:rgba(54,255,126,0.08); }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--bg); color: var(--crt);
    font-family: 'Share Tech Mono', monospace;
    min-height:100vh; padding:24px; overflow-x:hidden;
    background-image:
      radial-gradient(ellipse at 30% 20%, rgba(54,255,126,0.08), transparent 60%),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.95' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.10'/></svg>");
    text-shadow: 0 0 4px rgba(54,255,126,0.4);
  }
  body::after {
    content:''; position:fixed; inset:0; pointer-events:none; z-index:50;
    background: repeating-linear-gradient(0deg, rgba(0,0,0,0) 0, rgba(0,0,0,0) 2px, rgba(0,0,0,0.18) 3px);
  }
  .display { font-family:'Bruno Ace SC',sans-serif; }
  .container { max-width:1320px; margin:0 auto; padding:18px 26px 40px; position:relative; z-index:10; }

  .top {
    display:flex; justify-content:space-between; align-items:center;
    border:1px solid var(--crt-d); padding:10px 16px;
    background: linear-gradient(180deg, rgba(54,255,126,0.06), transparent);
  }
  .top .l { display:flex; gap:18px; align-items:center; font-size:13px; letter-spacing:0.18em; }
  .top .blink { animation: blink 1.4s steps(1) infinite; }
  @keyframes blink { 50% { opacity:0.2; } }
  .top .r { display:flex; gap:24px; font-size:13px; letter-spacing:0.18em; }

  .heading { padding:48px 0 14px; display:grid; grid-template-columns: 2fr 1fr; gap:30px; align-items:end; }
  .heading h1 {
    font-family:'Bruno Ace SC',sans-serif; font-weight:400;
    font-size:clamp(42px, 5.4vw, 76px); line-height:1.05; letter-spacing:0.02em;
    text-shadow: 0 0 12px rgba(54,255,126,0.6);
    animation: type 1.4s steps(40) both;
  }
  .heading h1 em { font-style:normal; color:var(--warn); text-shadow: 0 0 12px rgba(255,155,58,0.7); }
  .heading .deck { font-size:13px; line-height:1.6; color: var(--crt-d); letter-spacing:0.06em; }
  .heading .deck b { color: var(--crt); }
  @keyframes type { from { clip-path: inset(0 100% 0 0); } to { clip-path: inset(0 0 0 0); } }

  .scope-row { display:grid; grid-template-columns: 1fr 1fr 1fr 1.2fr; gap:18px; margin-top:24px; }
  .cell {
    border:1px solid var(--crt-d); padding:18px 18px 22px; position:relative;
    background: linear-gradient(180deg, rgba(54,255,126,0.04), transparent);
  }
  .cell .lab { font-size:11px; letter-spacing:0.32em; color:var(--crt-d); }
  .cell .num {
    font-family:'Bruno Ace SC',sans-serif; font-size:84px; line-height:1; margin-top:6px;
    text-shadow: 0 0 16px rgba(54,255,126,0.8);
  }
  .cell .ar { font-family:'Cairo',sans-serif; font-weight:600; color:var(--crt-d); font-size:13px; margin-top:6px; }
  .cell.warn .num { color: var(--warn); text-shadow: 0 0 16px rgba(255,155,58,0.8); }
  .cell.cool .num { color: #69d2ff; text-shadow: 0 0 16px rgba(105,210,255,0.6); }
  .cell .corner { position:absolute; width:8px; height:8px; border:1.5px solid var(--crt); }
  .cell .corner.tl { top:-1px; left:-1px; border-right:none; border-bottom:none; }
  .cell .corner.tr { top:-1px; right:-1px; border-left:none; border-bottom:none; }
  .cell .corner.bl { bottom:-1px; left:-1px; border-right:none; border-top:none; }
  .cell .corner.br { bottom:-1px; right:-1px; border-left:none; border-top:none; }

  .scope {
    grid-column: span 1; grid-row: span 2;
    aspect-ratio: 1; border:1px solid var(--crt-d); border-radius:50%;
    position:relative; background: radial-gradient(circle, rgba(54,255,126,0.08) 0%, rgba(2,16,10,1) 75%);
    overflow:hidden;
  }
  .scope::before, .scope::after {
    content:''; position:absolute; left:50%; top:50%; transform:translate(-50%,-50%);
    border:1px solid var(--crt-d); border-radius:50%;
  }
  .scope::before { width:35%; height:35%; }
  .scope::after { width:70%; height:70%; }
  .scope .cross-h, .scope .cross-v { position:absolute; background: var(--crt-d); }
  .scope .cross-h { top:50%; left:0; right:0; height:1px; }
  .scope .cross-v { left:50%; top:0; bottom:0; width:1px; }
  .scope .sweep {
    position:absolute; top:0; left:50%; width:50%; height:50%;
    transform-origin: 0 100%;
    background: linear-gradient(85deg, rgba(54,255,126,0.45) 0%, transparent 70%);
    animation: sweep 4s linear infinite;
  }
  @keyframes sweep { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .blip { position:absolute; width:8px; height:8px; border-radius:50%; background: var(--crt); box-shadow: 0 0 8px var(--crt); }
  .blip.b1 { top:34%; left:62%; animation: ping 2s ease-out infinite; }
  .blip.b2 { top:58%; left:38%; background: var(--warn); box-shadow: 0 0 10px var(--warn); animation: ping 2.2s ease-out infinite 0.5s; }
  .blip.b3 { top:42%; left:30%; }
  .blip.b4 { top:67%; left:62%; background: var(--alert); box-shadow: 0 0 10px var(--alert); animation: ping 1.8s ease-out infinite 1s; }
  @keyframes ping { 50% { transform: scale(1.4); opacity:0.4; } }

  .panels { display:grid; grid-template-columns: 1fr 1fr; gap:18px; margin-top:24px; }
  .panel { border:1px solid var(--crt-d); padding:16px 20px; }
  .panel h3 { font-size:11px; letter-spacing:0.28em; padding-bottom:8px; border-bottom:1px dashed var(--crt-d); color: var(--crt-d); margin-bottom:8px; }
  .row { display:grid; grid-template-columns: 110px 1fr 50px; gap:14px; align-items:center; padding:6px 0; font-size:14px; }
  .row .nm { letter-spacing:0.16em; }
  .row .bar { height:8px; background: rgba(54,255,126,0.08); position:relative; }
  .row .bar::after { content:''; position:absolute; left:0; top:0; bottom:0; width: var(--w,50%); background: var(--crt); box-shadow: 0 0 6px var(--crt); }
  .row.warn .bar::after { background: var(--warn); box-shadow: 0 0 6px var(--warn); }
  .row.alert .bar::after { background: var(--alert); box-shadow: 0 0 6px var(--alert); }
  .row .v { font-family:'Bruno Ace SC',sans-serif; font-size:18px; text-align:right; }

  .actions { margin-top:24px; padding:14px 20px; border:1px solid var(--crt-d); display:flex; gap:14px; align-items:center; flex-wrap:wrap; background: rgba(54,255,126,0.04); }
  .btn {
    font-family:'Share Tech Mono',monospace; font-size:13px; letter-spacing:0.2em;
    padding:10px 20px; border:1px solid var(--crt); color:var(--crt); text-decoration:none;
    background: rgba(54,255,126,0.05); transition: all 0.12s;
  }
  .btn:hover { background: var(--crt); color: var(--bg); text-shadow:none; }
  .btn.warn { color: var(--warn); border-color: var(--warn); }
  .btn.warn:hover { background: var(--warn); color: var(--bg); }
  .stamp { margin-left:auto; font-size:11px; color:var(--crt-d); letter-spacing:0.18em; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="l">
      <span class="blink">●</span> <span>NCT VTS · KING ABDULAZIZ PORT · ARABIAN GULF</span>
    </div>
    <div class="r">
      <span>BERTH 04</span><span>RNG 12NM</span><span>14:22:08 AST</span>
    </div>
  </header>

  <section class="heading">
    <div>
      <h1>VESSEL TRAFFIC :: 44 CONTACTS<br><em>12 INBOUND · UNASSIGNED</em></h1>
    </div>
    <div class="deck">
      RADAR SWEEP NOMINAL · ATC LINK ONLINE · WEATHER 28°C · WIND 14KT NW<br><br>
      <b>TICKETS HELDESK YARD —</b> 44 ACTIVE CONTACTS. 12 AWAITING ASSIGNMENT, 7 UNDER CREW HANDLING, 25 DISCHARGED IN LAST 7 DAYS.
    </div>
  </section>

  <section class="scope-row">
    <div class="cell">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="lab">— OPEN · في الساحة</div>
      <div class="num">12</div>
      <div class="ar">awaiting assignment</div>
    </div>
    <div class="cell warn">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="lab">— IN PROGRESS · قيد المعالجة</div>
      <div class="num">07</div>
      <div class="ar">under handling</div>
    </div>
    <div class="cell cool">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="lab">— CLOSED · تم الإنجاز</div>
      <div class="num">25</div>
      <div class="ar">discharged · 7d</div>
    </div>
    <div class="scope">
      <span class="cross-h"></span><span class="cross-v"></span>
      <div class="sweep"></div>
      <span class="blip b1"></span><span class="blip b2"></span><span class="blip b3"></span><span class="blip b4"></span>
    </div>
    <div class="cell" style="grid-column:1 / span 3;">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="lab">— BERTHING SUMMARY</div>
      <div style="margin-top:8px; font-size:13px; line-height:1.7; color: var(--crt);">
        > 44 ACTIVE / 25 RESOLVED-7D / +12% WoW / OLDEST UNASSIGNED 02D 14H<br>
        > AGENT POOL : 04 ACTIVE / 03 STANDBY / TOP : agent.K · 08 RESOLVED / SHIFT<br>
        > ALERTS : 0 / SLA BREACHES : 0 / SYSTEM HEALTH : NOMINAL
      </div>
    </div>
  </section>

  <section class="panels">
    <div class="panel">
      <h3>// CARGO BY CATEGORY</h3>
      <div class="row"><span class="nm">SOFTWARE</span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row"><span class="nm">HARDWARE</span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">NETWORK</span><span class="bar" style="--w:44%"></span><span class="v">08</span></div>
      <div class="row"><span class="nm">ACCESS REQ</span><span class="bar" style="--w:38%"></span><span class="v">07</span></div>
    </div>
    <div class="panel">
      <h3>// PRIORITY MANIFEST</h3>
      <div class="row alert"><span class="nm">HIGH · عاجل</span><span class="bar" style="--w:41%"></span><span class="v">09</span></div>
      <div class="row warn"><span class="nm">MEDIUM</span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">LOW</span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="btn" href="/admin/tickets">▶ FULL CONTACT LIST</a>
    <a class="btn warn" href="/admin/users">▶ CREW MANIFEST</a>
    <span class="stamp">NCT/04 · BRUNO ACE + SHARE TECH MONO · VTS RADAR</span>
  </div>
</div>
</body>
</html>
