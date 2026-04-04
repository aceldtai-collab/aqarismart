<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --ma-ink:#1f2a24;
        --ma-palm:#0f5a46;
        --ma-river:#2f7a72;
        --ma-brass:#b6842f;
        --ma-clay:#9d5a3b;
        --ma-sand:#efe4cf;
        --ma-cream:#fbf7ef;
        --ma-line:rgba(130,94,38,.16);
        --ma-soft:rgba(255,252,246,.94);
        --ma-soft-strong:rgba(255,248,235,.98);
    }
    body.mobile-auth-shell{
        background:
            radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 22%),
            radial-gradient(circle at top right, rgba(15,90,70,.1), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);
        color:var(--ma-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-auth-shell{
        font-family:'Cairo','Manrope',system-ui,sans-serif;
    }
    body.mobile-auth-shell aside{
        background:rgba(252,248,241,.98);
        color:var(--ma-ink);
    }
    body.mobile-auth-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.86)) !important;
    }
    body.mobile-auth-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.86));
        box-shadow:0 16px 36px -24px rgba(28,22,10,.55);
    }
    .ma-page{min-height:100vh}
    .ma-shell{padding-inline:1rem}
    .ma-grid{
        display:grid;
        gap:1rem;
    }
    @media (min-width:1024px){
        .ma-grid{
            grid-template-columns:minmax(0, 1fr) minmax(0, 1.02fr);
            align-items:start;
        }
    }
    .ma-hero{
        position:relative;
        overflow:hidden;
        border-radius:2rem;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.88) 54%, rgba(48,33,15,.82));
        color:#fff8ea;
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .ma-hero::after{
        content:"";
        position:absolute;
        inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .ma-hero-copy,
    .ma-form-shell{
        position:relative;
        z-index:1;
    }
    .ma-kicker{
        font-size:.7rem;
        font-weight:800;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,241,212,.74);
    }
    .ma-ornament{
        height:10px;
        width:104px;
        border-radius:999px;
        background:
            linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.32), rgba(15,90,70,.16)),
            repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.56) 10px 14px, transparent 14px 24px);
    }
    .ma-chip{
        display:inline-flex;
        align-items:center;
        gap:.55rem;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.08);
        padding:.7rem .95rem;
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:rgba(255,248,236,.88);
        backdrop-filter:blur(12px);
    }
    .ma-chip::before{
        content:"";
        width:.45rem;
        height:.45rem;
        border-radius:999px;
        background:var(--ma-brass);
        box-shadow:0 0 0 4px rgba(182,132,47,.16);
    }
    .ma-stat{
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.08);
        border-radius:1.35rem;
        padding:.95rem 1rem;
        backdrop-filter:blur(14px);
    }
    .ma-stat-label{
        font-size:.65rem;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:rgba(255,244,221,.62);
        font-weight:700;
    }
    .ma-stat-value{
        margin-top:.35rem;
        font-size:1rem;
        line-height:1.2;
        font-weight:800;
        color:#fff8ea;
    }
    .ma-note{
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.08);
        border-radius:1.55rem;
        padding:1rem;
        backdrop-filter:blur(14px);
    }
    .ma-note-link{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        background:#fff8ea;
        color:#233126;
        padding:.75rem 1rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        text-decoration:none;
        box-shadow:0 18px 30px -20px rgba(12,18,14,.6);
    }
    .ma-form-shell{
        border:1px solid rgba(182,132,47,.22);
        border-radius:2rem;
        background:linear-gradient(180deg, rgba(255,249,239,.98), rgba(247,237,214,.9));
        box-shadow:0 24px 54px -36px rgba(57,42,16,.28);
    }
    .ma-form-header{
        border-bottom:1px solid rgba(182,132,47,.16);
        background:linear-gradient(180deg, rgba(255,252,246,.88), rgba(255,245,224,.65));
    }
    .ma-panel{
        border:1px solid rgba(130,94,38,.14);
        border-radius:1.5rem;
        background:var(--ma-soft);
        box-shadow:0 22px 46px -36px rgba(55,38,12,.26);
    }
    .ma-section-kicker{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.2em;
        text-transform:uppercase;
        color:var(--ma-brass);
    }
    .ma-section-title{
        margin-top:.35rem;
        font-size:1.5rem;
        line-height:1.08;
        font-weight:900;
        letter-spacing:-.04em;
        color:var(--ma-ink);
    }
    .ma-section-text{
        margin-top:.55rem;
        color:#5f655f;
        font-size:.95rem;
        line-height:1.8;
    }
    .ma-label{
        display:block;
        margin-bottom:.55rem;
        font-size:.82rem;
        font-weight:800;
        letter-spacing:.02em;
        color:#31423b;
    }
    .ma-input-wrap{
        position:relative;
    }
    .ma-input-icon{
        position:absolute;
        inset-block:0;
        inset-inline-start:0;
        display:flex;
        align-items:center;
        padding-inline-start:1rem;
        color:rgba(79,83,73,.7);
        pointer-events:none;
    }
    .ma-input{
        width:100%;
        border:1px solid rgba(130,94,38,.16);
        border-radius:1.2rem;
        background:rgba(255,255,255,.82);
        color:var(--ma-ink);
        padding:.95rem 1rem .95rem 3rem;
        font-size:.95rem;
        box-shadow:inset 0 1px 0 rgba(255,255,255,.7);
        transition:border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .ma-input::placeholder{
        color:#8a867b;
    }
    .ma-input:focus{
        outline:none;
        border-color:rgba(182,132,47,.72);
        box-shadow:0 0 0 4px rgba(182,132,47,.12);
        transform:translateY(-1px);
    }
    .ma-input.is-invalid{
        border-color:rgba(190,24,93,.45);
        box-shadow:0 0 0 4px rgba(244,114,182,.12);
    }
    .ma-submit{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:100%;
        border:none;
        border-radius:1.2rem;
        padding:.98rem 1rem;
        background:linear-gradient(135deg, var(--ma-palm), var(--ma-brass));
        color:#fff8ea;
        font-size:.84rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        box-shadow:0 18px 34px -18px rgba(15,90,70,.8);
        transition:transform .18s ease, box-shadow .18s ease, opacity .18s ease;
    }
    .ma-submit:hover{
        transform:translateY(-1px);
        box-shadow:0 22px 36px -20px rgba(15,90,70,.82);
    }
    .ma-submit:disabled{
        opacity:.62;
        cursor:not-allowed;
        transform:none;
    }
    .ma-secondary{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:1.2rem;
        border:1px solid rgba(130,94,38,.16);
        background:rgba(255,255,255,.78);
        color:var(--ma-ink);
        padding:.95rem 1rem;
        font-size:.82rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        text-decoration:none;
        box-shadow:0 18px 32px -28px rgba(55,38,12,.35);
        transition:transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }
    .ma-secondary:hover{
        transform:translateY(-1px);
        border-color:rgba(182,132,47,.42);
        box-shadow:0 20px 36px -28px rgba(55,38,12,.44);
    }
    .ma-inline-link{
        font-weight:800;
        color:var(--ma-palm);
        text-decoration:none;
    }
    .ma-inline-link:hover{
        color:var(--ma-brass);
    }
    .ma-error{
        border-radius:1.25rem;
        border:1px solid rgba(190,24,93,.16);
        background:rgba(255,241,242,.88);
        color:#a11d48;
        padding:.9rem 1rem;
        font-size:.82rem;
        line-height:1.7;
    }
    .ma-field-error{
        margin-top:.45rem;
        font-size:.74rem;
        font-weight:700;
        color:#b42318;
    }
    .ma-muted{
        color:#6b7066;
    }
    .ma-step-track{
        display:flex;
        gap:.35rem;
        align-items:flex-start;
    }
    .ma-step-item{
        display:flex;
        flex:1 1 0%;
        align-items:flex-start;
        gap:.35rem;
        min-width:0;
    }
    .ma-step-pill{
        display:flex;
        flex-direction:column;
        align-items:center;
        gap:.45rem;
        min-width:0;
    }
    .ma-step-dot{
        display:flex;
        height:2.35rem;
        width:2.35rem;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        border:1px solid rgba(130,94,38,.24);
        background:rgba(255,255,255,.75);
        color:#766e60;
        font-size:.82rem;
        font-weight:800;
        transition:all .18s ease;
        box-shadow:0 12px 24px -20px rgba(55,38,12,.4);
    }
    .ma-step-dot.is-active{
        border-color:transparent;
        background:linear-gradient(135deg, var(--ma-palm), var(--ma-brass));
        color:#fff8ea;
        box-shadow:0 18px 28px -18px rgba(15,90,70,.72);
    }
    .ma-step-dot.is-done{
        border-color:rgba(15,90,70,.12);
        background:rgba(15,90,70,.12);
        color:var(--ma-palm);
    }
    .ma-step-label{
        max-width:100%;
        font-size:.66rem;
        line-height:1.4;
        font-weight:800;
        letter-spacing:.08em;
        text-transform:uppercase;
        text-align:center;
        color:#6d7069;
    }
    .ma-step-line{
        margin-top:1.05rem;
        height:2px;
        flex:1 1 0%;
        border-radius:999px;
        background:rgba(130,94,38,.18);
    }
    .ma-step-line.is-done{
        background:linear-gradient(90deg, var(--ma-palm), var(--ma-brass));
    }
    .ma-mini-list{
        display:grid;
        gap:.7rem;
    }
    .ma-mini-item{
        display:flex;
        gap:.8rem;
        align-items:flex-start;
    }
    .ma-mini-icon{
        display:flex;
        height:2.25rem;
        width:2.25rem;
        flex:none;
        align-items:center;
        justify-content:center;
        border-radius:1rem;
        background:rgba(255,255,255,.12);
        color:#fff8ea;
    }
    .ma-meter{
        display:flex;
        flex-wrap:wrap;
        gap:.55rem;
    }
    .ma-meter span{
        border-radius:999px;
        border:1px solid rgba(130,94,38,.14);
        background:rgba(255,255,255,.72);
        color:#4a514b;
        padding:.52rem .78rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.08em;
        text-transform:uppercase;
    }
    .ma-footer-note{
        border-top:1px solid rgba(130,94,38,.12);
    }
</style>
