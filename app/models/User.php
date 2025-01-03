<?php 
require_once(__DIR__.'/../config/db.php');
class User extends Database {

public function __construct(){
    parent::__construct();
}

public function register($user) {
   
    try {
        // Prepare and execute the insertion query
        $result = $this->connection->prepare("INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, email, role) VALUES (?, ?, ?, ?)");
        $result->execute($user);
        return $this->connection->lastInsertId();  
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

public function login($userData){
    
    try {
        $result = $this->connection->prepare("SELECT * FROM utilisateurs WHERE email=?");
        $result->execute([$userData[0]]);
        $user = $result->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($userData[1], $user["mot_de_passe"])){
           

           return  $user ;
        
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

public function getStatistics() {
    $statistics = [];

    // Total number of users
    $query = $this->connection->prepare("SELECT COUNT(*) AS total_users FROM utilisateurs");
    $query->execute();
    $statistics['total_users'] = $query->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Total number of published projects
    $query = $this->connection->prepare("SELECT COUNT(*) AS total_projects FROM projets");
    $query->execute();
    $statistics['total_projects'] = $query->fetch(PDO::FETCH_ASSOC)['total_projects'];

    // Total number of freelancers
    $query = $this->connection->prepare("SELECT COUNT(*) AS total_freelancers FROM utilisateurs WHERE role = '3'");
    $query->execute();
    $statistics['total_freelancers'] = $query->fetch(PDO::FETCH_ASSOC)['total_freelancers'];

    // Number of ongoing offers (status = 2)
    $query = $this->connection->prepare("SELECT COUNT(*) AS ongoing_offers FROM offres WHERE status = 2");
    $query->execute();
    $statistics['ongoing_offers'] = $query->fetch(PDO::FETCH_ASSOC)['ongoing_offers'];

    return $statistics;
}

public function getAllUsers($filter, $userToSearch =''){


      
        $query = "SELECT * FROM utilisateurs WHERE role != 1"; // by default we show all users except admins
        
        // add filter to query
        if ($filter == 'clients') {
            $query .= " AND role = 2";
        } elseif ($filter == 'freelancers') {
            $query .= " AND role = 3";
        }
        
        // add search condition to query
        if ($userToSearch) {
            $query .= " AND nom_utilisateur LIKE ?";
        }
        
        $resul = $this->connection->prepare($query);
        $resul->execute($userToSearch ? ["%$userToSearch%"] : []);
        
        // Fetch and return results
        $users = $resul->fetchAll(PDO::FETCH_ASSOC);
        return $users;
   

}
public function removeUser($id){
    $removeUser = $this->connection->prepare("DELETE FROM utilisateurs WHERE id_utilisateur=?");
    $removeUser->execute([$id]);
    return true;
}
public function changeStatus($id){
       // get the old status
       $stmt = $this->connection->prepare("SELECT is_active FROM utilisateurs WHERE id_utilisateur = ?");
       $stmt->execute([$id]);
       $currentStatus = $stmt->fetchColumn();

       $changeStatus = $this->connection->prepare("UPDATE utilisateurs SET is_active=? WHERE id_utilisateur=?");
       $changeStatus->execute([$currentStatus==0?1:0,$id]);
       return true;
   }


}