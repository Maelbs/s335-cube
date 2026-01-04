<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_CLIENT = 'client';
    const ROLE_COMMERCIAL = 'commercial';

    protected $table = 'client';
    protected $primaryKey = 'id_client';
    public $timestamps = false;

    protected $fillable = [
        'id_adresse_facturation',
        'nom_client',
        'prenom_client',
        'email_client',
        'mdp',
        'tel',
        'date_inscription',
        'date_naissance',
        'id_magasin',
        'role'
    ];

    protected $hidden = [
        'mdp',
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'date_naissance' => 'date',

    ];

    public function getAuthPassword()
    {
        return $this->mdp;
    }

    public function adresseFacturation()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse_facturation', 'id_adresse');
    }

    public function adressesLivraison()
    {
        return $this->belongsToMany(Adresse::class, 'adresse_livraison', 'id_client', 'id_adresse');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_client');
    }

    public function paniers()
    {
        return $this->hasMany(Panier::class, 'id_client');
    }

   
    public function velosEnregistres()
    {
        return $this->hasMany(VeloEnregistre::class, 'id_client');
    }


    public function codesPromoUtilises()
    {
        return $this->belongsToMany(
            CodePromo::class,           
            'utilisation_code_promo',   
            'id_client',                
            'id_codepromo'             
        );
    }

    public function isCommercial(): bool
    {
        return $this->role === self::ROLE_COMMERCIAL;
    }

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function magasin() {
        return $this->belongsTo(MagasinPartenaire::class, 'id_magasin', 'id_magasin');

    }

    public function __toString()
    {
        return sprintf(
            "Client [%d] : %s %s (%s)",
            $this->id_client,
            $this->prenom_client,
            $this->nom_client,
            $this->email_client
        );
    }

}