<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './Database/db.php';

// Ensure only logged-in users can submit the form
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$alert = "";

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($subject) || empty($message)) {
        $alert = "Please fill in all fields.";
    } else {
        // Insert into the contact_messages table
        $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, subject, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $subject, $message);
        
        if ($stmt->execute()) {
            $alert = "Your message has been sent successfully!";
        } else {
            $alert = "There was an error sending your message.";
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | A To B Delivery</title>
    <?php include './includes/head_tags.php'; ?>
    
    <!-- Add Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        * { font-family: "Roboto Mono", monospace; }
        body { background-color: var(--bs-success-bg-subtle); }

        /* Myanmar text */
        .mm-text {
            font-family: "Pyidaungsu", sans-serif;
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
        }

        /* Fade animation */
        .fade-in { opacity:0; transform:translateY(25px); transition:all 0.8s ease; }
        .fade-in.visible { opacity:1; transform:translateY(0); }

        /* Hero */
        .hero {
            padding-top:6rem;
            text-align:center;
        }
        .hero .card {
            border:none;
            border-radius:20px;
            background:#fff;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
            padding:3rem 2rem;
        }

        /* Section spacing */
        section { margin-top:2rem; margin-bottom:2rem; }

        /* Contact card */
        .contact-card {
            border:none;
            border-radius:1rem;
            background:#fff;
            box-shadow:0 6px 18px rgba(0,0,0,0.1);
            transition:transform 0.3s ease, box-shadow 0.3s ease;
            height:100%;
        }
        .contact-card:hover {
            transform:translateY(-5px);
            box-shadow:0 12px 28px rgba(0,0,0,0.15);
        }

        /* Contact form and info */
        .contact-info {
            padding-right: 30px;
        }

        .section-title {
            text-align:center;
            font-weight:700;
            color:#198754;
            margin-bottom:2.5rem;
        }

        /* Social Link Icons for Contact Section Only */
        .contact-info .social-link {
            font-size: 1.5rem; /* Size of the icons */
            color: #198754; /* Green color for icons */
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: color 0.3s ease;
        }

        .contact-info .social-link:hover {
            color: #155d27; /* Darker green when hovered */
        }

        .contact-info .social-link i {
            margin-right: 8px; /* Space between icon and text */
        }

        /* Prevent styling from affecting footer links */
        footer .social-link {
            color: inherit; /* Reset color for footer social links */
            font-size: 1.25rem; /* Smaller size for footer icons */
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include './includes/navbar.php'; ?>

    <!-- =======================
        HERO SECTION
    ======================= -->
    <section class="hero fade-in">
        <div class="container">
            <div class="card mx-auto text-center">
                <h1 class="fw-bold mb-3 text-success">Contact Us</h1>
                <p class="lead text-muted mb-1">Get in touch with us for inquiries or support.</p>
                <p class="mm-text">အကူအညီ သို့မဟုတ် မေးခွန်းများအတွက် ကျွန်ုပ်တို့ကို ဆက်သွယ်ပါ။</p>
            </div>
        </div>
    </section>

    <!-- =======================
        CONTACT FORM SECTION
    ======================= -->
    <section class="container fade-in">
        <h2 class="section-title">Contact Information <br><span class="mm-text">ဆက်သွယ်ရန် အချက်အလက်များ</span></h2>
        <div class="row">
            <!-- Left Column: Contact Info -->
            <div class="col-md-6 contact-info">
                <div class="card contact-card p-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><strong>Email:</strong> support@atobdelivery.com</li>
                        <li><strong>Phone:</strong> +123 456 7890</li>
                        <li><strong>Address:</strong> 123 Delivery Street, City, Country</li>
                    </ul>
                    <h5>Follow Us</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="social-link"><i class="bi bi-facebook"></i> Facebook</a></li>
                        <li><a href="#" class="social-link"><i class="bi bi-instagram"></i> Instagram</a></li>
                        <li><a href="#" class="social-link"><i class="bi bi-telegram"></i> Telegram</a></li>
                        <li><a href="#" class="social-link"><i class="bi bi-linkedin"></i> LinkedIn</a></li>
                        <li><a href="#" class="social-link"><i class="bi bi-youtube"></i> YouTube</a></li>
                    </ul>
                </div>
            </div>

            <!-- Right Column: Contact Form -->
            <div class="col-md-6">
                <?php if ($alert) { echo "<div class='alert alert-info'>$alert</div>"; } ?>
                <div class="card contact-card p-4">
                    <h5>Send Us a Message</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include './includes/footer.php'; ?>

    <!-- Fade-in animation -->
    <script>
        const fadeElements=document.querySelectorAll('.fade-in');
        const reveal=()=>{const trigger=window.innerHeight*0.9;
        fadeElements.forEach(e=>{if(e.getBoundingClientRect().top<trigger)e.classList.add('visible');});};
        window.addEventListener('scroll',reveal);window.addEventListener('load',reveal);
    </script>
</body>
</html>
