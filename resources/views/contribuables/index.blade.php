@extends('layouts.app')

@section('title', 'Contribuables')
@section('crumb', 'FiscalTrack / Gestion / Contribuables')

@section('content')
    <section class="section" id="sec-contribuables">

      <!-- vue 1 : formulaire de configuration du dossier -->
      <div id="contribSetupView">
        <div class="setup-wrap">
          <div class="setup-card">
            <!-- <div class="setup-mark"><svg><use href="#i-logo"/></svg></div> -->
            <h2>Configurer le dossier de suivi</h2>
            <p>Renseignez l'entreprise et l'année de suivi : FiscalTrack génère automatiquement l'entête du tableau des contribuables.</p>
            <div class="field"><label>Nom de l'entreprise</label><input id="setupNom" placeholder="Ex : TIA INTERNATIONNAL LTD"></div>
            <div class="field"><label>Année</label><input id="setupAnnee" type="number" placeholder="Ex : 2026" value="2026"></div>
            <button class="btn btn-primary" style="width:100%;justify-content:center" onclick="submitSetup()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- vue 2 : tableau du dossier -->
      <div id="contribDossierView" style="display:none;">
        <div class="section-head">
          <div><h2>Contribuables</h2><p>Gestion des clients suivis par le cabinet.</p></div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn btn-ghost" onclick="backToSetup()"><svg><use href="#i-edit"/></svg>Modifier le dossier</button>
            <button class="btn btn-primary" onclick="openAddContribuable()"><svg><use href="#i-plus"/></svg>Ajouter un contribuable</button>
          </div>
        </div>

        <div class="table-toolbar">
          <div class="toolbar">
            <div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Rechercher (nom, NIU…)" id="contribSearch"></div>
            <select class="filter-select" id="contribFilterCat"><option value="">Toutes catégories</option><option>Petite entreprise</option><option>Moyenne entreprise</option><option>Grande entreprise</option><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option></select>
            <select class="filter-select" id="contribFilterRegime"><option value="">Tous régimes</option><option>Réel</option><option>Simplifié</option><option>Classe</option><option>IGS Classe</option><option>NON PROFESSIONNEL</option></select>
            <button class="reset-btn" title="Réinitialiser le tableau" onclick="resetContribuables()"><svg><use href="#i-refresh"/></svg>Réinitialiser</button>
          </div>
          <div class="export-group">
            <button class="export-btn pdf" onclick="exportContribuablesPDF()"><svg><use href="#i-download"/></svg>Exporter PDF</button>
            <button class="export-btn excel" onclick="exportContribuablesExcel()"><svg><use href="#i-download"/></svg>Exporter Excel</button>
          </div>
        </div>

        <div class="panel dossier-panel">
          <div class="dossier-titlebar" id="dossierTitle">SUIVI DES DOSSIERS : TIA INTERNATIONNAL LTD 2026</div>
          <div class="dossier-scroll">
            <table class="dossier-table dossier-table-2row">
              <thead>
                <tr>
                  <th rowspan="2" class="sticky-col">Contribuable</th>
                  <th rowspan="2">NIU</th>
                  <th rowspan="2">Régime et classe</th>
                  <th rowspan="2">Mot de passe</th>
                  <th rowspan="2">Montant payé</th>
                  <th colspan="5" id="igsYearHeader">IGS 2026</th>
                  <th rowspan="2">Impôts payé</th>
                  <th rowspan="2">Loyer</th>
                  <th rowspan="2">Bail</th>
                  <th rowspan="2">Précompte</th>
                  <th rowspan="2">Timbre</th>
                  <th rowspan="2">Frais de paiement</th>
                  <th colspan="2">Frais de suivi</th>
                  <th colspan="3">Avis d'imposition</th>
                  <th colspan="3">Quittance</th>
                  <th colspan="3">ACF</th>
                  <th rowspan="2">Lieu</th>
                  <th rowspan="2">Téléphone</th>
                  <th rowspan="2" class="sticky-col print-hide" style="left:auto;right:0;">Actions</th>
                </tr>
                <tr>
                  <th>T1</th><th>T2</th><th>T3</th><th>T4</th><th>TDL</th>
                  <th>Payé</th><th>Non payé</th>
                  <th>IGS</th><th>Bail</th><th>Précompte</th>
                  <th>IGS</th><th>Bail</th><th>Précompte</th>
                  <th>IGS</th><th>Bail</th><th>Précompte</th>
                </tr>
              </thead>
              <tbody id="contribTbody"></tbody>
            </table>
          </div>
        </div>
        <div class="count-below" id="contribCount">— résultats</div>
      </div>
    </section>

    <!-- ===== DOCUMENTS ===== -->

