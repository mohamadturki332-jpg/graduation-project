<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HELPDESK // CONSTRUCTION ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Archivo+Black&family=Archivo:wght@400;500;700&family=Major+Mono+Display&display=swap" rel="stylesheet">
<style>
  :root { --bg:#1a1a17; --concrete:#2d2c27; --hi:#fcd900; --warn:#ff5e3a; --line:#3d3b34; --ink:#0a0a08; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--bg); color: #f0eee5;
    font-family: 'Archivo', sans-serif;
    min-height: 100vh;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='220' height='220'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.10'/></svg>"),
      linear-gradient(180deg, #1a1a17 0%, #232220 100%);
  }
  .hazard {
    background: repeating-linear-gradient(135deg, var(--hi) 0 22px, var(--ink) 22px 44px);
    height: 18px;
  }
  .container { max-width: 1320px; margin: 0 auto; padding: 0 48px; }
  .top { padding: 18px 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); }
  .badge { display: inline-flex; align-items: center; gap: 10px; font-family: 'Major Mono Display', monospace; font-size: 13px; color: var(--hi); letter-spacing: 0.18em; }
  .badge .dot { width: 10px; height: 10px; background: var(--warn); animation: pulse 1s ease-in-out infinite; }
  @keyframes pulse { 50% { opacity: 0.4; } }
  .meta { font-family: 'Major Mono Display', monospace; font-size: 11px; letter-spacing: 0.2em; opacity: 0.5; }

  .hero { display: grid; grid-template-columns: 1.1fr 1fr; gap: 40px; padding: 64px 0 48px; align-items: end; }
  .hero h1 {
    font-family: 'Bebas Neue', sans-serif; font-weight: 400;
    font-size: clamp(96px, 14vw, 224px); line-height: 0.86; letter-spacing: -0.005em;
    text-transform: uppercase;
    animation: slide 0.8s cubic-bezier(.2,.7,.2,1) both;
  }
  .hero h1 span.acc { color: var(--hi); }
  .hero h1 span.warn { color: var(--warn); }
  .hero .side {
    border-left: 4px solid var(--hi); padding: 14px 22px;
    background: var(--concrete);
    animation: slide 0.8s cubic-bezier(.2,.7,.2,1) 0.15s both;
  }
  .hero .side .lab { font-family: 'Major Mono Display', monospace; font-size: 11px; color: var(--hi); letter-spacing: 0.25em; margin-bottom: 10px; }
  .hero .side .txt { font-size: 14px; line-height: 1.55; color: rgba(240,238,229,0.78); }
  @keyframes slide { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: none; } }

  .stats {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 4px; margin: 56px 0;
    background: var(--ink);
    border: 4px solid var(--hi);
  }
  .stat { background: var(--concrete); padding: 38px 32px; position: relative; overflow: hidden; }
  .stat::before {
    content: attr(data-num); position: absolute; right: -10px; top: -30px; font-family: 'Bebas Neue', sans-serif;
    font-size: 280px; line-height: 1; opacity: 0.06; pointer-events: none;
  }
  .stat .lab { font-family: 'Archivo Black', sans-serif; font-size: 11px; letter-spacing: 0.3em; text-transform: uppercase; color: var(--hi); margin-bottom: 18px; }
  .stat .num { font-family: 'Bebas Neue', sans-serif; font-size: 156px; line-height: 0.86; }
  .stat .sub { margin-top: 10px; font-family: 'Major Mono Display', monospace; font-size: 11px; opacity: 0.55; letter-spacing: 0.18em; }
  .stat.warn .num { color: var(--warn); }
  .stat.ok .num { color: var(--hi); }

  .lower { display: grid; grid-template-columns: 1.4fr 1fr; gap: 28px; margin-bottom: 60px; }
  .panel { background: var(--concrete); border-top: 4px solid var(--hi); padding: 32px; }
  .panel.warn { border-top-color: var(--warn); }
  .panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
  .panel-head .h { font-family: 'Archivo Black', sans-serif; text-transform: uppercase; letter-spacing: 0.15em; font-size: 14px; }
  .panel-head .tag { font-family: 'Major Mono Display', monospace; font-size: 10px; padding: 4px 8px; background: var(--ink); color: var(--hi); letter-spacing: 0.2em; }
  .row { display: grid; grid-template-columns: 130px 1fr 50px; align-items: center; gap: 18px; padding: 12px 0; border-bottom: 1px dashed var(--line); }
  .row:last-child { border-bottom: none; }
  .row .nm { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 0.04em; }
  .row .bar { height: 22px; background: var(--ink); position: relative; overflow: hidden; }
  .row .bar::after { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: var(--w, 50%); background: var(--hi); background-image: repeating-linear-gradient(135deg, transparent 0 8px, rgba(0,0,0,0.18) 8px 10px); }
  .row.warn .bar::after { background: var(--warn); background-image: repeating-linear-gradient(135deg, transparent 0 8px, rgba(0,0,0,0.18) 8px 10px); }
  .row .v { font-family: 'Bebas Neue', sans-serif; font-size: 28px; text-align: right; color: var(--hi); }

  .actions { display: flex; gap: 12px; align-items: center; padding-bottom: 36px; }
  .btn { font-family: 'Archivo Black', sans-serif; text-transform: uppercase; letter-spacing: 0.18em; font-size: 13px; padding: 16px 26px; text-decoration: none; transition: transform 0.1s; }
  .btn.primary { background: var(--hi); color: var(--ink); }
  .btn.ghost { background: transparent; color: var(--hi); border: 2px solid var(--hi); }
  .btn:hover { transform: translateY(-2px); }
  .footer-meta { font-family: 'Major Mono Display', monospace; font-size: 11px; letter-spacing: 0.2em; opacity: 0.4; margin-left: auto; }
