<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request {{ ucfirst($status) }}</title>
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
            background-color: {{ $status === 'approved' ? '#10b981' : ($status === 'rejected' ? '#ef4444' : '#f59e0b') }};
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
        .status-badge {
            display: inline-block;
            background-color: {{ $status === 'approved' ? '#10b981' : ($status === 'rejected' ? '#ef4444' : '#f59e0b') }};
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .leave-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
        <h1>Crocodic Project Manager</h1>
        <h2>Leave Request {{ ucfirst($status) }}</h2>
    </div>
    
    <div class="content">
        <p>Hello {{ $leave->submittedBy->name }},</p>
        
        <p>Your leave request has been <span class="status-badge">{{ $status }}</span></p>
        
        <div class="leave-details">
            <div class="detail-row">
                <span class="detail-label">Leave Category:</span>
                <span>{{ $leave->leave_category }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Start Date:</span>
                <span>{{ $leave->start_date->format('d M Y, H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date:</span>
                <span>{{ $leave->end_date->format('d M Y, H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration:</span>
                <span>{{ $leave->duration_in_days }} day(s)</span>
            </div>
            @if($leave->description)
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span>{{ $leave->description }}</span>
            </div>
            @endif
        </div>
        
        @if($status === 'approved')
            <p><strong>Your leave has been approved!</strong> Please make sure to:</p>
            <ul>
                <li>Complete any pending tasks before your leave starts</li>
                <li>Inform your team members about your absence</li>
                @if($leave->bring_laptop)
                    <li>Remember to bring your laptop as requested</li>
                @endif
                @if(!$leave->still_be_contacted)
                    <li>You have indicated that you prefer not to be contacted during leave</li>
                @endif
            </ul>
        @elseif($status === 'rejected')
            <p><strong>Unfortunately, your leave request has been rejected.</strong></p>
            @if($leave->admin_note)
                <p><strong>Admin Note:</strong> {{ $leave->admin_note }}</p>
            @endif
            <p>Please contact HR or your manager for more information.</p>
        @endif
        
        <p>If you have any questions, please don't hesitate to contact HR.</p>
        
        <p>Best regards,<br>Crocodic Project Manager Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>