<?php
$conn = mysqli_connect('localhost', 'root', '', 'newevidance');

$m_msg = "";
$p_msg = "";

if(isset($_POST['m_submit'])){
   $mname = $_POST['mname'];
   $address = $_POST['address'];

   if($conn->query("INSERT INTO manufacturer(name,address)
                    VALUES('$mname','$address')")){
      $m_msg = "Manufacturer added successfully";
   } else {
      $m_msg = "Failed to add manufacturer";
   }
}

if(isset($_POST['submit'])){
   $name = $_POST['pname'];
   $price = $_POST['price'];
   $mid   = $_POST['mid'];

   if($conn->query("INSERT INTO product(name,price,manufacture_id)
                    VALUES('$name','$price','$mid')")){
      $p_msg = "Product added successfully";
   } else {
      $p_msg = "Failed to add product";
   }
}

if(isset($_GET['delete'])){
   $id = $_GET['delete'];
   $conn->query("DELETE FROM manufacturer WHERE id=$id");
}

$manu = $conn->query("SELECT * FROM manufacturer");
$result = $conn->query("SELECT * FROM product_above_5000");
$all = $conn->query("SELECT * FROM manufacturer");
?>

<!DOCTYPE html>
<html>
<head>
   <title>All In One</title>
</head>

<body>

<h2>Insert Manufacturer</h2>
<p><?php echo $m_msg; ?></p>

<form method="POST">
   Name:<br>
   <input type="text" name="mname"><br>

   Address:<br>
   <input type="text" name="address"><br><br>

   <input type="submit" name="m_submit" value="Insert Manufacturer">
</form>

<hr>

<h2>Insert Product</h2>
<p><?php echo $p_msg; ?></p>

<form method="POST">
   Product Name:<br>
   <input type="text" name="pname"><br>

   Price:<br>
   <input type="text" name="price"><br>

   Manufacturer:<br>
   <select name="mid">
      <option value="">Select</option>

      <?php while($row = $manu->fetch_row()) { ?>
         <option value="<?php echo $row[0]; ?>">
            <?php echo $row[1]; ?>
         </option>
      <?php } ?>

   </select><br><br>

   <input type="submit" name="submit" value="Insert Product">
</form>

<hr>

<h2>Manufacturer List</h2>

<table border="1">
<tr>
   <th>ID</th>
   <th>Name</th>
   <th>Address</th>
   <th>Action</th>
</tr>

<?php while($row = $all->fetch_row()) { ?>
<tr>
   <td><?php echo $row[0]; ?></td>
   <td><?php echo $row[1]; ?></td>
   <td><?php echo $row[2]; ?></td>
   <td>
      <a href="?delete=<?php echo $row[0]; ?>">Delete</a>
   </td>
</tr>
<?php } ?>

</table>

<hr>

<h2>Products Price > 5000</h2>

<table border="1">
<tr>
   <th>ID</th>
   <th>Name</th>
   <th>Price</th>
   <th>Manufacturer ID</th>
</tr>

<?php while($row = $result->fetch_row()) { ?>
<tr>
   <td><?php echo $row[0]; ?></td>
   <td><?php echo $row[1]; ?></td>
   <td><?php echo $row[2]; ?></td>
   <td><?php echo $row[3]; ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>