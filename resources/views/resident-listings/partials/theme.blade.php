<style>
    @include('public.partials.market-nav-styles')
    :root{
        --rl-ink:#1f2a24;
        --rl-palm:#0f5a46;
        --rl-river:#2f7a72;
        --rl-brass:#b6842f;
        --rl-clay:#9d5a3b;
        --rl-sand:#efe4cf;
        --rl-cream:#fbf7ef;
        --rl-line:rgba(130,94,38,.16);
    }
    .rl-shell{max-width:1320px;margin:0 auto;padding:0 1rem 4rem}
    .rl-hero{position:relative;overflow:hidden;border-radius:2rem;background:radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%),linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.92) 52%, rgba(182,132,47,.84));color:#fff8ea;box-shadow:0 30px 64px -34px rgba(28,22,10,.58)}
    .rl-hero::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);pointer-events:none}
    .rl-hero-copy{position:relative;z-index:1;padding:2rem}
    .rl-kicker{font-size:.72rem;font-weight:800;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,241,212,.74)}
    .rl-ornament{height:10px;width:106px;border-radius:999px;background:linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.34), rgba(182,132,47,.16)),repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.58) 10px 14px, transparent 14px 24px)}
    .rl-card{border:1px solid var(--rl-line);border-radius:1.8rem;background:rgba(255,252,246,.94);box-shadow:0 22px 46px -34px rgba(55,38,12,.36);padding:1.5rem}
    .rl-section-kicker{font-size:.68rem;font-weight:800;letter-spacing:.2em;text-transform:uppercase;color:var(--rl-brass)}
    .rl-section-title{margin-top:.35rem;font-size:1.6rem;line-height:1.08;font-weight:900;letter-spacing:-.04em;color:var(--rl-ink)}
    .rl-section-text{margin-top:.55rem;color:#5f655f;font-size:.95rem;line-height:1.8}
    .rl-button{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;border-radius:1.15rem;padding:.95rem 1rem;font-size:.78rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease}
    .rl-button:hover{transform:translateY(-1px)}
    .rl-button-primary{background:linear-gradient(135deg, var(--rl-palm), var(--rl-brass));color:#fff8ea;box-shadow:0 18px 34px -18px rgba(15,90,70,.8)}
    .rl-button-secondary{border:1px solid rgba(130,94,38,.16);background:rgba(255,255,255,.82);color:var(--rl-ink);box-shadow:0 18px 32px -28px rgba(55,38,12,.35)}
    .rl-pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.42rem .72rem;font-size:.68rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .rl-pill.success{background:rgba(15,90,70,.12);color:var(--rl-palm)}
    .rl-pill.warn{background:rgba(182,132,47,.14);color:var(--rl-brass)}
    .rl-pill.soft{background:rgba(130,94,38,.08);color:#6e6759}
    .rl-input,.rl-select,.rl-textarea{width:100%;border:1px solid rgba(130,94,38,.16);background:rgba(255,255,255,.82);color:var(--rl-ink);border-radius:1rem;padding:.9rem 1rem;transition:border-color .2s ease, box-shadow .2s ease}
    .rl-input:focus,.rl-select:focus,.rl-textarea:focus{outline:none;border-color:rgba(182,132,47,.72);box-shadow:0 0 0 4px rgba(182,132,47,.12)}
    .rl-label{display:block;margin-bottom:.45rem;font-size:.74rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#6f746b}
    .rl-grid{display:grid;gap:1rem}
    .rl-listing-card{display:grid;gap:1rem;border:1px solid rgba(130,94,38,.14);border-radius:1.5rem;background:rgba(255,255,255,.78);padding:1rem;box-shadow:0 20px 44px -34px rgba(55,38,12,.34)}
    .rl-thumb{width:100%;height:210px;border-radius:1.25rem;object-fit:cover;background:#e5e7eb}
    .rl-stat-grid{display:grid;gap:.75rem;grid-template-columns:repeat(4,minmax(0,1fr))}
    .rl-stat{border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);border-radius:1.35rem;padding:.95rem 1rem}
    .rl-stat-label{font-size:.64rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,244,221,.62)}
    .rl-stat-value{margin-top:.35rem;font-size:1rem;line-height:1.2;font-weight:800;color:#fff8ea}
    .rl-gallery-grid{display:grid;gap:.85rem;grid-template-columns:repeat(3,minmax(0,1fr))}
    .rl-photo-card{position:relative}
    .rl-photo-card img{width:100%;height:160px;object-fit:cover;border-radius:1rem;background:#e5e7eb}
    .rl-photo-remove{position:absolute;top:.55rem;right:.55rem;border:none;border-radius:999px;background:rgba(15,23,42,.78);color:#fff;padding:.35rem .55rem;font-size:.72rem;cursor:pointer}
    .rl-help{margin-top:.45rem;color:#7b7568;font-size:.82rem;line-height:1.7}
    .rl-upload-panel{border:1px dashed rgba(130,94,38,.26);border-radius:1.2rem;background:rgba(255,248,235,.78);padding:1rem}
    .rl-step-pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.55rem .85rem;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;border:1px solid rgba(130,94,38,.14);background:rgba(255,255,255,.78);color:#6e6759}
    .rl-step-pill.is-active{background:linear-gradient(135deg,var(--rl-palm),var(--rl-brass));color:#fff;border-color:transparent;box-shadow:0 18px 34px -18px rgba(15,90,70,.8)}
    .rl-radio-card{display:flex;align-items:flex-start;gap:.8rem;border:1px solid rgba(130,94,38,.14);border-radius:1.2rem;background:rgba(255,255,255,.78);padding:1rem;cursor:pointer;transition:border-color .18s ease,box-shadow .18s ease,transform .18s ease}
    .rl-radio-card:hover{transform:translateY(-1px);border-color:rgba(182,132,47,.32);box-shadow:0 20px 44px -34px rgba(55,38,12,.34)}
    .rl-radio-card input{margin-top:.2rem}
    .rl-summary-row{display:flex;justify-content:space-between;gap:1rem;padding:.6rem 0;border-bottom:1px solid rgba(130,94,38,.12)}
    .rl-summary-row:last-child{border-bottom:none;padding-bottom:0}
    @media (max-width: 900px){.rl-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width: 768px){.rl-hero-copy{padding:1.4rem}.rl-gallery-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
