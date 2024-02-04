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

// Function to make a reservation
function makeSpaReservation($firstName, $lastName, $email, $reservationDate, $reservationTime, $reservationType, $selectedSpa, $selectedCity, $paymentType, $conn) {
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
    $reservationType = "spa"; // Set the reservation type to "spa"
    $selectedSpa = $_POST["spa"];
    $selectedCity = $_POST["town"];
    $paymentType = $_POST["payment_type"];

    // Check if spa is set
    if (isset($_POST["spa"])) {
        makeSpaReservation($firstName, $lastName, $email, $reservationDate, $reservationTime, $reservationType, $selectedSpa, $selectedCity, $paymentType, $conn);
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
    <title>Spa Reservation System</title>
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
    <h1>Welcome to the Spa Reservation System!</h1>

    <!-- Reservation Form -->
    <form method="post" action="">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="town">Select a City:</label>
        <select name="town" id="town" onchange="populateSpas()" required>
            <option value="Casablanca">Casablanca</option>
            <option value="Rabat">Rabat</option>
            <option value="Marrakech">Marrakech</option>
            <option value="Fes">Fes</option>
            <option value="Tangier">Tangier</option>
            <!-- Add more cities as needed -->
        </select>

        <label for="spa">Select a Spa:</label>
        <select name="spa" id="spa" required>
            <!-- This will be populated dynamically based on the selected city -->
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
        <input type="text" name="reservation_type" id="reservation_type" value="spa" disabled>

        <input type="submit" value="Make Reservation">
    </form>

    <!-- Display Spa Options -->
    <h2>Available Spas</h2>
    <div class="options" id="spaOptions">
        <!-- Populate this section dynamically based on the selected city -->
    </div>

    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href='frontpage.php'">Back to Main Page</button>
</div>

<script>
    function populateSpas() {
        var spas = {
            Casablanca: ['Spa 1', 'Spa 2', 'Spa 3'],
            Rabat: ['Spa A', 'Spa B', 'Spa C'],
            Marrakech: ['Spa X', 'Spa Y', 'Spa Z'],
            Fes: ['Spa I', 'Spa II', 'Spa III'],
            Tangier: ['Spa P', 'Spa Q', 'Spa R']
        };

        var selectedCity = document.getElementById('town').value;
        var spaSelect = document.getElementById('spa');
        var spaOptions = document.getElementById('spaOptions');

        // Clear existing options
        spaSelect.innerHTML = '';
        spaOptions.innerHTML = '';

        // Populate spa dropdown
        spas[selectedCity].forEach(function (spa) {
            var option = document.createElement('option');
            option.value = spa;
            option.textContent = spa;
            spaSelect.appendChild(option);
        });

        // Display spa options
        spas[selectedCity].forEach(function (spa) {
            var optionDiv = document.createElement('div');
            optionDiv.className = 'option';
            optionDiv.innerHTML = '<img src="https://via.placeholder.com/200?text=' + spa + '" alt="' + spa + '"><p>' + spa + '</p>';
            spaOptions.appendChild(optionDiv);
        });

        // Additional conditions for each city
        if (selectedCity === 'Casablanca') {
            // Add specific conditions for Casablanca
            console.log('Specific conditions for Casablanca');
        } else if (selectedCity === 'Rabat') {
            // Add specific conditions for Rabat
            console.log('Specific conditions for Rabat');
        } else if (selectedCity === 'Marrakech') {
            // Add specific conditions for Marrakech
            console.log('Specific conditions for Marrakech');
        } else if (selectedCity === 'Fes') {
            // Add specific conditions for Fes
            console.log('Specific conditions for Fes');
        } else if (selectedCity === 'Tangier') {
            // Add specific conditions for Tangier
            console.log('Specific conditions for Tangier');
        }
    }
</script>

</body>
</html>
