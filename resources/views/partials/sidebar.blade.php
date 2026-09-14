<aside class="sidebar">

    {{-- =====================================================
         LOGO FISCALTRACK
    ====================================================== --}}
    <div class="brand">

        <a href="{{ route('dashboard') }}" class="brand-link">

            {{-- Logo --}}
            <img
                src="{{ asset('images/fiscaltrack-logo.png') }}"
                alt="FiscalTrack"
                class="brand-logo"
            >

            <span class="brand-name">FiscalTrack</span>

        </a>

    </div>


    {{-- =====================================================
         NAVIGATION PRINCIPALE
    ====================================================== --}}
    <nav>

        {{-- =================================================
             GÉNÉRAL
        ================================================== --}}
        <div class="nav-group">

            <div class="nav-label">
                Général
            </div>

            {{-- Tableau de bord --}}
            <a
                href="{{ route('dashboard') }}"
                class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                data-section="dashboard"
                data-roles="admin,comptable,fiscal"
            >

                <svg>
                    <use href="#i-grid"></use>
                </svg>

                <span>
                    Tableau de bord
                </span>

            </a>

        </div>


        {{-- =================================================
             GESTION
        ================================================== --}}
        <div class="nav-group">

            <div class="nav-label">
                Gestion
            </div>


            {{-- ---------------------------------------------
                 CONTRIBUABLES
            ---------------------------------------------- --}}
            <a
                href="{{ route('contribuables') }}"
                class="nav-item {{ request()->routeIs('contribuables') ? 'active' : '' }}"
                data-section="contribuables"
                data-roles="admin,comptable"
            >

                <svg>
                    <use href="#i-users"></use>
                </svg>

                <span>
                    Contribuables
                </span>

            </a>


            {{-- ---------------------------------------------
                 DOCUMENTS
            ---------------------------------------------- --}}
            <a
                href="{{ route('documents') }}"
                class="nav-item {{ request()->routeIs('documents') ? 'active' : '' }}"
                data-section="documents"
                data-roles="admin,comptable"
            >

                <svg>
                    <use href="#i-folder"></use>
                </svg>

                <span>
                    Documents (GED)
                </span>

            </a>


            {{-- ---------------------------------------------
                 ARCHIVES
            ---------------------------------------------- --}}
            <a
                href="{{ route('archives') }}"
                class="nav-item {{ request()->routeIs('archives') ? 'active' : '' }}"
                data-section="archives"
                data-roles="admin,comptable"
            >

                <svg>
                    <use href="#i-archive"></use>
                </svg>

                <span>
                    Archives
                </span>

            </a>


            {{-- ---------------------------------------------
                 DÉCLARATIONS
            ---------------------------------------------- --}}
            <a
                href="{{ route('declarations') }}"
                class="nav-item {{ request()->routeIs('declarations') ? 'active' : '' }}"
                data-section="declarations"
                data-roles="admin,fiscal"
            >

                <svg>
                    <use href="#i-file"></use>
                </svg>

                <span>
                    Déclaration
                </span>

            </a>

        </div>


        {{-- =================================================
             SUIVI
        ================================================== --}}
        <div class="nav-group">

            <div class="nav-label">
                Suivi
            </div>


            {{-- ---------------------------------------------
                 NOTIFICATIONS
            ---------------------------------------------- --}}
            <a
                href="{{ route('notifications') }}"
                class="nav-item {{ request()->routeIs('notifications') ? 'active' : '' }}"
                data-section="notifications"
                data-roles="admin,comptable,fiscal"
            >

                <svg>
                    <use href="#i-bell"></use>
                </svg>

                <span>
                    Notifications
                </span>

                {{-- Nombre de notifications --}}
                <span
                    class="badge"
                    id="navNotifBadge"
                >
                    3
                </span>

            </a>


            {{-- ---------------------------------------------
                 COMPTES UTILISATEURS
            ---------------------------------------------- --}}
            <a
                href="{{ route('comptes') }}"
                class="nav-item {{ request()->routeIs('comptes') ? 'active' : '' }}"
                data-section="comptes"
                data-roles="admin"
            >

                <svg>
                    <use href="#i-settings"></use>
                </svg>

                <span>
                    Comptes utilisateurs
                </span>

            </a>

        </div>

    </nav>

</aside>