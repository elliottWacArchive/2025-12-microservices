// app.js
const express = require("express");
const bodyParser = require("body-parser");
const mongoose = require("mongoose");

// routes requirements
const discussionRoutes = require("./Routes/discussion.route");

// init app
const app = express();

// connect MongoDB with mongoose
let dev_db_url =
    "mongodb://admin:admin123@mongodb:27017/express_db?authSource=admin";
let mongoDB = process.env.MONGODB_URI || dev_db_url;

mongoose.connect(mongoDB);
mongoose.Promise = global.Promise;

let db = mongoose.connection;
db.on("error", console.error.bind(console, "Connexion error on MongoDB: "));
db.once("open", () => {
    console.log("Connected to MongoDB successfully!");
});

// Utilisation de body parser
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));

// routes
app.use("/discussions", discussionRoutes);

let port = 5555;

app.listen(port, () => {
    console.log(`Server started! (http://localhost:${port})`);
});
