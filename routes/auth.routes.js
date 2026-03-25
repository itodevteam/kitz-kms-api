const express = require('express');
const router = express.Router();
const authController = require('../controller/auth.controller');

// login
router.post('/login', authController.login);

// refresh token
router.post('/refresh', authController.refresh);

module.exports = router;