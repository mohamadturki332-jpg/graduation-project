<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Helpdesk · Riso Edition</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Recoleta&family=Recoleta:wght@700&family=DM+Mono:wght@300;400;500&family=Caprasimo&display=swap" rel="stylesheet">
<style>
  :root { --paper:#efe4cf; --ink:#15140f; --blue:#3850e3; --red:#ec3d3d; --grain:#c5b89c; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    font-family: 'DM Mono', monospace;
    background: var(--paper); color: var(--ink);
    min-height: 100vh; padding: 0;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='1.4' numOctaves='2' seed='4'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.18'/></svg>");
    background-size: 180px 180px;
    overflow-x: hidden;
  }
  body::before {
    content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 100; mix-blend-mode: multiply; opacity: 0.55;
    background: radial-gradient(circle at 1.5px 1.5px, var(--grain) 0.8px, transparent 0);
    background-size: 4px 4px;
  }
  .crosshair { position: fixed; width: 22px; height: 22px; pointer-events: none; }
  .crosshair::before, .crosshair::after { content: ''; position: absolute; background: var(--ink); }
  .crosshair::before { left: 50%; top: 0; bottom: 0; width: 1px; transform: translateX(-50%); }
  .crosshair::after { top: 50%; left: 0; right: 0; height: 1px; transform: translateY(-50%); }
  .ch-tl { top: 12px; left: 12px; }
  .ch-tr { top: 12px; right: 12px; }
  .ch-bl { bottom: 12px; left: 12px; }
  .ch-br { bottom: 12px; right: 12px; }

  .container { max-width: 1180px; margin: 0 auto; padding: 56px 64px 80px; position: relative; }
  .top { display: flex; justify-content: space-between; align-items: baseline; font-size: 11px; text-transform: uppercase; letter-spacing: 0.18em; color: rgba(21,20,15,0.72); }
  .top .stamp { border: 1px solid var(--ink); padding: 4px 8px; transform: rotate(-2deg); }
  .hero { margin: 56px 0 64px; position: relative; }
  .hero h1 {
    font-family: 'Caprasimo', serif; font-weight: 400;
    font-size: clamp(80px, 13vw, 200px); line-height: 0.85; letter-spacing: -0.03em;
    color: var(--blue);
    mix-blend-mode: multiply;
    animation: pop 0.8s cubic-bezier(.2,1.2,.4,1) both;
  }
  .hero h1 .red {
    display: inline-block; color: var(--red); transform: rotate(-3deg) translateY(-12px);
    margin: 0 6px;
  }
  .hero .sub {
    position: absolute; right: 0; top: 50%;
    width: 280px; max-width: 40%;
    background: var(--red); color: var(--paper);
    padding: 14px 18px; transform: rotate(2deg);
    font-size: 13px; line-height: 1.45;
    box-shadow: 6px 6px 0 var(--ink);
    animation: pop 0.8s cubic-bezier(.2,1.2,.4,1) 0.2s both;
  }
  @keyframes pop { from { opacity: 0; transform: translateY(20px) rotate(-2deg); } to { opacity: 1; } }

  .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; margin: 80px 0 60px; }
  .stat {
    border: 2px solid var(--ink); padding: 22px 22px 26px; position: relative;
    background: var(--paper);
    box-shadow: 8px 8px 0 var(--blue);
    animation: pop 0.7s cubic-bezier(.2,1.2,.4,1) both;
  }
  .stat:nth-child(2) { box-shadow: 8px 8px 0 var(--red); animation-delay: 0.1s; transform: translateY(14px); }
  .stat:nth-child(3) { animation-delay: 0.2s; transform: translateY(-6px); }
  .stat .lab { font-size: 11px; text-transform: uppercase; letter-spacing: 0.25em; color: rgba(21,20,15,0.7); }
  .stat .num { font-family: 'Caprasimo', serif; font-size: 88px; line-height: 1; margin-top: 4px; color: var(--ink); }
  .stat .meta { margin-top: 10px; font-size: 11px; color: rgba(21,20,15,0.6); }

  .lower { display: grid; grid-template-columns: 1.4fr 1fr; gap: 36px; }
  .panel { border: 2px solid var(--ink); padding: 24px 28px; background: var(--paper); position: relative; }
  .panel-head { font-size: 11px; text-transform: uppercase; letter-spacing: 0.25em; padding-bottom: 12px; border-bottom: 2px dotted var(--ink); margin-bottom: 16px; }
  .row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px dotted rgba(21,20,15,0.3); }
  .row:last-child { border-bottom: none; }
  .row .nm { font-family: 'Caprasimo', serif; font-size: 22px; min-width: 140px; }
  .row .bar { flex: 1; height: 14px; background: var(--paper); border: 1.5px solid var(--ink); position: relative; }
  .row .bar::after { content: ''; position: absolute; inset: 0; width: var(--w, 50%); background: var(--blue); mix-blend-mode: multiply; }
  .row.red .bar::after { background: var(--red); }
  .row .v { font-size: 13px; min-width: 30px; text-align: right; }

  .actions { margin-top: 56px; display: flex; gap: 14px; align-items: center; }
  .btn {
    display: inline-block; padding: 12px 22px; font-family: 'Caprasimo', serif; font-size: 18px;
    border: 2px solid var(--ink); background: var(--ink); color: var(--paper);
    text-decoration: none; box-shadow: 5px 5px 0 var(--red);
    transition: transform 0.12s;
  }
  .btn:hover { transform: translate(-2px,-2px); box-shadow: 7px 7px 0 var(--red); }
  .btn.ghost { background: var(--paper); color: var(--ink); box-shadow: 5px 5px 0 var(--blue); }
  .footer-note { margin-top: 40px; font-size: 11px; color: rgba(21,20,15,0.55); text-align: center; text-transform: uppercase; letter-spacing: 0.2em; }
