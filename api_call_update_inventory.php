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
if (isset($_POST["inventory" . $_POST["product_id"]])) {
    $inventory_data = [
        "location_id" => $_POST["location"],
        "inventory_item_id" => $_POST["variant"],
        "available_adjustment" => $_POST["quantity"],
    ];

    $update_inventory = shopify_call(
        $token,
        $shop,
        "/admin/api/2023-07/inventory_levels/adjust.json",
        $inventory_data,
        "POST"
    );
    if (!empty($update_inventory["response"])) {
        echo '<script>alert("Inventory added Successfully ")</script>';
    } else {
        echo '<script>alert("Inventory Not added ")</script>';
    }
}
if (isset($_POST["add" . $_POST["id"]])) {
    $tags = str_replace("X", " ", $_POST["tags"]);
    $create_data1 = [
        "product" => [
            "tags" => $tags,
        ],
    ];
    $created_product1 = shopify_call(
        $token,
        $shop,
        "/admin/products/" . $_POST["id"] . ".json",
        $create_data1,
        "PUT"
    );
    $created_product_response1 = json_decode($created_product1["response"]);
    if (!empty($created_product_response1)) {
        header("Location: api_call_add_remove_tag.php");
        exit();
    }
}
if (isset($_POST["button2" . $_POST["id"]])) {
    $create_data1 = [
        "product" => [
            "tags" => "",
        ],
    ];
    $created_product1 = shopify_call(
        $token,
        $shop,
        "/admin/products/" . $_POST["id"] . ".json",
        $create_data1,
        "PUT"
    );
    $created_product_response1 = json_decode($created_product1["response"]);

    if (!empty($created_product_response1)) {
        header("Location: api_call_add_remove_tag.php");
        exit();
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
   a {
    color: #333!important;
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
   #contact-form h2 {
   margin: 0;
   color: #8495a5;
   font-size: 2.5em;
   font-weight: 300;
   }
   #contact-form {
   max-width: 1208px;
   max-width: 75.5rem;
   margin: 0 auto;
   }
   #contact-form label {
   display: block;
   }
   #contact-form input[type="text"],input[type="number"]{
   margin: 0 0 1em;
   border: 1px solid #ccc;
   outline: none;
   }
   #contact-form input.invalid, textarea.invalid {
   border-color: #d5144d;
   }
   #contact-form select {
   padding: 0.7em;
   font: 300 100%/1.2 Ubuntu, sans-serif;
   width: 73%;
   margin-top: 10px;
   }
   @media screen and (min-width: 30em) {
   #contact-form h2 {
   margin-left: 26.3736%;
   font-size: 2em;
   line-height: 1.5;
   }
   label > span {
   vertical-align: top;
   display: inline-block;
   width: 25%;
   padding: 0.5em;
   border: 1px solid transparent;
   text-align: right;
   }
   input, textarea,select {
   width: 73.6263%;
   line-height: 1.5;
   }
   textarea {
   height: 10em;
   }
   }
   @media screen and (min-width: 48em) {
   #contact-form {
   text-align: justify;
   line-height: 0;
   }
   #contact-form:after {
   content: '';
   display: inline-block;
   width: 100%;
   }
   #contact-form h2 {
   margin-left: 17.2661%;
   }
   #contact-form form, #contact-form aside {
   vertical-align: top;
   display: inline-block;
   width: 65.4676%;
   text-align: left;
   line-height: 1.5;
   }
   }
   .modal-body input[type="submit"] {
   margin: 10px;
   /* padding: 10px; */
   padding: 0.7em;
   font: 300 100%/1.2 Ubuntu, sans-serif;
   width: 73%;
   align-items: center;
   justify-content: center;
   margin-left: 97px;
   }
</style>
<div class="container">
   <h2>Products</h2>
   <table class="table">
      <thead>
         <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php
         $counter = 0;
         foreach ($created_product_response->products as $product) { ?>
         <tr>
            <td><?php echo ++$counter; ?></td>
            <td><?php echo $product->title; ?></td>
            <td>
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal<?php echo $product->id; ?>">Add Inventory</button>
               <form method="post" style="display:none" >
                  <input type="hidden" name="id" id="product_id" value="<?php echo $product->id; ?>">
                  <input type="text" name="tags" id='tagsssss<?php echo $product->id; ?>' >
                  <input type="submit" name="add<?php echo $product->id; ?>" id="add<?php echo $product->id; ?>" value="Add">
                  <input type="submit" name="button2<?php echo $product->id; ?>" id="button2<?php echo $product->id; ?>"
                     class="button" value="Button2" />
               </form>
               <?php
               $data = [];
               $single_product = shopify_call(
                   $token,
                   $shop,
                   "/admin/products/" . $product->id . ".json",
                   $data,
                   "GET"
               );

               $single_product1 = json_decode($single_product["response"]);

               $data = [];
               $location = shopify_call(
                   $token,
                   $shop,
                   "/admin/locations.json",
                   $data,
                   "GET"
               );

               $locations = json_decode($location["response"]);
               ?>
               <div id="myModal<?php echo $product->id; ?>" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                     <!-- Modal content-->
                     <div class="modal-content">
                        <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal">&times;</button>
                           <h4 class="modal-title">Modal Header</h4>
                        </div>
                        <div class="modal-body">
                           <section id="contact-form">
                              <h2>Add Inventory</h2>
                              <form method="post" enctype="multipart/form-data">
                                 <input type="hidden" name="product_id" id="product_id" value="<?php echo $product->id; ?>">                                
                                 <label><span>Quantity: </span><input type="number" id="quantity" name="quantity" required placeholder="Quantity"></label>
                                 <label><span>location: </span>
                                 <select name="location" required>
                                    <?php foreach (
                                        $locations->locations
                                        as $loc
                                    ) { ?>
                                    <option value='<?php echo $loc->id; ?>'><?php echo $loc->country_name; ?></option>
                                    <?php } ?>
                                 </select>
                                 <label><span>Inventory: </span>
                                 <select name="variant" required>
                                    <?php foreach (
                                        $single_product1->product->variants
                                        as $variant
                                    ) { 
                                       if($variant->title == "Default Title"){
                                          ?>
                                          <option value='<?php echo $variant->inventory_item_id; ?>'><?php echo $product->title; ?></option>
                                   
                                          <?php
                                       }else{
                                       ?>
                                    <option value='<?php echo $variant->inventory_item_id; ?>'><?php echo $variant->title; ?></option>
                                    <?php }} ?>
                                 </select>
                                 <input name="inventory<?php echo $product->id; ?>" type="submit" value="Send"/>
                              </form>
                           </section>
                           </form>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                  </div>
               </div>
         </tr>
         <?php }
         ?>
      </tbody>
   </table>
</div>