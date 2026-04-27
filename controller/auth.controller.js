const authService = require('../services/auth.services');

// LOGIN
exports.login = async (req, res) => {
  try {
    const { username, password } = req.body;
    
    const result = await authService.login({ 
      UserName: username, 
      Password: password 
    });

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

// Submenu Auth
exports.setSubmenu = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await authService.setSubmenu(flag, cond);

    res.json({
      success: true,
      message: "Select submenu data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveSubmenu = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.saveSubmenu(data);

    res.status(200).json({
      success: true,
      message: "Save submenu data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteSubmenu = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.deleteSubmenu(data);

    res.status(200).json({
      success: true,
      message: "Delete submenu data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Permission Auth
exports.setPermission = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await authService.setPermission(flag, cond);

    res.json({
      success: true,
      message: "Select permission data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.savePermission = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.savePermission(data);

    res.status(200).json({
      success: true,
      message: "Save permission data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.deletePermission = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.deletePermission(data);

    res.status(200).json({
      success: true,
      message: "Delete permission data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Account Auth
exports.setAccount = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await authService.setAccount(flag, cond);

    res.json({
      success: true,
      message: "Select account data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveAccount = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.saveAccount(data);

    res.status(200).json({
      success: true,
      message: "Save account data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.deleteAccount = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await authService.deleteAccount(data);

    res.status(200).json({
      success: true,
      message: "Delete account data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};