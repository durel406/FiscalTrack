@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('crumb', 'FiscalTrack / Accueil')

@section('content')

    <!-- ===== DASHBOARD ===== -->
    <section class="section active" id="sec-dashboard">
      <div class="section-head">
        <div>
          <h2>Bonjour, {{ explode(' ', $authUser['name'])[0] }}</h2>
          <p>Voici l'état des gestions fiscal et social de TIA International Ltd aujourd'hui.</p>
        </div>
        <button class="btn btn-ghost" onclick="goTo('declarations')">Voir les déclarations</button>
      </div>

      <div class="kpi-grid">
        <div class="kpi">
          <div class="top"><div class="ic tone-blue"><svg><use href="#i-users"/></svg></div></div>
          <div class="value num" id="kpiContribuables">0</div>
          <div class="label">Contribuables actifs</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-amber"><svg><use href="#i-clock"/></svg></div></div>
          <div class="value num" id="kpiDeclarationsEnAttente">0</div>
          <div class="label">Déclarations en attente</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-navy"><svg><use href="#i-folder"/></svg></div></div>
          <div class="value num" id="kpiDocuments">0</div>
          <div class="label">Documents dans la GED</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-green"><svg><use href="#i-check"/></svg></div></div>
          <div class="value num" id="kpiConformite">—</div>
          <div class="label">Taux de conformité</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-head">
            <div><h3>Échéances à venir</h3><div class="sub">Prochaines dates limites de déclaration</div></div>
          </div>
          <div class="timeline" id="timelineList"></div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <div><h3>Activité récente</h3><div class="sub">Dernières actions dans FiscalTrack</div></div>
          </div>
          <div class="feed" id="activityFeed"></div>
        </div>
      </div>

      <div class="panel" style="margin-top:16px;">
        <div class="panel-head">
          <div><h3>Calendrier des échéances</h3><div class="sub">Mensuelle (15 du mois) · Trimestrielle (+15j fin de trimestre) · Annuelle (28 fév / 15 mars / 30 juin)</div></div>
        </div>
        <div class="cal-head">
          <div class="cal-nav"><button class="mini-btn" onclick="calNav(-1)"><svg><use href="#i-chevron-left"/></svg></button></div>
          <div class="label" id="calMonthLabel"></div>
          <div class="cal-nav"><button class="mini-btn" onclick="calNav(1)"><svg><use href="#i-chevron-right"/></svg></button></div>
        </div>
        <div class="cal-dow" id="calDow"></div>
        <div class="cal-grid" id="calendarGrid"></div>
        <div class="cal-legend"><span><i style="background:var(--amber)"></i>Échéance à surveiller (non déclarée)</span></div>
        <div class="cal-details" id="calendarDetails"></div>
      </div>
    </section>

@endsection

@push('scripts')
<script>
refreshNotifications(); renderTimeline(); renderFeed(); renderCalendar(); renderKPIs();
</script>
@endpush
