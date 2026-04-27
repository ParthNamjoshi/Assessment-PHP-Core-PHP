<?php
require_once 'config.php';

// Get database connection
$conn = getDBConnection();

// 1. Get total number of responses
$total_query = "SELECT COUNT(*) as total FROM surveys";
$total_result = $conn->query($total_query);
$total_responses = $total_result->fetch_assoc()['total'];

// 2. Get average rating
$avg_query = "SELECT AVG(rating) as avg_rating FROM surveys";
$avg_result = $conn->query($avg_query);
$avg_rating = $avg_result->fetch_assoc()['avg_rating'];

// 3. Get age group breakdown
$age_query = "SELECT age_group, COUNT(*) as count 
              FROM surveys 
              GROUP BY age_group 
              ORDER BY age_group";
$age_result = $conn->query($age_query);

// Store age group data in array
$age_data = [];
while ($row = $age_result->fetch_assoc()) {
    $percentage = ($total_responses > 0) ? ($row['count'] / $total_responses) * 100 : 0;
    $age_data[] = [
        'group' => $row['age_group'],
        'count' => $row['count'],
        'percentage' => round($percentage, 2)
    ];
}

// 4. Get rating distribution
$rating_query = "SELECT rating, COUNT(*) as count 
                 FROM surveys 
                 GROUP BY rating 
                 ORDER BY rating";
$rating_result = $conn->query($rating_query);

// Store rating data in array
$rating_data = [];
for ($i = 1; $i <= 5; $i++) {
    $rating_data[$i] = 0;
}

while ($row = $rating_result->fetch_assoc()) {
    $rating_data[$row['rating']] = $row['count'];
}

// 5. Get recent feedback (last 5)
$feedback_query = "SELECT name, rating, feedback, created_at 
                   FROM surveys 
                   WHERE feedback IS NOT NULL AND feedback != '' 
                   ORDER BY created_at DESC 
                   LIMIT 5";
$feedback_result = $conn->query($feedback_query);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .bar {
            background: #667eea;
            height: 30px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: white;
            font-weight: bold;
        }
        
        .rating-bars {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .rating-bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .rating-label {
            width: 120px;
            font-weight: bold;
        }
        
        .bar-container {
            flex: 1;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .feedback-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .feedback-card .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #666;
            font-size: 14px;
        }
        
        .feedback-card .name {
            font-weight: bold;
            color: #333;
        }
        
        .feedback-card .rating {
            color: #ff9800;
        }
        
        .feedback-card .text {
            color: #555;
            line-height: 1.6;
        }
        
        .back-link {
            text-align: center;
            margin-top: 40px;
        }
        
        .back-link a {
            display: inline-block;
            padding: 15px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .back-link a:hover {
            background: #764ba2;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Survey Results Dashboard</h1>
        <p class="subtitle">Real-time analytics and insights</p>
        
        <?php if ($total_responses > 0): ?>
        
        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>TOTAL RESPONSES</h3>
                <div class="number"><?php echo $total_responses; ?></div>
            </div>
            <div class="stat-card">
                <h3>AVERAGE RATING</h3>
                <div class="number"><?php echo number_format($avg_rating, 2); ?>/5</div>
            </div>
            <div class="stat-card">
                <h3>SATISFACTION</h3>
                <div class="number"><?php echo round(($avg_rating / 5) * 100); ?>%</div>
            </div>
        </div>
        
        <!-- Age Group Breakdown -->
        <div class="section">
            <h2>📈 Age Group Distribution</h2>
            <table>
                <thead>
                    <tr>
                        <th>Age Group</th>
                        <th>Count</th>
                        <th>Percentage</th>
                        <th>Visual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($age_data as $age): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($age['group']); ?></strong></td>
                        <td><?php echo $age['count']; ?></td>
                        <td><?php echo $age['percentage']; ?>%</td>
                        <td>
                            <div style="width: <?php echo $age['percentage']; ?>%; background: #667eea; height: 10px; border-radius: 5px;"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Rating Distribution -->
        <div class="section">
            <h2>⭐ Rating Distribution</h2>
            <div class="rating-bars">
                <?php 
                $max_count = max($rating_data);
                $rating_labels = [
                    1 => '1 Star - Poor',
                    2 => '2 Stars - Fair',
                    3 => '3 Stars - Good',
                    4 => '4 Stars - Very Good',
                    5 => '5 Stars - Excellent'
                ];
                
                foreach ($rating_data as $rating => $count): 
                    $percentage = ($max_count > 0) ? ($count / $max_count) * 100 : 0;
                ?>
                <div class="rating-bar-row">
                    <div class="rating-label"><?php echo $rating_labels[$rating]; ?></div>
                    <div class="bar-container">
                        <div class="bar" style="width: <?php echo $percentage; ?>%;">
                            <?php echo $count; ?> responses
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Recent Feedback -->
        <div class="section">
            <h2>💬 Recent Feedback</h2>
            <?php if ($feedback_result->num_rows > 0): ?>
                <?php while ($feedback = $feedback_result->fetch_assoc()): ?>
                <div class="feedback-card">
                    <div class="header">
                        <span class="name"><?php echo htmlspecialchars($feedback['name']); ?></span>
                        <span>
                            <span class="rating">⭐ <?php echo $feedback['rating']; ?>/5</span>
                            <span style="margin-left: 10px; color: #999;">
                                <?php echo date('M d, Y', strtotime($feedback['created_at'])); ?>
                            </span>
                        </span>
                    </div>
                    <div class="text">
                        "<?php echo htmlspecialchars($feedback['feedback']); ?>"
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No feedback submitted yet.</p>
            <?php endif; ?>
        </div>
        
        <?php else: ?>
        <div class="no-data">
            <h2>No survey responses yet</h2>
            <p>Be the first to submit a survey!</p>
        </div>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="index.php">← Back to Survey Form</a>
        </div>
    </div>
</body>
</html>
