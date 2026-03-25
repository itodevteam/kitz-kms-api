// const express = require('express');
// const jwt = require('jsonwebtoken');
// const router = express.Router();
// const { sql, poolPromise } = require('../config/db');

// router.post('/login', async (req, res) => {
//   const { username, password } = req.body;
  
//   try {
//     const pool = await poolPromise;
//     const result = await pool
//     .request()
//     .input("username", sql.NVarChar, username)
//     .input("password", sql.NVarChar, password)
//     .query("EXEC zsp_GetLogin @username,@password");

//     const user = result.recordset[0];
//     if (!user) {
//       return res.status(401).json({ message: 'Invalid credentials' });
//     }

//     const accessToken = jwt.sign(
//       { uid: user.UserId },
//       process.env.JWT_SECRET,
//       { expiresIn: process.env.JWT_EXPIRE }
//     );

//     const refreshToken = jwt.sign(
//       { uid: user.UserId },
//       process.env.JWT_SECRET,
//       { expiresIn: process.env.REFRESH_EXPIRE }
//     );

//     res.json({ accessToken, refreshToken });
//   } catch (err) {
//     console.error('Login error:', err);
//     res.status(500).json({ message: 'Internal server error' });
//   }
// });


// router.post('/refresh', (req, res) => {
//   const authHeader = req.headers['authorization'];
//   const refreshToken = authHeader && authHeader.split(' ')[1];

//   if (!refreshToken) {
//     return res.status(401).json({ message: 'Refresh token required' });
//   }

//   try {
//     const decoded = jwt.verify(refreshToken, process.env.JWT_SECRET);
//     const newAccessToken = jwt.sign(
//       { uid: decoded.uid },
//       process.env.JWT_SECRET,
//       { expiresIn: process.env.JWT_EXPIRE }
//     );

//     res.json({ accessToken: newAccessToken });
//   } catch (err) {
//     res.status(403).json({ message: 'Invalid refresh token' });
//   }
// });

// module.exports = router;
