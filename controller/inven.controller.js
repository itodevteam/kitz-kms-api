const e = require("cors");
const vendorServices = require("../services/vendor.sercices");

exports.getReceiveData = async (req, res) => {
  try {
    const { data } = req.body;

   const result = await vendorServices.getReceiveData(data);

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


exports.getBacklogData = async (req, res) => {
  try {
    const { data } = req.body;

   const result = await vendorServices.getBacklogData(data);

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
