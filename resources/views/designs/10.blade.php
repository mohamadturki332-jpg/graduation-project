<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>The Helpdesk Tribune — May 7th, 1926</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=UnifrakturCook:wght@700&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
<style>
  :root { --paper:#eee5cc; --ink:#1a160e; --rule:#1a160e; --soft:#5a5042; --red:#7f1810; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'Old Standard TT', serif;
    min-height: 100vh; padding: 24px;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.10'/></svg>");
  }
  .frame { max-width: 1180px; margin: 0 auto; border: 1.5px solid var(--ink); padding: 28px 40px 40px; background: var(--paper); }
  .frame::before, .frame::after {
    content:''; display: block; height: 1px; background: var(--ink); margin: 0 -40px;
  }
  .crim { font-family: 'Crimson Pro', serif; }
  .double-rule { border-top: 1px solid var(--ink); border-bottom: 4px double var(--ink); padding: 6px 0; }

  .top { font-family: 'Crimson Pro', serif; font-style: italic; font-size: 13px; display: flex; justify-content: space-between; padding: 6px 0; }
  .masthead { text-align: center; padding: 14px 0 16px; }
  .masthead h1 {
    font-family: 'UnifrakturCook', cursive; font-weight: 700;
    font-size: clamp(56px, 9vw, 124px); line-height: 0.95; letter-spacing: 0.005em;
    color: var(--ink);
    animation: fade 1s ease both;
  }
  .masthead .sub { margin-top: 4px; font-family: 'Old Standard TT', serif; font-style: italic; font-size: 16px; color: var(--soft); }
  .meta-row {
    border-top: 4px double var(--ink); border-bottom: 1px solid var(--ink);
    padding: 10px 0; display: flex; justify-content: space-between;
    font-family: 'Crimson Pro', serif; font-size: 12px; text-transform: uppercase; letter-spacing: 0.18em;
  }
  @keyframes fade { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

  .lead {
    padding: 36px 0 26px; text-align: center; border-bottom: 1px solid var(--ink);
  }
  .lead .tag { font-family: 'Crimson Pro', serif; font-size: 13px; text-transform: uppercase; letter-spacing: 0.32em; color: var(--red); margin-bottom: 16px; }
  .lead h2 {
    font-family: 'Old Standard TT', serif; font-weight: 700;
    font-size: clamp(40px, 5.5vw, 76px); line-height: 1.05; letter-spacing: -0.005em;
  }
  .lead .deck { font-family: 'Old Standard TT', serif; font-style: italic; font-size: 19px; max-width: 680px; margin: 16px auto 0; color: var(--soft); line-height: 1.45; }

  .columns { padding: 36px 0 18px; column-count: 3; column-gap: 36px; column-rule: 1px solid var(--ink); }
  .columns > .article { break-inside: avoid; margin-bottom: 22px; }
  .columns h3 {
    font-family: 'Old Standard TT', serif; font-weight: 700; font-size: 19px;
    text-transform: uppercase; letter-spacing: 0.06em;
    border-bottom: 1px solid var(--ink); padding-bottom: 6px; margin-bottom: 10px; text-align: center;
  }
  .columns p { font-family: 'Crimson Pro', serif; font-size: 16px; line-height: 1.55; text-align: justify; hyphens: auto; }
  .columns p::first-letter {
    font-family: 'UnifrakturCook', cursive; font-weight: 700;
    font-size: 3.4em; float: left; line-height: 0.85; padding: 6px 8px 0 0; color: var(--red);
  }
  .columns p.no-cap::first-letter { all: unset; }

  .stat-strip {
    margin-top: 8px; border-top: 4px double var(--ink); border-bottom: 4px double var(--ink); padding: 28px 0;
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
  }
  .stat-cell { text-align: center; padding: 0 20px; border-right: 1px solid var(--ink); animation: fade 1s ease 0.2s both; }
  .stat-cell:last-child { border-right: none; }
  .stat-cell .lab { font-family: 'Crimson Pro', serif; font-size: 12px; letter-spacing: 0.3em; text-transform: uppercase; color: var(--soft); margin-bottom: 12px; }
  .stat-cell .num { font-family: 'Old Standard TT', serif; font-size: 96px; line-height: 1; }
  .stat-cell .note { font-family: 'Old Standard TT', serif; font-style: italic; font-size: 14px; color: var(--soft); margin-top: 8px; }

  .lower { display: grid; grid-template-columns: 1fr 1fr; gap: 36px; padding: 28px 0; border-bottom: 1px solid var(--ink); }
  .lower h4 {
    font-family: 'Old Standard TT', serif; font-weight: 700; font-size: 16px;
    text-transform: uppercase; letter-spacing: 0.18em; text-align: center;
    border-top: 1px solid var(--ink); border-bottom: 1px solid var(--ink); padding: 8px 0; margin-bottom: 14px;
  }
  .row { display: flex; justify-content: space-between; align-items: baseline; padding: 6px 0; border-bottom: 1px dotted rgba(26,22,14,0.4); font-family: 'Crimson Pro', serif; font-size: 17px; }
  .row:last-child { border-bottom: none; }
  .row .v { font-family: 'Old Standard TT', serif; font-weight: 700; }

  .footer-bar { padding-top: 18px; display: flex; justify-content: space-between; align-items: baseline; font-family: 'Crimson Pro', serif; font-size: 13px; }
  .footer-bar a { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; margin-right: 22px; }
  .footer-bar a:hover { color: var(--red); }
  .footer-bar .colophon { font-style: italic; color: var(--soft); }
</style>
</head>
<body>
<div class="frame">
  <div class="top">
    <span>Tuesday, May the Seventh, MMXXVI</span>
    <span>Price: One Penny</span>
  </div>
  <div class="masthead">
    <h1>The Helpdesk Tribune</h1>
    <div class="sub">— "All the queues that's fit to print" —</div>
  </div>
  <div class="meta-row">
    <span>Vol. XII · No. 142</span>
    <span>Admin Edition</span>
    <span>Six pages</span>
  </div>

  <section class="lead">
    <div class="tag">— Editorial —</div>
    <h2>A Steady Hand at the Queue;<br>Forty-Four Tickets in Play.</h2>
    <p class="deck">In a week marked more by quiet diligence than spectacle, the help desk reports forty-four open matters across the system. Twelve await assignment; twenty-five have been brought to a satisfactory conclusion.</p>
  </section>

  <section class="columns">
    <article class="article">
      <h3>The Open Files</h3>
      <p>Twelve tickets remain unassigned at the time of going to press. The longest has been waiting upon the queue for two days &mdash; a perfectly respectable figure for a desk of this calibre, though the Editor remarks that any single ticket older than three days warrants the immediate attention of an idle agent.</p>
    </article>
    <article class="article">
      <h3>In the Workshop</h3>
      <p>Seven tickets are presently being attended to by the agent corps. Of these, two are flagged High Priority and merit swift resolution. The remainder progress at a steady, unhurried pace, as is customary for matters concerning the access of new colleagues to legacy systems.</p>
    </article>
    <article class="article">
      <h3>The Closed Books</h3>
      <p>Twenty-five tickets have been brought to conclusion in the past seven days &mdash; an uptick of twelve per cent over the previous week. The Editor extends his compliments to the agents responsible, with particular mention of agent.K, whose tally of eight resolved in a single shift sets a notable mark.</p>
    </article>
  </section>

  <section class="stat-strip">
    <div class="stat-cell"><div class="lab">Open</div><div class="num">12</div><div class="note">awaiting an agent</div></div>
    <div class="stat-cell"><div class="lab">In Progress</div><div class="num">07</div><div class="note">in capable hands</div></div>
    <div class="stat-cell"><div class="lab">Closed</div><div class="num">25</div><div class="note">brought to a close</div></div>
  </section>

  <section class="lower">
    <div>
      <h4>Categories of Note</h4>
      <div class="row"><span>Software</span><span class="v">eighteen</span></div>
      <div class="row"><span>Hardware</span><span class="v">eleven</span></div>
      <div class="row"><span>Network</span><span class="v">eight</span></div>
      <div class="row"><span>Access Request</span><span class="v">seven</span></div>
    </div>
    <div>
      <h4>Priority Returns</h4>
      <div class="row"><span>High</span><span class="v">nine</span></div>
      <div class="row"><span>Medium</span><span class="v">twenty-two</span></div>
      <div class="row"><span>Low</span><span class="v">thirteen</span></div>
    </div>
  </section>

  <footer class="footer-bar">
    <div>
      <a href="/admin/tickets">— Read the full ticket index —</a>
      <a href="/admin/users">— Subscriber roster —</a>
    </div>
    <div class="colophon">Design X &middot; Set in UnifrakturCook, Old Standard, &amp; Crimson Pro</div>
  </footer>
</div>
</body>
</html>
