<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Helpdesk · Spatial Overview</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@200;300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root { --bg-a:#0a0815; --bg-b:#1a1530; --ink:#f5f2ff; --soft:#bdb5dd; --accent:#a8ffea; --rose:#ffb4c8; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--bg-a); color: var(--ink);
    font-family: 'Manrope', sans-serif; font-weight: 300;
    -webkit-font-smoothing: antialiased; min-height: 100vh; overflow-x: hidden;
    background-image:
      radial-gradient(ellipse 60% 50% at 18% 18%, rgba(168,255,234,0.18), transparent 60%),
      radial-gradient(ellipse 55% 60% at 82% 22%, rgba(255,180,200,0.18), transparent 60%),
      radial-gradient(ellipse 70% 60% at 50% 100%, rgba(120,90,255,0.22), transparent 65%),
      linear-gradient(180deg, var(--bg-a) 0%, var(--bg-b) 100%);
  }
  body::after {
    content:''; position: fixed; inset: 0; pointer-events: none; z-index: 1;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.18'/></svg>");
    mix-blend-mode: overlay; opacity: 0.4;
  }
  .serif { font-family: 'Instrument Serif', serif; font-weight: 400; }

  .container { max-width: 1280px; margin: 0 auto; padding: 36px 56px 80px; position: relative; z-index: 10; }
  .top {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 22px; backdrop-filter: blur(20px) saturate(140%);
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 999px;
  }
  .top .nm { display: flex; align-items: center; gap: 12px; font-size: 14px; letter-spacing: 0.04em; }
  .top .nm .orb {
    width: 18px; height: 18px; border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, var(--accent), #6a8fff 60%, transparent 70%);
    box-shadow: 0 0 18px rgba(168,255,234,0.6);
  }
  .top .when { font-size: 12px; color: var(--soft); letter-spacing: 0.18em; text-transform: uppercase; }

  .hero { padding: 110px 0 60px; max-width: 920px; }
  .kicker { font-size: 12px; color: var(--accent); letter-spacing: 0.32em; text-transform: uppercase; margin-bottom: 24px; animation: fade 1s ease both; }
  h1 {
    font-family: 'Instrument Serif', serif; font-weight: 400;
    font-size: clamp(60px, 8vw, 116px); line-height: 1.02; letter-spacing: -0.025em;
    animation: fade 1.2s cubic-bezier(.2,.7,.2,1) 0.1s both;
  }
  h1 em { font-style: italic; color: var(--rose); }
  h1 .num { color: var(--accent); }
  .lede { margin-top: 24px; max-width: 580px; font-size: 17px; line-height: 1.55; color: var(--soft); animation: fade 1.2s cubic-bezier(.2,.7,.2,1) 0.25s both; }
  @keyframes fade { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

  .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin: 56px 0 24px; }
  .glass {
    backdrop-filter: blur(28px) saturate(150%);
    background: linear-gradient(150deg, rgba(255,255,255,0.10), rgba(255,255,255,0.04));
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 28px;
    box-shadow: 0 1px 0 rgba(255,255,255,0.18) inset, 0 30px 60px -20px rgba(0,0,0,0.5);
    position: relative; overflow: hidden;
  }
  .glass::before {
    content: ''; position: absolute; inset: 0; pointer-events: none; opacity: 0.5;
    background: radial-gradient(ellipse at top left, rgba(255,255,255,0.18), transparent 60%);
  }
  .stat { padding: 28px 28px 32px; animation: fade 1s cubic-bezier(.2,.7,.2,1) both; position: relative; }
  .stat:nth-child(1){ animation-delay: 0.4s; }
  .stat:nth-child(2){ animation-delay: 0.5s; }
  .stat:nth-child(3){ animation-delay: 0.6s; }
  .stat .lab { font-size: 11px; color: var(--soft); text-transform: uppercase; letter-spacing: 0.28em; }
  .stat .num { font-family: 'Instrument Serif', serif; font-size: 92px; line-height: 1; margin-top: 14px; font-weight: 400; }
  .stat .meta { margin-top: 12px; font-size: 13px; color: var(--soft); font-style: italic; font-family: 'Instrument Serif', serif; }
  .stat .pill {
    position: absolute; top: 22px; right: 22px;
    font-size: 10px; padding: 4px 10px; border-radius: 999px; letter-spacing: 0.18em; text-transform: uppercase;
  }
  .stat .pill.cy { background: rgba(168,255,234,0.18); color: var(--accent); border: 1px solid rgba(168,255,234,0.35); }
  .stat .pill.rs { background: rgba(255,180,200,0.18); color: var(--rose); border: 1px solid rgba(255,180,200,0.35); }
  .stat .pill.gn { background: rgba(160,255,160,0.16); color: #b6f3b6; border: 1px solid rgba(160,255,160,0.32); }

  .pair { display: grid; grid-template-columns: 1.4fr 1fr; gap: 18px; margin-top: 18px; }
  .panel { padding: 28px 30px; }
  .panel h3 { font-size: 11px; color: var(--soft); text-transform: uppercase; letter-spacing: 0.28em; margin-bottom: 22px; }
  .row { display: grid; grid-template-columns: 130px 1fr 60px; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid rgba(255,255,255,0.08); }
  .row:last-child { border-bottom: none; }
  .row .nm { font-family: 'Instrument Serif', serif; font-size: 21px; }
  .row .bar { height: 6px; background: rgba(255,255,255,0.08); border-radius: 99px; overflow: hidden; }
  .row .bar::after { content: ''; display: block; height: 100%; width: var(--w, 50%); background: linear-gradient(90deg, var(--accent), var(--rose)); border-radius: 99px; }
  .row .v { font-family: 'Instrument Serif', serif; font-size: 22px; text-align: right; color: var(--soft); }

  .actions { padding-top: 36px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
  .btn {
    font-size: 14px; padding: 14px 22px; border-radius: 999px; text-decoration: none;
    background: rgba(255,255,255,0.10); border: 1px solid rgba(255,255,255,0.18); color: var(--ink);
    backdrop-filter: blur(18px); transition: all 0.18s;
  }
  .btn.primary { background: var(--ink); color: var(--bg-a); border-color: var(--ink); }
  .btn:hover { transform: translateY(-1px); }
  .stamp { margin-left: auto; font-size: 11px; color: var(--soft); letter-spacing: 0.22em; text-transform: uppercase; opacity: 0.7; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="nm"><span class="orb"></span> <span>Helpdesk · Spatial overview</span></div>
    <div class="when">Tue 7 May 2026 · 14:22 GMT</div>
  </header>

  <section class="hero">
    <div class="kicker">— a calm afternoon —</div>
    <h1>You have <span class="num">44</span> tickets in the system, <em>twelve</em> still waiting on a hand.</h1>
    <p class="lede">Software is the most-asked-about category this week, and high-priority items remain comfortably below average. Nothing is on fire. Floating panels below show the breakdown.</p>
  </section>

  <section class="stats">
    <div class="stat glass"><span class="pill cy">stable</span><div class="lab">Open</div><div class="num">12</div><div class="meta">awaiting an agent</div></div>
    <div class="stat glass"><span class="pill rs">active</span><div class="lab">In progress</div><div class="num">07</div><div class="meta">currently in hand</div></div>
    <div class="stat glass"><span class="pill gn">cleared</span><div class="lab">Closed</div><div class="num">25</div><div class="meta">resolved this week</div></div>
  </section>

  <section class="pair">
    <div class="panel glass">
      <h3>— Tickets by category</h3>
      <div class="row"><span class="nm">Software</span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row"><span class="nm">Hardware</span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network</span><span class="bar" style="--w:44%"></span><span class="v">08</span></div>
      <div class="row"><span class="nm">Access request</span><span class="bar" style="--w:38%"></span><span class="v">07</span></div>
    </div>
    <div class="panel glass">
      <h3>— By priority</h3>
      <div class="row"><span class="nm">High</span><span class="bar" style="--w:41%"></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium</span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low</span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="btn primary" href="/admin/tickets">View all tickets →</a>
    <a class="btn" href="/admin/users">Manage users</a>
    <span class="stamp">Design 09 · Instrument Serif + Manrope</span>
  </div>
</div>
</body>
</html>
