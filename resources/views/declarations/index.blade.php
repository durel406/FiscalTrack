@extends('layouts.app')

@section('title', 'Obligations')
@section('crumb', 'FiscalTrack / Déclaration / Obligations')

@section('content')
    <section class="section" id="sec-declarations">
      <div class="section-head">
        <div>
          <h2>Suivi des obligations fiscales</h2>
          <p>Chaque obligation (contribuable × type × période) a une échéance et un justificatif GED obligatoire. Le statut est calculé automatiquement.</p>
        </div>
        <button class="btn btn-primary" type="button" onclick="openObligationModal()"><svg><use href="#i-plus"/></svg>Nouvelle obligation</button>
      </div>

      <div class="org-links">
        <a class="org-link" href="https://www.impots.cm" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
          <div class="ic"><svg><use href="#i-link"/></svg></div>
          <div class="meta"><span>Direction Générale des Impôts (DGI)</span><span class="sub">www.impots.cm — Télédéclaration DGI</span></div>
        </a>
        <a class="org-link" href="https://www.cnps.cm" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
          <div class="ic"><svg><use href="#i-link"/></svg></div>
          <div class="meta"><span>Caisse Nationale de Prévoyance Sociale (CNPS)</span><span class="sub">www.cnps.cm — Télédéclaration CNPS</span></div>
        </a>
      </div>

      <div class="kpi-grid" style="margin-bottom:16px;">
        <div class="kpi">
          <div class="top"><div class="ic tone-amber"><svg><use href="#i-clock"/></svg></div></div>
          <div class="value num" id="declKpiAttente">0</div>
          <div class="label">En attente</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-amber"><svg><use href="#i-alert"/></svg></div></div>
          <div class="value num" id="declKpiRetard">0</div>
          <div class="label">En retard</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-blue"><svg><use href="#i-alert"/></svg></div></div>
          <div class="value num" id="declKpiProches">0</div>
          <div class="label">Échéance ≤ 7 j</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-navy"><svg><use href="#i-folder"/></svg></div></div>
          <div class="value num" id="declKpiSansPiece">0</div>
          <div class="label">Sans justificatif</div>
        </div>
      </div>

      <div class="panel" style="margin-bottom:16px;">
        <div class="panel-head">
          <div><h3>Obligations à suivre</h3><div class="sub">Filtres opérationnels — le statut se met à jour selon échéance et pièce jointe.</div></div>
          <div class="toolbar" style="flex-wrap:wrap;">
            <div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Nom ou NIU…" id="oblSearch"></div>
            <select class="filter-select" id="oblFilterContrib"><option value="">Tous contribuables</option></select>
            <select class="filter-select" id="oblFilterAnnee"><option value="">Toutes années</option></select>
            <select class="filter-select" id="oblFilterPeriode">
              <option value="">Toutes périodes</option>
              <option value="T1">T1</option><option value="T2">T2</option><option value="T3">T3</option><option value="T4">T4</option>
              <option value="ANNUEL">Annuel</option><option value="AUTRE">Autre</option>
            </select>
            <select class="filter-select" id="oblFilterType"><option value="">Tous types</option></select>
            <select class="filter-select" id="oblFilterOrg"><option value="">Tous organismes</option><option>DGI</option><option>CNPS</option></select>
            <select class="filter-select" id="oblFilterStatut">
              <option value="">Tous statuts</option>
              <option value="a_declarer">À déclarer</option>
              <option value="penalite">En retard</option>
              <option value="justificatif_depose">Justificatif déposé</option>
            </select>
            <select class="filter-select" id="oblFilterEcheance">
              <option value="">Toutes échéances</option>
              <option value="proche">Proches (J−7)</option>
              <option value="aujourdhui">Aujourd'hui</option>
              <option value="retard">En retard</option>
              <option value="sans_date">Sans date</option>
              <option value="ok">OK / clôturées</option>
            </select>
            <select class="filter-select" id="oblFilterPiece">
              <option value="">Pièce : toutes</option>
              <option value="manquante">Sans justificatif</option>
              <option value="ok">Avec justificatif</option>
            </select>
            <button class="reset-btn" type="button" onclick="resetObligationFilters()"><svg><use href="#i-refresh"/></svg>Reset</button>
          </div>
        </div>
        <div class="dossier-scroll" style="max-height:56vh;">
          <table class="dossier-table" style="width:100%;min-width:960px;">
            <thead>
              <tr>
                <th>Contribuable</th>
                <th>Obligation</th>
                <th>Période</th>
                <th>Échéance</th>
                <th>Montant</th>
                <th>Organisme</th>
                <th>Statut</th>
                <th>Justificatif</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="oblTbody"></tbody>
          </table>
        </div>
      </div>
      <div class="count-below" id="oblCount">— résultats</div>

      <div class="panel" style="margin-top:16px;">
        <div class="panel-head">
          <div><h3>Contribuables non en règle</h3><div class="sub">Au moins une obligation ouverte (sans justificatif / en retard).</div></div>
        </div>
        <div id="complianceList" style="padding:6px 18px 16px;display:flex;flex-direction:column;gap:8px;"></div>
      </div>
    </section>

