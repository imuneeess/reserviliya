<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'reservationdatabase';

$conn = new mysqli($host, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Fonction pour effectuer une réservation de voiture
function faireReservationLocationVoiture($prenom, $nom, $email, $dateReservation, $heureReservation, $typeReservation, $locationVoitureSelectionnee, $villeSelectionnee, $typePaiement, $conn) {
    // Étape 1 : Insérer dans la table clients
    $requeteInsertionClient = "INSERT INTO clients (Prenom, Nom, Email) VALUES ('$prenom', '$nom', '$email')";
    $conn->query($requeteInsertionClient);

    // Étape 2 : Récupérer l'ID client généré
    $idClient = $conn->insert_id;

    // Étape 3 : Insérer dans la table réservations
    $requeteInsertionReservation = "INSERT INTO reservations (IDClient, DateReservation, HeureReservation, TypeReservation, type_paiement) 
                              VALUES ('$idClient', '$dateReservation', '$heureReservation', '$typeReservation','$typePaiement')";
    $resultat = $conn->query($requeteInsertionReservation);

    // Vérifier si la requête a réussi
    if (!$resultat) {
        echo "Erreur : " . $conn->error;
    }
}

// Gérer la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST["first_name"];
    $nom = $_POST["last_name"];
    $email = $_POST["email"];
    $dateReservation = $_POST["reservation_date"];
    $heureReservation = $_POST["reservation_time"];
    $typeReservation = "location de voiture"; // Définir le type de réservation sur "location de voiture"
    $locationVoitureSelectionnee = $_POST["car_rental"];
    $villeSelectionnee = $_POST["town"];
    $typePaiement = $_POST["payment_type"];

    // Vérifier si car_rental est défini
    if (isset($_POST["car_rental"])) {
        faireReservationLocationVoiture($prenom, $nom, $email, $dateReservation, $heureReservation, $typeReservation, $locationVoitureSelectionnee, $villeSelectionnee, $typePaiement, $conn);
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Réservation de Voitures de Location</title>
    <!-- Ajoutez le lien vers votre feuille de style ici -->
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
    <h1>Bienvenue dans le Système de Réservation de Voitures de Location !</h1>

    <!-- Formulaire de réservation -->
    <form method="post" action="">
        <label for="first_name">Prénom :</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">Nom :</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="email">E-mail :</label>
        <input type="email" name="email" id="email" required>

        <label for="town">Sélectionnez une ville au Maroc :</label>
        <select name="town" id="town" onchange="populateCarRentals()" required>
            <option value="Casablanca">Casablanca</option>
            <option value="Rabat">Rabat</option>
            <option value="Marrakech">Marrakech</option>
            <option value="Fes">Fes</option>
            <option value="Tanger">Tanger</option>
            <!-- Ajoutez plus de villes au besoin -->
        </select>

        <label for="car_rental">Sélectionnez une voiture de location :</label>
        <select name="car_rental" id="car_rental" required>
            <!-- Cela sera rempli dynamiquement en fonction de la ville sélectionnée -->
        </select>

        <label for="reservation_date">Sélectionnez la date de réservation :</label>
        <input type="date" name="reservation_date" id="reservation_date" required>

        <label for="reservation_time">Sélectionnez l'heure de réservation :</label>
        <input type="time" name="reservation_time" id="reservation_time" required>

        <label for="payment_type">Sélectionnez le type de paiement :</label>
        <select name="payment_type" id="payment_type" required>
            <option value="cash">Espèces</option>
            <option value="card">Carte</option>
        </select>

        <label for="reservation_type">Type de Réservation :</label>
        <input type="text" name="reservation_type" id="reservation_type" value="location de voiture" disabled>

        <input type="submit" value="Effectuer la réservation">
    </form>

    <!-- Afficher les options de location de voitures disponibles -->
    <h2>Voitures de Location Disponibles</h2>
    <div class="options" id="carRentalOptions">
        <!-- Remplir cette section dynamiquement en fonction de la ville sélectionnée -->
    </div>

    <!-- Bouton de retour -->
    <button class="back-button" onclick="window.location.href='frontpage.php'">Retour à la Page Principale</button>
</div>

<script>
    function populateCarRentals() {
        var locationsVoitures = {
            Casablanca: ['Location de voiture 1', 'Location de voiture 2', 'Location de voiture 3'],
            Rabat: ['Location de voiture A', 'Location de voiture B', 'Location de voiture C'],
            Marrakech: ['Location de voiture X', 'Location de voiture Y', 'Location de voiture Z'],
            Fes: ['Location de voiture I', 'Location de voiture II', 'Location de voiture III'],
            Tanger: ['Location de voiture P', 'Location de voiture Q', 'Location de voiture R']
        };

        var villeSelectionnee = document.getElementById('town').value;
        var selectLocationVoiture = document.getElementById('car_rental');
        var optionsLocationVoiture = document.getElementById('carRentalOptions');

        // Effacer les options existantes
        selectLocationVoiture.innerHTML = '';
        optionsLocationVoiture.innerHTML = '';

        // Remplir le menu déroulant de location de voitures
        locationsVoitures[villeSelectionnee].forEach(function (locationVoiture) {
            var option = document.createElement('option');
            option.value = locationVoiture;
            option.textContent = locationVoiture;
            selectLocationVoiture.appendChild(option);
        });

        // Afficher les options de location de voitures
        locationsVoitures[villeSelectionnee].forEach(function (locationVoiture) {
            var optionDiv = document.createElement('div');
            optionDiv.className = 'option';
            optionDiv.innerHTML = '<img src="https://via.placeholder.com/200?text=' + locationVoiture + '" alt="' + locationVoiture + '"><p>' + locationVoiture + '</p>';
            optionsLocationVoiture.appendChild(optionDiv);
        });

        // Conditions supplémentaires pour chaque ville
        if (villeSelectionnee === 'Casablanca') {
            // Ajouter des conditions spécifiques pour Casablanca
            console.log('Conditions spécifiques pour Casablanca');
        } else if (villeSelectionnee === 'Rabat') {
            // Ajouter des conditions spécifiques pour Rabat
            console.log('Conditions spécifiques pour Rabat');
        } else if (villeSelectionnee === 'Marrakech') {
            // Ajouter des conditions spécifiques pour Marrakech
            console.log('Conditions spécifiques pour Marrakech');
        } else if (villeSelectionnee === 'Fes') {
            // Ajouter des conditions spécifiques pour Fes
            console.log('Conditions spécifiques pour Fes');
        } else if (villeSelectionnee === 'Tanger') {
            // Ajouter des conditions spécifiques pour Tanger
            console.log('Conditions spécifiques pour Tanger');
        }
    }
</script>

</body>
</html>
