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
// Menu Auth
exports.setMenu = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await authService.setMenu(flag, cond);

    res.json({
      success: true,
      message: "Select menu data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveMenu = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.saveMenu(data);

    res.status(200).json({
      success: true,
      message: "Save menu data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteMenu = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.deleteMenu(data);

    res.status(200).json({
      success: true,
      message: "Delete menu data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};