<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT Shipping Gazette — May 7 · 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Cormorant+Garamond:wght@500;700&family=Cairo:wght@600;700&display=swap" rel="stylesheet">
<style>
  :root { --paper:#f1ead3; --ink:#1a160e; --rule:#1a160e; --soft:#5a5042; --rust:#7a3812; --teal:#1c4f4e; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'Old Standard TT', serif;
    min-height:100vh; padding:24px;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.7' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.10'/></svg>");
  }
  .ar { font-family:'Cairo',sans-serif; }
  .frame { max-width:1240px; margin:0 auto; border:1.5px solid var(--ink); padding:28px 44px 44px; background: var(--paper); }
  .frame::before, .frame::after {
    content:''; display:block; height:1px; background:var(--ink); margin:0 -44px;
  }

  .top { display:flex; justify-content:space-between; align-items:baseline; padding:6px 0; font-family:'Crimson Pro'; font-style:italic; font-size:13px; }
  .masthead { text-align:center; padding:14px 0 12px; }
  .masthead h1 {
    font-family:'Cormorant Garamond',serif; font-weight:700;
    font-size:clamp(56px, 8vw, 108px); line-height:0.95; letter-spacing:0.005em; color: var(--ink);
    animation: fade 1s ease both;
  }
  .masthead .sub { margin-top:6px; font-style:italic; font-size:16px; color:var(--soft); }
  .masthead .ar { font-family:'Cairo'; font-style:normal; font-size:18px; color:var(--rust); margin-top:4px; display:block; }
  .meta-row {
    border-top:4px double var(--ink); border-bottom:1px solid var(--ink);
    padding:10px 0; display:flex; justify-content:space-between;
    font-family:'Crimson Pro'; font-size:12px; text-transform:uppercase; letter-spacing:0.18em;
  }
  @keyframes fade { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform:none; } }

  .lead { padding:32px 0 26px; text-align:center; border-bottom:1px solid var(--ink); }
  .lead .tag { font-family:'Crimson Pro'; font-size:13px; text-transform:uppercase; letter-spacing:0.32em; color: var(--rust); margin-bottom:14px; }
  .lead h2 {
    font-family:'Cormorant Garamond',serif; font-weight:700;
    font-size:clamp(36px,5vw,68px); line-height:1.05; letter-spacing:-0.005em;
  }
  .lead h2 em { font-style:italic; color: var(--teal); }
  .lead .deck { font-style:italic; font-size:18px; color:var(--soft); max-width:680px; margin:14px auto 0; line-height:1.45; }

  .columns { padding:32px 0 14px; column-count:3; column-gap:36px; column-rule:1px solid var(--ink); }
  .columns > .article { break-inside: avoid; margin-bottom:22px; }
  .columns h3 {
    font-family:'Cormorant Garamond',serif; font-weight:700; font-size:20px;
    text-transform:uppercase; letter-spacing:0.06em;
    border-bottom:1px solid var(--ink); padding-bottom:6px; margin-bottom:10px; text-align:center;
  }
  .columns h3 small { font-family:'Cairo'; font-size:12px; color: var(--rust); display:block; margin-top:2px; letter-spacing:0; }
  .columns p { font-family:'Crimson Pro'; font-size:16px; line-height:1.55; text-align:justify; hyphens:auto; }
  .columns p::first-letter {
    font-family:'Cormorant Garamond'; font-weight:700;
    font-size:3.4em; float:left; line-height:0.85; padding:6px 8px 0 0; color: var(--rust);
  }

  .stat-strip {
    margin-top:8px; border-top:4px double var(--ink); border-bottom:4px double var(--ink); padding:24px 0;
    display:grid; grid-template-columns: repeat(3,1fr); gap:20px;
  }
  .stat-cell { text-align:center; padding:0 20px; border-right:1px solid var(--ink); animation: fade 1s ease 0.2s both; }
  .stat-cell:last-child { border-right:none; }
  .stat-cell .lab { font-family:'Crimson Pro'; font-size:12px; letter-spacing:0.3em; text-transform:uppercase; color: var(--soft); margin-bottom:8px; }
  .stat-cell .ar { font-family:'Cairo'; font-size:13px; color: var(--rust); margin-bottom:10px; }
  .stat-cell .num { font-family:'Cormorant Garamond'; font-weight:700; font-size:96px; line-height:1; }
  .stat-cell .note { font-style:italic; font-size:14px; color:var(--soft); margin-top:6px; }

  .lower { display:grid; grid-template-columns: 1fr 1fr; gap:36px; padding:24px 0; border-bottom:1px solid var(--ink); }
  .lower h4 {
    font-family:'Cormorant Garamond'; font-weight:700; font-size:16px;
    text-transform:uppercase; letter-spacing:0.18em; text-align:center;
    border-top:1px solid var(--ink); border-bottom:1px solid var(--ink); padding:8px 0; margin-bottom:14px;
  }
  .row { display:flex; justify-content:space-between; align-items:baseline; padding:6px 0; border-bottom:1px dotted rgba(26,22,14,0.4); font-family:'Crimson Pro'; font-size:17px; }
  .row:last-child { border-bottom:none; }
  .row .nm small { font-family:'Cairo'; font-size:13px; color:var(--rust); margin-left:6px; }
  .row .v { font-family:'Cormorant Garamond'; font-weight:700; font-size:22px; }

  .footer-bar { padding-top:18px; display:flex; justify-content:space-between; align-items:baseline; font-family:'Crimson Pro'; font-size:13px; }
  .footer-bar a { color: var(--ink); text-decoration:underline; text-underline-offset:4px; margin-right:22px; }
  .footer-bar a:hover { color: var(--rust); }
  .footer-bar .colophon { font-style:italic; color:var(--soft); }