</style>
</head>
<body>
<span class="crosshair ch-tl"></span><span class="crosshair ch-tr"></span><span class="crosshair ch-bl"></span><span class="crosshair ch-br"></span>
<div class="container">
  <header class="top">
    <span>HELPDESK · VOL. 1 · ED. 03</span>
    <span class="stamp">PRINTED 07.05.2026</span>
  </header>
  <section class="hero">
    <h1>Forty<span class="red">·four</span><br>tickets.</h1>
    <div class="sub">A loud week for the queue. Twelve still waiting. Read on if you've got a minute.</div>
  </section>
  <section class="stats">
    <div class="stat"><div class="lab">— Open</div><div class="num">12</div><div class="meta">awaiting an agent</div></div>
    <div class="stat"><div class="lab">— In progress</div><div class="num">07</div><div class="meta">being worked on</div></div>
    <div class="stat"><div class="lab">— Closed</div><div class="num">25</div><div class="meta">resolved this week</div></div>
  </section>
  <section class="lower">
    <div class="panel">
      <div class="panel-head">— Tickets by category</div>
      <div class="row"><span class="nm">Software</span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row red"><span class="nm">Hardware</span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network</span><span class="bar" style="--w:44%"></span><span class="v">08</span></div>
      <div class="row red"><span class="nm">Access</span><span class="bar" style="--w:38%"></span><span class="v">07</span></div>
    </div>
    <div class="panel">
      <div class="panel-head">— Priority mix</div>
      <div class="row red"><span class="nm">High</span><span class="bar" style="--w:41%"></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium</span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low</span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>
  <div class="actions">
    <a href="/admin/tickets" class="btn">All tickets →</a>
    <a href="/admin/users" class="btn ghost">Manage users</a>
  </div>
  <div class="footer-note">DESIGN 03 / 10 · TWO-COLOUR RISOGRAPH · CAPRASIMO + DM MONO</div>
</div>
</body>
</html>
