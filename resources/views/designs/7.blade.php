<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Helpdesk · Bauhaus Composition</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@300;400;700;900&family=DM+Mono:wght@300;400;500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<style>
  :root { --paper:#f0ece2; --ink:#0e0e0c; --red:#e23918; --yellow:#ffcb1a; --blue:#1a4ad9; --line:#0e0e0c; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'DM Mono', monospace;
    min-height: 100vh; padding: 0;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.05'/></svg>");
  }
  .container { max-width: 1280px; margin: 0 auto; padding: 36px 56px 64px; }
  .top { display: flex; justify-content: space-between; align-items: center; padding-bottom: 18px; border-bottom: 3px solid var(--ink); }
  .mark { display: flex; align-items: center; gap: 10px; }
  .mark .sq { width: 20px; height: 20px; background: var(--red); }
  .mark .ci { width: 20px; height: 20px; background: var(--yellow); border-radius: 50%; }
  .mark .tr { width: 0; height: 0; border-left: 11px solid transparent; border-right: 11px solid transparent; border-bottom: 19px solid var(--blue); }
  .mark .nm { font-family: 'Big Shoulders Display', sans-serif; font-weight: 900; font-size: 22px; letter-spacing: 0.06em; text-transform: uppercase; margin-left: 6px; }
  .meta { font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; }

  .frame { display: grid; grid-template-columns: 7fr 5fr; grid-template-rows: auto auto; gap: 20px; margin-top: 32px; }
  .h-block {
    grid-column: 1 / 2; padding: 36px 32px;
    background: var(--yellow); position: relative;
    animation: drop 0.7s cubic-bezier(.2,1,.4,1) both;
  }
  .h-block .lab { font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: 0.28em; text-transform: uppercase; }
  .h-block h1 {
    font-family: 'Big Shoulders Display', sans-serif; font-weight: 900;
    font-size: clamp(96px, 13vw, 196px); line-height: 0.85; letter-spacing: -0.01em;
    text-transform: uppercase; margin-top: 14px;
  }
  .h-block .desc { font-family: 'DM Serif Display', serif; font-style: italic; font-size: 18px; max-width: 480px; margin-top: 22px; }

  .red-stack {
    grid-column: 2 / 3; background: var(--red); color: var(--paper);
    padding: 32px 28px; display: flex; flex-direction: column; justify-content: space-between;
    animation: drop 0.7s cubic-bezier(.2,1,.4,1) 0.1s both;
  }
  .red-stack .lab { font-size: 11px; letter-spacing: 0.28em; text-transform: uppercase; opacity: 0.85; }
  .red-stack .num { font-family: 'Big Shoulders Display', sans-serif; font-weight: 900; font-size: 196px; line-height: 0.86; }
  .red-stack .note { font-family: 'DM Serif Display', serif; font-style: italic; font-size: 16px; opacity: 0.9; }

  .blue-stack {
    grid-column: 1 / 2; background: var(--blue); color: var(--paper);
    padding: 28px 32px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; align-items: end;
    animation: drop 0.7s cubic-bezier(.2,1,.4,1) 0.2s both;
  }
  .blue-stack .cell .lab { font-size: 11px; letter-spacing: 0.28em; text-transform: uppercase; opacity: 0.85; }
  .blue-stack .cell .num { font-family: 'Big Shoulders Display', sans-serif; font-weight: 700; font-size: 88px; line-height: 0.95; margin-top: 8px; }

  .donut-block { grid-column: 2 / 3; background: var(--paper); border: 3px solid var(--ink); padding: 24px 28px;
    animation: drop 0.7s cubic-bezier(.2,1,.4,1) 0.3s both;
    display: flex; align-items: center; gap: 20px;
  }
  .donut-block svg { flex: 0 0 140px; }
  .donut-block .legend { font-size: 11px; line-height: 1.9; letter-spacing: 0.05em; }
  .donut-block .legend .dot { display: inline-block; width: 9px; height: 9px; margin-right: 8px; vertical-align: middle; }
  .donut-block h3 { font-family: 'Big Shoulders Display', sans-serif; font-weight: 900; font-size: 18px; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 10px; }

  @keyframes drop { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: none; } }

  .table-block { margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .col { border: 3px solid var(--ink); padding: 24px 28px; }
  .col h3 { font-family: 'Big Shoulders Display', sans-serif; font-weight: 900; font-size: 22px; letter-spacing: 0.06em; text-transform: uppercase; padding-bottom: 14px; border-bottom: 2px solid var(--ink); margin-bottom: 14px; }
  .row { display: grid; grid-template-columns: 24px 1fr 60px; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid var(--ink); }
  .row:last-child { border-bottom: none; }
  .row .glyph { width: 18px; height: 18px; }
  .row .glyph.sq { background: var(--red); }
  .row .glyph.ci { background: var(--yellow); border-radius: 50%; }
  .row .glyph.tr { width: 0; height: 0; border-left: 9px solid transparent; border-right: 9px solid transparent; border-bottom: 16px solid var(--blue); background: transparent; }
  .row .glyph.dia { background: var(--ink); transform: rotate(45deg); width: 14px; height: 14px; margin-left: 2px; }
  .row .nm { font-family: 'Big Shoulders Display', sans-serif; font-weight: 700; font-size: 26px; letter-spacing: 0.04em; text-transform: uppercase; }
  .row .v { font-family: 'Big Shoulders Display', sans-serif; font-weight: 900; font-size: 30px; text-align: right; }

  .footer { margin-top: 40px; display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 3px solid var(--ink); }
  .actions { display: flex; gap: 12px; }
  .btn {
    font-family: 'Big Shoulders Display', sans-serif; font-weight: 900;
    text-transform: uppercase; letter-spacing: 0.1em; font-size: 18px;
    padding: 14px 22px; text-decoration: none; border: 3px solid var(--ink);
  }
  .btn.red { background: var(--red); color: var(--paper); }
  .btn.yel { background: var(--yellow); color: var(--ink); }
  .stamp { font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="mark"><span class="sq"></span><span class="ci"></span><span class="tr"></span><span class="nm">Helpdesk</span></div>
    <div class="meta">Composition № 7 / Tue 07.05.2026</div>
  </header>

  <section class="frame">
    <div class="h-block">
      <div class="lab">— Total in queue</div>
      <h1>44<br>Tickets.</h1>
      <p class="desc">A balanced week. Twelve still arriving, twenty-five resolved &mdash; agents holding the line.</p>
    </div>
    <div class="red-stack">
      <div class="lab">Open</div>
      <div class="num">12</div>
      <div class="note">awaiting an agent</div>
    </div>
    <div class="blue-stack">
      <div class="cell"><div class="lab">In progress</div><div class="num">07</div></div>
      <div class="cell"><div class="lab">Closed</div><div class="num">25</div></div>
      <div class="cell"><div class="lab">High prio</div><div class="num">09</div></div>
    </div>
    <div class="donut-block">
      <svg viewBox="0 0 42 42">
        <circle cx="21" cy="21" r="15.9" fill="none" stroke="#0e0e0c" stroke-width="6" stroke-dasharray="27 100" stroke-dashoffset="0"/>
        <circle cx="21" cy="21" r="15.9" fill="none" stroke="#ffcb1a" stroke-width="6" stroke-dasharray="16 100" stroke-dashoffset="-27"/>
        <circle cx="21" cy="21" r="15.9" fill="none" stroke="#e23918" stroke-width="6" stroke-dasharray="57 100" stroke-dashoffset="-43"/>
      </svg>
      <div>
        <h3>Status mix</h3>
        <div class="legend">
          <div><span class="dot" style="background:#0e0e0c"></span>Open · 27%</div>
          <div><span class="dot" style="background:#ffcb1a"></span>In progress · 16%</div>
          <div><span class="dot" style="background:#e23918"></span>Closed · 57%</div>
        </div>
      </div>
    </div>
  </section>

  <section class="table-block">
    <div class="col">
      <h3>By Category</h3>
      <div class="row"><span class="glyph sq"></span><span class="nm">Software</span><span class="v">18</span></div>
      <div class="row"><span class="glyph ci"></span><span class="nm">Hardware</span><span class="v">11</span></div>
      <div class="row"><span class="glyph tr"></span><span class="nm">Network</span><span class="v">08</span></div>
      <div class="row"><span class="glyph dia"></span><span class="nm">Access</span><span class="v">07</span></div>
    </div>
    <div class="col">
      <h3>By Priority</h3>
      <div class="row"><span class="glyph sq"></span><span class="nm">High</span><span class="v">09</span></div>
      <div class="row"><span class="glyph ci"></span><span class="nm">Medium</span><span class="v">22</span></div>
      <div class="row"><span class="glyph tr"></span><span class="nm">Low</span><span class="v">13</span></div>
    </div>
  </section>

  <footer class="footer">
    <div class="actions">
      <a href="/admin/tickets" class="btn red">All tickets ▸</a>
      <a href="/admin/users" class="btn yel">Manage users</a>
    </div>
    <div class="stamp">Design 07 · Big Shoulders + DM Mono + DM Serif</div>
  </footer>
</div>
</body>
</html>
