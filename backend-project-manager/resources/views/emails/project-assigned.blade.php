<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Project Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .project-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4f46e5;
        }
        .project-title {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
        }
        .level-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .level-easy { background-color: #10b981; color: white; }
        .level-medium { background-color: #f59e0b; color: white; }
        .level-hard { background-color: #ef4444; color: white; }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Project Manager</h1>
        <h2>New Project Assignment</h2>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>You have been assigned to a new project. Here are the details:</p>
        
        <div class="project-card">
            <div class="project-title">{{ $project->project_name }}</div>
            
            <div class="detail-row">
                <span class="detail-label">Project Director:</span>
                <span>{{ $project->director->name ?? 'Not assigned' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Start Date:</span>
                <span>{{ $project->start_date->format('d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Deadline:</span>
                <span>{{ $project->deadline->format('d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Level:</span>
                <span>
                    <span class="level-badge level-{{ $project->level }}">
                        {{ ucfirst($project->level) }}
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span>{{ ucfirst($project->status) }}</span>
            </div>
        </div>
        
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Log in to your Project Manager account to view detailed project information</li>
            <li>Check your assigned tasks and deadlines</li>
            <li>Contact the project director if you have any questions</li>
            <li>Update your task progress regularly</li>
        </ul>
        
        <p>We're excited to have you on this project!</p>
        
        <p>Best regards,<br>Crocodic Project Manager Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>