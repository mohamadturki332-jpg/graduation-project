<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Helpdesk — A Quiet Overview</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Spectral:ital,wght@0,300;0,400;0,500;0,700;1,400&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root { --paper:#f6f1e7; --warm:#ede5d3; --ink:#262521; --soft:#7c7669; --line:#d9d1bf; --thread:#b8431e; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'Manrope', sans-serif; font-weight: 400;
    -webkit-font-smoothing: antialiased; min-height: 100vh;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.7' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.04'/></svg>");
  }
  .serif { font-family: 'Spectral', serif; }
  .container { max-width: 1100px; margin: 0 auto; padding: 96px 64px 96px; }
  .top { display: flex; justify-content: space-between; align-items: baseline; padding-bottom: 56px; border-bottom: 1px solid var(--line); }
  .id { font-size: 12px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--soft); }
  .id .accent { color: var(--thread); }
  .date { font-family: 'Spectral', serif; font-style: italic; font-size: 14px; color: var(--soft); }

  .intro { padding: 80px 0 56px; max-width: 760px; animation: rise 1s cubic-bezier(.2,.7,.2,1) both; }
  .eyebrow { font-size: 11px; letter-spacing: 0.32em; text-transform: uppercase; color: var(--thread); margin-bottom: 26px; }
  h1 {
    font-family: 'Spectral', serif; font-weight: 300;
    font-size: clamp(48px, 6vw, 84px); line-height: 1.05; letter-spacing: -0.02em;
  }
  h1 em { font-style: italic; font-weight: 400; }
  @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

  .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; margin: 72px 0 80px; }
  .stat { padding: 0 32px 0 0; position: relative; animation: rise 0.9s cubic-bezier(.2,.7,.2,1) both; }
  .stat:nth-child(1){ animation-delay: 0.15s; }
  .stat:nth-child(2){ animation-delay: 0.25s; padding-left: 32px; border-left: 1px solid var(--line); }
  .stat:nth-child(3){ animation-delay: 0.35s; padding-left: 32px; border-left: 1px solid var(--line); }
  .stat .lab { font-size: 11px; letter-spacing: 0.28em; text-transform: uppercase; color: var(--soft); margin-bottom: 22px; }
  .stat .num { font-family: 'Spectral', serif; font-size: 96px; line-height: 0.95; font-weight: 300; letter-spacing: -0.025em; }
  .stat .accent-line { width: 24px; height: 2px; background: var(--thread); margin: 22px 0 14px; }
  .stat .meta { font-family: 'Spectral', serif; font-style: italic; font-size: 14px; color: var(--soft); }

  .pair { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; padding: 56px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
  .pair h2 { font-family: 'Spectral', serif; font-weight: 400; font-style: italic; font-size: 22px; margin-bottom: 28px; color: var(--ink); }
  .row { display: flex; justify-content: space-between; align-items: baseline; padding: 14px 0; border-bottom: 1px solid var(--line); }
  .row:last-child { border-bottom: none; }
  .row .nm { font-family: 'Spectral', serif; font-size: 19px; }
  .row .nm .dots { color: var(--line); margin: 0 8px; }
  .row .v { font-size: 13px; color: var(--soft); letter-spacing: 0.05em; }

  .links { padding-top: 64px; display: flex; gap: 32px; align-items: baseline; }
  .links a {
    font-family: 'Spectral', serif; font-size: 19px; color: var(--ink);
    text-decoration: none; border-bottom: 1px solid var(--ink);
    padding-bottom: 4px; transition: color 0.2s, border-color 0.2s;
  }
  .links a:hover { color: var(--thread); border-bottom-color: var(--thread); }
  .links a em { font-style: italic; color: var(--thread); margin-left: 6px; }
  .footer-mark { margin-top: 80px; text-align: center; font-size: 11px; letter-spacing: 0.28em; text-transform: uppercase; color: var(--soft); }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="id">Helpdesk · <span class="accent">Admin overview</span></div>
    <div class="date">Tuesday, the seventh of May, 2026</div>
  </header>
  <section class="intro">
    <div class="eyebrow">— Where we are</div>
    <h1>Forty-four tickets — a <em>steady</em>, unremarkable week.</h1>
  </section>
  <section class="stats">
    <div class="stat"><div class="lab">Open</div><div class="num">12</div><div class="accent-line"></div><div class="meta">Awaiting an agent.</div></div>
    <div class="stat"><div class="lab">In progress</div><div class="num">07</div><div class="accent-line"></div><div class="meta">Currently in hand.</div></div>
    <div class="stat"><div class="lab">Closed</div><div class="num">25</div><div class="accent-line"></div><div class="meta">Resolved this week.</div></div>
  </section>
  <section class="pair">
    <div>
      <h2>By category</h2>
      <div class="row"><span class="nm">Software<span class="dots"> · · · · · · · · · </span></span><span class="v">eighteen</span></div>
      <div class="row"><span class="nm">Hardware<span class="dots"> · · · · · · · · · </span></span><span class="v">eleven</span></div>
      <div class="row"><span class="nm">Network<span class="dots"> · · · · · · · · · · </span></span><span class="v">eight</span></div>
      <div class="row"><span class="nm">Access request<span class="dots"> · · · </span></span><span class="v">seven</span></div>
    </div>
    <div>
      <h2>By priority</h2>
      <div class="row"><span class="nm">High<span class="dots"> · · · · · · · · · · · · </span></span><span class="v">nine</span></div>
      <div class="row"><span class="nm">Medium<span class="dots"> · · · · · · · · · · </span></span><span class="v">twenty-two</span></div>
      <div class="row"><span class="nm">Low<span class="dots"> · · · · · · · · · · · · · </span></span><span class="v">thirteen</span></div>
    </div>
  </section>
  <div class="links">
    <a href="/admin/tickets">Read the ticket index<em>↗</em></a>
    <a href="/admin/users">Roster of users<em>↗</em></a>
  </div>
  <div class="footer-mark">Design Five · Spectral &amp; Manrope · Set quietly.</div>
</div>
</body>
</html>
