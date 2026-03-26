const mplantService = require("../services/mplant.service");

exports.getPlant = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await mplantService.getPlant(flag, cond);

    res.json({
      success: true,
      message: "Plant Data",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};