<div class="overlay" id="ov-modalContribuable">
  <div class="modal">
    <div class="modal-head"><h3 id="modalContribuableTitle">Ajouter un contribuable</h3><button class="mini-btn" onclick="closeModal('modalContribuable')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="modal-subtitle">Identification</div>
      <div class="field"><label>Nom du contribuable</label><input id="f-contrib-nom" placeholder="Ex : SARL KOUAM &amp; FILS"></div>
      <div class="field-row">
        <div class="field"><label>NIU</label><input id="f-contrib-niu" placeholder="M0123456789X"></div>
        <div class="field"><label>Mot de passe</label><input id="f-contrib-pass" type="text" placeholder="Ex : Pass2026"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Régime</label><select id="f-contrib-regime"><option>Réel</option><option>Simplifié</option><option>Classe</option><option>IGS Classe</option><option>NON PROFESSIONNEL</option><option>Autres</option></select><input type="text" id="f-contrib-regime-autre" class="autre-field" placeholder="Précisez le régime" style="display:none;"></div>
        <div class="field"><label>Catégorie / classe</label><select id="f-contrib-cat"><option>Petite entreprise</option><option>Moyenne entreprise</option><option>Grande entreprise</option><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>Autres</option></select><input type="text" id="f-contrib-cat-autre" class="autre-field" placeholder="Précisez la catégorie / classe" style="display:none;"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Lieu</label><input id="f-contrib-lieu" placeholder="Ex : Douala"></div>
        <div class="field"><label>Téléphone</label><input id="f-contrib-tel" placeholder="6XX XX XX XX"></div>
      </div>

      <div class="modal-subtitle">Paiements &amp; charges</div>
      <div class="field-row">
        <div class="field"><label>Montant payé (FCFA)</label><input id="f-contrib-montant" type="number" placeholder="0"></div>
        <div class="field"><label>Impôts payé (FCFA)</label><input id="f-contrib-impots" type="number" placeholder="0"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Loyer (FCFA)</label><input id="f-contrib-loyer" type="number" placeholder="0"></div>
        <div class="field"><label>Bail (FCFA)</label><input id="f-contrib-bail" type="number" placeholder="0"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Précompte (FCFA)</label><input id="f-contrib-precompte" type="number" placeholder="0"></div>
        <div class="field"><label>Timbre (FCFA)</label><input id="f-contrib-timbre" type="number" placeholder="0"></div>
      </div>
      <div class="field"><label>Frais de paiement (FCFA)</label><input id="f-contrib-fraispaiement" type="number" placeholder="0"></div>

      <div class="modal-subtitle" id="f-igs-subtitle">IGS — Trimestres &amp; TDL</div>
      <div class="field-row-5">
        <div class="field"><label>T1</label><input id="f-contrib-t1" type="number" placeholder="0"></div>
        <div class="field"><label>T2</label><input id="f-contrib-t2" type="number" placeholder="0"></div>
        <div class="field"><label>T3</label><input id="f-contrib-t3" type="number" placeholder="0"></div>
        <div class="field"><label>T4</label><input id="f-contrib-t4" type="number" placeholder="0"></div>
        <div class="field"><label>TDL</label><input id="f-contrib-tdl" type="number" placeholder="0"></div>
      </div>

      <div class="modal-subtitle">Frais de suivi</div>
      <div class="field-row">
        <div class="field"><label>Payé (FCFA)</label><input id="f-contrib-fspaye" type="number" placeholder="0"></div>
        <div class="field"><label>Non payé (FCFA)</label><input id="f-contrib-fsnonpaye" type="number" placeholder="0"></div>
      </div>

      <div class="modal-subtitle">Avis d'imposition — dates</div>
      <div class="field-row-3">
        <div class="field"><label>IGS</label><input id="f-contrib-aiigs" type="date"></div>
        <div class="field"><label>Bail</label><input id="f-contrib-aibail" type="date"></div>
        <div class="field"><label>Précompte</label><input id="f-contrib-aiprecompte" type="date"></div>
      </div>

      <div class="modal-subtitle">Quittance — dates</div>
      <div class="field-row-3">
        <div class="field"><label>IGS</label><input id="f-contrib-qigs" type="date"></div>
        <div class="field"><label>Bail</label><input id="f-contrib-qbail" type="date"></div>
        <div class="field"><label>Précompte</label><input id="f-contrib-qprecompte" type="date"></div>
      </div>

      <div class="modal-subtitle">ACF — dates</div>
      <div class="field-row-3">
        <div class="field"><label>IGS</label><input id="f-contrib-acfigs" type="date"></div>
        <div class="field"><label>Bail</label><input id="f-contrib-acfbail" type="date"></div>
        <div class="field"><label>Précompte</label><input id="f-contrib-acfprecompte" type="date"></div>
      </div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" id="modalContribuableCancelBtn" onclick="closeModal('modalContribuable')">Annuler</button><button class="btn btn-primary" id="modalContribuableSaveBtn" onclick="submitContribuable()">Enregistrer</button></div>
  </div>
</div>

<!-- Modal-Document -->

@endsection

@push('scripts')
<script>
showContribView();
</script>
@endpush
