{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); z-index:1030;">
    <div class="container">
        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="{{ asset('image/cnrst.png') }}" alt="CNRST" style="height:42px; width:auto; margin-right:10px; object-fit:contain;">
            <span class="fw-bold fs-5">{{ config('app.name') }}</span>
        </a>

        {{-- Mobile toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="/#accueil">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="/#fonctionnalites">Fonctionnalités</a></li>
                <li class="nav-item"><a class="nav-link" href="/#propos">À propos</a></li>
                <li class="nav-item"><a class="nav-link" href="/#localisation">Localisation</a></li>
                <li class="nav-item"><a class="nav-link" href="/#contact">Contact</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a href="/login" class="btn btn-outline-green rounded-pill btn-sm px-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                </a>
                @if(!app()->isDownForMaintenance())
                <a href="/inscription-entreprise" class="btn btn-green rounded-pill btn-sm px-3">
                    <i class="bi bi-building me-1"></i>S'inscrire
                </a>
                @endif
            </div>
        </div>
    </div>
</nav>
