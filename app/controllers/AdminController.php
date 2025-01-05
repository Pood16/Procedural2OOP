<?php 
require_once (__DIR__.'/../models/User.php');
require_once (__DIR__.'/../models/Category.php');
require_once (__DIR__.'/../models/SubCategory.php');
require_once (__DIR__.'/../models/Project.php');

class AdminController extends BaseController {
    private $UserModel;
    private $CatModel;
    private $subCatModel;

    public function __construct(){

        $this->UserModel = new User();
        $this->CatModel = new Category();
        $this->subCatModel = new SubCategory();
        $this->projectModel = new Project();
     }

    public function index() {
      
        if(!isset($_SESSION['user_loged_in_id'])){
            header("Location: /login ");
            exit;
        }
        $statistics =  $this->UserModel->getStatistics();
        $this->renderDashboard('admin/index', ["statistics" => $statistics]);
    }
   
   public function categories() {
       $categories = $this->CatModel->getAllCategories();
       $this->renderDashboard('admin/categories', ["categories" => $categories]);
    }
   public function testimonials() {
      $this->renderDashboard('admin/testimonials');
   }
   public function projects() {

      // Get filter and search values from GET
        $filter_by_cat = isset($_GET['filter_by_cat']) ? $_GET['filter_by_cat'] : 'all';
        $filter_by_sub_cat = isset($_GET['filter_by_sub_cat']) ? $_GET['filter_by_sub_cat'] : 'all';
        $projectToSearch = isset($_GET['projectToSearch']) ? $_GET['projectToSearch'] : '';
        $filter_by_status = isset($_GET['filter_by_status']) ? $_GET['filter_by_status'] : '';
        // Call showProjects with both filters and the search term
        $categories = $this->CatModel->getAllCategories();
        $subcategories = $this->subCatModel->getAllSubcategories();
        $projects = $this->projectModel->showProjects($filter_by_cat, $filter_by_sub_cat,$filter_by_status, $projectToSearch);
        $this->renderDashboard('admin/projects',["projects" => $projects, "categories" => $categories, "subcategories" => $subcategories] );
   }

   public function handleUsers(){
         // Get filter and search values from GET
         $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Default to 'all' if no filter is selected
         $userToSearch = isset($_GET['userToSearch']) ? $_GET['userToSearch'] : ''; // Default to empty if no search term is provided
         // Call showUsers with both filter and search term
         $users = $this->UserModel->getAllUsers($filter, $userToSearch);
         $this->renderDashboard('admin/users',["users"=> $users]);
   }
   public function removeUser(){
         $user_id = $_POST['remove_user'];
         $this->UserModel->removeUser($user_id);
         $this->handleUsers();  
   }
   public function changeStatus(){
      // check the post request to block the user
      if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['block_user_id'])) {
         $id = $_POST['block_user_id'];
         $result = $this->UserModel->changeStatus($id);
      }
      $this->handleUsers();
   }

   public function crudCategory(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
       
            if (isset($_POST["add_modify_category"])) {
                
                $category_name = trim($_POST["category_name_input"]);
                $category_id = isset($_POST["category_id_input"]) ? trim($_POST["category_id_input"]) : '';
                if (!empty($category_name)) {
                    if($category_id==0){
                        $this->CatModel->AddCategory($category_name);
                        header("Location: /admin/categories");
                        exit();
                    }else{
                        $this->CatModel->UpdateCategory($category_id,$category_name);
                        header("Location: /admin/categories");
                        exit();
                    }
                    
                } 
            }
            if (isset($_POST["add_modify_subcategory"])) {
                $subcategory_name = trim($_POST["subcategory_name_input"]);
                $category_id = $_POST["category_parent_id_input"];
                $subcategory_id = (int)trim($_POST["subcategory_id_input"]);
                

                if (!empty($subcategory_name)) {
                    if($subcategory_id==0){
                        $this->CatModel->AddSubCategory($category_id,$subcategory_name);
                        header("Location: /admin/categories");
                        exit();
                    }
                    else{
                        $this->CatModel->UpdateSubCategory($subcategory_id,$subcategory_name); 
                        header("Location: /admin/categories");
                        exit();
                    }
                    
                } 
                
            } 
            if (isset($_POST["delete_categorie"])) {
                $id_categorie=$_POST['id_categorie'];
        
                $this->CatModel->DeleteCategory($id_categorie);
                header("Location: /admin/categories");
                exit();
            }
    
            // delete subcategorie
            if (isset($_POST["delete_sub_category"])) {
                $id_sous_categorie=$_POST['id_sub_categorie'];
                $this->CatModel->DeleteSubCategory($id_sous_categorie);
                header("Location: /admin/categories");
                exit();
            }
        }
    }

    public function removeProject(){
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_project'])) {
            $idUser = $_POST['id_projet'];
            $this->projectModel->deleteProject($idUser);
            header('Location: /admin/projects');
            exit();
        }
    }

}