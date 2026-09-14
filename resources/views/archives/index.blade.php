@extends('layouts.app')

@section('title', 'Archives')
@section('crumb', 'FiscalTrack / Gestion / Archives')

@section('content')
    <section class="section" id="sec-archives">
      <div class="section-head">
        <div><h2>Archives</h2><p>Documents archivés — restaurez-les ou supprimez-les définitivement.</p></div>
      </div>
      <div class="panel">
        <div class="panel-head">
          <div><h3>Documents archivés</h3></div>
          <div class="toolbar">
            <div class="search-box"><svg><use href="#i-search"/></svg><input placeholder="Rechercher un document archivé…" id="archiveSearch"></div>
            <button class="reset-btn" title="Réinitialiser le tableau" onclick="resetArchives()"><svg><use href="#i-refresh"/></svg>Réinitialiser</button>
          </div>
        </div>
        <table>
          <thead><tr><th>Document</th><th>Contribuable</th><th>Fournisseur</th><th>Montant</th><th>Archivé le</th><th></th></tr></thead>
          <tbody id="archiveTbody"></tbody>
        </table>
      </div>
      <div class="count-below" id="archiveCount">— résultats</div>
    </section>

    <!-- ===== DECLARATIONS ===== -->

@endsection

@push('scripts')
<script>
renderArchives();
</script>
@endpush
