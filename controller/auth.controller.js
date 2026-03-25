const authService = require('../services/auth.services');

// LOGIN
exports.login = async (req, res) => {
  try {
    const result = await authService.login(req.body);

    if (!result) {
      return res.status(401).json({ message: 'Invalid credentials' });
    }

    res.json(result);
  } catch (err) {
    console.error('Login error:', err);
    res.status(500).json({ message: err.message });
  }
};

// REFRESH TOKEN
exports.refresh = (req, res) => {
  try {
    const result = authService.refresh(req.headers);

    res.json(result);
  } catch (err) {
    res.status(403).json({ message: err.message });
  }
};