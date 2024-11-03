<?php
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "your_database_name"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$emp_id = $name = $designation = $salary = $doj = "";
$errors = [];

// Handle form submission for updating employee details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Get the data from the form
    $emp_id = $_POST['emp_id'];
    $name = trim($_POST['name']);
    $designation = trim($_POST['designation']);
    $salary = $_POST['salary'];
    $doj = $_POST['doj'];

    // Validation
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }
    if (empty($designation)) {
        $errors['designation'] = "Designation is required";
    }
    if (empty($salary) || !is_numeric($salary)) {
        $errors['salary'] = "Valid salary is required";
    }
    if (empty($doj)) {
        $errors['doj'] = "Date of joining is required";
    }

    // If no errors, proceed with updating the database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE employees SET name=?, designation=?, salary=?, doj=? WHERE emp_id=?");
        $stmt->bind_param("ssdsi", $name, $designation, $salary, $doj, $emp_id);

        if ($stmt->execute()) {
            echo "<div class='success'>Employee details updated successfully!</div>";
        } else {
            echo "<div class='error'>Error updating record: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle form submission for retrieving employee details
if (isset($_POST['retrieve'])) {
    $emp_id = $_POST['emp_id'];

    if (!empty($emp_id)) {
        $stmt = $conn->prepare("SELECT * FROM employees WHERE emp_id=?");
        $stmt->bind_param("i", $emp_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $employee = $result->fetch_assoc();
            $name = $employee['name'];
            $designation = $employee['designation'];
            $salary = $employee['salary'];
            $doj = $employee['doj'];
        } else {
            echo "<div class='error'>No employee found with ID: $emp_id</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='error'>Employee ID is required to retrieve details.</div>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 400px;
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        .success {
            color: green;
            font-size: 1.1em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Employee Details</h2>
    <form method="post" action="">
        <label for="emp_id">Employee ID:</label>
        <input type="number" name="emp_id" value="<?php echo htmlspecialchars($emp_id); ?>" required>

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <div class="error"><?php echo isset($errors['name']) ? $errors['name'] : ''; ?></div>

        <label for="designation">Designation:</label>
        <input type="text" name="designation" value="<?php echo htmlspecialchars($designation); ?>">
        <div class="error"><?php echo isset($errors['designation']) ? $errors['designation'] : ''; ?></div>

        <label for="salary">Salary:</label>
        <input type="number" name="salary" value="<?php echo htmlspecialchars($salary); ?>" step="0.01">
        <div class="error"><?php echo isset($errors['salary']) ? $errors['salary'] : ''; ?></div>

        <label for="doj">Date of Joining:</label>
        <input type="date" name="doj" value="<?php echo htmlspecialchars($doj); ?>">
        <div class="error"><?php echo isset($errors['doj']) ? $errors['doj'] : ''; ?></div>

        <input type="submit" name="update" value="Update Employee Details">
        <input type="submit" name="retrieve" value="Retrieve Employee Details">
    </form>
</body>
</html>
