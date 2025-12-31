const express = require("express");
const discussionController = require("../Controllers/discussion.controller");

const router = express.Router();

// Create
router.post("/", discussionController.createDiscussion);

// Read (tout)
router.get("/", discussionController.listDiscussions);

// Read (unique)
router.get("/:id", discussionController.getDiscussionById);

// Update
router.put("/:id", discussionController.updateDiscussion);

// Delete
router.delete("/:id", discussionController.deleteDiscussion);

module.exports = router;
