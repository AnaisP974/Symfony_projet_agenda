<?php

    /**
     * Cette fonction permet de vérifier si cette valeur est vide ou non.
     * 
     * Elel retourne "true" si elle est vide et "false" dans le cas contraire
     *
     * @param string $value
     * @return boolean
     */
    function is_blank(string $value) : bool {
        //si ce n'est pas une chaine de caractère
        if(!is_string($value)){
            return true;
        }
        //strlen()=> fonction qui permet de compter le nb de caractère dans une chaine de caractère mais laorsque la chaîne de caractère comporte des caractères spéciaux ex: "bébé", au lieu de compter 4caractères il va nous dire qu'il comporte 6caractères
        //mb_strlen()=>compte le nb de caractères même s'il comporte des accents le "é" = 1 et pas à 2
        if (mb_strlen($value, "UTF-8") == 0){
            return true;
        }
        return false;
        // avec empty() si la valeur correspond à un 0 il sera considéré comme vide et pas comme valeur !!!

    }


    /**
     * Cette fonction permet de vérifier si la valeur n'est pas vide.
     * 
     * Elle retourne "true" si la valeur n'est pas vide et "false" dans le cas contraire
     *
     * @param string $value
     * @return boolean
     */
    function is_not_blank(string $value){
        if(!is_string($value)){
            return true;
        }
        if (mb_strlen($value, "UTF-8") == 0){
            return false;
        }
        return true;
    }


        /**
         * Cette fonction vérifie si longueur de la chaîne est supérieure ou non à la longueur attendue
         * 
         * Elle retourne 'true' si la longueur de la valeur est supérieur, et 'false' dans le cas contraire 
         *
         * @param string $value
         * @param integer $length
         * @return boolean
         */
        function length_is_greater_than(string $value, int $length) : bool {
            if(!is_string($value)){
                return true ;
            }
            return (mb_strlen($value) > $length) ? true : false ;
        }



        /**
         * Cette fonction vérifie si longueur de la chaîne est inférieur ou non à la longueur attendue
         * 
         * Elle retourne 'true' si la longueur de la valeur est inférieur, et 'false' dans le cas contraire 
         *
         * @param string $value
         * @param integer $length
         * @return boolean
         */
        function length_is_less_than(string $value, int $length) : bool {
            if(!is_string($value)){
                return true ;
            }
            return (mb_strlen($value) < $length) ? true : false ;
        }

        /**
         * Cette fonction vérifie si la valeur est un email valide ou pas
         * 
         * Elle retourne 'true' si la valeur de l'email est invalide et 'false' dans le cas contraire
         *
         * @param string $value
         * @return boolean
         */
        function is_invalid_email(string $value) : bool{
            // la fonction filter_var() vérifie si la valeur est bien un email grâce à la constante FILTER_VALIDATE_EMAIL, s'il s'agit bien d'un email retourne false, sinon retourne true 
            return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? false : true ;
            
        }

        /**
         * Cette fonction vérifie si la valeur existe déjà dans une colonne d'une table de la base de données ou non.
         * 
         * Elle retourne "true" si la valeur existe déjà et false dans le cas contraire
         *
         * @param string $value
         * @param string $table
         * @param string $column
         * @return boolean
         */
        function is_already_email_on_create(string $value, string $table, string $column) : bool {
            //on établie une conexxion avec la base de données
            require __DIR__ . "/../db/connexion.php";
            //je prépare la requête
            $req = $db->prepare("SELECT * FROM {$table} WHERE {$column} = :{$column}");
            //je lui passe la valeur
            $req->bindValue(":{$column}", $value);
            //j'exécute la requête
            $req->execute();
            //puis je récupère la réponse
            $row = $req->rowCount();
            //si la valeur récupérée est = 0, c'est qu'elle n'existe pas, sinon c'est qu'elle existe
            if ($row == 0) {
                return false;
            }
            return true;
        }


        /**
         * Cette function permet de vérifier s'il s'agit d'un nombre.
         * 
         * Elle retourne true s'il ce n'est pas un nombre et false dans le cas contraire.
         *
         * @param string $value
         * @return boolean
         */
        function is_not_a_number(string $value) : bool {
            //is_int() => vérifie si c'est un entier ; is_numeric()=> si c'est un nb
            
            // Il faudra convertir l'age en entier pour utiliser le is_int() :
            //$age_converted = intval($age);
            //ou $age_converted = (int) $age;
            //ou $age_converted = $age * 1;


            if(!is_numeric($value)){
                return true;
            }
            return false ;
        }

        /**
         * Cette fonction vérifie si la valeur est comprise entre le minimum et le maximum.
         * 
         * elle retourne "true" si ce n'est pas entre le min et le maxx, "false" dans le cas contraire
         *
         * @param string $value
         * @param integer $min
         * @param integer $max
         * @return boolean
         */
        function is_not_between(string $value, int $min, int $max) : bool {
            $value_converted = (int) $value;
            if( ($value_converted < $min) || ($value_converted > $max) ){
                return true;
            }
            return false;
        }


        /**
         * Cette fonction vérifie si le numéro de télaphone est valide ou non
         * 
         * Elle retourne "true" si le numéro de téléphone n'est pas valide et "false" dans le cas contraire.
         *
         * @param string $value
         * @return boolean
         */
        function is_invalid_phone(string $value) : bool {
            if ( preg_match("/^[0-9\s\-\+\(\)]{5,30}$/", $value) ) 
            {
                return false;
            }
            
            return true;
        }