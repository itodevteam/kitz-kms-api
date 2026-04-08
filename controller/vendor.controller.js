const e = require("cors");
const vendorServices = require("../services/vendor.sercices");

exports.getVendorWaitConfirm = async (req, res) => {
  try {
    const userNo = req.params.userNo;
    const result = await vendorServices.getVendorWaitConfirm(userNo);
    res.json(result);
  } catch (error) {
    console.error("CONTROLLER ERROR:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};
