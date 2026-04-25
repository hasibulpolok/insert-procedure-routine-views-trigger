<?php
$conn = mysqli_connect('localhost', 'root', '', 'newevidance');

if (isset($_POST['m_submit'])) {
   $mname = $_POST['mname'];
   $address = $_POST['address'];

   $conn->query("INSERT INTO manufacturer(name, address)
                 VALUES('$mname','$address')");
}

if (isset($_POST['submit'])) {

   $name = $_POST['pname'];
   $price = $_POST['price'];
   $mid   = $_POST['mid'];

   $conn->query("INSERT INTO product(name, price, manufacture_id)
                 VALUES('$name','$price','$mid')");
}

$manu = $conn->query("SELECT * FROM manufacturer");
$result = $conn->query("SELECT * FROM product_above_5000");
?>

<!DOCTYPE html>
<html>
<head>
   <title>All In One</title>
</head>

<body>

<h2>Insert Manufacturer</h2>
<form method="POST">
   Name:<br>
   <input type="text" name="mname"><br>

   Address:<br>
   <input type="text" name="address"><br><br>

   <input type="submit" name="m_submit" value="Insert Manufacturer">
</form>

<hr>

<h2>Insert Product</h2>
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