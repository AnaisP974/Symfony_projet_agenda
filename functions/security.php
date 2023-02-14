<?php
// le script est fortement typé
declare(strict_types=1);
    
    /**
     * Cette fonction permet de vérifier si le token de sécurité provenant du formulaire n'est pas le même que celui généré par le système,
     * elle retourne "true" si les token ne sont pas les mêmes et "false" dans le cas contraire
     * @param string $form_token
     * @param string $system_token
     * @return boolean
     */
    function csrf_middleware(string $form_token, string $system_token) : bool {
        if(!isset($form_token) || !isset($system_token)){
            return true;
        }
        if(!is_string($form_token) || !is_string($system_token)){
            return true;
        }
        if($form_token !== $system_token){
            return true;
        }
        return false;
    }


    /**
     * Cette fonction vérifie la présence d'un robot spameur, on va regarder si la value de notre input hidden a été rempli ou non
     * Elle retourne "true" si un robot est détecté et "false" dans le cas contraire
     * @param string $value
     * @return boolean
     */
    function honeypot_middleware(string $value ) : bool {
        // isset() vérifie si la variable existe et empty() vérifie si le champ est vide. 
        if(!isset($value) || !empty($value)) {
            return true ;
        }
        return false;
    }


    /**
     * Cette fonction protège le serveur contre les attaques de type XSS.
     *
     * Elle se charge de rendre au propre toutes les données provenant provenant du formulaire.
     * 
     * @param array $data
     * @return array
     */
    function xss_protection($data) : array {
        $tab = [];
        foreach ($data as $key => $value){
            $tab[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            // htmlspecialchars est l'équivalent de htmlentities
        }
        return $tab;
    }