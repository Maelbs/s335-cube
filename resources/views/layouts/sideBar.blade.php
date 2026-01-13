<style>
.btn-delete-account {
  position: relative;
  width: 100%;
  padding: 16px;
  background-color:rgb(199, 4, 4);
  color: #fff;
  border: none;
  border-radius: 4px;
  font-family: "Damas Font", sans-serif;
  font-weight: 700;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  cursor: pointer;
  overflow: hidden;
  z-index: 1;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  clip-path: polygon(15px 0, 100% 0, 100% 100%, 0% 100%);
  border-radius: 0 !important;
}

.anonym-button {
    margin-bottom: 15px;
}

.btn-delete-account::before {
  content: "";
  position: absolute;
  top: 0;
  left: -15px;
  width: 0%;
  height: 100%;
  background-color:rgb(245, 11, 11);
  z-index: -1;
  transform: skewX(-20deg);
  transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-delete-account:hover::before {
  width: 160%;
}

.btn-delete-account:hover {
  box-shadow: 0 8px 25px rgba(0, 113, 227, 0.35);
  transform: translateY(-2px);
}

.sidebar-content {
    position: relative;
}

.delete-account-section {
    position: relative;
    width: 100%;
    margin-top: 20px;
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
<a href="{{ route('profil.anonymize.show') }}"
   class="btn-delete-account anonym-button">
    <i class="fa-solid fa-user-secret"></i>
    Anonymiser mon compte
</a>

{{-- SUPPRIMER --}}
<a href="{{ route('profil.destroy.show') }}"
   class="btn-delete-account">
    <i class="fa-solid fa-trash-can"></i>
    Supprimer mon compte
</a>

</div>

</aside>