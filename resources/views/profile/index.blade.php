@extends('layouts.app')

@section('title', 'Mon profil')
@section('crumb', 'FiscalTrack / Compte / Mon profil')

@section('content')
<section class="section" id="sec-profile">
  <div class="section-head">
    <div>
      <h2>Mon profil</h2>
      <p>Informations personnelles et sécurité de votre compte FiscalTrack.</p>
    </div>
  </div>

  <div class="grid-2">
    <div class="panel">
      <div class="panel-head">
        <div><h3>Identité</h3><div class="sub">Visible par l'équipe du cabinet selon vos droits.</div></div>
      </div>
      <div style="padding:16px 18px 20px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
          <div class="avatar" style="width:52px;height:52px;font-size:16px;" id="profileAvatar">{{ strtoupper(\Illuminate\Support\Str::substr($profile['name'], 0, 2)) }}</div>
          <div>
            <div class="cell-strong" id="profileNameLabel">{{ $profile['name'] }}</div>
            <div style="font-size:12.5px;color:var(--text-400);">{{ $profile['role_label'] }} · {{ $profile['status'] === 'active' ? 'Actif' : 'Suspendu' }}</div>
          </div>
        </div>
        <div class="field"><label>Nom complet</label><input id="f-profile-name" value="{{ $profile['name'] }}" maxlength="150"></div>
        <div class="field"><label>E-mail</label><input id="f-profile-email" type="email" value="{{ $profile['email'] }}"></div>
        <div class="field-row">
          <div class="field"><label>Rôle</label><input value="{{ $profile['role_label'] }}" disabled></div>
          <div class="field"><label>Membre depuis</label><input value="{{ $profile['created_at'] }}" disabled></div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:8px;">
          <button class="btn btn-primary" type="button" onclick="saveProfile()">Enregistrer</button>
        </div>
        <p id="profileMsg" style="display:none;margin:12px 0 0;font-size:12.5px;"></p>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div><h3>Sécurité</h3><div class="sub">Changez votre mot de passe régulièrement.</div></div>
      </div>
      <div style="padding:16px 18px 20px;">
        <div class="field"><label>Mot de passe actuel</label><input id="f-profile-current" type="password" autocomplete="current-password"></div>
        <div class="field"><label>Nouveau mot de passe</label><input id="f-profile-password" type="password" autocomplete="new-password" placeholder="6 caractères minimum"></div>
        <div class="field"><label>Confirmer le nouveau mot de passe</label><input id="f-profile-password-confirm" type="password" autocomplete="new-password"></div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:8px;">
          <button class="btn btn-primary" type="button" onclick="saveProfilePassword()">Modifier le mot de passe</button>
        </div>
        <p id="profilePassMsg" style="display:none;margin:12px 0 0;font-size:12.5px;"></p>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
const profileData = @json($profile);

function showProfileMsg(id, text, ok){
  const el = document.getElementById(id);
  if(!el) return;
  el.style.display = 'block';
  el.style.color = ok ? 'var(--green)' : 'var(--red)';
  el.textContent = text;
}

async function saveProfile(){
  const name = (document.getElementById('f-profile-name').value||'').trim();
  const email = (document.getElementById('f-profile-email').value||'').trim();
  if(!name || !email){ showProfileMsg('profileMsg','Nom et e-mail obligatoires.', false); return; }
  try{
    const data = await apiUsers('/profile', 'PUT', { name, email });
    Object.assign(profileData, data.profile);
    document.getElementById('profileNameLabel').textContent = data.profile.name;
    document.getElementById('profileAvatar').textContent = initials(data.profile.name);
    if(typeof authUser !== 'undefined'){
      authUser.name = data.profile.name;
      document.getElementById('userNameLabel').textContent = data.profile.name;
      document.getElementById('userAvatar').textContent = initials(data.profile.name);
    }
    showProfileMsg('profileMsg', data.message || 'Profil mis à jour.', true);
  }catch(e){ showProfileMsg('profileMsg', e.message, false); }
}

async function saveProfilePassword(){
  const current_password = document.getElementById('f-profile-current').value;
  const password = document.getElementById('f-profile-password').value;
  const password_confirmation = document.getElementById('f-profile-password-confirm').value;
  if(!current_password || !password){ showProfileMsg('profilePassMsg','Renseignez tous les champs.', false); return; }
  if(password !== password_confirmation){ showProfileMsg('profilePassMsg','La confirmation ne correspond pas.', false); return; }
  try{
    const data = await apiUsers('/profile/password', 'PUT', { current_password, password, password_confirmation });
    document.getElementById('f-profile-current').value = '';
    document.getElementById('f-profile-password').value = '';
    document.getElementById('f-profile-password-confirm').value = '';
    showProfileMsg('profilePassMsg', data.message || 'Mot de passe modifié.', true);
  }catch(e){ showProfileMsg('profilePassMsg', e.message, false); }
}
</script>
@endpush
