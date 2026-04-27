<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Survey Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .rating-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .rating-option {
            display: flex;
            align-items: center;
        }
        
        .rating-option input[type="radio"] {
            margin-right: 5px;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .error {
            background: #fee;
            border: 1px solid #fcc;
            padding: 10px;
            border-radius: 5px;
            color: #c00;
            margin-bottom: 20px;
        }
        
        .success {
            background: #efe;
            border: 1px solid #cfc;
            padding: 10px;
            border-radius: 5px;
            color: #060;
            margin-bottom: 20px;
        }
        
        .link {
            text-align: center;
            margin-top: 20px;
        }
        
        .link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Event Survey Form</h1>
        
        <?php
        // Display success message
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo '<div class="success">✅ Thank you! Your survey has been submitted successfully.</div>';
        }
        
        // Display error message
        if (isset($_GET['error'])) {
            echo '<div class="error">❌ ' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>
        
        <form action="submit.php" method="POST" id="surveyForm">
            <!-- Name Field -->
            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" required 
                       placeholder="Enter your full name" maxlength="100">
            </div>
            
            <!-- Age Group Field -->
            <div class="form-group">
                <label for="age_group">Age Group <span class="required">*</span></label>
                <select id="age_group" name="age_group" required>
                    <option value="">-- Select Age Group --</option>
                    <option value="18-25">18-25</option>
                    <option value="26-35">26-35</option>
                    <option value="36-45">36-45</option>
                    <option value="46+">46+</option>
                </select>
            </div>
            
            <!-- Satisfaction Rating -->
            <div class="form-group">
                <label>Satisfaction Rating <span class="required">*</span></label>
                <div class="rating-group">
                    <div class="rating-option">
                        <input type="radio" id="rating1" name="rating" value="1" required>
                        <label for="rating1">1 - Poor</label>
                    </div>
                    <div class="rating-option">
                        <input type="radio" id="rating2" name="rating" value="2">
                        <label for="rating2">2 - Fair</label>
                    </div>
                    <div class="rating-option">
                        <input type="radio" id="rating3" name="rating" value="3">
                        <label for="rating3">3 - Good</label>
                    </div>
                    <div class="rating-option">
                        <input type="radio" id="rating4" name="rating" value="4">
                        <label for="rating4">4 - Very Good</label>
                    </div>
                    <div class="rating-option">
                        <input type="radio" id="rating5" name="rating" value="5">
                        <label for="rating5">5 - Excellent</label>
                    </div>
                </div>
            </div>
            
            <!-- Feedback Text -->
            <div class="form-group">
                <label for="feedback">Additional Feedback (Optional)</label>
                <textarea id="feedback" name="feedback" 
                          placeholder="Share your thoughts about the event..."></textarea>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn">Submit Survey</button>
        </form>
        
        <div class="link">
            <a href="report.php">📊 View Survey Results</a>
        </div>
    </div>
    
    <script>
        // Client-side validation
        document.getElementById('surveyForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const ageGroup = document.getElementById('age_group').value;
            const rating = document.querySelector('input[name="rating"]:checked');
            
            if (name === '') {
                alert('Please enter your name');
                e.preventDefault();
                return false;
            }
            
            if (ageGroup === '') {
                alert('Please select your age group');
                e.preventDefault();
                return false;
            }
            
            if (!rating) {
                alert('Please select a satisfaction rating');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>