

<?php 


require_once(__DIR__.'/../config/db.php');

class SubCategory extends database {

    public function __construct(){
        parent::__construct();
    }




    public function getAllSubcategories(){
        $subCategoriesQuery = $this->connection->prepare("SELECT * FROM sous_categories");
        $subCategoriesQuery->execute();
        // Fetch and return results
        $subcategories = $subCategoriesQuery->fetchAll();
        return $subcategories;
    }


   
}