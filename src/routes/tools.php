<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Get all installed tools
$app->get('/api/tools',function(request $request, response $response){
    $stmt = "SELECT 
    sv.svr_id,
    sv.svr_name,
    sv.svr_addr,
    sv.svr_ip,
    sv.instns_to_access,
    sv.instns_to_req_acc,
    isw.*
    FROM
    b_servers AS sv
        INNER JOIN
    b_sw_inst_locn AS ln ON sv.svr_id = ln.svr_id
        INNER JOIN
    (SELECT 
        sw.sw_id,
            sw.sw_name,
            sw.date_of_instn,
            sw.sw_url,
            sw.sw_desc,
            b.cat_name,
            b.subcat_name
    FROM
        b_installed_sw AS sw
    INNER JOIN (SELECT 
        c.cat_id, c.cat_name, s.subcat_id, s.subcat_name
    FROM
        b_sw_cats AS c
    INNER JOIN b_sw_cat_subcats AS a ON c.cat_id = a.cat_id
    INNER JOIN b_sw_subcats AS s ON a.subcat_id = s.subcat_id) AS b ON sw.cat_id = b.cat_id
        AND sw.subcat_id = b.subcat_id) AS isw ON ln.sw_id = isw.sw_id";

   //Connect to db and get installed softwares
    try{
    $dbo = new Connect();

    $result = $dbo->executeSQL($stmt);
    if($result->rowCount()){
        $tools = $result->fetchAll(PDO::FETCH_OBJ);
        $dbo = null;
        echo json_encode($tools);
        }
    } catch (PDOException $e){
    echo '{
        "error": {"text": '.$e->getMessage().'};
        }';
    } 

});


//Get specific installed tool
$app->get('/api/tools/{sw_id}',function(request $request, response $response){

    //get software id
    $sw_id = $request->getAttribute('sw_id');
    $stmt = "SELECT 
    sv.svr_id,
    sv.svr_name,
    sv.svr_addr,
    sv.svr_ip,
    sv.instns_to_access,
    sv.instns_to_req_acc,
    isw.*
    FROM
    b_servers AS sv
        INNER JOIN
    b_sw_inst_locn AS ln ON sv.svr_id = ln.svr_id
        INNER JOIN
    (SELECT 
        sw.sw_id,
            sw.sw_name,
            sw.date_of_instn,
            sw.sw_url,
            sw.sw_desc,
            b.cat_name,
            b.subcat_name
    FROM
        b_installed_sw AS sw
    INNER JOIN (SELECT 
        c.cat_id, c.cat_name, s.subcat_id, s.subcat_name
    FROM
        b_sw_cats AS c
    INNER JOIN b_sw_cat_subcats AS a ON c.cat_id = a.cat_id
    INNER JOIN b_sw_subcats AS s ON a.subcat_id = s.subcat_id) AS b ON sw.cat_id = b.cat_id
        AND sw.subcat_id = b.subcat_id) AS isw ON ln.sw_id = isw.sw_id
        WHERE isw.sw_id=$sw_id";

   //Connect to database and fetch tools
    try{
    $dbo = new Connect();

    $result = $dbo->executeSQL($stmt);
    if($result->rowCount()){
        $tool = $result->fetchAll(PDO::FETCH_OBJ);
        $dbo = null;
        echo json_encode($tool);
        }
    } catch (PDOException $e){
    //print out error if connection to database fails
    echo '{
        "error": {"text": '.$e->getMessage().'};
        }';
    } 
    
});


//Add newly installed tool
 $app->get('/api/tools/add',function(request $request, response $response){

    //get software params
    $sw_name = $request->getParam('sw_name');
    $sw_url  = $request->getParam('sw_url');
    $date_of_instn = $request->getParam('date_of_instn');
    $sw_desc = $request->getParam('sw_desc');
    $cat_id = $request->getParam('cat_id');
    $subcat_id = $request->getParam('subcat_id');

    $sql = "INSERT INTO b_installed_sw(sw_name, sw_url, date_of_instn, sw_desc, cat_id, sub_catid) 
             VALUES(:sw_name, :sw_url, :date_of_instn, :sw_desc, :cat_id, :sub_catid)";

   //Connect to database and fetch tools
    try{
    $dbo = new Connect();
    $dbo = $dbo->conecta();
    $stmt = $dbo->prepare($sql);
    
    $stmt->bindParam(':sw_name', $sw_name);
    $stmt->bindParam(':sw_url', $sw_url);
    $stmt->bindParam(':date_of_instn', $date_of_instn);
    $stmt->bindParam(':sw_desc', $sw_desc);
    $stmt->bindParam(':cat_id', $cat_id);
    $stmt->bindParam(':subcat_id', $subcat_id);

    $stmt->execute();

    echo '{"Status":{"Text": "Software Added"}}';

    } catch (PDOException $e){
    //print out error if connection to database fails
    echo '{
        "error": {"text": '.$e->getMessage().'};
        }';
    } 
    
}); 


//Update installed tool
$app->put('/api/tools/update/{sw_id}',function(request $request, response $response){

    //get the id to update
    $sw_id = $request->getAttribute('sw_id');

    //get software params
    $sw_name = $request->getParam('sw_name');
    $sw_url  = $request->getParam('sw_url');
    $date_of_instn = $request->getParam('date_of_instn');
    $sw_desc = $request->getParam('sw_desc');
    $cat_id = $request->getParam('cat_id');
    $subcat_id = $request->getParam('subcat_id');

    $sql = "UPDATE b_installed_sw SET 
                        sw_name =  :sw_name,
                        sw_url = :sw_url,
                        date_of_instn = :date_of_instn,  
                        sw_desc = :sw_desc,
                        cat_id = :cat_id, 
                        sub_catid = :sub_catid)
            WHERE = sw_id = $sw_id";

   //Connect to database and fetch tools
    try{
    $dbo = new Connect();
    $dbo = $dbo->conecta();
    $stmt = $dbo->prepare($sql);
    
    $stmt->bindParam(':sw_name', $sw_name);
    $stmt->bindParam(':sw_url', $sw_url);
    $stmt->bindParam(':date_of_instn', $date_of_instn);
    $stmt->bindParam(':sw_desc', $sw_desc);
    $stmt->bindParam(':cat_id', $cat_id);
    $stmt->bindParam(':subcat_id', $subcat_id);

    $stmt->execute();

    echo '{"Status":{"Text": "Software Added"}}';

    } catch (PDOException $e){
    //print out error if connection to database fails
    echo '{
        "error": {"text": '.$e->getMessage().'};
        }';
    } 
    
}); 


//Delete tool
$app->get('/api/tools/delete/{sw_id}',function(request $request, response $response){

    //get software id
    $sw_id = $request->getAttribute('sw_id');

    $sql = "DELETE FROM b_installed_sw WHERE sw_id=$sw_id";

   //Connect to database and fetch tools
    try{
    $dbo = new Connect();
    
    $dbo = $dbo->conecta();

    $stmt = $dbo->prepare($sql);

    $stmt->execute();
    
    $dbo = null;
    echo '{
        "Status": {"text": "Software deleted"};
        }';
    } catch (PDOException $e){
    //print out error if connection to database fails
    echo '{
        "error": {"text": '.$e->getMessage().'};
        }';
    } 
    
});