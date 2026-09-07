<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Gantry HUD</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Archivo+Black&family=JetBrains+Mono:wght@400;700&family=Cairo:wght@700;900&display=swap" rel="stylesheet">
<style>
  :root { --hi:#ffd400; --hi2:#ffaa00; --ink:#0a0a0a; --grey:#1a1a1a; --grey2:#2a2a2a; --paper:#f4f1e6; --green:#3df562; --red:#ff3030; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--ink); color: var(--paper);
    font-family: 'JetBrains Mono', monospace;
    min-height:100vh; padding:0;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.06'/></svg>");
  }
  .display { font-family:'Bebas Neue',sans-serif; }
  .blk { font-family:'Archivo Black',sans-serif; }
  .ar { font-family:'Cairo',sans-serif; }

  .hazard-bar {
    height:18px; background:
      repeating-linear-gradient(135deg, var(--hi) 0 22px, var(--ink) 22px 44px);
  }

  .container { max-width:1320px; margin:0 auto; padding:24px 36px 48px; }

  .top {
    display:flex; justify-content:space-between; align-items:center;
    background: var(--grey); padding:14px 22px; border:2px solid var(--hi);
    margin-bottom:20px;
  }
  .badge { display:flex; align-items:center; gap:14px; }
  .badge .helmet {
    width:48px; height:48px; background:var(--hi); border:3px solid var(--ink);
    display:flex; align-items:center; justify-content:center;
    font-family:'Archivo Black'; font-size:22px; color:var(--ink);
    transform: skewX(-6deg);
  }
  .badge .nm { font-family:'Bebas Neue'; font-size:24px; letter-spacing:0.04em; line-height:1; }
  .badge .ar { font-size:13px; color: var(--hi); margin-top:2px; }
  .indicators { display:flex; gap:18px; }
  .ind { display:flex; align-items:center; gap:6px; font-size:11px; letter-spacing:0.16em; text-transform:uppercase; }
  .ind .led { width:10px; height:10px; box-shadow: 0 0 6px currentColor; }
  .ind.g { color: var(--green); }
  .ind.g .led { background: var(--green); animation: blink 1.4s steps(1) infinite; }
  .ind.h { color: var(--hi); }
  .ind.h .led { background: var(--hi); }
  @keyframes blink { 50% { opacity:0.2; } }

  .hero {
    background: var(--grey); padding:36px 32px; border:2px solid var(--hi);
    display:grid; grid-template-columns: 2fr 1fr; gap:36px; align-items:end;
    position:relative;
  }
  .hero::before {
    content:''; position:absolute; top:0; left:0; right:0; height:6px;
    background: repeating-linear-gradient(90deg, var(--hi) 0 30px, var(--ink) 30px 60px);
  }
  .hero .lab { font-family:'JetBrains Mono'; font-size:11px; letter-spacing:0.32em; color:var(--hi); }
  .hero h1 {
    font-family:'Archivo Black',sans-serif;
    font-size:clamp(56px, 8vw, 124px); line-height:0.95; letter-spacing:-0.01em;
    color: var(--paper); margin-top:6px;
    animation: slidein 0.8s cubic-bezier(.2,.7,.2,1) both;
  }
  .hero h1 em {
    font-style:normal; background: var(--hi); color: var(--ink);
    padding: 0 16px; display:inline-block; transform: skewX(-6deg);
  }
  .hero .stat-side { border-left:2px solid var(--hi); padding-left:24px; }
  .hero .stat-side .lab { color:var(--paper); }
  .hero .stat-side .num { font-family:'Archivo Black'; font-size:88px; line-height:1; color: var(--hi); margin-top:6px; }
  .hero .stat-side .desc { font-size:12px; line-height:1.6; color: var(--paper); margin-top:8px; }
  @keyframes slidein { from { opacity:0; transform: translateX(-24px); } to { opacity:1; transform:none; } }

  .row-bar { display:flex; gap:20px; margin-top:28px; align-items:stretch; }
  .pill {
    flex:1; padding:24px 22px 26px; border:2px solid var(--hi); position:relative;
    background: var(--grey);
    animation: slidein 0.8s cubic-bezier(.2,.7,.2,1) both;
  }
  .pill:nth-child(1){ animation-delay:0.1s; }
  .pill:nth-child(2){ animation-delay:0.2s; background: var(--hi); color:var(--ink); }
  .pill:nth-child(3){ animation-delay:0.3s; }
  .pill .corner {
    position:absolute; top:-2px; right:-2px; padding:4px 10px; background: var(--hi); color: var(--ink);
    font-family:'JetBrains Mono'; font-size:11px; font-weight:700; letter-spacing:0.18em;
  }
  .pill:nth-child(2) .corner { background: var(--ink); color: var(--hi); }
  .pill .lab { font-family:'JetBrains Mono'; font-size:11px; letter-spacing:0.28em; }
  .pill .num { font-family:'Archivo Black'; font-size:96px; line-height:1; margin:8px 0 4px; }
  .pill .ar { font-family:'Cairo'; font-weight:700; font-size:18px; }

  .lower { display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:28px; }
  .panel { background: var(--grey); border:2px solid var(--hi); padding:20px 24px; }
  .panel h3 {
    font-family:'Bebas Neue'; font-size:24px; letter-spacing:0.06em; color: var(--hi);
    border-bottom:2px solid var(--hi); padding-bottom:8px; margin-bottom:12px;
    display:flex; justify-content:space-between; align-items:center;
  }
  .panel h3 small { font-family:'JetBrains Mono'; font-size:11px; color: var(--paper); letter-spacing:0.18em; }
  .row { display:grid; grid-template-columns: 140px 1fr 50px; gap:16px; align-items:center; padding:8px 0; border-bottom:1px dashed rgba(255,212,0,0.25); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'Bebas Neue'; font-size:20px; letter-spacing:0.04em; }
  .row .nm small { font-family:'Cairo'; font-size:13px; color:var(--hi); margin-left:8px; }
  .row .bar { height:14px; border:1.5px solid var(--hi); position:relative; background: var(--ink); }
  .row .bar::after {
    content:''; position:absolute; left:0; top:0; bottom:0; width: var(--w,50%);
    background: repeating-linear-gradient(135deg, var(--hi) 0 8px, var(--hi2) 8px 16px);
  }
  .row .v { font-family:'Archivo Black'; font-size:22px; text-align:right; color: var(--hi); }
  .row.alert .v { color: var(--red); }

  .actions {
    margin-top:28px; padding:18px 22px; background: var(--ink); border:2px solid var(--hi);
    display:flex; gap:14px; align-items:center; flex-wrap:wrap;
  }
  .btn {
    font-family:'Archivo Black'; font-size:16px; letter-spacing:0.06em;
    padding:14px 24px; text-decoration:none; border:3px solid var(--ink);
    background: var(--hi); color: var(--ink);
    box-shadow: 4px 4px 0 var(--paper);
    transition: all 0.1s;
  }
  .btn.alt { background: var(--ink); color: var(--hi); border-color: var(--hi); box-shadow: 4px 4px 0 var(--hi); }
  .btn:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 var(--paper); }
  .btn.alt:hover { box-shadow: 6px 6px 0 var(--hi); }
  .stamp { margin-left:auto; font-family:'JetBrains Mono'; font-size:11px; letter-spacing:0.18em; color: var(--hi); }
