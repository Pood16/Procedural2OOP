<?php 
require_once(__DIR__.'/../config/db.php');
class Category extends Database {

public function __construct(){
    parent::__construct();
}
public function getAllCategories(){
    $query = $this->connection->prepare("SELECT c.id_categorie, c.nom_categorie,sc.id_sous_categorie,sc.nom_sous_categorie FROM categories c LEFT JOIN sous_categories sc ON c.id_categorie = sc.id_categorie");
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($results as $row) {
            $id_categorie = $row['id_categorie'];

            // Initialize category if not present
            if (!isset($categories[$id_categorie])) {
                $categories[$id_categorie] = [
                    'id_categorie' => $id_categorie,
                    'nom_categorie' => $row['nom_categorie'],
                    'sous_categories' => []
                ];
            }

            // Add subcategories
            if (!empty($row['id_sous_categorie'])) {
                $categories[$id_categorie]['sous_categories'][] = [
                    'id_sous_categorie' => $row['id_sous_categorie'],
                    'nom_sous_categorie' => $row['nom_sous_categorie']
                ];
            }
        }

        return $categories;
}

public function AddCategory($category_name){
    $AddCategoryQuery = $this->connection->prepare("INSERT INTO categories (nom_categorie) VALUES :category_name");
    $AddCategoryQuery->bindParam(':category_name', $category_name, PDO::PARAM_STR);
    $AddCategoryQuery->execute();
}
public function updateCategory($category_id,$category_name){
    $modifyCategoryQuery = $this->connection->prepare("UPDATE categories SET nom_categorie = ? WHERE id_categorie = ?");
    $modifyCategoryQuery->execute([$category_name,$category_id]);
}
public function AddSubCategory($category_id,$subcategory_name){
    $AddSubCategoryQuery = $this->connection->prepare("INSERT INTO sous_categories (nom_sous_categorie, id_categorie) VALUES (:subcategory_name, :category_id)");
    $AddSubCategoryQuery->execute([':subcategory_name' => $subcategory_name,':category_id' => $category_id]);
}
public function UpdateSubCategory($subcategory_id,$subcategory_name){
    $modifySubCategoryQuery = $this->connection->prepare("UPDATE sous_categories SET nom_sous_categorie = ? WHERE id_sous_categorie = ?");
    $modifySubCategoryQuery->execute([$subcategory_name,$subcategory_id]);
}
public function DeleteCategory($id_categorie){
    $deleteCategorieQuery=$this->connection->prepare("DELETE FROM categories WHERE id_categorie=?");
    $deleteCategorieQuery->execute([$id_categorie]);
}
public function DeleteSubCategory($id_sous_categorie){
    $deleteSubCategorieQuery=$this->connection->prepare("DELETE FROM sous_categories WHERE id_sous_categorie=?");
    $deleteSubCategorieQuery->execute([$id_sous_categorie]);
}
}