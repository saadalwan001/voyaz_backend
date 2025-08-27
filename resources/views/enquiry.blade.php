<!DOCTYPE html>
<html>
<head>
    <title>Trip Enquiry</title>
</head>

<body>
<h2>New Trip Enquiry</h2>

<p><strong>Trip Name:</strong> {{ $data['tripName'] }}</p>
<p><strong>Name:</strong> {{ $data['name'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Country:</strong> {{ $data['country'] }}</p>
<p><strong>Contact Number:</strong> {{ $data['contactNumber'] }}</p>
<p><strong>Number Of Adults:</strong> {{ $data['adults'] }}</p>
<p><strong>Number Of Children:</strong> {{ $data['children'] }}</p>
<p><strong>Subject:</strong> {{ $data['subject'] }}</p>
<p><strong>Message:</strong></p>
<p>{{ $data['message'] }}</p>
</body>
</html>
