@extends('layouts.app')

@section('title', 'Notifications')
@section('crumb', 'FiscalTrack / Suivi / Notifications')

@section('content')
    <section class="section" id="sec-notifications">
      <div class="section-head">
        <div><h2>Notifications</h2><p>Alertes automatiques générées avant et après les dates d'échéance.</p></div>
        <button class="btn btn-ghost" id="markAllReadSection">Tout marquer comme lu</button>
      </div>
      <div class="panel">
        <div class="panel-head"><div><h3>Toutes les alertes</h3><div class="sub">Générées par le Scheduler FiscalTrack</div></div></div>
        <div class="notif-list" id="notifListFull" style="max-height:none;"></div>
      </div>
    </section>

    <!-- ===== COMPTES ===== -->

@endsection

@push('scripts')
<script>
refreshNotifications();
</script>
@endpush
