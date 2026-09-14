<!-- ============ APPBAR ============ -->
<header class="appbar">
    <button class="menu-toggle" id="menuToggle"><svg><use href="#i-menu"/></svg></button>
    <div class="appbar-title">
      <h1 id="pageTitle">Tableau de bord</h1>
      <div class="crumb" id="pageCrumb">FiscalTrack / Accueil</div>
    </div>

    <button class="icon-btn" id="darkModeBtn" title="Mode sombre" style="margin-left:auto;">
      <svg id="darkModeIcon"><use href="#i-moon"/></svg>
    </button>

    <button class="icon-btn" id="fullscreenBtn" title="Plein écran (F11)">
      <svg id="fullscreenIcon"><use href="#i-expand"/></svg>
    </button>

    <button class="icon-btn" id="notifBtn">
      <svg><use href="#i-bell"/></svg>
      <span class="dot" id="notifDot"></span>
    </button>

    <button class="user-chip" id="userBtn">
      <div class="avatar" id="userAvatar">{{ strtoupper(\Illuminate\Support\Str::substr($authUser['name'], 0, 2)) }}</div>
      <div class="who">
        <div class="name" id="userNameLabel">{{ $authUser['name'] }}</div>
        <div class="role" id="userRoleLabel">{{ $authUser['role_label'] }}</div>
      </div>
      <svg style="width:14px;height:14px;color:var(--text-400)"><use href="#i-chevron"/></svg>
    </button>

    <!-- notifications dropdown -->
    <div class="dropdown" id="notifDropdown">
      <div class="dropdown-head">
        <h3>Notifications</h3>
        <button class="link-btn" id="markAllRead">Tout marquer comme lu</button>
      </div>
      <div class="notif-list" id="notifList"></div>
    </div>

    <!-- user dropdown -->
    <div class="dropdown user-dropdown" id="userDropdown" style="right:24px;width:200px;">
      <button><svg><use href="#i-user"/></svg>Mon profil</button>
      <button><svg><use href="#i-settings"/></svg>Paramètres</button>
      <hr>
      <button class="danger" id="logoutBtn" type="button"><svg><use href="#i-logout"/></svg>Se déconnecter</button>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
      @csrf
    </form>
  </header>

  <!-- ============ MAIN ============ -->
