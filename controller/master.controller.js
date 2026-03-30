const masterService = require("../services/master.service");

// Plant Master
exports.setPlant = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterService.setPlant(flag, cond);

    res.json({
      success: true,
      message: "Select plant data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.savePlant = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.savePlant(data);

    res.status(200).json({
      success: true,
      message: "Save plant data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deletePlant = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.deletePlant(data);

    res.status(200).json({
      success: true,
      message: "Delete plant data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Category Master
exports.setCategory = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterService.setCategory(flag, cond);

    res.json({
      success: true,
      message: "Select category data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveCategory = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.saveCategory(data);

    res.status(200).json({
      success: true,
      message: "Save category data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteCategory = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.deleteCategory(data);

    res.status(200).json({
      success: true,
      message: "Delete category data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Unit Master
exports.setUnit = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterService.setUnit(flag, cond);

    res.json({
      success: true,
      message: "Select unit data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveUnit = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.saveUnit(data);

    res.status(200).json({
      success: true,
      message: "Save unit data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteUnit = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.deleteUnit(data);

    res.status(200).json({
      success: true,
      message: "Delete unit data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};


// Language Master
exports.setLanguage = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterService.setLanguage(flag, cond);

    res.json({
      success: true,
      message: "Select language data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveLanguage = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.saveLanguage(data);

    res.status(200).json({
      success: true,
      message: "Save language data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteLanguage = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.deleteLanguage(data);

    res.status(200).json({
      success: true,
      message: "Delete language data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

