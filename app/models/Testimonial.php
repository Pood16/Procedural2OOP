<?php



require_once(__DIR__.'/../config/db.php');
class Testimonial extends database {


    public function __construct(){
        parent::__construct();
    }

    function getTestimonials() {
        // Base query
        $queryStr = "SELECT p.titre_projet, t.commentaire, t.id_temoignage, o.montant, o.delai, o.id_offre
                    FROM temoignages t
                    JOIN offres o ON t.id_offre = o.id_offre
                    JOIN projets p ON o.id_projet = p.id_projet";
        // Prepare and execute the query
        $query = $this->connection->prepare($queryStr);
        $query->execute();
        $result = $query->fetchAll();
        // Fetch and return results
        return $result;
    }


}