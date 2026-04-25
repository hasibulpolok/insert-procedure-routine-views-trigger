<?php
$conn = mysqli_connect('localhost', 'root', '', 'newevidance');

$result = $conn->query("SELECT * FROM product_above_5000");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products Above 5000</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h3>Products Price > 5000</h3>

    <table class="table table-bordered table-striped">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Manufacturer ID</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['price']; ?></td>
            <td><?php echo $row['manufacture_id']; ?></td>
        </tr>
        <?php } ?>

    </table>

</div>

</body>
</html>