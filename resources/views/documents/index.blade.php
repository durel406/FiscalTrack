@extends('layouts.app')

@section('title', 'Documents (GED)')
@section('crumb', 'FiscalTrack / Gestion / Documents')

@section('content')
    <section class="section" id="sec-documents">
      <div class="section-head">
        <div><h2>Documents — Mini-GED</h2><p>Factures, avis d'imposition, quittances et justificatifs centralisés.</p></div>
        <button class="btn btn-primary" onclick="openAddDocument()"><svg><use href="#i-plus"/></svg>Ajouter un document</button>
      </div>
      <div class="panel">
        <div class="panel-head">
          <div><h3>Gestion des documents</h3></div>
          <div class="toolbar">
            <div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Rechercher un document…" id="docSearch"></div>
            <select class="filter-select" id="docFilterContrib"><option value="">Tous contribuables</option></select>
            <select class="filter-select" id="docFilterType"><option value="">Tous les types</option><option>Avis d'imposition</option><option>Quittance</option><option>Facture</option><option>Attestation d'immatriculation</option><option>ATMP</option><option>ACS</option><option>ACF</option></select>
            <button class="reset-btn" title="Réinitialiser le tableau" onclick="resetDocuments()"><svg><use href="#i-refresh"/></svg>Réinitialiser</button>
          </div>
        </div>
        <table>
          <thead><tr><th>Document</th><th>Contribuable</th><th>Fournisseur</th><th>Montant</th><th>Date de création</th><th>Dernière modification</th><th></th></tr></thead>
          <tbody id="docTbody"></tbody>
        </table>
      </div>
      <div class="count-below" id="docCount">— résultats</div>
    </section>

<div class="overlay" id="ov-modalDocument">
  <div class="modal">
    <div class="modal-head"><h3 id="modalDocumentTitle">Ajouter un document</h3><button class="mini-btn" onclick="closeModal('modalDocument')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field"><label>Nom du document</label><input id="f-doc-nom" placeholder="Ex : Quittance IGS T3"></div>
      <div class="field-row">
        <div class="field"><label>Type</label><select id="f-doc-type" onchange="onDocTypeChange()"><option>Avis d'imposition</option><option>Quittance</option><option>Reçue</option><option>Facture de vente</option><option>Facture d'achat</option><option>Attestation d'immatriculation</option><option>ATMP</option><option>ACS</option><option>ACF</option><option>Autres</option></select><input type="text" id="f-doc-type-autre" class="autre-field" placeholder="Précisez le type de document" style="display:none;"></div>
        <div class="field"><label>Contribuable associé</label><select id="f-doc-contrib"></select></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Fournisseur</label><input id="f-doc-fournisseur" placeholder="Ex : DGI Cameroun"></div>
        <div class="field"><label id="f-doc-montant-label">Montant (FCFA)</label><input id="f-doc-montant" type="number" placeholder="0"></div>
      </div>
      <div class="field">
        <label>Fichiers numériques (PDF, PNG)</label>
        <input type="file" id="f-doc-file" accept=".pdf,.png,.jpg,.jpeg" multiple required>
        <p id="doc-file-hint" style="display:none;font-size:11px;color:var(--text-400);margin-top:5px;">Laissez ce champ vide pour conserver le fichier déjà enregistré.</p>
      </div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" onclick="closeModal('modalDocument')">Annuler</button><button class="btn btn-primary" id="modalDocumentSaveBtn" onclick="submitDocument()">Enregistrer</button></div>
  </div>
</div>

<div class="overlay" id="ov-modalDocumentView">
  <div class="modal">
    <div class="modal-head"><h3>Fiche du document</h3><button class="mini-btn" onclick="closeModal('modalDocumentView')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field"><label>Nom</label><div class="cell-strong" id="fiche-doc-nom">—</div></div>
      <div class="field-row">
        <div class="field"><label>Type</label><div id="fiche-doc-type">—</div></div>
        <div class="field"><label>Contribuable associé</label><div id="fiche-doc-contrib">—</div></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Fournisseur</label><div id="fiche-doc-fournisseur">—</div></div>
        <div class="field"><label>Montant (FCFA)</label><div id="fiche-doc-montant">—</div></div>
      </div>
      <div class="field"><label>Date d'ajout</label><div id="fiche-doc-date">—</div></div>
      <div class="field" id="fiche-doc-preview-wrap" style="display:none;">
        <label>Aperçu du fichier</label>
        <div id="fiche-doc-preview"></div>
      </div>
      <div id="fiche-doc-nofile" style="display:none;font-size:12.5px;color:var(--text-400);">Aucun fichier numérique associé.</div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal('modalDocumentView')">Fermer</button>
      <button class="btn btn-primary" id="fiche-doc-download-btn"><svg><use href="#i-download"/></svg>Télécharger</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
populateDocContribFilter();
renderDocuments();
</script>
@endpush
