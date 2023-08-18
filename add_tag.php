
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
        "/admin/products/"
        .$_GET['id'].
        ".json",
        $create_data,
        "GET"
    );
    $created_product_response = json_decode($created_product["response"]);


if(isset($_POST['add'])){
    $tags = str_replace('X', ' ', $_POST["tags"]);
    if(!empty($created_product_response->product->tags)){
        $tag =  $created_product_response->product->tags.",".$tags;
    }
    else{
        $tag = $tags;
    }
    $create_data1 = [
        "product" => [
            "tags" => $tag,
        ]
    ];
    $created_product1 = shopify_call(
        $token,
        $shop,
        "/admin/products/".
       $_GET['id'].
        ".json",
        $create_data1,
        "PUT"
    );
    $created_product_response1= json_decode($created_product1["response"]);
    if(!empty($created_product_response1)){
        header("Location: api_call_add_remove_tag.php");
        exit;
    }
    else{
        echo "<script>alert('tag not added')</script>";
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
input[type="text"],input[type="number"]{
  margin: 0 0 1em;
  border: 1px solid #ccc;
  outline: none;
}
input.invalid, textarea.invalid {
  border-color: #d5144d;
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
  		<h2>Update Inventory</h2>
  		<form method="post" enctype="multipart/form-data">
  			<label><span>Tags: </span><input type="text" id="tag" name="tags" required placeholder="Tag Id"></label>
           		<input name="add" type="submit" value="Send"/>
  		</form>		
  		</section>
  </body>

</html>