<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>The Helpdesk Daily — No. 142</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,700;1,9..144,400&family=IBM+Plex+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root { --paper:#f4efe6; --ink:#1a1815; --accent:#b1331e; --muted:#7a7368; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'Fraunces', serif;
    font-feature-settings: 'liga' 1, 'ss01' 1;
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    background-image:
      radial-gradient(circle at 1px 1px, rgba(26,24,21,0.045) 1px, transparent 0),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.06'/></svg>");
    background-size: 24px 24px, 200px 200px;
  }
  .mono { font-family: 'IBM Plex Mono', monospace; }
  .container { max-width: 1280px; margin: 0 auto; padding: 0 56px; }
  .masthead {
    border-bottom: 2px solid var(--ink);
    padding: 24px 0 20px;
    display: flex; justify-content: space-between; align-items: baseline;
  }
  .nameplate { font-size: 13px; text-transform: uppercase; letter-spacing: 0.32em; font-weight: 500; }
  .issue { font-family: 'IBM Plex Mono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.2em; color: var(--muted); }
  .hero {
    padding: 96px 0 80px;
    border-bottom: 1px solid var(--ink);
    display: grid; grid-template-columns: 7fr 5fr; gap: 80px;
  }
  .kicker {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.3em;
    color: var(--accent); margin-bottom: 28px;
    animation: rise 0.7s cubic-bezier(.2,.7,.2,1) both;
  }
  h1 {
    font-size: clamp(64px, 9vw, 138px);
    line-height: 0.88; font-weight: 300; letter-spacing: -0.04em;
    font-variation-settings: 'opsz' 144;
    animation: rise 0.9s cubic-bezier(.2,.7,.2,1) 0.1s both;
  }
  h1 em { font-style: italic; font-weight: 400; color: var(--accent); }
  .lede {
    font-size: 19px; line-height: 1.55; color: var(--muted);
    max-width: 36ch; align-self: end;
    animation: rise 0.9s cubic-bezier(.2,.7,.2,1) 0.25s both;
  }
  .lede::first-letter { font-family: 'Fraunces', serif; font-size: 1.5em; font-weight: 700; color: var(--ink); }
  @keyframes rise { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: none; } }

  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); border-bottom: 1px solid var(--ink); }
  .stat-cell { padding: 56px 32px 56px 0; border-right: 1px solid var(--ink); animation: rise 0.8s cubic-bezier(.2,.7,.2,1) both; }
  .stat-cell:nth-child(1) { animation-delay: 0.35s; }
  .stat-cell:nth-child(2) { animation-delay: 0.45s; padding-left: 32px; }
  .stat-cell:nth-child(3) { animation-delay: 0.55s; border-right: none; padding-left: 32px; padding-right: 0; }
  .stat-label { font-family: 'IBM Plex Mono', monospace; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3em; color: var(--muted); margin-bottom: 24px; }
  .stat-value { font-size: 124px; line-height: 1; font-weight: 300; letter-spacing: -0.04em; font-variation-settings: 'opsz' 144; }
  .stat-meta { margin-top: 20px; font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--muted); }

  .lower { padding: 80px 0; display: grid; grid-template-columns: repeat(12, 1fr); gap: 40px; }
  .col-cat { grid-column: 1 / 7; }
  .col-pri { grid-column: 8 / 13; }
  .section-head { font-family: 'IBM Plex Mono', monospace; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3em; color: var(--accent); border-bottom: 1px solid var(--ink); padding-bottom: 16px; margin-bottom: 24px; }
  .row { display: flex; align-items: baseline; justify-content: space-between; border-bottom: 1px dotted rgba(26,24,21,0.3); padding: 18px 0; }
  .row-label { font-size: 22px; }
  .row-value { font-family: 'IBM Plex Mono', monospace; font-size: 13px; color: var(--muted); }

  .colophon { border-top: 2px solid var(--ink); padding: 32px 0; display: flex; justify-content: space-between; font-family: 'IBM Plex Mono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.2em; color: var(--muted); }
  .colophon a { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; margin-right: 24px; }
  .colophon a:hover { color: var(--accent); }
</style>
</head>
<body>
<div class="container">
  <header class="masthead">
    <div class="nameplate">The Helpdesk Daily</div>
    <div class="issue">No. 142 · Tue 7 May 2026 · Admin Edition</div>
  </header>
  <section class="hero">
    <div>
      <div class="kicker">— The State of the Queue</div>
      <h1>Forty-four<br>tickets, <em>twelve</em><br>still unread.</h1>
    </div>
    <p class="lede">The week opens slowly. Open tickets edge upward but agents are clearing them faster than they arrive. Software remains the loudest neighbour on the queue, while access requests have all but vanished.</p>
  </section>
  <section class="grid-3">
    <div class="stat-cell"><div class="stat-label">Open</div><div class="stat-value">12</div><div class="stat-meta">awaiting an agent</div></div>
    <div class="stat-cell"><div class="stat-label">In Progress</div><div class="stat-value">07</div><div class="stat-meta">currently being worked</div></div>
    <div class="stat-cell"><div class="stat-label">Closed</div><div class="stat-value">25</div><div class="stat-meta">this week · ↑ 12%</div></div>
  </section>
  <section class="lower">
    <div class="col-cat">
      <h2 class="section-head">By Category</h2>
      <div class="row"><span class="row-label">Software</span><span class="row-value">— 18</span></div>
      <div class="row"><span class="row-label">Hardware</span><span class="row-value">— 11</span></div>
      <div class="row"><span class="row-label">Network</span><span class="row-value">— 08</span></div>
      <div class="row"><span class="row-label">Access request</span><span class="row-value">— 07</span></div>
    </div>
    <div class="col-pri">
      <h2 class="section-head">By Priority</h2>
      <div class="row"><span class="row-label">High</span><span class="row-value">— 09</span></div>
      <div class="row"><span class="row-label">Medium</span><span class="row-value">— 22</span></div>
      <div class="row"><span class="row-label">Low</span><span class="row-value">— 13</span></div>
    </div>
  </section>
  <footer class="colophon">
    <div>
      <a href="/admin/tickets">Read the full index →</a>
      <a href="/admin/users">Subscriber roster</a>
    </div>
    <div>Design 01 · Fraunces & IBM Plex Mono</div>
  </footer>
</div>
</body>
</html>
