<?php
require_once "config.php"; 

$search = "";
$results = [];

if (isset($_POST['search'])) {
    $search = trim($_POST['search']);
    $sql = "SELECT * FROM beehive_readings 
            WHERE reading_id LIKE ? 
               OR timestamp LIKE ? 
               OR temperature LIKE ? 
               OR humidity LIKE ? 
               OR weight LIKE ? 
               OR fan_status LIKE ?";

    if ($stmt = $link->prepare($sql)) {
        $param = "%" . $search . "%";
        $stmt->bind_param("ssssss", $param, $param, $param, $param, $param, $param);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}

$link->close();
?>

<form method="post">
    <input type="text" name="search" placeholder="Search here..." 
           value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<?php if (!empty($results)): ?>
    <h3>Search Results:</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Timestamp</th>
            <th>Temperature (°C)</th>
            <th>Humidity (%)</th>
            <th>Weight (kg)</th>
            <th>Fan Status</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['reading_id']); ?></td>
                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($row['temperature']); ?></td>
                <td><?php echo htmlspecialchars($row['humidity']); ?></td>
                <td><?php echo htmlspecialchars($row['weight']); ?></td>
                <td><?php echo $row['fan_status'] ? "ON" : "OFF"; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif (isset($_POST['search'])): ?>
    <p>No results found.</p>
<?php endif; ?>
