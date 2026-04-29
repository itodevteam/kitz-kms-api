const masterServices = require("../services/master.services");

// Plant Master
exports.setPlant = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setPlant(flag, cond);

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

    await masterServices.savePlant(data);

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

    await masterServices.deletePlant(data);

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

    const data = await masterServices.setCategory(flag, cond);

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

    await masterServices.saveCategory(data);

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

    await masterServices.deleteCategory(data);

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

    const data = await masterServices.setUnit(flag, cond);

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

    await masterServices.saveUnit(data);

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

    await masterServices.deleteUnit(data);

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

    const data = await masterServices.setLanguage(flag, cond);

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

    await masterServices.saveLanguage(data);

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

    await masterServices.deleteLanguage(data);

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
// Employee Master
exports.setEmployee = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setEmployee(flag, cond);

    res.json({
      success: true,
      message: "Select employee data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveEmployee = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveEmployee(data);

    res.status(200).json({
      success: true,
      message: "Save employee data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteEmployee = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteEmployee(data);

    res.status(200).json({
      success: true,
      message: "Delete employee data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Currency Master
exports.setCurrency = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setCurrency(flag, cond);

    res.json({
      success: true,
      message: "Select currency data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveCurrency = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveCurrency(data);

    res.status(200).json({
      success: true,
      message: "Save currency data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteCurrency = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteCurrency(data);

    res.status(200).json({
      success: true,
      message: "Delete currency data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Department Master
exports.setDepartment = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setDepartment(flag, cond);

    res.json({
      success: true,
      message: "Select department data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveDepartment = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveDepartment(data);

    res.status(200).json({
      success: true,
      message: "Save department data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteDepartment = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteDepartment(data);

    res.status(200).json({
      success: true,
      message: "Delete department data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};


// Matrix Master
exports.setMatrix = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setMatrix(flag, cond);

    res.json({
      success: true,
      message: "Select matrix data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveMatrix = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveMatrix(data);

    res.status(200).json({
      success: true,
      message: "Save matrix data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteMatrix = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteMatrix(data);

    res.status(200).json({
      success: true,
      message: "Delete matrix data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Paymentterm Master
exports.setPaymentterm = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setPaymentterm(flag, cond);

    res.json({
      success: true,
      message: "Select paymentterm data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.savePaymentterm = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.savePaymentterm(data);

    res.status(200).json({
      success: true,
      message: "Save paymentterm data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deletePaymentterm = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deletePaymentterm(data);

    res.status(200).json({
      success: true,
      message: "Delete paymentterm data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Position Master
exports.setPosition = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setPosition(flag, cond);

    res.json({
      success: true,
      message: "Select position data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.savePosition = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.savePosition(data);

    res.status(200).json({
      success: true,
      message: "Save position data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deletePosition = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deletePosition(data);

    res.status(200).json({
      success: true,
      message: "Delete position data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Positionpattern Master
exports.setPositionpattern = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setPositionpattern(flag, cond);

    res.json({
      success: true,
      message: "Select positionpattern data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.savePositionpattern = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.savePositionpattern(data);

    res.status(200).json({
      success: true,
      message: "Save positionpattern data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deletePositionpattern = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deletePositionpattern(data);

    res.status(200).json({
      success: true,
      message: "Delete positionpattern data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Sex Master
exports.setSex = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setSex(flag, cond);

    res.json({
      success: true,
      message: "Select sex data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveSex = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveSex(data);

    res.status(200).json({
      success: true,
      message: "Save sex data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteSex = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteSex(data);

    res.status(200).json({
      success: true,
      message: "Delete sex data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Status Master
exports.setStatus = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setStatus(flag, cond);

    res.json({
      success: true,
      message: "Select status data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveStatus = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveStatus(data);

    res.status(200).json({
      success: true,
      message: "Save status data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteStatus = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteStatus(data);

    res.status(200).json({
      success: true,
      message: "Delete status data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Titlename Master
exports.setTitlename = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setTitlename(flag, cond);

    res.json({
      success: true,
      message: "Select title name data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveTitlename = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveTitlename(data);

    res.status(200).json({
      success: true,
      message: "Save title name data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteTitlename = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteTitlename(data);

    res.status(200).json({
      success: true,
      message: "Delete title name data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Vendor Master
exports.setVendor = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setVendor(flag, cond);

    res.json({
      success: true,
      message: "Select vendor data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveVendor = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveVendor(data);

    res.status(200).json({
      success: true,
      message: "Save vendor data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteVendor = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteVendor(data);

    res.status(200).json({
      success: true,
      message: "Delete vendor data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Vendor contact Master
exports.setVendorcontact = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setVendorcontact(flag, cond);

    res.json({
      success: true,
      message: "Select vendor contact data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveVendorcontact = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveVendorcontact(data);

    res.status(200).json({
      success: true,
      message: "Save vendor contact data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteVendorcontact = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteVendorcontact(data);

    res.status(200).json({
      success: true,
      message: "Delete vendor contact data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
// Item Master
exports.setItem = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterServices.setItem(flag, cond);

    res.json({
      success: true,
      message: "Select item data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.saveItem = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.saveItem(data);

    res.status(200).json({
      success: true,
      message: "Save item data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
exports.deleteItem = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterServices.deleteItem(data);

    res.status(200).json({
      success: true,
      message: "Delete item data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

// Keyword Master
exports.setKeyword = async (req, res) => {
  try {
    const { flag, cond, languageen } = req.body.data[0]; // ดึงค่าจาก body

    const result = await masterServices.setKeyword(flag, cond, languageen);

    res.json({
      success: true,
      message: result.message // คืน message จาก stored procedure โดยตรง
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

