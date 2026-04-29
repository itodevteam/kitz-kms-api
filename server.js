const express = require("express");
const bodyParser = require("body-parser");
require("dotenv").config();

const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: '*',
  },
});

app.use(cors());
app.use(express.json());
app.use(bodyParser.json());

app.use((req, res, next) => {
  req.io = io;
  next();
});

// API Routes
const AuthRoutes = require('./routes/auth.routes');
const MasterRoutes = require("./routes/master.routes");
const PORoutes = require("./routes/po.routes");
const QCROutes = require("./routes/qc.routes");
const VendorRoutes = require('./routes/vendor.routes');
const ReceiveRoutes = require('./routes/receive.routes');
const InvenRoutes = require('./routes/inven.routes');
const DashboardRoutes = require('./routes/dashboard.routes')(io);

// Middleware
const loggerMiddleware = require("./middleware/logger");
const errorLogger = require("./middleware/errorlogger");

//app.use(loggerMiddleware);
// API Routes
app.use('/api/auth', AuthRoutes);
app.use("/api/master", MasterRoutes);
app.use("/api/po", PORoutes);
app.use("/api/qc", QCROutes);
app.use("/api/vendor", VendorRoutes);
app.use("/api/receive", ReceiveRoutes);
app.use("/api/inven", InvenRoutes);
app.use("/api/dashboard", DashboardRoutes); 

//app.use(errorLogger);

app.use((err, req, res, next) => {
  console.error('🔥 ERROR:', err.stack);
  res.status(500).json({ message: err.message });
});

app.get('/', (req, res) => {
  res.send('Hello Node JS');
});

// API Port
const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`Server listening on port ${PORT}`);
});