</style>
</head>
<body>
<div class="hazard"></div>
<div class="container">
  <div class="top">
    <div class="badge"><span class="dot"></span> HELPDESK // OPS BAY 04</div>
    <div class="meta">SHIFT 02 · 14:22:08 · TUE 07.05.2026</div>
  </div>
  <section class="hero">
    <h1>QUEUE<br>STATUS<br><span class="acc">FORTY-FOUR</span></h1>
    <div class="side">
      <div class="lab">> SHIFT NOTES</div>
      <div class="txt">Twelve units in intake, seven on the line. Closure rate steady. No critical incidents in the last six hours. Recommend agent.k take a break — eight straight closes today.</div>
    </div>
  </section>
  <section class="stats">
    <div class="stat" data-num="12"><div class="lab">▣ Open</div><div class="num">12</div><div class="sub">awaiting agent</div></div>
    <div class="stat warn" data-num="07"><div class="lab">▣ In progress</div><div class="num">07</div><div class="sub">live workload</div></div>
    <div class="stat ok" data-num="25"><div class="lab">▣ Closed</div><div class="num">25</div><div class="sub">this week</div></div>
  </section>
  <section class="lower">
    <div class="panel">
      <div class="panel-head"><div class="h">Category breakdown</div><div class="tag">/CAT</div></div>
      <div class="row"><div class="nm">Software</div><div class="bar" style="--w:100%"></div><div class="v">18</div></div>
      <div class="row"><div class="nm">Hardware</div><div class="bar" style="--w:61%"></div><div class="v">11</div></div>
      <div class="row"><div class="nm">Network</div><div class="bar" style="--w:44%"></div><div class="v">08</div></div>
      <div class="row"><div class="nm">Access</div><div class="bar" style="--w:38%"></div><div class="v">07</div></div>
    </div>
    <div class="panel warn">
      <div class="panel-head"><div class="h">Priority mix</div><div class="tag">/PRI</div></div>
      <div class="row warn"><div class="nm">High</div><div class="bar" style="--w:41%"></div><div class="v">09</div></div>
      <div class="row"><div class="nm">Medium</div><div class="bar" style="--w:100%"></div><div class="v">22</div></div>
      <div class="row"><div class="nm">Low</div><div class="bar" style="--w:59%"></div><div class="v">13</div></div>
    </div>
  </section>
  <div class="actions">
    <a href="/admin/tickets" class="btn primary">▶ Open queue</a>
    <a href="/admin/users" class="btn ghost">⚙ Manage users</a>
    <span class="footer-meta">DESIGN 04 / 10 · BEBAS + ARCHIVO + MAJOR MONO</span>
  </div>
</div>
<div class="hazard"></div>
</body>
</html>
