@extends('layouts.app')

@section('title', 'Notifications')
@section('crumb', 'FiscalTrack / Suivi / Notifications')

@section('content')
    <section class="section" id="sec-notifications">
      <div class="section-head">
        <div>
          <h2>Notifications</h2>
          <p>Alertes d'échéances fiscales (J−7, jour J, retard) — synchronisées par le scheduler FiscalTrack.</p>
        </div>
        <button class="btn btn-ghost" id="markAllReadSection" type="button">Tout marquer comme lu</button>
      </div>
      <div class="panel">
        <div class="panel-head">
          <div><h3>Toutes les alertes</h3><div class="sub">Une alerte = une obligation (contribuable × type × période)</div></div>
        </div>
        <div class="notif-list" id="notifListFull" style="max-height:none;"></div>
      </div>
    </section>
@endsection

@push('scripts')
<script>
refreshNotifications();
</script>
@endpush
