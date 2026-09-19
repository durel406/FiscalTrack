@extends('layouts.app')

@section('title', 'Types d\'obligations')
@section('crumb', 'FiscalTrack / Déclaration / Types')

@section('content')
<section class="section" id="sec-obligation-types">
  <div class="section-head">
    <div>
      <h2>Types d'obligations</h2>
      <p>Catalogue DGI / CNPS — périodicité et organisme par défaut pour le suivi fiscal.</p>
    </div>
    <button class="btn btn-primary" type="button" onclick="openTrackedTypeModal()"><svg><use href="#i-plus"/></svg>Ajouter un type</button>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div><h3>Catalogue</h3><div class="sub">Utilisé lors de la création d'une obligation de suivi.</div></div>
    </div>
    <div style="padding:0 18px 16px;overflow:auto;">
      <table>
        <thead>
          <tr>
            <th>Libellé</th>
            <th>Périodicité</th>
            <th>Organisme</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="trackedDocList"></tbody>
      </table>
    </div>
  </div>
</section>

<div class="overlay" id="ov-modalTrackedType">
  <div class="modal" style="max-width:520px;">
    <div class="modal-head"><h3 id="modalTrackedTypeTitle">Ajouter un type d'obligation</h3><button class="mini-btn" type="button" onclick="closeModal('modalTrackedType')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <input type="hidden" id="f-tracked-id">
      <div class="field"><label>Libellé</label><input id="f-tracked-nom" placeholder="Ex : IGS, TVA, Cotisations CNPS"></div>
      <div class="field-row">
        <div class="field"><label>Périodicité</label>
          <select id="f-tracked-periodicite">
            <option value="libre">Libre</option>
            <option value="mensuelle">Mensuelle</option>
            <option value="trimestrielle">Trimestrielle</option>
            <option value="annuelle">Annuelle</option>
          </select>
        </div>
        <div class="field"><label>Organisme</label>
          <select id="f-tracked-organisme">
            <option value="">—</option>
            <option value="DGI">DGI</option>
            <option value="CNPS">CNPS</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" type="button" onclick="closeModal('modalTrackedType')">Annuler</button>
      <button class="btn btn-primary" type="button" onclick="submitTrackedType()">Enregistrer</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
renderTrackedDocList();
</script>
@endpush
