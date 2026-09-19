@extends('layouts.app')

@section('title', 'Paramètres')
@section('crumb', 'FiscalTrack / Compte / Paramètres')

@section('content')
<section class="section" id="sec-settings">
  <div class="section-head">
    <div>
      <h2>Paramètres</h2>
      <p>Préférences d'affichage et d'alertes pour votre session FiscalTrack.</p>
    </div>
  </div>

  <div class="grid-2">
    <div class="panel">
      <div class="panel-head">
        <div><h3>Apparence</h3><div class="sub">Appliquées immédiatement sur cet appareil.</div></div>
      </div>
      <div style="padding:8px 18px 18px;">
        <div class="settings-row">
          <div>
            <div class="cell-strong">Mode sombre</div>
            <div class="sub">Réduit la luminosité pour le travail prolongé.</div>
          </div>
          <label class="switch">
            <input type="checkbox" id="set-dark">
            <span class="slider"></span>
          </label>
        </div>
        <div class="settings-row">
          <div>
            <div class="cell-strong">Densité compacte des listes</div>
            <div class="sub">Réduit l'espacement des tableaux de suivi.</div>
          </div>
          <label class="switch">
            <input type="checkbox" id="set-compact">
            <span class="slider"></span>
          </label>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div><h3>Alertes fiscales</h3><div class="sub">Rappels d'échéances J−7 / jour J / retard.</div></div>
      </div>
      <div style="padding:8px 18px 18px;">
        <div class="settings-row">
          <div>
            <div class="cell-strong">Badge notifications</div>
            <div class="sub">Affiche le compteur d'alertes non lues dans la barre.</div>
          </div>
          <label class="switch">
            <input type="checkbox" id="set-notif-badge" checked>
            <span class="slider"></span>
          </label>
        </div>
        <div class="settings-row">
          <div>
            <div class="cell-strong">Prioriser les retards</div>
            <div class="sub">Met en avant les échéances dépassées dans le menu.</div>
          </div>
          <label class="switch">
            <input type="checkbox" id="set-notif-late" checked>
            <span class="slider"></span>
          </label>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
          <button class="btn btn-ghost" type="button" onclick="goTo('notifications')">Voir les notifications</button>
          <button class="btn btn-ghost" type="button" onclick="markAllRead()">Tout marquer comme lu</button>
        </div>
      </div>
    </div>
  </div>

  <div class="panel" style="margin-top:16px;">
    <div class="panel-head">
      <div><h3>Compte connecté</h3><div class="sub">{{ $profile['name'] }} · {{ $profile['email'] }} · {{ $profile['role_label'] }}</div></div>
      <button class="btn btn-ghost" type="button" onclick="goTo('profile')">Modifier mon profil</button>
    </div>
    <div style="padding:14px 18px 18px;font-size:12.5px;color:var(--text-600);line-height:1.55;">
      Les préférences d'apparence sont enregistrées sur cet appareil. Le profil et le mot de passe sont enregistrés sur le serveur FiscalTrack.
    </div>
  </div>
</section>
@endsection

@push('scripts')
<style>
.settings-row{
  display:flex;align-items:center;justify-content:space-between;gap:16px;
  padding:14px 0;border-bottom:1px solid var(--line);
}
.settings-row:last-child{border-bottom:none;}
.settings-row .sub{font-size:12px;color:var(--text-400);margin-top:3px;}
.switch{position:relative;display:inline-block;width:44px;height:24px;flex:none;}
.switch input{opacity:0;width:0;height:0;}
.switch .slider{
  position:absolute;cursor:pointer;inset:0;background:#c5cede;border-radius:999px;transition:.2s;
}
.switch .slider:before{
  content:"";position:absolute;height:18px;width:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s;
  box-shadow:0 1px 3px rgba(16,29,71,.2);
}
.switch input:checked + .slider{background:var(--indigo-600);}
.switch input:checked + .slider:before{transform:translateX(20px);}
body.compact-ui table td, body.compact-ui table th{padding-top:7px;padding-bottom:7px;}
body.compact-ui .panel-head{padding-top:10px;padding-bottom:10px;}
</style>
<script>
function prefGet(key, def){
  try{ const v = localStorage.getItem(key); return v===null ? def : v; }catch(e){ return def; }
}
function prefSet(key, val){
  try{ localStorage.setItem(key, val); }catch(e){}
}

function applyCompact(on){
  document.body.classList.toggle('compact-ui', !!on);
  prefSet('fiscaltrack-compact', on ? '1' : '0');
}
function applyNotifBadge(on){
  prefSet('fiscaltrack-notif-badge', on ? '1' : '0');
  const navBadge = document.getElementById('navNotifBadge');
  const notifDot = document.getElementById('notifDot');
  if(!on){
    if(navBadge) navBadge.style.display = 'none';
    if(notifDot) notifDot.style.display = 'none';
  } else if(typeof renderNotifications === 'function'){
    renderNotifications();
  }
}
function applyNotifLate(on){
  prefSet('fiscaltrack-notif-late', on ? '1' : '0');
  if(typeof refreshNotifications === 'function') refreshNotifications();
}

(function initSettings(){
  const dark = prefGet('fiscaltrack-dark','0')==='1';
  const compact = prefGet('fiscaltrack-compact','0')==='1';
  const badge = prefGet('fiscaltrack-notif-badge','1')!=='0';
  const late = prefGet('fiscaltrack-notif-late','1')!=='0';

  const darkEl = document.getElementById('set-dark');
  const compactEl = document.getElementById('set-compact');
  const badgeEl = document.getElementById('set-notif-badge');
  const lateEl = document.getElementById('set-notif-late');

  if(darkEl){ darkEl.checked = dark; darkEl.addEventListener('change', ()=> applyDarkMode(darkEl.checked)); }
  if(compactEl){ compactEl.checked = compact; applyCompact(compact); compactEl.addEventListener('change', ()=> applyCompact(compactEl.checked)); }
  if(badgeEl){ badgeEl.checked = badge; applyNotifBadge(badge); badgeEl.addEventListener('change', ()=> applyNotifBadge(badgeEl.checked)); }
  if(lateEl){ lateEl.checked = late; lateEl.addEventListener('change', ()=> applyNotifLate(lateEl.checked)); }
})();
</script>
@endpush