</style>
</head>
<body>
<div class="frame">
  <div class="top">
    <span>Tuesday, May the Seventh, MMXXVI</span>
    <span>Price: One Riyal</span>
  </div>
  <div class="masthead">
    <h1>The NCT Shipping Gazette</h1>
    <div class="sub">— "All the cargo that's fit to log" —</div>
    <span class="ar">جريدة الميناء · الشركة الوطنية للحاويات</span>
  </div>
  <div class="meta-row">
    <span>Vol. XII · No. 142</span>
    <span>Berth 04 Edition · Dammam</span>
    <span>Six pages</span>
  </div>

  <section class="lead">
    <div class="tag">— Editorial · Yard Report —</div>
    <h2>A Steady Hand on the Quay; <em>Forty-Four</em> Crates in Play.</h2>
    <p class="deck">In a week marked more by quiet diligence than spectacle, the help desk reports forty-four open matters across the yard. Twelve await berthing; twenty-five have been brought to a clean discharge.</p>
  </section>

  <section class="columns">
    <article class="article">
      <h3>The Open Yard <small>الساحة المفتوحة</small></h3>
      <p>Twelve crates remain unassigned at the time of going to press. The longest has been waiting upon the dock for two days &mdash; a perfectly respectable figure for a terminal of this calibre, though the Editor remarks that any single crate older than three days warrants the immediate attention of an idle agent on the morning shift.</p>
    </article>
    <article class="article">
      <h3>In the Workshop <small>قيد المعالجة</small></h3>
      <p>Seven crates are presently being attended to by the agent corps. Of these, two are flagged High Priority and merit swift resolution before the next vessel docks. The remainder progress at a steady, unhurried pace, as is customary for matters concerning the access of new colleagues to the manifest systems.</p>
    </article>
    <article class="article">
      <h3>The Closed Books <small>تم الإنجاز</small></h3>
      <p>Twenty-five crates have been brought to a clean discharge in the past seven days &mdash; an uptick of twelve per cent over the previous week. The Editor extends his compliments to the agents responsible, with particular mention of agent.K, whose tally of eight resolved in a single shift sets a notable mark for the season.</p>
    </article>
  </section>

  <section class="stat-strip">
    <div class="stat-cell"><div class="lab">Open</div><div class="ar">في الساحة</div><div class="num">12</div><div class="note">awaiting an agent</div></div>
    <div class="stat-cell"><div class="lab">In Progress</div><div class="ar">قيد المعالجة</div><div class="num">07</div><div class="note">in capable hands</div></div>
    <div class="stat-cell"><div class="lab">Closed</div><div class="ar">تم الإنجاز</div><div class="num">25</div><div class="note">brought to discharge</div></div>
  </section>

  <section class="lower">
    <div>
      <h4>Cargo Categories</h4>
      <div class="row"><span class="nm">Software <small>برمجيات</small></span><span class="v">eighteen</span></div>
      <div class="row"><span class="nm">Hardware <small>أجهزة</small></span><span class="v">eleven</span></div>
      <div class="row"><span class="nm">Network <small>شبكة</small></span><span class="v">eight</span></div>
      <div class="row"><span class="nm">Access Request <small>طلب وصول</small></span><span class="v">seven</span></div>
    </div>
    <div>
      <h4>Priority Returns</h4>
      <div class="row"><span class="nm">High <small>عاجل</small></span><span class="v">nine</span></div>
      <div class="row"><span class="nm">Medium <small>متوسط</small></span><span class="v">twenty-two</span></div>
      <div class="row"><span class="nm">Low <small>منخفض</small></span><span class="v">thirteen</span></div>
    </div>
  </section>

  <footer class="footer-bar">
    <div>
      <a href="/admin/tickets">— Read the full ticket index —</a>
      <a href="/admin/users">— Subscriber roster —</a>
    </div>
    <div class="colophon">NCT/08 · Set in Cormorant Garamond, Old Standard, &amp; Crimson Pro</div>
  </footer>
</div>
</body>
</html>
