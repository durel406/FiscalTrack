@extends('layouts.app')

@section('title', 'Contribuables')
@section('crumb', 'FiscalTrack / Gestion / Contribuables')

@section('content')
    <section class="section" id="sec-contribuables">
      <div class="section-head">
        <div><h2>Contribuables</h2><p>Répertoire des contribuables suivis par le cabinet.</p></div>
        <button class="btn btn-primary" onclick="openAddContribuable()"><svg><use href="#i-plus"/></svg>Ajouter un contribuable</button>
      </div>

      <div class="table-toolbar">
        <div class="toolbar">
          <div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Rechercher un contribuable…" id="contribSearch"></div>
          <input class="filter-select" id="contribFilterNiu" placeholder="Filtrer par NIU" type="search">
          <select class="filter-select" id="contribFilterRegime">
            <option value="">Tous régimes/classes</option>
            <option>Réel</option><option>Simplifié</option><option>Classe</option>
            <option>Classe 1</option><option>Classe 2</option><option>Classe 3</option><option>Classe 4</option><option>Classe 5</option>
            <option>Classe 6</option><option>Classe 7</option><option>Classe 8</option><option>Classe 9</option>
            <option>IGS Classe</option><option>NON PROFESSIONNEL</option><option value="Autre">Autre</option>
          </select>
          <button class="reset-btn" title="Réinitialiser le tableau" onclick="resetContribuables()"><svg><use href="#i-refresh"/></svg>Réinitialiser</button>
        </div>
        <div class="export-group">
          <button class="export-btn excel" onclick="exportContribuablesExcel()"><svg><use href="#i-download"/></svg>Exporter Excel</button>
        </div>
      </div>

      <div class="panel">
        <div class="dossier-scroll">
          <table class="dossier-table" style="width:100%;min-width:1120px;">
            <thead><tr>
              <th>Nom/Raison sociale</th><th>Prénom/Sigle</th><th>NIU</th><th>Activité principale</th>
              <th>Régime</th><th>Centre de rattachement</th><th>Ville</th><th>Quartier</th><th>Lieux-dit</th><th>Actions</th>
            </tr></thead>
            <tbody id="contribTbody"></tbody>
          </table>
        </div>
      </div>
      <div class="count-below" id="contribCount">— résultats</div>
    </section>

<div class="overlay" id="ov-modalContribuable">
  <div class="modal">
    <div class="modal-head"><h3 id="modalContribuableTitle">Ajouter un contribuable</h3><button class="mini-btn" onclick="closeModal('modalContribuable')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="modal-subtitle">Identification</div>
      <div class="field-row">
        <div class="field"><label>Nom/Raison sociale</label><input id="f-contrib-nom" placeholder="Ex : SARL KOUAM &amp; FILS"></div>
        <div class="field"><label>Prénom/Sigle</label><input id="f-contrib-prenom" placeholder="Ex : K&amp;F"></div>
      </div>
      <div class="field"><label>NIU</label><input id="f-contrib-niu" placeholder="M0123456789X"></div>
      <div class="field"><label>Activité principale</label><input id="f-contrib-activite" placeholder="Ex : Commerce général"></div>
      <div class="field-row">
        <div class="field"><label>Régime / Classe</label><select id="f-contrib-regime"><option value="">Sélectionner</option><option>Réel</option><option>Simplifié</option><option>Classe</option><option>Classe 1</option><option>Classe 2</option><option>Classe 3</option><option>Classe 4</option><option>Classe 5</option><option>Classe 6</option><option>Classe 7</option><option>Classe 8</option><option>Classe 9</option><option>IGS Classe</option><option>NON PROFESSIONNEL</option><option value="Autre">Autre</option></select><input id="f-contrib-regime-autre" placeholder="Précisez le régime ou la classe" style="display:none;margin-top:7px;"></div>
        <div class="field"><label>Centre de rattachement</label><input id="f-contrib-centre" placeholder="Ex : DGI Centre 1"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Ville</label><input id="f-contrib-ville" placeholder="Ex : Douala"></div>
        <div class="field"><label>Quartier</label><input id="f-contrib-quartier" placeholder="Ex : Akwa"></div>
      </div>
      <div class="field"><label>Lieux-dit</label><input id="f-contrib-lieux-dit" placeholder="Ex : près du marché"></div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" id="modalContribuableCancelBtn" onclick="closeModal('modalContribuable')">Annuler</button><button class="btn btn-primary" id="modalContribuableSaveBtn" onclick="submitContribuable()">Enregistrer</button></div>
  </div>
</div>

<!-- Modal-Document -->

@endsection

@push('scripts')
<script>
renderContribuables();
</script>
@endpush
