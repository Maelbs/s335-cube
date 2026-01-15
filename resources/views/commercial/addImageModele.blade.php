<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter des Photos - {{ $article->nom_article }}</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/addModele.css') }}">
    <style>
        .photo-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .preview-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 5px solid #2196f3;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Ajout Photos : {{ $article->nom_article }}</h1>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">Réf : {{ $article->reference }}</p>

        <div class="info-box">
            <strong>Info :</strong> Les images seront stockées dans <code>public/VELOS/{{ $article->reference }}/</code>.
            Le dossier sera créé automatiquement s'il n'existe pas.
        </div>

        @if ($errors->any())
            <div class="error-container">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- enctype="multipart/form-data" est OBLIGATOIRE pour les fichiers --}}
        <form action="{{ route('commercial.store.imageModele') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Champ caché pour la référence --}}
            <input type="hidden" name="reference" value="{{ $article->reference }}">

            <div class="form-group">
                <label for="photos" class="form-label">Sélectionner des images (JPG, PNG)</label>
                {{-- 'multiple' permet de sélectionner plusieurs fichiers --}}
                <input type="file" name="photos[]" id="photos" class="form-input" multiple accept="image/*" required onchange="previewImages()">
            </div>

            {{-- Prévisualisation JS --}}
            <div id="preview-container" class="photo-preview"></div>

            <div class="form-group" style="margin-top: 15px;">
                <div class="checkbox-item">
                    <input type="checkbox" id="est_principale" name="est_principale" value="1">
                    <label for="est_principale">Définir la première image comme <strong>Photo Principale</strong></label>
                </div>
            </div>

            <button type="submit" class="btn-submit">Inserer les images</button>
        </form>

        <div class="but-annuler">
            <a href="{{ route('commercial.dashboard') }}" class="link-annuler">Retour au tableau de bord</a>
        </div>
    </div>

    <script>
        function previewImages() {
            var preview = document.getElementById('preview-container');
            var files   = document.getElementById('photos').files;
            
            preview.innerHTML = ""; // Reset

            if (files) {
                [].forEach.call(files, function(file) {
                    var reader = new FileReader();
                    
                    reader.onloadend = function () {
                        var img = document.createElement("img");
                        img.src = reader.result;
                        img.className = "preview-img";
                        preview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</body>
</html>