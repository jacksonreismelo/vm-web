const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
const PORT = 3000;

app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public')));

app.post('/login', (req, res) => {
    const { username, password } = req.body;
    
    // Exemplos de credenciais. Em um aplicativo real, você deve verificar em um banco de dados.
    const validUsername = 'user';
    const validPassword = 'password';
    
    if (username === validUsername && password === validPassword) {
        res.json({ success: true });
    } else {
        res.json({ success: false, message: 'Invalid username or password' });
    }
});

app.get('/dashboard', (req, res) => {
    res.send('<h1>Dashboard</h1><p>Welcome to the dashboard!</p>');
});

app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});