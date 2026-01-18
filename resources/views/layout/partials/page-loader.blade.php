{{-- PadangPro Fullscreen Loader (Aesthetic + Persist Across Navigation) --}}

<style>
  /* Loader visibility controlled by html.pp-loading */
  #pp-loader { display: none; }
  html.pp-loading #pp-loader { display: block; }

  :root{
    --pp-blue: rgba(59,130,246,0.95);
  }

  /* ======================
     Background Layers
     ====================== */
  .pp-bg{
    background-image:
      radial-gradient(900px 520px at 15% 25%, rgba(34,197,94,0.20), transparent 65%),
      radial-gradient(800px 520px at 85% 70%, rgba(59,130,246,0.16), transparent 68%),
      linear-gradient(to bottom right, #15803d, #064e3b),
      linear-gradient(to right, rgba(21,128,61,0.32), rgba(6,78,59,0.55));
    background-blend-mode: screen, screen, normal, overlay;
  }

  .pp-dots{
    background-image: radial-gradient(rgba(255,255,255,0.14) 1px, transparent 1px);
    background-size: 18px 18px;
  }

  .pp-pitch{
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='520' height='520' viewBox='0 0 520 520'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.18)' stroke-width='2'%3E%3Crect x='26' y='26' width='468' height='468' rx='18'/%3E%3Cline x1='260' y1='26' x2='260' y2='494'/%3E%3Ccircle cx='260' cy='260' r='64'/%3E%3Ccircle cx='260' cy='260' r='6' fill='rgba(255,255,255,0.18)' stroke='none'/%3E%3Crect x='26' y='146' width='96' height='228' rx='10'/%3E%3Crect x='398' y='146' width='96' height='228' rx='10'/%3E%3Crect x='26' y='196' width='46' height='128' rx='10'/%3E%3Crect x='448' y='196' width='46' height='128' rx='10'/%3E%3C/g%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 50% 60%;
    background-size: 520px 520px;
  }

  .pp-vignette{
    background: radial-gradient(1200px 700px at 50% 45%, transparent 45%, rgba(0,0,0,0.28) 100%);
  }

  /* ======================
     Fade Animations
     ====================== */
  #pp-loader{ animation: ppFadeIn .16s ease-out both; }
  @keyframes ppFadeIn { from {opacity:0} to {opacity:1} }

  #pp-loader.pp-fadeout{ animation: ppFadeOut .28s ease-in forwards; }
  @keyframes ppFadeOut { to { opacity: 0; } }

  /* ======================
     Spinner + Logo
     ====================== */
  .pp-spinner-wrap{
    width: 76px;
    height: 76px;
    position: relative;
    display: grid;
    place-items: center;
  }

  .pp-spinner{
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    border: 5px solid rgba(255,255,255,0.20);
    border-top-color: var(--pp-blue);
    animation: ppSpin .9s linear infinite;
    box-shadow: 0 14px 45px rgba(0,0,0,0.35);
  }

  .pp-spinner::after{
    content:"";
    position:absolute;
    inset: 10px;
    border-radius: 9999px;
    border: 4px solid rgba(255,255,255,0.18);
    border-bottom-color: rgba(255,255,255,0.55);
    animation: ppSpinReverse 1.4s linear infinite;
  }

  @keyframes ppSpin{ to { transform: rotate(360deg); } }
  @keyframes ppSpinReverse{ to { transform: rotate(-360deg); } }

  /* Centered logo */
  .pp-spinner-logo{
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    pointer-events: none;
  }

  .pp-spinner-logo img{
    width: 28px;
    height: 28px;
    object-fit: contain;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.35));
  }

  /* Progress bar */
  .pp-bar{
    height: 6px;
    border-radius: 9999px;
    background: rgba(255,255,255,0.18);
    overflow: hidden;
  }

  .pp-bar span{
    display:block;
    height:100%;
    width:40%;
    background: linear-gradient(90deg, rgba(255,255,255,0.35), rgba(59,130,246,0.9), rgba(255,255,255,0.35));
    animation: ppBar 1.15s ease-in-out infinite;
  }

  @keyframes ppBar{
    0%{ transform: translateX(-70%); }
    50%{ transform: translateX(90%); }
    100%{ transform: translateX(170%); }
  }

  @media (prefers-reduced-motion: reduce){
    *{ animation: none !important; }
  }
</style>

{{-- Show loader instantly on NEXT page --}}
<script>
  (function(){
    if (sessionStorage.getItem('pp_show_loader') === '1') {
      document.documentElement.classList.add('pp-loading');
    }
  })();
</script>

<div id="pp-loader" class="fixed inset-0 z-[9999]">
  <div class="absolute inset-0 pp-bg"></div>
  <div class="absolute inset-0 pp-dots opacity-65"></div>
  <div class="absolute inset-0 pp-pitch opacity-22"></div>
  <div class="absolute inset-0 pp-vignette"></div>

  <div class="relative h-full w-full flex items-center justify-center px-6 text-white">
    <div class="text-center max-w-md w-full">

      <!-- Brand -->
      <div class="flex items-center justify-center gap-4">
        <img src="{{ asset('images/logoPadang.png') }}" class="w-12 h-12" alt="PadangPro">
        <div class="text-left">
          <div class="text-3xl font-extrabold leading-none">
            PadangPro<span class="text-blue-300">.</span>
          </div>
          <div class="text-sm text-white/80">Reserve • Play • Repeat</div>
        </div>
      </div>

      <!-- Spinner -->
      <div class="mt-8 flex justify-center">
        <div class="pp-spinner-wrap">
          <div class="pp-spinner"></div>
          <div class="pp-spinner-logo">
            <img src="{{ asset('images/logoPadang.png') }}" alt="PadangPro">
          </div>
        </div>
      </div>

      <!-- Text -->
      <div class="mt-6 text-sm text-white/85">Loading your next page…</div>

      <!-- Progress -->
      <div class="mt-5 px-10">
        <div class="pp-bar"><span></span></div>
      </div>

      <!-- Tip -->
      <div class="mt-6 text-xs text-white/70">
        Tip: You can book faster using “Real-Time Slots”.
      </div>

    </div>
  </div>
</div>

<script>
(function(){
  const loader = document.getElementById('pp-loader');
  if (!loader) return;

  const MIN_VISIBLE_TIME = 1000;

  function hideAfterMin(){
    setTimeout(() => {
      loader.classList.add('pp-fadeout');
      setTimeout(() => {
        document.documentElement.classList.remove('pp-loading');
        loader.classList.remove('pp-fadeout');
        sessionStorage.removeItem('pp_show_loader');
      }, 280);
    }, MIN_VISIBLE_TIME);
  }

  if (document.documentElement.classList.contains('pp-loading')) {
    window.addEventListener('pageshow', hideAfterMin, { once: true });
  }

  function markNext(){ sessionStorage.setItem('pp_show_loader','1'); }

  document.addEventListener('click', e => {
    const a = e.target.closest('a');
    if (!a) return;
    const href = a.getAttribute('href') || '';
    if (!href || href.startsWith('#') || a.target === '_blank') return;
    try {
      if (new URL(href, location.origin).origin !== location.origin) return;
    } catch { return; }
    markNext();
  }, true);

  document.addEventListener('submit', e => {
    if (e.target.checkValidity && !e.target.checkValidity()) return;
    markNext();
  }, true);
})();
</script>
