<?php
   // Get our helper function
   require_once "inc/functions.php";
       $query = [
           "Content-type" => "application/json", // Tell Shopify that we're expecting a response in JSON format
       ];
   
       // Create product data
   
         $create_data = [];
   
   
       // Run API call to modify the product
       $created_product = shopify_call(
           $token,
           $shop,
           "/admin/products.json",
           $create_data,
           "GET"
       );
       $created_product_response = json_decode($created_product["response"]);
   
   if(isset($_POST['add'. $_POST['id']])){
       $tags = str_replace('X', ' ', $_POST["tags"]);
       $create_data1 = [
           "product" => [
               "tags" => $tags,
           ]
       ];
       $created_product1 = shopify_call(
           $token,
           $shop,
           "/admin/products/".
          $_POST['id'].
           ".json",
           $create_data1,
           "PUT"
       );
       $created_product_response1= json_decode($created_product1["response"]);
       if(!empty($created_product_response1)){
        header("Location: api_call_add_remove_tag.php");
        exit;
     }
   }
   if(isset($_POST['button2'. $_POST['id']])){
    $create_data1 = [
        "product" => [
            "tags" => '',
        ]
    ];
    $created_product1 = shopify_call(
        $token,
        $shop,
        "/admin/products/".
       $_POST['id'].
        ".json",
        $create_data1,
        "PUT"
    );
    $created_product_response1= json_decode($created_product1["response"]);
 if(!empty($created_product_response1)){
    header("Location: api_call_add_remove_tag.php");
    exit;
 }
}

 
      ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
   .tags {
   /* width: 80%; */
   /* border-radius: 25px; */
   border: 1px solid #8080805c;
   padding: 8px 20px;
   margin: 20px 20px 20px 20px;
   /* background: grey; */
   overflow: visible;
   box-shadow: 5px 5px 2px #888888;
   position: relative;
   border-radius: 20px;
   display: inline-block;
   }
   .x {
   width: 25px;
   position: absolute;
   top: -10px;
   right: -10px;
   border-radius: 50px;
   border: 1px solid black;
   height: 25px;
   /* font-size: 18px; */
   justify-content: center;
   text-align: center;
   align-items: center;
   font-weight: 700;
   }
</style>
<div class="container">
   <h2>Products</h2>
   <table class="table">
      <thead>
         <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Tags</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $counter = 0;
            foreach($created_product_response->products as $product){?>
         <tr>
            <td><?php echo ++$counter; ?></td>
            <td><?php echo $product->title; ?></td>
            <td>
               <ul id="items<?php echo $product->id;?>">
                  <?php
                     $string = $product->tags;
                     if(!empty( $string)){
                     $str_arr = explode (",", $string); 
                     foreach($str_arr as $tag){?>
                  <li id="tags<?php echo $product->id;?>" class="tags">
                     <button id = "x<?php echo $product->id;?>" class="x" onclick="remove(<?php echo $product->id;?>)">
                     X
                     </button>
                     <form method="post" style="display:none" >
                                        <input type="hidden" name="id" id="product_id" value="<?php echo $product->id;?>">
                                        <input type="text" name="tags" id='tagsssss<?php echo $product->id;?>' >
                                        <input type="submit" name="add<?php echo $product->id;?>" id="add<?php echo $product->id;?>" value="Add">
                                        <input type="submit" name="button2<?php echo $product->id;?>" id="button2<?php echo $product->id;?>"
                class="button" value="Button2" />
                                    </form>
                     <?php echo $tag;?>
                  </li>
                  <script>
                  function remove(id){
        // var id = document.getElementById("product_id").value;
        console.log('tags'+id)
        document.getElementById('tags'+id).remove()
        var ul = document.getElementsByTagName('ul');
        var li = ul[0].getElementsByTagName('li');
        var array = new Array();
        for (var i = 0; i < li.length; i++) {
            array.push(li[i].innerText)
        }
        console.log(array);
      if(array != ""){
        document.getElementById("tagsssss"+id).value = array;
        $( "#add"+id ).trigger( "click" );
      }else{
        $( "#button2"+id).trigger( "click" );
      }
      
    }
                               </script>  
                  <?php }
                     }
                     ?>
               </ul>
            </td>
            <td> <button class="btn btn-default"><a href="add_tag.php?id=<?php echo $product->id;?>">Add Tag</a></button></td>
         </tr>
         <?php
            }
            ?>
      </tbody>
   </table>
</div>