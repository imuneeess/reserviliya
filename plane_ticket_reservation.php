<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'reservationdatabase';

$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to make a plane ticket reservation
function makePlaneTicketReservation($firstName, $lastName, $email, $reservationDate, $reservationTime, $reservationType, $selectedDestination, $selectedClass, $paymentType, $conn) {
    // Step 1: Insert into customers table
    $insertCustomerQuery = "INSERT INTO customers (FirstName, LastName, Email) VALUES ('$firstName', '$lastName', '$email')";
    $conn->query($insertCustomerQuery);

    // Step 2: Retrieve the generated CustomerId
    $customerId = $conn->insert_id;

    // Step 3: Insert into reservations table
    $insertReservationQuery = "INSERT INTO reservations (CustomerID, ReservationDate, ReservationTime, ReservationType, type_paiement) 
                              VALUES ('$customerId', '$reservationDate', '$reservationTime', '$reservationType','$paymentType')";
    $result = $conn->query($insertReservationQuery);

    // Check if the query was successful
    if (!$result) {
        echo "Error: " . $conn->error;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $reservationDate = $_POST["reservation_date"];
    $reservationTime = $_POST["reservation_time"];
    $reservationType = "plane ticket"; // Set the reservation type to "plane ticket"
    $selectedDestination = $_POST["destination"];
    $selectedClass = $_POST["class"];
    $paymentType = $_POST["payment_type"];

    // Check if destination is set
    if (isset($_POST["destination"])) {
        makePlaneTicketReservation($firstName, $lastName, $email, $reservationDate, $reservationTime, $reservationType, $selectedDestination, $selectedClass, $paymentType, $conn);
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
    <title>Plane Ticket Reservation System</title>
    <!-- Add your stylesheet link here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        select, input {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 15px;
        }

        input[type="submit"] {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .options {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .option {
            text-align: center;
            margin: 20px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .option:hover {
            transform: scale(1.1);
        }

        .option img {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome to the Plane Ticket Reservation System!</h1>

    <!-- Reservation Form -->
    <form method="post" action="">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="destination">Select a Destination:</label>
        <select name="destination" id="destination" onchange="populateClasses()" required>
            <option value="New York">New York</option>
            <option value="Paris">Paris</option>
            <option value="Tokyo">Tokyo</option>
            <option value="Dubai">Dubai</option>
            <option value="Sydney">Sydney</option>
            <!-- Add more destinations as needed -->
        </select>

        <label for="class">Select Class:</label>
        <select name="class" id="class" required>
            <!-- This will be populated dynamically based on the selected destination -->
        </select>

        <label for="reservation_date">Select Reservation Date:</label>
        <input type="date" name="reservation_date" id="reservation_date" required>

        <label for="reservation_time">Select Reservation Time:</label>
        <input type="time" name="reservation_time" id="reservation_time" required>

        <label for="payment_type">Select Payment Type:</label>
        <select name="payment_type" id="payment_type" required>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
        </select>

        <label for="reservation_type">Reservation Type:</label>
        <input type="text" name="reservation_type" id="reservation_type" value="plane ticket" disabled>

        <input type="submit" value="Make Reservation">
    </form>

    <!-- Display Plane Ticket Options -->
    <h2>Available Destinations</h2>
    <div class="options" id="planeTicketOptions">
        <!-- Populate this section dynamically based on the selected destination -->
    </div>

    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href='frontpage.php'">Back to Main Page</button>
</div>

<script>
    function populateClasses() {
        var classes = {
            'New York': ['Economy', 'Business', 'First Class'],
            'Paris': ['Economy', 'Business', 'First Class'],
            'Tokyo': ['Economy', 'Business', 'First Class'],
            'Dubai': ['Economy', 'Business', 'First Class'],
            'Sydney': ['Economy', 'Business', 'First Class']
        };

        var selectedDestination = document.getElementById('destination').value;
        var classSelect = document.getElementById('class');
        var planeTicketOptions = document.getElementById('planeTicketOptions');

        // Clear existing options
        classSelect.innerHTML = '';
        planeTicketOptions.innerHTML = '';

        // Populate class dropdown
        classes[selectedDestination].forEach(function (className) {
            var option = document.createElement('option');
            option.value = className;
            option.textContent = className;
            classSelect.appendChild(option);
        });

        // Display plane ticket options
        classes[selectedDestination].forEach(function (className) {
            var optionDiv = document.createElement('div');
            optionDiv.className = 'option';
            optionDiv.innerHTML = '<img src="https://via.placeholder.com/200?text=' + className + '" alt="' + className + '"><p>' + className + '</p>';
            planeTicketOptions.appendChild(optionDiv);
        });
    }
</script>

</body>
</html>
