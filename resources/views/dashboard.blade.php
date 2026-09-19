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
          <div>
            <h3>Calendrier fiscal</h3>
            <div class="sub">Vue mensuelle des échéances — cliquez un jour pour le détail opérationnel</div>
          </div>
          <a class="btn btn-ghost" href="{{ route('declarations.index') }}" style="text-decoration:none;">Voir le suivi</a>
        </div>
        <div class="cal-fiscal">
          <div class="cal-fiscal-toolbar">
            <div class="cal-nav">
              <button type="button" class="mini-btn" title="Mois précédent" onclick="calNav(-1)"><svg><use href="#i-chevron-left"/></svg></button>
              <button type="button" class="cal-btn-today" onclick="calGoToday()">Aujourd'hui</button>
              <button type="button" class="mini-btn" title="Mois suivant" onclick="calNav(1)"><svg><use href="#i-chevron-right"/></svg></button>
            </div>
            <div class="cal-month-title" id="calMonthLabel"></div>
            <div class="cal-nav" style="min-width:110px;justify-content:flex-end;">
              <span style="font-size:11.5px;color:var(--text-400);" id="calMonthHint">—</span>
            </div>
          </div>
          <div class="cal-month-kpis" id="calMonthKpis"></div>
          <div class="cal-fiscal-layout">
            <div>
              <div class="cal-board">
                <div class="cal-dow" id="calDow"></div>
                <div class="cal-grid" id="calendarGrid"></div>
              </div>
              <div class="cal-legend-pro">
                <span><i style="background:var(--red)"></i>En retard</span>
                <span><i style="background:var(--amber)"></i>Échéance du jour</span>
                <span><i style="background:var(--blue-500)"></i>Proche (≤ 7 j)</span>
                <span><i style="background:var(--green)"></i>À venir</span>
              </div>
            </div>
            <aside class="cal-agenda" id="calendarAgenda">
              <div class="cal-agenda-head">
                <div class="eyebrow">Agenda du jour</div>
                <h4 id="calAgendaTitle">Sélectionnez une date</h4>
              </div>
              <div class="cal-agenda-body" id="calendarDetails">
                <div class="cal-agenda-empty">
                  <svg><use href="#i-clock"/></svg>
                  <div>Sélectionnez un jour du calendrier pour afficher les obligations à suivre.</div>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </section>

@endsection

@push('scripts')
<script>
refreshNotifications(); renderTimeline(); renderFeed(); renderCalendar(); renderKPIs();
</script>
@endpush
