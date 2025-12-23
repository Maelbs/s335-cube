<?php

namespace App\Http\Controllers;

class MailController extends Controllers{

    public function sendMail(){
        

        
        $to_email = "maelbouviersobrino@hotmail.com";
        $subject_prefix = "Nouveau message depuis le formulaire de contact";
         
        // Vérifier si la requête est POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérer et nettoyer les données du formulaire
            $name = strip_tags(trim($_POST["name"]));
            $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
            $message = strip_tags(trim($_POST["message"]));
            // Validation
            $errors = [];
            if (empty($name)) {
                $errors[] = "Le nom est requis.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Une adresse email valide est requise.";
            }
            if (empty($message)) {
                $errors[] = "Le message est requis.";
            }
            // Si pas d'erreurs, envoyer l'email
            if (empty($errors)) {
                // Préparer le sujet et le contenu de l'email
                $subject = "$subject_prefix - Message de $name";
                $email_content = "Nom: $name\n";
                $email_content .= "Email: $email\n\n";
                $email_content .= "Message:\n$message\n";
                // En-têtes de l'email
                $headers = "From: $name <$email>\r\n";
                $headers .= "Reply-To: $email\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                // Envoyer l'email
                if (mail($to_email, $subject, $email_content, $headers)) {
                    // Redirection vers une page de succès ou message de confirmation
                    header("Location: index.html?success=1");
                    exit;
                } else {
                    $error_message = "Une erreur s'est produite lors de l'envoi du message. Veuillez réessayer.";
                }
            } else {
                $error_message = implode("<br>", $errors);
            }
        } else {
        
            exit;
        }
        
    }
}