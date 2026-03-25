const masterService = require("../services/master.service");

// GET ALL ITEM
exports.getItems = async (req, res) => {
  try {
    const data = await masterService.getItems();

    res.status(200).json({
      success: true,
      message: "Item Data",
      data,
    });
  } catch (err) {
    console.error(err);
    res.status(500).send(err.message);
  }
};

// GET ITEM BY OWNER
exports.getItemByOwner = async (req, res) => {
  try {
    const { ownercode } = req.body;
    const data = await masterService.getItemByOwner(ownercode);

    res.status(200).json({
      success: true,
      message: "Item Data",
      data,
    });
  } catch (err) {
    res.status(500).send(err.message);
  }
};

// GET VENDOR
exports.getVendor = async (req, res) => {
  try {
    const data = await masterService.getVendor();

    res.status(200).json({
      success: true,
      message: "Vendor Data",
      data,
    });
  } catch (err) {
    res.status(500).send(err.message);
  }
};