const e = require("cors");
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

exports.getPOMaster = async (req, res) => {
  try {
    const result = await poService.getPOMaster(); 

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

exports.getPODetail = async (req, res) => {
  try {
    const { purOrderNo } = req.body; // หรือ req.query

    const data = await poService.getPODetail(purOrderNo);

    if (!data || data.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No PO detail"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO detail",
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

exports.getPOWaitPrepare = async (req, res) => {
  try {
    const data = await poService.getPOWaitPrepare();

    if (!data || data.length === 0) {
      return res.status(404).json({
        message: "No PO waiting prepare"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO Waiting Prepare",
      data: data
    });

  } catch (err) {
    console.error("PO Wait Prepare Error:", err);
    res.status(500).json({
      message: err.message
    });
  }
};

exports.getPOWaitApprove = async (req, res) => {
  try {
    const { userNo } = req.body; // หรือ req.query

    const data = await poService.getPOWaitApprove(userNo);

    if (!data || data.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No PO waiting approve"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO Waiting Approve",
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

exports.getPOWaitApproveDetail = async (req, res) => {
  try {
    const { userNo } = req.body; // หรือ req.query

    const data = await poService.getPOWaitApproveDetail(userNo);

    if (!data || data.length === 0) {
      return res.status(404).json({
        success: false,
        message: "No PO waiting approve"
      });
    }

    res.status(200).json({
      success: true,
      message: "PO Waiting Approve",
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

exports.createPOApproval = async (req, res) => {
  try {
    const { data, createBy } = req.body;

    const result = await poService.createPOApproval(data, createBy);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.updatePOApproval = async (req, res) => {
  try {
    const { data, createBy } = req.body;

    const result = await poService.updatePOApproval(data, createBy);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.poApproval = async (req, res) => {
  try {
    const { payload } = req.body;

    if (!payload || !Array.isArray(payload) || payload.length === 0) {
      return res.status(400).json({
        success: false,
        message: "Invalid payload format"
      });
    }

    const result = await poService.poApproval(payload);

    res.json({
      success: result.success > 0,
      message: result.success > 0 ? "Approval successful" : "Approval failed",
      totalRow: result.total,
      data: result.failed
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
