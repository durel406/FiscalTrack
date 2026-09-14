@extends('layouts.app')

@section('title', 'Déclaration')
@section('crumb', 'FiscalTrack / Suivi / Déclaration')

@section('content')
    <section class="section" id="sec-declarations">
      <div class="section-head">
        <div><h2>Déclaration</h2><p>Suivi des documents à suivre par contribuable, avec statut de déclaration.</p></div>
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

      <div class="panel" style="margin-bottom:16px;">
        <div class="panel-head">
          <div><h3>Échéances des documents à suivre</h3><div class="sub">Assignez une date limite à chaque document à suivre (configurés depuis l'écran Documents). Ces dates apparaissent sur le calendrier du tableau de bord.</div></div>
        </div>
        <div id="trackedDeadlineList" style="padding:6px 18px 16px;display:flex;flex-direction:column;gap:8px;"></div>
      </div>

      <div class="panel">
        <div class="panel-head">
          <div><h3>Suivi par contribuable</h3></div>
          <div class="toolbar">
            <div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Rechercher un contribuable…" id="declSearch"></div>
            <select class="filter-select" id="declFilterOrg"><option value="">Tous organismes</option><option>DGI</option><option>CNPS</option></select>
            <select class="filter-select" id="declFilterStatut"><option value="">Tous les contribuables</option><option value="non_conforme">Non en règle</option><option value="conforme">En règle</option></select>
            <button class="reset-btn" title="Réinitialiser le tableau" onclick="resetDeclarations()"><svg><use href="#i-refresh"/></svg>Réinitialiser</button>
          </div>
        </div>
        <div class="dossier-scroll" style="max-height:52vh;">
          <table class="dossier-table" style="width:max-content;min-width:100%;">
            <thead id="declThead"></thead>
            <tbody id="declTbody"></tbody>
          </table>
        </div>
      </div>
      <div class="count-below" id="declCount">— résultats</div>

      <div class="panel" style="margin-top:16px;">
        <div class="panel-head">
          <div><h3>Contribuables non en règle</h3><div class="sub">Un contribuable apparaît ici dès qu'au moins un de ses documents à suivre n'est pas au statut « Déclaré ».</div></div>
        </div>
        <div id="complianceList" style="padding:6px 18px 16px;display:flex;flex-direction:column;gap:8px;"></div>
      </div>
    </section>

    <!-- ===== NOTIFICATIONS ===== -->

@endsection

@push('scripts')
<script>
renderTrackedDeadlineList(); renderDeclarations();
</script>
@endpush
