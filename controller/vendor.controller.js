const e = require("cors");
const vendorServices = require("../services/vendor.sercices");

exports.poVendorConfirm = async (req, res) => {
  try {
    const { data } = req.body;

   const result = await vendorServices.poVendorConfirm(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.createDeliveryDetail = async (req, res) => {
  try {
    // const { data } = req.body;
    const data = req.body.data[0];

    const result = await vendorServices.createDeliveryDetail(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.updateDeliveryDetail = async (req, res) => {
  try {
    const { data } = req.body;

   const result = await vendorServices.updateDeliveryDetail(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};
