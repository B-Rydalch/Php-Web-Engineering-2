<?php
    session_start();
    require('dbconnection.php');
    $db = connect_db();

    //variables
    $ritem = htmlspecialchars($_POST['ritem']);
    $rquantity = htmlspecialchars($_POST['ramount']);
    $chefid = 1; 
    
    echo "variables initialized<br>";

    // grab what the user is wanting to remove from database and confirm quantity is there. 
    $stmt = $db->prepare("SELECT id, item_name, quantity, category FROM inventory WHERE item_name = :it");
    $stmt->bindValue(':it', $ritem, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // update the inventory and insert into grocery list
    if (($row['quantity'] - $rquantity) == 0) {
            
        // delete row 
        $stmt = $db->prepare("DELETE FROM inventory WHERE item_name = :rit AND id = :rid");
        $stmt->bindValue(':rit', $row['item_name'], PDO::PARAM_STR);
        $stmt->bindValue(':rid', $row['id'], PDO::PARAM_INT);
        $stmt->execute();

    } else if(($row['quantity'] - $rquantity) > 0) {

        // sql statements
        $sql = $db->prepare("UPDATE inventory SET quantity = (:ramt - :rqty) 
                                WHERE id = :rid AND item_name = rit;");
        $sql2= $db->prepare('INSERT INTO shopping (item_name, quantity, category, chef_id ) 
                                    VALUES (:iname, :iqty, :ict, :cook);');


        // binding variables 
        $sql  = $db->bindValue(':ramt', $row['quantity'], PDO::PARAM_INT);
        $sql  = $db->bindValue(':rqty', $rquantity, PDO::PARAM_INT);
        $sql  = $db->bindValue(':rid', $row['id'], PDO::PARAM_INT);
        $sql  = $db->bindValue(':rit', $ritem, PDO::PARAM_STR);
        $sql2 = $db->bindValue(':iname', $ritem, PDO::PARAM_STR);
        $sql2 = $db->bindValue(':iqty', $rquantity, PDO::PARAM_INT);
        $sql2 = $db->bindValue(':ict', $row['category'], PDO::PARAM_STR);
        $sql2 = $db->bindValue(':cook', $chefid, PDO::PARAM_INT);

        $sql->execut();
        $sql2->execute();



        // update inventory
        // $stmt = $db->prepare("UPDATE inventory SET quantity = (:ramt - :rqty) 
        //                         WHERE id = :rid AND item_name = rit;");
        // $stmt->bindValue(':ramt', $row['quantity'], PDO::PARAM_INT);
        // $stmt->bindValue(':rqty', $rquantity, PDO::PARAM_INT);
        // $stmt->bindValue(':rid', $row['id'], PDO::PARAM_INT);
        // $stmt->bindValue(':rit', $ritem, PDO::PARAM_STR);
        // echo $stmt;
        // $stmt->execute();  
        
        //echo "stmt executed";

        // insert into shopping list
        // $sql = $db->prepare('INSERT INTO shopping (item_name, quantity, category, chef_id ) 
        //                         VALUES (:iname, :iqty, :ict, :cook);');
        // $sql = $db->bindValue(':iname', $ritem, PDO::PARAM_STR);
        // $sql = $db->bindValue(':iqty', $rquantity, PDO::PARAM_INT);
        // $sql = $db->bindValue(':ict', $row['category'], PDO::PARAM_STR);
        // $sql = $db->bindValue(':cook', $chefid, PDO::PARAM_INT);
        // $sql->execute();

        //echo "sql executed";

        // return to page
        // $new_page = "index.php";
        // header("Location: $new_page");
        
        // die();

    } else{
        // ERROR handler
        
        // return to page
        // $new_page = "index.php";
        // header("Location: $new_page");
    }

    $new_page = "index.php";
    header("Location: $new_page");
        
    die();

?>