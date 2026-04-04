<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --account-ink:#1f2a24;
        --account-primary:#0f5a46;
        --account-accent:#b6842f;
        --account-primary-rgb:15 90 70;
        --account-accent-rgb:182 132 47;
        --account-clay:#9d5a3b;
        --account-line:rgba(130,94,38,.16);
        --account-soft:rgba(255,252,246,.94);
        --account-cream:#fbf7ef;
    }
    body.mobile-account-shell{
        background:
            radial-gradient(circle at top left, rgb(var(--account-accent-rgb) / .14), transparent 22%),
            radial-gradient(circle at top right, rgb(var(--account-primary-rgb) / .12), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);
        color:var(--account-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-account-shell{
        font-family:'Cairo','Manrope',system-ui,sans-serif;
    }
    body.mobile-account-shell aside{
        background:rgba(252,248,241,.98);
        color:var(--account-ink);
    }
    body.mobile-account-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--account-primary-rgb) / .92) 56%, rgba(48,33,15,.86)) !important;
    }
    body.mobile-account-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--account-primary-rgb) / .92) 56%, rgba(48,33,15,.86));
        box-shadow:0 16px 36px -24px rgba(28,22,10,.55);
    }
    .mpa-page{min-height:100vh}
    .mpa-shell{padding-inline:1rem}
    .mpa-state,
    .mpa-card{
        border:1px solid var(--account-line);
        border-radius:1.8rem;
        background:var(--account-soft);
        box-shadow:0 22px 46px -34px rgba(55,38,12,.36);
    }
    .mpa-state{
        padding:2rem 1.35rem;
        text-align:center;
    }
    .mpa-hero{
        position:relative;
        overflow:hidden;
        border-radius:2rem;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.16), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--account-primary-rgb) / .92) 52%, rgb(var(--account-accent-rgb) / .84));
        color:#fff8ea;
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .mpa-hero::after{
        content:"";
        position:absolute;
        inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .mpa-hero-copy{position:relative;z-index:1}
    .mpa-kicker{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,241,212,.74);
    }
    .mpa-ornament{
        height:10px;
        width:106px;
        border-radius:999px;
        background:
            linear-gradient(90deg, rgb(var(--account-primary-rgb) / .16), rgba(182,132,47,.34), rgb(var(--account-accent-rgb) / .16)),
            repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.58) 10px 14px, transparent 14px 24px);
    }
    .mpa-avatar{
        display:flex;
        height:4.6rem;
        width:4.6rem;
        flex:none;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        border-radius:1.6rem;
        background:rgba(255,255,255,.16);
        color:#fff8ea;
        font-size:1.25rem;
        font-weight:900;
        box-shadow:inset 0 0 0 1px rgba(255,255,255,.16);
    }
    .mpa-avatar img{
        height:100%;
        width:100%;
        object-fit:cover;
    }
    .mpa-chip{
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.08);
        padding:.68rem .95rem;
        font-size:.67rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:rgba(255,248,236,.88);
        backdrop-filter:blur(12px);
    }
    .mpa-chip::before{
        content:"";
        width:.45rem;
        height:.45rem;
        border-radius:999px;
        background:#f6cb74;
        box-shadow:0 0 0 4px rgba(182,132,47,.16);
    }
    .mpa-stat{
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.08);
        border-radius:1.35rem;
        padding:.95rem 1rem;
        backdrop-filter:blur(14px);
    }
    .mpa-stat-label{
        font-size:.64rem;
        font-weight:700;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:rgba(255,244,221,.62);
    }
    .mpa-stat-value{
        margin-top:.35rem;
        font-size:1rem;
        line-height:1.2;
        font-weight:800;
        color:#fff8ea;
    }
    .mpa-section-head{
        display:flex;
        align-items:end;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:1rem;
    }
    .mpa-section-kicker{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.2em;
        text-transform:uppercase;
        color:rgb(var(--account-accent-rgb));
    }
    .mpa-section-title{
        margin-top:.35rem;
        font-size:1.45rem;
        line-height:1.08;
        font-weight:900;
        letter-spacing:-.04em;
        color:var(--account-ink);
    }
    .mpa-section-text{
        margin-top:.55rem;
        color:#5f655f;
        font-size:.95rem;
        line-height:1.8;
    }
    .mpa-button{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.55rem;
        border-radius:1.15rem;
        padding:.95rem 1rem;
        font-size:.78rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        text-decoration:none;
        transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .mpa-button:hover{
        transform:translateY(-1px);
    }
    .mpa-button-primary{
        background:linear-gradient(135deg, var(--account-primary), rgb(var(--account-accent-rgb)));
        color:#fff8ea;
        box-shadow:0 18px 34px -18px rgba(15,90,70,.8);
    }
    .mpa-button-secondary{
        border:1px solid rgba(130,94,38,.16);
        background:rgba(255,255,255,.82);
        color:var(--account-ink);
        box-shadow:0 18px 32px -28px rgba(55,38,12,.35);
    }
    .mpa-icon-box{
        display:flex;
        height:2.6rem;
        width:2.6rem;
        flex:none;
        align-items:center;
        justify-content:center;
        border-radius:1rem;
        background:rgba(15,90,70,.1);
        color:var(--account-primary);
    }
    .mpa-list{
        display:grid;
        gap:.8rem;
    }
    .mpa-list-row{
        display:flex;
        align-items:flex-start;
        gap:.85rem;
        border-radius:1.25rem;
        border:1px solid rgba(130,94,38,.12);
        background:rgba(255,255,255,.7);
        padding:.9rem .95rem;
    }
    .mpa-row-meta{
        min-width:0;
        flex:1;
    }
    .mpa-row-label{
        font-size:.7rem;
        font-weight:800;
        letter-spacing:.16em;
        text-transform:uppercase;
        color:#8b846f;
    }
    .mpa-row-value{
        margin-top:.35rem;
        color:var(--account-ink);
        font-size:.94rem;
        line-height:1.7;
        font-weight:800;
        word-break:break-word;
    }
    .mpa-note{
        border-radius:1.25rem;
        border:1px dashed rgba(130,94,38,.26);
        background:rgba(255,248,235,.78);
        padding:1rem;
        color:#5f655f;
        font-size:.92rem;
        line-height:1.8;
    }
    .mpa-action-grid,
    .mpa-metric-grid{
        display:grid;
        gap:.85rem;
        grid-template-columns:repeat(2, minmax(0, 1fr));
    }
    .mpa-action-card,
    .mpa-metric-card{
        display:flex;
        flex-direction:column;
        gap:.75rem;
        border:1px solid rgba(130,94,38,.14);
        border-radius:1.45rem;
        background:rgba(255,255,255,.78);
        padding:1rem;
        text-decoration:none;
        color:var(--account-ink);
        box-shadow:0 20px 44px -34px rgba(55,38,12,.34);
        transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .mpa-action-card:hover,
    .mpa-metric-card:hover{
        transform:translateY(-2px);
        border-color:rgba(182,132,47,.32);
        box-shadow:0 26px 48px -34px rgba(55,38,12,.44);
    }
    .mpa-action-title,
    .mpa-metric-value{
        font-size:1.08rem;
        font-weight:900;
        color:var(--account-ink);
        line-height:1.15;
    }
    .mpa-action-text,
    .mpa-metric-label{
        font-size:.74rem;
        line-height:1.7;
        font-weight:700;
        color:#6f746b;
        text-transform:uppercase;
        letter-spacing:.1em;
    }
    .mpa-action-meta{
        font-size:.82rem;
        line-height:1.7;
        color:#5f655f;
    }
    .mpa-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        padding:.42rem .72rem;
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
    }
    .mpa-pill.success{
        background:rgba(15,90,70,.12);
        color:var(--account-primary);
    }
    .mpa-pill.warn{
        background:rgba(182,132,47,.14);
        color:rgb(var(--account-accent-rgb));
    }
    .mpa-pill.soft{
        background:rgba(130,94,38,.08);
        color:#6e6759;
    }
    .mpa-stack{
        display:grid;
        gap:1rem;
    }
    .mpa-progress{
        display:grid;
        gap:.7rem;
    }
    .mpa-progress-row{
        display:grid;
        gap:.45rem;
    }
    .mpa-progress-label{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        font-size:.82rem;
        font-weight:800;
        color:var(--account-ink);
    }
    .mpa-progress-bar{
        height:.58rem;
        overflow:hidden;
        border-radius:999px;
        background:rgba(130,94,38,.12);
    }
    .mpa-progress-fill{
        height:100%;
        border-radius:999px;
        background:linear-gradient(90deg, var(--account-primary), rgb(var(--account-accent-rgb)));
    }
    .mpa-timeline{
        display:grid;
        gap:.8rem;
    }
    .mpa-timeline-item{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        border:1px solid rgba(130,94,38,.12);
        border-radius:1.3rem;
        background:rgba(255,255,255,.76);
        padding:1rem;
    }
    .mpa-danger{
        border-color:rgba(190,24,93,.14);
        background:rgba(255,241,242,.64);
    }
    .mpa-danger button{
        width:100%;
    }
    .mpa-spinner{
        height:2.6rem;
        width:2.6rem;
        border:3px solid rgba(182,132,47,.18);
        border-top-color:var(--account-primary);
        border-radius:999px;
        animation:mpa-spin .85s linear infinite;
        margin-inline:auto;
    }
    @keyframes mpa-spin{
        to{transform:rotate(360deg)}
    }
    @media (min-width:640px){
        .mpa-shell{
            padding-inline:1.5rem;
        }
    }
</style>
