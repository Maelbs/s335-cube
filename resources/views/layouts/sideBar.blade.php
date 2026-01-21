<style>

@font-face {
    font-family: 'Damas Font';
    src: url('../font/font.woff2');
    font-display: swap;
}

i{
    margin-right: 10px;
}

.delete-account-section {
        margin-top: 0px;
        display: flex;
        flex-direction: column;
        align-items: center; 
        gap: 15px;
        padding-bottom: 20px;
    }

    .btn-delete-account {
        display: flex;
        align-items: center;
        justify-content: center; 
        width: 100%;
        padding: 16px;
        background-color: #c70404;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: "Damas Font", sans-serif;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .anonym-button {
        background-color: #111;
        color: #777;
        border: 1px solid #222;
    }

    .btn-delete-account:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(233, 34, 34, 0.73);
    }

</style>
<aside class="sidebar">
    <div class="sidebar-content">
        <div class="user-greeting">
            <span class="welcome-label">Bienvenue</span>
            <h1 class="user-name">
                {{ strtolower($client->prenom_client) }}
                {{ substr(strtolower($client->nom_client), 0, 1) }}.
            </h1>
            
            <form class="logout-form" action="{{ route('logout') }}" method="POST">
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
    <div class="delete-account-section">
    {{-- ANONYMISER --}}
    <a href="{{ route('profil.anonymize.show') }}" class="btn-delete-account anonym-button">
        <i class="fa-solid fa-user-secret"></i>
        Anonymiser mon compte
    </a>
</div>


<div class="delete-account-section">
    {{-- SUPPRIMER --}}
    <a href="{{ route('profil.destroy.show') }}" class="btn-delete-account btn-delete-danger">
        <i class="fa-solid fa-trash-can"> </i>
        Supprimer mon compte
    </a>
</div>
 
</aside>