<aside class="sidebar">
    <div class="sidebar-content">
        <div class="user-greeting">
            <span class="welcome-label">Bienvenue</span>
            <h1 class="user-name">
                {{ strtolower($client->prenom_client) }}
                {{ substr(strtolower($client->nom_client), 0, 1) }}.
            </h1>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-link">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Se déconnecter
                </button>
            </form>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="{{ route('profil') }}"
                       class="nav-item {{ request()->routeIs('profil') ? 'active' : '' }}">
                        TABLEAU DE BORD
                    </a>
                </li>

                <li>
                    <a href="{{ route('profil.update.form') }}"
                       class="nav-item {{ request()->routeIs('profil.update.form') ? 'active' : '' }}">
                        MON PROFIL
                    </a>
                </li>

                <li>
                    <a href="{{ route('client.commandes') }}"
                       class="nav-item {{ request()->routeIs('client.commandes') ? 'active' : '' }}">
                        MES COMMANDES
                    </a>
                </li>

                <li>
                    <a href="{{ route('client.adresses') }}" class="nav-item {{ request()->routeIs('client.adresses') ? 'active' : '' }}">
                        MES ADRESSES
                    </a>
                </li>

                <li>
                    <a href="#" class="nav-item {{ request()->is('velos*') ? 'active' : '' }}">
                        MES VÉLOS
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
