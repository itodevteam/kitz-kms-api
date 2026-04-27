const e = require("cors");
const poServices = require("../services/po.services");

exports.uploadPO = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data) || data.length === 0) {
      return res.status(400).json({
        success: false,
        message: "Invalid data format"
      });
    }

    const result = await poServices.insertPO(data);

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

exports.getPurOrderMaster = async (req, res) => {
  try {
    const data = req.body.data[0];

    const result = await poServices.getPurOrderMaster(data);

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

exports.getPurOrderDetail = async (req, res) => {
  try {
    const data = req.body.data[0];

    const result = await poServices.getPurOrderDetail(data);

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

exports.getPOWaitPrepare = async (req, res) => {
  try {
    const data = await poServices.getPOWaitPrepare();

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

    const data = await poServices.getPOWaitApprove(userNo);

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

    const result = await poServices.createPOApproval(data, createBy);

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

    const result = await poServices.updatePOApproval(data, createBy);

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

exports.poApprovalConfirm = async (req, res) => {
  try {
    const { data } = req.body;

    const result = await poServices.poApprovalConfirm(data);

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

exports.deleteParation = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await poServices.deleteParation(data);

    res.status(200).json({
      success: true,
      message: "Delete PO paration data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.setPOApprove = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await poServices.setPOApprove(flag, cond);

    res.json({
      success: true,
      message: "Select approve detail data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.poSendingConfirm = async (req, res) => {
  try {
    const { data } = req.body;

    const result = await poServices.poSendingConfirm(data);

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

exports.poApprovalReject = async (req, res) => {
  try {
    const { data } = req.body;

    const result = await poServices.poApprovalReject(data);

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

exports.poApprovalRenew = async (req, res) => {
  try {
    const { data } = req.body;

    const result = await poServices.poApprovalRenew(data);

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
