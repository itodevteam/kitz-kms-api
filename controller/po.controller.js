const poService = require("../services/po.services");

exports.uploadPO = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data) || data.length === 0) {
      return res.status(400).json({
        success: false,
        message: "Invalid data format"
      });
    }

    const result = await poService.insertPO(data);

    res.json({
      success: result.status.success === 1,
      message: result.status.message,
      totalRow: result.status.totalRow,
      data: result.data
    });

  } catch (error) {
    console.error("API ERROR:", error);

    res.status(500).json({
      success: false,
      message: "Internal Server Error",
      error: error.message
    });
  }
};