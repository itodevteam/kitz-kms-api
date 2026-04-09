const e = require("cors");
const vendorServices = require("../services/vendor.sercices");

exports.getVendorWaitConfirm = async (req, res) => {
  try {
    const { userNo } = req.body;

    const data = await vendorServices.getVendorWaitConfirm(userNo);

    if (!data || data.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No PO waiting confirm"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO Waiting Confirm",
      data: data
    });

  } catch (err) {
    console.error("API ERROR:", err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.poVendorConfirm = async (req, res) => {
  try {
    const data = req.body;

    const result = await vendorServices.poVendorConfirm(data);

    if (!result || result.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No PO waiting confirm"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO Waiting Confirm",
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
    const data = req.body;

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
