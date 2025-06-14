<div>
    <h1>Dashboard</h1>
    <div id="userInfo">
        <p>Loading user information...</p>
    </div>
    <button onclick="logout()">Logout</button>
    
<script>
    // Check if user is authenticated
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
    }

    // Fetch user information
    async function fetchUser() {
        try {
            const response = await fetch('/api/user', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const userData = await response.json();
                document.getElementById('userInfo').innerHTML = `
                    <h3>Welcome, ${userData.name}!</h3>
                    <p><strong>Email:</strong> ${userData.email}</p>
                    <p><strong>ID:</strong> ${userData.id}</p>
                `;
            } else {
                console.error('Failed to fetch user data');
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        } catch (error) {
            console.error('Error fetching user:', error);
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    }

    // Logout function
    async function logout() {
        try {
            const response = await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            // Clear token regardless of response
            localStorage.removeItem('token');
            window.location.href = '/login';
            
        } catch (error) {
            console.error('Logout error:', error);
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    }

    // Load user data when page loads
    fetchUser();
</script>
</div>
