@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('crumb', 'FiscalTrack / Accueil')

@section('content')

    <section class="section active" id="sec-dashboard">
      <div class="section-head">
        <div>
          <h2>Bonjour, {{ explode(' ', $authUser['name'])[0] }}</h2>
          <p>Pilotage du suivi fiscal et social — échéances, retards et pièces manquantes.</p>
        </div>
        <button class="btn btn-ghost" onclick="goTo('declarations')">Voir les obligations</button>
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
          <div class="label">Obligations en attente</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-amber"><svg><use href="#i-alert"/></svg></div></div>
          <div class="value num" id="kpiRetards">0</div>
          <div class="label">En retard</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-blue"><svg><use href="#i-alert"/></svg></div></div>
          <div class="value num" id="kpiProches">0</div>
          <div class="label">Échéance ≤ 7 jours</div>
        </div>
        <div class="kpi">
          <div class="top"><div class="ic tone-navy"><svg><use href="#i-folder"/></svg></div></div>
          <div class="value num" id="kpiSansPiece">0</div>
          <div class="label">Sans justificatif</div>
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
        <div class="kpi">
          <div class="top"><div class="ic tone-green"><svg><use href="#i-check"/></svg></div></div>
          <div class="value num" id="kpiCloturees">0</div>
          <div class="label">Obligations clôturées</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-head">
            <div><h3>Échéances à venir</h3><div class="sub">Prochaines obligations non clôturées</div></div>
          </div>
          <div class="timeline" id="timelineList"></div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <div><h3>Alertes prioritaires</h3><div class="sub">Retards et échéances proches (J−7)</div></div>
          </div>
          <div class="feed" id="activityFeed"></div>
        </div>
      </div>

      <div class="panel" style="margin-top:16px;">
        <div class="panel-head">
          <div><h3>Calendrier des échéances</h3><div class="sub">Basé sur les dates limites des obligations (par contribuable / période)</div></div>
        </div>
        <div class="cal-head">
          <div class="cal-nav"><button class="mini-btn" onclick="calNav(-1)"><svg><use href="#i-chevron-left"/></svg></button></div>
          <div class="label" id="calMonthLabel"></div>
          <div class="cal-nav"><button class="mini-btn" onclick="calNav(1)"><svg><use href="#i-chevron-right"/></svg></button></div>
        </div>
        <div class="cal-dow" id="calDow"></div>
        <div class="cal-grid" id="calendarGrid"></div>
        <div class="cal-legend"><span><i style="background:var(--amber)"></i>Échéance à surveiller (non clôturée)</span></div>
        <div class="cal-details" id="calendarDetails"></div>
      </div>
    </section>

@endsection

@push('scripts')
<script>
refreshNotifications(); renderTimeline(); renderFeed(); renderCalendar(); renderKPIs();
</script>
@endpush
