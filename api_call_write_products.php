<?php

// Get our helper function
require_once "inc/functions.php";
if (isset($_POST["submit"])) {
    $query = [
        "Content-type" => "application/json", // Tell Shopify that we're expecting a response in JSON format
    ];

    // Create product data

      $create_data = [
        "product" => [
            "title" => $_POST["title"],
            "body_html" => $_POST["description"],
            "vendor" => $_POST["vendor"],
            "product_type" => $_POST["product_type"],
            "tags" => $_POST["tags"],
            "status" => $_POST["status"],

        ]
    ];


    // Run API call to modify the product
    $created_product = shopify_call(
        $token,
        $shop,
        "/admin/products.json",
        $create_data,
        "POST"
    );


    $created_product_response = json_decode($created_product["response"]);

    $total = count($_FILES["image"]["name"]);
    // Loop through each file
    if( $total > 1) {
      
        for ($i = 0; $i < $total; $i++) {
            $image = file_get_contents($_FILES["image"]["tmp_name"][$i]);
            $imageData = base64_encode($image);
            $image_data = [
                "image" => [
                    "attachment" => $imageData,
                    "filename" => $_FILES["image"]["name"][$i],
                ],
            ];
    //call to images.json api
            $attached_image_product = shopify_call(
                $token,
                $shop,
                "/admin/api/2023-04/products/" .
                    $created_product_response->product->id .
                    "/images.json",
                $image_data,
                "POST"
            );
        }
    }

   $url_image= explode(",",$_POST['url']);
   $url_count = count($url_image);
   // Loop through each file
   if($url_count > 0) {
       for ($i = 0; $i < $url_count; $i++) {
           $image_url_data = [
               "image" => [
                   "src" => $url_image[$i],
               ],
           ];
   //call to images.json api
           $attached_image_url_product = shopify_call(
               $token,
               $shop,
               "/admin/api/2023-04/products/" .
                   $created_product_response->product->id .
                   "/images.json",
               $image_url_data,
               "POST"
           );
       }
   }
    //Create variant data

    if($_POST['variant'] == '')
    {
        $variant_data = [
          "variant" => [
              "price" => $_POST["price"],
              "barcode" => $_POST["barcode"],
              "sku" => $_POST["sku"],
              "grams" => $_POST["weight"],
          ],
      ];
  }else{
    $variant_data = [
      "variant" => [
          "option1" => $_POST["variant"],
          "price" => $_POST["price"],
          "barcode" => $_POST["barcode"],
          "sku" => $_POST["sku"],
          "grams" => $_POST["weight"],
      ],
  ];
  }
    $attached_variant_product = shopify_call(
        $token,
        $shop,
        "/admin/api/2023-04/variants/" .
        $created_product_response->product->variants[0]->id .
            ".json",
        $variant_data,
        "PUT"
    );

    if (
        !empty($attached_image_product["response"]) ||
        !empty($created_product["response"]) ||
        !empty($attached_variant_product["response"])
    ) {
      $set_invetory_item_id = json_decode($created_product["response"]);




      //set tracked inventory to true
        $tracked_data = [
            "inventory_item" => [
                "id" => $set_invetory_item_id->variant->inventory_item_id,
                "tracked" => true,
            ],
        ];
        $inventory_set_true = shopify_call(
            $token,
            $shop,
            "/admin/api/2023-04/inventory_items/" .
                $set_invetory_item_id->variant->inventory_item_id .
                ".json",
            $tracked_data,
            "PUT"
        );
        //Get the location from locations api
        $location = [];
        $location = shopify_call(
            $token,
            $shop,
            "/admin/api/2023-04/locations.json",
            $location,
            "GET"
        );
        $location = json_decode($location["response"]);
        //Set quantity for
        if (!empty($_POST["quantity"])) {
            $inventory_quantity = [
                "inventory_item_id" =>
                    $set_invetory_item_id->variant->inventory_item_id,
                "location_id" => $location->locations[6]->id,
                "available" => $_POST["quantity"],
            ];
            $inventory_quantity = shopify_call(
                $token,
                $shop,
                "/admin/api/2023-04/inventory_levels/set.json",
                $inventory_quantity,
                "POST"
            );

            if (!empty($inventory_quantity["response"])) {
                echo '<script>alert("Product added Successfully ")</script>';
            } else {
                echo '<script>alert("Product Not added ")</script>';
            }
        }
    } else {
        echo '<script>alert("Product Not add ")</script>';
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
  <style>
  html {
  font: 300 100%/1.5 Ubuntu, sans-serif;
  color: #333;
  overflow-x: hidden;
}
h2 {
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
#contact, label, input[name="submit"] {
  position: relative;
}
label > span, input, textarea, button,select {
  box-sizing: border-box;
}
label {
  display: block;
}
label > span {
  display: none;
}
input, textarea, button ,select{
  width: 100%;
  padding: 0.5em;
  border: none;
  font: 300 100%/1.2 Ubuntu, sans-serif;
}
input[type="text"], input[type="email"], textarea, select, input[type="number"], input[type="file"],input[type="url"]{
  margin: 0 0 1em;
  border: 1px solid #ccc;
  outline: none;
}
input.invalid, textarea.invalid {
  border-color: #d5144d;
}
textarea {
  height: 6em;
}
input[type="submit"], button {
  background: #a7cd80;
  color: #333;
}
input[type="submit"]:hover, button:hover {
  background: #91b36f;
}
 select {
  padding: 0.7em;
    font: 300 100%/1.2 Ubuntu, sans-serif;
    width: 73%;
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
  input, textarea, button,select {
    width: 73.6263%;
    line-height: 1.5;
  }
  textarea {
    height: 10em;
  }
  input[type="submit"], button {
    margin-left: 26.3736%;
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
</style>
  </head>
<body>
<section id="contact-form">
  <h2>Create Product</h2>
  <form method="post" enctype="multipart/form-data">
    <label><span>Title</span><input type="text" id="title" name="title" required></label>
    <label><span>Description</span><input type="text" id="description" name="description" ></label>
    <label><span>Vendor</span><input type="text" id="vendor" name="vendor" required></label>
    <label><span>Product_type</span>
      <select name="product_type" required>
        <option value='Board Games'>Board Games</option>
        <option value='Miniatures'>Miniatures</option>
        <option value='Collectibles'>Collectibles</option>
        <option value='Roleplaying Games'>Roleplaying Games</option>
        <option value='Accessories'>Accessories</option>
      </select>
    </label>
    <label><span>Product_tags</span>
      <select name="tags" required>
        <option value='Board Games'>Board Games</option>
        <option value='Miniatures'>Miniatures</option>
        <option value='Collectibles'>Collectibles</option>
        <option value='Roleplaying Games'>Roleplaying Games</option>
        <option value='Accessories'>Accessories</option>
      </select>
    </label>
    <label><span>Media</span><input type="file" id="image" name="image[]" multiple></label>
    <label><span>URL(separate the urls by comma)</span><input type="url" id="url" name="url" ></label>
    <label><span>Variant</span><input type="text" id="variant" name="variant"></label>
    <label><span>Price</span><input type="number" id="price" name="price" required></label>
    <label><span>Quantity</span><input type="number" id="quantity" name="quantity"></label>
    <label><span>Weight</span><input type="number" id="weight" name="weight"></label>
    <label><span>Barcode</span><input type="text" id="barcode" name="barcode" required></label>
    <label><span>SKU</span><input type="text" id="sku" name="sku" required></label>

    <label><span>Status</span>
    <select name="status" required>
      <option value='draft'>Draft</option>
      <option value='archived'>Archived</option>
      <option value='active'>Active</option>
    </select>
    </label>
    <input name="submit" type="submit" value="Send"/>
  </form>
</section>
</body>
</html>
