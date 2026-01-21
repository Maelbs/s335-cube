<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Script de diagnostic et optimisation';

    public function handle()
    {
        // 1. Définition et vérification du chemin
        $path = public_path('images');
        
        $this->info("------------------------------------------------");
        $this->info("DÉMARRAGE DU DIAGNOSTIC");
        $this->info("Dossier ciblé (Absolu) : " . $path);
        
        if (!File::exists($path)) {
            $this->error("ERREUR CRITIQUE : Le dossier n'existe pas à cet endroit !");
            $this->comment("Vérifiez que votre dossier 'images' est bien dans le dossier 'public' de Laravel.");
            return;
        }

        // 2. Scan des fichiers
        $this->info("Scan en cours (cela peut prendre quelques secondes)...");
        $files = File::allFiles($path);
        $count = count($files);

        $this->info("Fichiers trouvés au total : " . $count);
        $this->info("------------------------------------------------");

        if ($count === 0) {
            $this->warn("Aucun fichier trouvé. Vérifiez les permissions ou le chemin.");
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($files as $file) {
            // Récupère l'extension en minuscule
            $extension = strtolower($file->getExtension());
            $relativePath = $file->getRelativePathname();

            // On filtre
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                
                try {
                    // Test de lecture
                    $realPath = $file->getRealPath();
                    
                    // On essaie d'ouvrir l'image
                    $img = Image::make($realPath);

                    // LOGIQUE D'OPTIMISATION
                    $wasModified = false;

                    // 1. Redimensionner si > 1000px
                    if ($img->width() > 1000) {
                        $img->resize(1000, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $img->save($realPath, 80);
                        $wasModified = true;
                    }

                    // 2. Créer le WebP
                    $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $realPath);
                    if (!file_exists($webpPath)) {
                        $img->encode('webp', 75)->save($webpPath);
                    }

                    // Nettoyage mémoire
                    $img->destroy();
                    
                } catch (\Throwable $e) {
                    // AFFICHER L'ERREUR RÉELLE
                    $this->newLine();
                    $this->error("Erreur sur : " . $relativePath);
                    $this->error("Cause : " . $e->getMessage()); // C'est cette ligne qui va nous aider
                }
            } else {
                // Optionnel : voir les fichiers ignorés (décommenter si besoin)
                // $this->line("Fichier ignoré (mauvaise extension) : " . $relativePath);
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Terminé.");
    }
}