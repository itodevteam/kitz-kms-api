const e = require("cors");
const vendorServices = require("../services/vendor.sercices");

exports.poVendorConfirm = async (req, res) => {
  try {
    const data = req.body.data[0];

    const result = await vendorServices.poVendorConfirm(data);

    if (!result || result.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No PO Vendor confirm"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO Vendor Confirmed",
      data: result
    });

  } catch (err) {
    console.error("API ERROR:", err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.getPurOrderMaster = async (req, res) => {
  try {
    const data = req.body.data[0];

    const result = await vendorServices.getPurOrderMaster(data);

    if (!result || result.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No Purchase Order found"
      });
    }

    res.status(200).json({
      success: true,
      message: "Purchase Order Master Data",
      data: result
    });

  } catch (err) {
    console.error("API ERROR:", err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
