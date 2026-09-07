<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Riso Press</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caprasimo&family=DM+Mono:wght@400;500&family=Cairo:wght@600;800&display=swap" rel="stylesheet">
<style>
  :root { --paper:#f3ecda; --orange:#ff5a1f; --navy:#1c2c4a; --grey:#3a3a3a; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--navy);
    font-family: 'DM Mono', monospace; font-weight:400;
    min-height:100vh; padding:0;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='6' height='6'><circle cx='1' cy='1' r='0.7' fill='%231c2c4a' opacity='0.18'/></svg>"),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.10'/></svg>");
    background-size: 6px 6px, 280px 280px;
  }
  body::before {
    content:''; position:fixed; top:0; right:0; width:60%; height:60%;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='4' height='4'><circle cx='1' cy='1' r='0.6' fill='%23ff5a1f' opacity='0.20'/></svg>");
    background-size: 4px 4px;
    pointer-events:none; mix-blend-mode: multiply; z-index:0;
  }
  .display { font-family:'Caprasimo',serif; }
  .ar { font-family:'Cairo',sans-serif; }
  .container { max-width:1200px; margin:0 auto; padding:36px 48px 60px; position:relative; z-index:1; }

  .reg {
    position:fixed; width:32px; height:32px; pointer-events:none; z-index:5;
    background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><circle cx='16' cy='16' r='14' fill='none' stroke='%23ff5a1f' stroke-width='1'/><line x1='16' y1='0' x2='16' y2='32' stroke='%231c2c4a'/><line x1='0' y1='16' x2='32' y2='16' stroke='%231c2c4a'/></svg>");
  }
  .reg.tl { top:14px; left:14px; }
  .reg.tr { top:14px; right:14px; }
  .reg.bl { bottom:14px; left:14px; }
  .reg.br { bottom:14px; right:14px; }

  .top {
    display:flex; justify-content:space-between; align-items:flex-end; padding-bottom:18px;
    border-bottom:3px solid var(--navy);
  }
  .brand { display:flex; gap:14px; align-items:center; }
  .brand .mk {
    width:54px; height:54px; background: var(--orange); color: var(--paper);
    display:flex; align-items:center; justify-content:center;
    font-family:'Caprasimo',serif; font-size:32px; transform:rotate(-4deg);
    box-shadow: 4px 4px 0 var(--navy);
  }
  .brand h2 { font-family:'Caprasimo'; font-size:22px; line-height:1; }
  .brand .sub { font-size:11px; letter-spacing:0.2em; color:var(--grey); margin-top:4px; }
  .brand .sub .ar { font-size:13px; color:var(--navy); }
  .meta { font-size:11px; line-height:1.7; text-align:right; letter-spacing:0.1em; }
  .meta b { color: var(--orange); font-weight:500; }

  .hero {
    margin-top:50px; position:relative;
    padding:30px 0;
  }
  .hero .kicker {
    display:inline-block; background:var(--navy); color:var(--paper); padding:6px 14px;
    font-size:11px; letter-spacing:0.28em;
    transform: rotate(-1.2deg);
    box-shadow: 4px 4px 0 var(--orange);
  }
  .hero h1 {
    font-family:'Caprasimo',serif;
    font-size:clamp(64px,9vw,140px); line-height:0.95; margin-top:24px;
    color:var(--navy);
    animation: pop 0.8s cubic-bezier(.4,1.5,.6,1) both;
  }
  .hero h1 strong {
    color: var(--orange);
    -webkit-text-stroke: 2px var(--navy);
    paint-order: stroke fill;
    display:inline-block; transform: rotate(-2deg);
  }
  .hero .deck {
    margin-top:18px; max-width:560px; font-family:'DM Mono'; font-size:14px; line-height:1.7; color: var(--grey);
  }
  @keyframes pop { from { opacity:0; transform: translateY(20px) rotate(-2deg) scale(0.96); } to { opacity:1; transform:none; } }

  .ports { display:grid; grid-template-columns: repeat(3,1fr); gap:24px; margin-top:48px; }
  .port {
    padding:26px 24px 30px; background: var(--paper); border:3px solid var(--navy); position:relative;
    box-shadow: 8px 8px 0 var(--orange);
    animation: pop 0.7s cubic-bezier(.4,1.5,.6,1) both;
  }
  .port:nth-child(1){ animation-delay:0.05s; }
  .port:nth-child(2){ animation-delay:0.15s; transform: rotate(0.6deg); box-shadow: -8px 8px 0 var(--orange); }
  .port:nth-child(3){ animation-delay:0.25s; }
  .port .stamp {
    position:absolute; top:-14px; right:14px; padding:4px 10px;
    background: var(--orange); color: var(--paper); font-family:'DM Mono'; font-size:11px; letter-spacing:0.2em;
    transform: rotate(2deg);
  }
  .port .lab { font-family:'DM Mono'; font-size:11px; letter-spacing:0.28em; color: var(--grey); }
  .port .num { font-family:'Caprasimo',serif; font-size:108px; line-height:0.95; color: var(--navy); margin:8px 0 6px; }
  .port .ar { font-family:'Cairo'; font-weight:600; font-size:18px; color: var(--orange); }
  .port .desc { font-size:12px; color:var(--grey); margin-top:6px; line-height:1.5; }

  .panels { display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-top:48px; }
  .panel {
    background: var(--navy); color: var(--paper); padding:24px 26px; position:relative;
    box-shadow: -8px 8px 0 var(--orange);
  }
  .panel::before {
    content:''; position:absolute; inset:6px; border:1px dashed rgba(243,236,218,0.3); pointer-events:none;
  }
  .panel.b { box-shadow: 8px 8px 0 var(--orange); }
  .panel h3 { font-family:'Caprasimo'; font-size:24px; padding-bottom:10px; border-bottom:1px solid rgba(243,236,218,0.3); margin-bottom:10px; }
  .panel h3 small { font-family:'DM Mono'; font-size:11px; letter-spacing:0.22em; color: var(--orange); }
  .row { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px dotted rgba(243,236,218,0.18); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'DM Mono'; font-size:14px; letter-spacing:0.06em; }
  .row .nm small { font-family:'Cairo'; color: var(--orange); margin-left:8px; }
  .row .v { font-family:'Caprasimo'; font-size:24px; color: var(--orange); }

  .actions { margin-top:48px; display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
  .btn {
    font-family:'Caprasimo'; font-size:22px; padding:16px 26px; text-decoration:none;
    border:3px solid var(--navy); color: var(--navy);
    background: var(--paper);
    box-shadow: 6px 6px 0 var(--orange);
    transition: all 0.12s;
  }
  .btn.primary { background: var(--orange); color:var(--paper); box-shadow: 6px 6px 0 var(--navy); }
  .btn:hover { transform: translate(-3px,-3px); box-shadow: 9px 9px 0 var(--orange); }
  .btn.primary:hover { box-shadow: 9px 9px 0 var(--navy); }
  .stamp-foot { margin-left:auto; font-family:'DM Mono'; font-size:11px; letter-spacing:0.18em; color:var(--grey); }
</style>
</head>
<body>
<span class="reg tl"></span><span class="reg tr"></span><span class="reg bl"></span><span class="reg br"></span>

<div class="container">
  <header class="top">
    <div class="brand">
      <div class="mk">N</div>
      <div>
        <h2>National Container Terminal</h2>
        <div class="sub">DAMMAM · KAP04 · <span class="ar">الميناء</span></div>
      </div>
    </div>
    <div class="meta">
      ISSUE №142<br>
      <b>07 / V / 2026</b><br>
      RUN: 1 of 1 · 14:22 AST
    </div>
  </header>

  <section class="hero">
    <div class="kicker">— ADMIN PRESS RELEASE — DAILY YARD REPORT —</div>
    <h1>FORTY-FOUR<br><strong>SHIPMENTS</strong></h1>
    <p class="deck">A two-colour print of today's helpdesk yard. Twelve crates remain on the dock awaiting an agent; seven are presently being worked; twenty-five have shipped out clean in the last seven days. <span style="color:var(--orange);">Software</span> is the heaviest category this week.</p>
  </section>

  <section class="ports">
    <div class="port">
      <span class="stamp">№ 01</span>
      <div class="lab">— OPEN</div>
      <div class="num">12</div>
      <div class="ar">في الساحة</div>
      <div class="desc">Logged but not yet picked up by an agent. Oldest is 2d 14h.</div>
    </div>
    <div class="port">
      <span class="stamp">№ 02</span>
      <div class="lab">— IN PROGRESS</div>
      <div class="num">07</div>
      <div class="ar">قيد المعالجة</div>
      <div class="desc">Currently being worked. Two flagged High priority.</div>
    </div>
    <div class="port">
      <span class="stamp">№ 03</span>
      <div class="lab">— CLOSED</div>
      <div class="num">25</div>
      <div class="ar">تم الإنجاز</div>
      <div class="desc">Resolved in the last 7 days. +12% over last week.</div>
    </div>
  </section>

  <section class="panels">
    <div class="panel">
      <h3>By category <small>// FOUR BUCKETS</small></h3>
      <div class="row"><span class="nm">Software <small>برمجيات</small></span><span class="v">18</span></div>
      <div class="row"><span class="nm">Hardware <small>أجهزة</small></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network <small>شبكة</small></span><span class="v">08</span></div>
      <div class="row"><span class="nm">Access <small>طلب وصول</small></span><span class="v">07</span></div>
    </div>
    <div class="panel b">
      <h3>By priority <small>// THREE FLAGS</small></h3>
      <div class="row"><span class="nm">High <small>عاجل</small></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium <small>متوسط</small></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low <small>منخفض</small></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="btn primary" href="/admin/tickets">→ Read all tickets</a>
    <a class="btn" href="/admin/users">→ Manage users</a>
    <span class="stamp-foot">NCT/05 · CAPRASIMO + DM MONO + CAIRO · RISO PRESS</span>
  </div>
</div>
</body>
</html>