</style>
</head>
<body>
<div class="hazard-bar"></div>
<div class="container">
  <header class="top">
    <div class="badge">
      <div class="helmet">N</div>
      <div>
        <div class="nm">NCT GANTRY OPS · STATION 04</div>
        <div class="ar">عمليات الرافعة الجسرية · الرصيف الرابع</div>
      </div>
    </div>
    <div class="indicators">
      <div class="ind g"><span class="led"></span> SYSTEMS NOMINAL</div>
      <div class="ind h"><span class="led"></span> CRANE 04 OFFLINE-STANDBY</div>
      <div class="ind g"><span class="led"></span> 14:22 AST</div>
    </div>
  </header>

  <section class="hero">
    <div>
      <div class="lab">— ⚠ HELPDESK YARD · LIVE</div>
      <h1>44 LOADS<br><em>IN THE QUEUE</em></h1>
    </div>
    <div class="stat-side">
      <div class="lab">— UNASSIGNED</div>
      <div class="num">12</div>
      <div class="desc">Awaiting agent pickup. Operator: hook the oldest first. Two High-priority loads flagged.</div>
    </div>
  </section>

  <section class="row-bar">
    <div class="pill">
      <span class="corner">A·01</span>
      <div class="lab">— OPEN</div>
      <div class="num">12</div>
      <div class="ar">في الساحة</div>
    </div>
    <div class="pill">
      <span class="corner">A·02</span>
      <div class="lab">— IN PROGRESS</div>
      <div class="num">07</div>
      <div class="ar">قيد المعالجة</div>
    </div>
    <div class="pill">
      <span class="corner">A·03</span>
      <div class="lab">— CLOSED</div>
      <div class="num">25</div>
      <div class="ar">تم الإنجاز</div>
    </div>
  </section>

  <section class="lower">
    <div class="panel">
      <h3>CARGO TYPE <small>// FOUR LANES</small></h3>
      <div class="row"><span class="nm">SOFTWARE <small>برمجيات</small></span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row"><span class="nm">HARDWARE <small>أجهزة</small></span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">NETWORK <small>شبكة</small></span><span class="bar" style="--w:44%"></span><span class="v">08</span></div>
      <div class="row"><span class="nm">ACCESS <small>وصول</small></span><span class="bar" style="--w:38%"></span><span class="v">07</span></div>
    </div>
    <div class="panel">
      <h3>LOAD CLASS <small>// THREE BANDS</small></h3>
      <div class="row alert"><span class="nm">HIGH ⚠ <small>عاجل</small></span><span class="bar" style="--w:41%"></span><span class="v">09</span></div>
      <div class="row"><span class="nm">MEDIUM <small>متوسط</small></span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">LOW <small>منخفض</small></span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="btn" href="/admin/tickets">▶ HOOK QUEUE</a>
    <a class="btn alt" href="/admin/users">▶ CREW ROSTER</a>
    <span class="stamp">NCT/06 · BEBAS + ARCHIVO BLACK + JETBRAINS MONO · GANTRY HUD</span>
  </div>
</div>
<div class="hazard-bar"></div>
</body>
</html>
