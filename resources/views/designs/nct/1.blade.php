<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Terminal Schematic</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Major+Mono+Display&family=Spectral:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root { --paper:#eef3f5; --ink:#0c2a4a; --line:#0c2a4a; --rust:#a85a2e; --soft:#516c89; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'Spectral', serif; font-weight: 400;
    min-height: 100vh; padding: 32px;
    background-image:
      linear-gradient(rgba(12,42,74,0.08) 1px, transparent 1px),
      linear-gradient(90deg, rgba(12,42,74,0.08) 1px, transparent 1px),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.04'/></svg>");
    background-size: 40px 40px, 40px 40px, 240px 240px;
  }
  .mono { font-family: 'Major Mono Display', monospace; }
  .ar { font-family: 'Cairo', sans-serif; }
  .frame { max-width: 1240px; margin: 0 auto; border: 2px solid var(--ink); padding: 28px 36px 36px; background: rgba(238,243,245,0.6); position: relative; }
  .frame::before, .frame::after {
    content:''; position:absolute; width:14px; height:14px; border:2px solid var(--ink); background: var(--paper);
  }
  .frame::before { top:-8px; left:-8px; }
  .frame::after { bottom:-8px; right:-8px; }
  .corner { position:absolute; width:14px; height:14px; border:2px solid var(--ink); background: var(--paper); }
  .c-tr { top:-8px; right:-8px; }
  .c-bl { bottom:-8px; left:-8px; }

  .top { display:flex; justify-content:space-between; align-items:flex-end; padding-bottom:16px; border-bottom:1px solid var(--ink); }
  .brand { display:flex; align-items:center; gap:14px; }
  .brand .mk {
    width:46px; height:46px; border:2px solid var(--ink); display:flex; align-items:center; justify-content:center;
    font-family:'Major Mono Display'; font-size:22px; background: var(--paper); position:relative;
  }
  .brand .mk::after { content:''; position:absolute; inset:4px; border:1px dashed var(--ink); }
  .brand .nm { font-weight:600; font-size:13px; letter-spacing:0.18em; text-transform:uppercase; }
  .brand .ar { display:block; font-size:13px; color:var(--soft); margin-top:2px; }
  .meta { font-family:'Major Mono Display'; font-size:11px; text-align:right; color: var(--soft); line-height:1.6; }

  .titlebar { padding:36px 0 22px; border-bottom:1px solid var(--ink); }
  .titlebar .num { font-family:'Major Mono Display'; font-size:11px; color:var(--rust); letter-spacing:0.32em; }
  .titlebar h1 {
    font-family:'Spectral',serif; font-weight:300; font-size:clamp(48px,6.4vw,88px); line-height:1.02; letter-spacing:-0.018em; margin-top:8px;
    animation: drawin 1s ease both;
  }
  .titlebar h1 em { font-style:italic; color:var(--rust); }
  .titlebar .deck { font-style:italic; color:var(--soft); margin-top:14px; max-width:680px; font-size:17px; }
  @keyframes drawin { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform:none; } }

  .schematic { display:grid; grid-template-columns: repeat(3, 1fr); gap:0; padding-top:28px; }
  .cell {
    border:1px solid var(--ink); padding:24px 22px 26px; position:relative; background: var(--paper);
    margin-left:-1px; margin-bottom:-1px;
    animation: drawin 0.8s ease both;
  }
  .cell:nth-child(1){ animation-delay:0.1s; }
  .cell:nth-child(2){ animation-delay:0.2s; }
  .cell:nth-child(3){ animation-delay:0.3s; }
  .cell .tag { font-family:'Major Mono Display'; font-size:10px; letter-spacing:0.28em; color:var(--soft); }
  .cell .num { font-family:'Spectral',serif; font-weight:300; font-size:120px; line-height:0.95; color:var(--ink); margin:6px 0 10px; }
  .cell .ar { color:var(--soft); font-size:14px; }
  .cell .pin {
    position:absolute; top:14px; right:14px; width:10px; height:10px; border:1.5px solid var(--ink); background:var(--paper);
  }
  .cell .pin::after { content:''; position:absolute; top:-6px; left:-6px; width:22px; height:22px; border:1px solid var(--ink); border-radius:50%; opacity:0.4; }

  .lower { display:grid; grid-template-columns: 1fr 1fr; gap:0; margin-top:28px; }
  .panel { border:1px solid var(--ink); padding:22px 24px 26px; background: var(--paper); margin-left:-1px; }
  .panel:first-child { margin-left:0; }
  .panel h3 { font-family:'Major Mono Display'; font-size:12px; letter-spacing:0.22em; padding-bottom:10px; border-bottom:1px solid var(--ink); margin-bottom:12px; }
  .row { display:grid; grid-template-columns: 1fr 70px 60px; align-items:center; gap:14px; padding:8px 0; border-bottom:1px dotted rgba(12,42,74,0.4); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'Spectral',serif; font-size:18px; }
  .row .nm .ar { font-size:13px; color:var(--soft); margin-left:6px; }
  .row .ln { height:1px; background: var(--ink); position:relative; }
  .row .ln::before, .row .ln::after { content:''; position:absolute; top:50%; transform:translateY(-50%); width:6px; height:6px; background:var(--ink); border-radius:50%; }
  .row .ln::before { left:-3px; } .row .ln::after { right:-3px; }
  .row .v { font-family:'Major Mono Display'; font-size:22px; text-align:right; }

  .legend { display:flex; gap:20px; margin-top:24px; padding-top:16px; border-top:1px solid var(--ink); font-family:'Major Mono Display'; font-size:11px; color:var(--soft); letter-spacing:0.16em; }
  .actions { margin-top:28px; display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
  .btn {
    font-family:'Major Mono Display'; font-size:12px; letter-spacing:0.22em; padding:14px 22px;
    border:1.5px solid var(--ink); color:var(--ink); text-decoration:none; background:var(--paper);
    transition: all 0.15s;
  }
  .btn.primary { background: var(--ink); color: var(--paper); }
  .btn:hover { background: var(--rust); color:var(--paper); border-color: var(--rust); }
  .stamp { margin-left:auto; font-family:'Spectral'; font-style:italic; font-size:13px; color:var(--soft); }
</style>
</head>
<body>
<div class="frame">
  <span class="corner c-tr"></span><span class="corner c-bl"></span>
  <header class="top">
    <div class="brand">
      <div class="mk">N</div>
      <div>
        <div class="nm">National Container Terminal</div>
        <span class="ar">الشركة الوطنية للحاويات · King Abdulaziz Port</span>
      </div>
    </div>
    <div class="meta">
      DWG NO. NCT-HD-2026-007<br>
      SCALE 1:200 · SHEET 01/01<br>
      REV B · 07 MAY 2026 · 14:22 AST
    </div>
  </header>

  <section class="titlebar">
    <div class="num">— SECTION A · TICKET TERMINAL OVERVIEW</div>
    <h1>Forty-four <em>shipments</em> on the manifest;<br>twelve await berth assignment.</h1>
    <p class="deck">Drawing details the present condition of the helpdesk yard. Each container represents a logged ticket. Quay crane operators are advised to review High-priority lots first.</p>
  </section>

  <section class="schematic">
    <div class="cell">
      <span class="pin"></span>
      <div class="tag">A·01 · OPEN / IN YARD</div>
      <div class="num">12</div>
      <div class="ar">في الساحة — awaiting agent</div>
    </div>
    <div class="cell">
      <span class="pin"></span>
      <div class="tag">A·02 · IN PROGRESS</div>
      <div class="num">07</div>
      <div class="ar">قيد المعالجة — under handling</div>
    </div>
    <div class="cell">
      <span class="pin"></span>
      <div class="tag">A·03 · CLOSED / CLEARED</div>
      <div class="num">25</div>
      <div class="ar">تم الإنجاز — discharged</div>
    </div>
  </section>

  <section class="lower">
    <div class="panel">
      <h3>SECTION B · CARGO MANIFEST BY CATEGORY</h3>
      <div class="row"><span class="nm">Software <span class="ar">برمجيات</span></span><span class="ln"></span><span class="v">18</span></div>
      <div class="row"><span class="nm">Hardware <span class="ar">أجهزة</span></span><span class="ln"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network <span class="ar">شبكة</span></span><span class="ln"></span><span class="v">08</span></div>
      <div class="row"><span class="nm">Access request <span class="ar">طلب وصول</span></span><span class="ln"></span><span class="v">07</span></div>
    </div>
    <div class="panel">
      <h3>SECTION C · PRIORITY DESIGNATION</h3>
      <div class="row"><span class="nm">High <span class="ar">عاجل</span></span><span class="ln"></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium <span class="ar">متوسط</span></span><span class="ln"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low <span class="ar">منخفض</span></span><span class="ln"></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="legend">
    <span>◦ PIN = TRACKED</span>
    <span>— LINE = MEASURED</span>
    <span>▢ FRAME = ENCLOSED LOT</span>
  </div>

  <div class="actions">
    <a class="btn primary" href="/admin/tickets">SECTION B · TICKETS →</a>
    <a class="btn" href="/admin/users">SECTION D · ROSTER →</a>
    <span class="stamp">NCT/01 · Spectral + Major Mono · Blueprint Schematic</span>
  </div>
</div>
</body>
</html>