<div class="overlay" id="ov-modalObligation">
  <div class="modal" style="max-width:560px;">
    <div class="modal-head"><h3 id="modalObligationTitle">Nouvelle obligation</h3><button class="mini-btn" type="button" onclick="closeModal('modalObligation')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field"><label>Contribuable</label><select id="f-obl-contrib"></select></div>
      <div class="field"><label>Type d'obligation</label><select id="f-obl-type"></select></div>
      <div class="field-row">
        <div class="field"><label>Année fiscale</label><input type="number" id="f-obl-annee" min="2000" max="2100"></div>
        <div class="field"><label>Période</label>
          <select id="f-obl-periode">
            <option value="T1">T1 (jan–mar)</option>
            <option value="T2">T2 (avr–juin)</option>
            <option value="T3">T3 (juil–sep)</option>
            <option value="T4">T4 (oct–déc)</option>
            <option value="ANNUEL">Annuel</option>
            <option value="M01">Janvier</option><option value="M02">Février</option><option value="M03">Mars</option>
            <option value="M04">Avril</option><option value="M05">Mai</option><option value="M06">Juin</option>
            <option value="M07">Juillet</option><option value="M08">Août</option><option value="M09">Septembre</option>
            <option value="M10">Octobre</option><option value="M11">Novembre</option><option value="M12">Décembre</option>
            <option value="AUTRE">Autre</option>
          </select>
        </div>
      </div>
      <div class="field-row">
        <div class="field"><label>Date limite</label><input type="date" id="f-obl-deadline"><div class="sub" style="font-size:11px;margin-top:4px;">Laissée vide = calcul auto selon périodicité.</div></div>
        <div class="field"><label>Organisme</label>
          <select id="f-obl-org"><option value="">—</option><option>DGI</option><option>CNPS</option><option>Autres</option></select>
        </div>
      </div>
      <div class="field-row">
        <div class="field"><label>Montant à payer (FCFA)</label><input type="number" id="f-obl-montant" min="0" step="1" placeholder="Ex : 150000"></div>
        <div class="field"><label>Justificatif (optionnel)</label><input type="file" id="f-obl-fichier" accept=".pdf,.png,.jpg,.jpeg"><div class="sub" style="font-size:11px;margin-top:4px;">Si joint, le statut passe automatiquement à « Justificatif déposé ».</div></div>
      </div>
      <div class="field"><label>Nom du document</label><input id="f-obl-doc-nom" placeholder="Ex : Quittance IGS T1 2026 (si fichier joint)"></div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" type="button" onclick="closeModal('modalObligation')">Annuler</button>
      <button class="btn btn-primary" type="button" onclick="submitObligation()">Enregistrer</button>
    </div>
  </div>
</div>

<div class="overlay" id="ov-modalOblJustificatif">
  <div class="modal" style="max-width:480px;">
    <div class="modal-head"><h3>Joindre un justificatif</h3><button class="mini-btn" type="button" onclick="closeModal('modalOblJustificatif')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <p style="font-size:13px;color:var(--text-600);margin:0 0 12px;" id="oblJustifHint">Le dépôt d'une pièce clôture automatiquement l'obligation.</p>
      <input type="hidden" id="f-obl-justif-id">
      <div class="field"><label>Nom du document</label><input id="f-obl-justif-nom" placeholder="Ex : Quittance IGS T2 2026"></div>
      <div class="field"><label>Fichier (PDF / PNG / JPG)</label><input type="file" id="f-obl-justif-file" accept=".pdf,.png,.jpg,.jpeg"></div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" type="button" onclick="closeModal('modalOblJustificatif')">Annuler</button>
      <button class="btn btn-primary" type="button" onclick="submitObligationJustificatif()">Déposer</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
renderObligationsList();
renderObligationKpis();
</script>
@endpush
