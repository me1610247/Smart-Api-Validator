<h2>Hi,</h2>
<p>Thanks for using our API. Please verify your email:</p>
<a href="{{ url('/api/verify-email?token=' . $token) }}">
    Click here to verify your email
</a>
