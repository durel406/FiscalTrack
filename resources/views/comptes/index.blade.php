@extends('layouts.app')

@section('title', 'Comptes utilisateurs')
@section('crumb', 'FiscalTrack / Administration / Comptes')

@section('content')
    <section class="section" id="sec-comptes">
      <div class="section-head">
        <div><h2>Comptes utilisateurs</h2><p>Créer et gérer les accès des membres du cabinet.</p></div>
        <button class="btn btn-primary" onclick="openAddUser()"><svg><use href="#i-plus"/></svg>Créer un compte</button>
      </div>
      <div class="panel">
        <div class="panel-head">
          <div><h3>Membres du cabinet</h3><div class="sub" id="userCount">— résultats</div></div>
          <div class="toolbar"><div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Rechercher un compte…" id="userSearch"></div>
            <button class="reset-btn" title="Réinitialiser le tableau" onclick="resetUsers()"><svg><use href="#i-refresh"/></svg>Réinitialiser</button></div>
        </div>
        <table>
          <thead><tr><th>Utilisateur</th><th>Email</th><th>Rôle</th><th>Statut</th><th></th></tr></thead>
          <tbody id="userTbody"></tbody>
        </table>
      </div>
    </section>


<div class="overlay" id="ov-modalUser">
  <div class="modal">
    <div class="modal-head"><h3 id="modalUserTitle">Créer un compte utilisateur</h3><button class="mini-btn" onclick="closeModal('modalUser')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field-row">
        <div class="field"><label>Nom</label><input id="f-user-nom" placeholder="Nom"></div>
        <div class="field"><label>Prénom</label><input id="f-user-prenom" placeholder="Prénom"></div>
      </div>
      <div class="field"><label>Email</label><input id="f-user-email" type="email" placeholder="prenom.nom@fiscaltrack.test"></div>
      <div class="field-row">
        <div class="field"><label>Rôle</label><select id="f-user-role"><option>Comptable</option><option>Responsable fiscal</option><option>Administrateur</option><option>Autres</option></select><input type="text" id="f-user-role-autre" class="autre-field" placeholder="Précisez le rôle" style="display:none;"></div>
        <div class="field"><label>Statut</label><select id="f-user-statut"><option>Actif</option><option>Suspendu</option></select></div>
      </div>
      <div class="field"><label>Mot de passe temporaire</label><input id="f-user-pass" type="password" placeholder="••••••••"></div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" onclick="closeModal('modalUser')">Annuler</button><button class="btn btn-primary" id="modalUserSaveBtn" onclick="submitUser()">Créer le compte</button></div>
  </div>
</div>

@endsection

@push('scripts')
<script>
reloadUsers();
</script>
@endpush
