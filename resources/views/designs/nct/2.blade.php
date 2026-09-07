<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Bridge Console</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=VT323&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root { --wood-a:#2a1810; --wood-b:#3d2418; --brass:#c9a96e; --brass-dim:#7a6645; --crt:#7df59d; --amber:#ffb347; --red:#ff5d4a; --ink:#0a0805; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--ink); color: var(--brass);
    font-family: 'Cormorant Garamond', serif;
    min-height:100vh; padding:32px;
    background-image:
      radial-gradient(ellipse at 50% 0%, rgba(201,169,110,0.05) 0%, transparent 50%),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.95' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.06'/></svg>");
  }
  .crt { font-family:'VT323', monospace; }
  .ar { font-family:'Cairo',sans-serif; }

  .console {
    max-width:1280px; margin:0 auto;
    background: linear-gradient(180deg, var(--wood-b) 0%, var(--wood-a) 100%);
    border:6px solid var(--brass); border-radius:14px;
    padding:32px 36px 40px;
    box-shadow: 0 0 0 2px var(--ink), 0 30px 80px rgba(0,0,0,0.7), inset 0 1px 0 rgba(255,255,255,0.06);
    position:relative;
  }
  .console::before {
    content:''; position:absolute; inset:8px; border:1px solid rgba(201,169,110,0.3); border-radius:8px; pointer-events:none;
  }
  .rivet {
    position:absolute; width:10px; height:10px; background: radial-gradient(circle at 30% 30%, var(--brass), #4a3a25);
    border-radius:50%; box-shadow: 0 1px 2px rgba(0,0,0,0.6);
  }
  .rivet.tl { top:18px; left:18px; }
  .rivet.tr { top:18px; right:18px; }
  .rivet.bl { bottom:18px; left:18px; }
  .rivet.br { bottom:18px; right:18px; }

  .top { display:flex; justify-content:space-between; align-items:center; padding-bottom:18px; border-bottom:1px solid rgba(201,169,110,0.3); }
  .badge {
    display:flex; align-items:center; gap:14px;
  }
  .badge .anchor {
    width:54px; height:54px; border:2px solid var(--brass); border-radius:50%;
    display:flex; align-items:center; justify-content:center; font-family:'Cormorant Garamond'; font-size:28px; color:var(--brass);
    background: radial-gradient(circle, rgba(201,169,110,0.1), transparent);
  }
  .badge .nm { font-family:'Cormorant Garamond'; font-weight:600; font-size:18px; letter-spacing:0.06em; }
  .badge .ar { display:block; font-size:13px; color:var(--brass-dim); margin-top:2px; }
  .indicators { display:flex; gap:18px; }
  .ind {
    display:flex; align-items:center; gap:8px; font-family:'VT323'; font-size:18px; color:var(--brass);
  }
  .ind .dot { width:10px; height:10px; border-radius:50%; box-shadow: 0 0 8px currentColor; }
  .ind.g .dot { background: var(--crt); color: var(--crt); animation: pulse 2s ease-in-out infinite; }
  .ind.a .dot { background: var(--amber); color: var(--amber); }
  @keyframes pulse { 50% { opacity:0.4; } }

  .heading { padding:36px 0 28px; text-align:center; }
  .heading .small { font-family:'VT323'; font-size:18px; color:var(--brass-dim); letter-spacing:0.4em; }
  .heading h1 {
    font-family:'Cormorant Garamond',serif; font-style:italic; font-weight:400;
    font-size:clamp(48px, 6vw, 88px); line-height:1.05; color: var(--brass);
    margin-top:6px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    animation: glow 1.4s ease both;
  }
  .heading h1 strong { font-style:normal; font-weight:600; color:#e8c98a; text-shadow: 0 0 18px rgba(201,169,110,0.5); }
  @keyframes glow { from { opacity:0; filter: blur(6px); } to { opacity:1; filter:none; } }

  .gauges { display:grid; grid-template-columns: repeat(3,1fr); gap:24px; margin-top:18px; }
  .gauge {
    background: radial-gradient(circle at 50% 30%, #1a0f08 0%, #0a0604 70%);
    border:3px solid var(--brass); border-radius: 220px 220px 14px 14px;
    padding:24px 24px 28px; text-align:center; position:relative;
    box-shadow: inset 0 0 30px rgba(0,0,0,0.8), 0 8px 0 -2px var(--wood-a), 0 10px 20px rgba(0,0,0,0.5);
    animation: glow 1.2s ease both;
  }
  .gauge:nth-child(2){ animation-delay:0.15s; }
  .gauge:nth-child(3){ animation-delay:0.3s; }
  .gauge::before {
    content:''; position:absolute; top:14px; left:50%; transform:translateX(-50%);
    width:70%; height:70px; border:2px solid var(--brass-dim); border-bottom:none; border-radius: 200px 200px 0 0;
  }
  .gauge .lab { font-family:'VT323'; font-size:18px; color:var(--brass-dim); letter-spacing:0.32em; padding-top:10px; }
  .gauge .num { font-family:'VT323'; font-size:120px; line-height:1; color:var(--crt); text-shadow: 0 0 18px rgba(125,245,157,0.6), 0 0 4px rgba(125,245,157,0.9); margin:8px 0; }
  .gauge.amber .num { color:var(--amber); text-shadow: 0 0 18px rgba(255,179,71,0.6), 0 0 4px rgba(255,179,71,0.9); }
  .gauge.red .num { color:var(--red); text-shadow: 0 0 18px rgba(255,93,74,0.6), 0 0 4px rgba(255,93,74,0.9); }
  .gauge .sub { font-family:'Cormorant Garamond'; font-style:italic; font-size:15px; color:var(--brass-dim); }

  .strip { margin-top:32px; display:grid; grid-template-columns: 1fr 1fr; gap:24px; }
  .panel {
    background: linear-gradient(180deg, rgba(10,6,4,0.6), rgba(10,6,4,0.4));
    border:2px solid var(--brass); border-radius:8px; padding:20px 24px;
    box-shadow: inset 0 0 20px rgba(0,0,0,0.5);
  }
  .panel h3 { font-family:'VT323'; font-size:18px; letter-spacing:0.28em; color:var(--brass-dim); padding-bottom:10px; border-bottom:1px solid rgba(201,169,110,0.2); margin-bottom:10px; }
  .row { display:grid; grid-template-columns: 1fr 36px; align-items:baseline; gap:12px; padding:8px 0; border-bottom:1px dotted rgba(201,169,110,0.2); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'Cormorant Garamond'; font-size:19px; color:var(--brass); }
  .row .nm small { font-family:'Cairo'; font-size:13px; color:var(--brass-dim); margin-left:6px; }
  .row .v { font-family:'VT323'; font-size:24px; color:var(--crt); text-align:right; }

  .actions { margin-top:32px; display:flex; gap:14px; align-items:center; flex-wrap:wrap; padding-top:20px; border-top:1px solid rgba(201,169,110,0.3); }
  .lever {
    font-family:'VT323'; font-size:20px; letter-spacing:0.18em; padding:14px 28px;
    background: linear-gradient(180deg, var(--brass) 0%, #8a7140 100%); color: var(--ink); text-decoration:none;
    border-radius:6px; box-shadow: 0 4px 0 #4a3a25, inset 0 1px 0 rgba(255,255,255,0.3);
    transition: all 0.1s;
  }
  .lever:hover { transform: translateY(2px); box-shadow: 0 2px 0 #4a3a25; }
  .lever.crt { background: linear-gradient(180deg, var(--crt) 0%, #4ec77a 100%); }
  .stamp { margin-left:auto; font-family:'Cormorant Garamond'; font-style:italic; font-size:14px; color: var(--brass-dim); }
</style>
</head>
<body>
<div class="console">
  <span class="rivet tl"></span><span class="rivet tr"></span><span class="rivet bl"></span><span class="rivet br"></span>

  <header class="top">
    <div class="badge">
      <div class="anchor">⚓</div>
      <div>
        <div class="nm">M/V Helpdesk · Bridge Console</div>
        <span class="ar">جسر القيادة · National Container Terminal</span>
      </div>
    </div>
    <div class="indicators">
      <div class="ind g"><span class="dot"></span> SYSTEM NOMINAL</div>
      <div class="ind a"><span class="dot"></span> BERTH 04 ACTIVE</div>
      <div class="ind g"><span class="dot"></span> 14:22 AST</div>
    </div>
  </header>

  <section class="heading">
    <div class="small">— TICKET STATUS · STARBOARD READOUT —</div>
    <h1>Forty-four crates aboard,<br><strong>twelve still on the dock.</strong></h1>
  </section>

  <section class="gauges">
    <div class="gauge"><div class="lab">OPEN</div><div class="num">12</div><div class="sub">awaiting agent</div></div>
    <div class="gauge amber"><div class="lab">IN PROGRESS</div><div class="num">07</div><div class="sub">in capable hands</div></div>
    <div class="gauge"><div class="lab">CLOSED</div><div class="num">25</div><div class="sub">manifest discharged</div></div>
  </section>

  <section class="strip">
    <div class="panel">
      <h3>— CARGO BY CATEGORY —</h3>
      <div class="row"><span class="nm">Software <small>برمجيات</small></span><span class="v">18</span></div>
      <div class="row"><span class="nm">Hardware <small>أجهزة</small></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network <small>شبكة</small></span><span class="v">08</span></div>
      <div class="row"><span class="nm">Access request <small>طلب وصول</small></span><span class="v">07</span></div>
    </div>
    <div class="panel">
      <h3>— PRIORITY MANIFEST —</h3>
      <div class="row"><span class="nm">High <small>عاجل</small></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium <small>متوسط</small></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low <small>منخفض</small></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="lever" href="/admin/tickets">▶ CARGO HOLD</a>
    <a class="lever crt" href="/admin/users">▶ CREW ROSTER</a>
    <span class="stamp">NCT/02 · Cormorant Garamond + VT323 · Bridge Console</span>
  </div>
</div>
</body>
</html>
