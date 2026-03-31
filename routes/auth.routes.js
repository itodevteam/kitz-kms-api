const express = require('express');
const router = express.Router();
const authController = require('../controller/auth.controller');

// login
router.post('/login', authController.login);

// refresh token
router.post('/refresh', authController.refresh);

// Menu Master
router.post("/setmenu", authController.setMenu);
router.post("/savemenu", authController.saveMenu);
router.post("/deletemenu", authController.deleteMenu);

module.exports = router;