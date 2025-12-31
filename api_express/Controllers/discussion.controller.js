const mongoose = require("mongoose");
const Discussion = require("../Models/discussion.model");

const ALLOWED_FIELDS = ["title", "participants", "createdBy", "lastMessageId"];

const isPositiveInt = (value) => Number.isInteger(value) && value > 0;

const pickAllowedFields = (data) => {
    const payload = {};
    ALLOWED_FIELDS.forEach((field) => {
        if (Object.prototype.hasOwnProperty.call(data, field)) {
            payload[field] = data[field];
        }
    });
    return payload;
};

const validateCreatePayload = (data) => {
    const errors = [];
    const payload = pickAllowedFields(data);

    if (!Array.isArray(payload.participants)) {
        errors.push("participants is required and must be an array");
    } else if (
        payload.participants.length < 2 ||
        !payload.participants.every(isPositiveInt)
    ) {
        errors.push("participants must contain at least 2 positive integers");
    }

    if (!isPositiveInt(payload.createdBy)) {
        errors.push("createdBy is required and must be a positive integer");
    }

    if (
        payload.title !== undefined &&
        (typeof payload.title !== "string" || payload.title.length > 80)
    ) {
        errors.push("title must be a string (max 80 chars)");
    }

    if (
        payload.lastMessageId !== undefined &&
        !isPositiveInt(payload.lastMessageId)
    ) {
        errors.push("lastMessageId must be a positive integer");
    }

    return { errors, payload };
};

const validateUpdatePayload = (data) => {
    const errors = [];
    const payload = pickAllowedFields(data);

    if (Object.keys(payload).length === 0) {
        errors.push("at least one updatable field is required");
        return { errors, payload };
    }

    if (payload.participants !== undefined) {
        if (!Array.isArray(payload.participants)) {
            errors.push("participants must be an array");
        } else if (
            payload.participants.length < 2 ||
            !payload.participants.every(isPositiveInt)
        ) {
            errors.push("participants must contain at least 2 positive integers");
        }
    }

    if (payload.createdBy !== undefined && !isPositiveInt(payload.createdBy)) {
        errors.push("createdBy must be a positive integer");
    }

    if (
        payload.title !== undefined &&
        (typeof payload.title !== "string" || payload.title.length > 80)
    ) {
        errors.push("title must be a string (max 80 chars)");
    }

    if (
        payload.lastMessageId !== undefined &&
        !isPositiveInt(payload.lastMessageId)
    ) {
        errors.push("lastMessageId must be a positive integer");
    }

    return { errors, payload };
};

const isValidObjectId = (id) => mongoose.Types.ObjectId.isValid(id);

const createDiscussion = async (req, res) => {
    const { errors, payload } = validateCreatePayload(req.body || {});
    if (errors.length) {
        return res.status(400).json({ errors });
    }

    try {
        const discussion = new Discussion(payload);
        const saved = await discussion.save();
        return res.status(201).json(saved);
    } catch (error) {
        return res.status(400).json({ error: error.message });
    }
};

const listDiscussions = async (req, res) => {
    try {
        const discussions = await Discussion.find().sort({ createdAt: -1 });
        return res.json(discussions);
    } catch (error) {
        return res.status(500).json({ error: error.message });
    }
};

const getDiscussionById = async (req, res) => {
    const { id } = req.params;
    if (!isValidObjectId(id)) {
        return res.status(400).json({ error: "Invalid discussion id" });
    }

    try {
        const discussion = await Discussion.findById(id);
        if (!discussion) {
            return res.status(404).json({ error: "Discussion not found" });
        }
        return res.json(discussion);
    } catch (error) {
        return res.status(500).json({ error: error.message });
    }
};

const updateDiscussion = async (req, res) => {
    const { id } = req.params;
    if (!isValidObjectId(id)) {
        return res.status(400).json({ error: "Invalid discussion id" });
    }

    const { errors, payload } = validateUpdatePayload(req.body || {});
    if (errors.length) {
        return res.status(400).json({ errors });
    }

    try {
        const updated = await Discussion.findByIdAndUpdate(id, payload, {
            new: true,
            runValidators: true,
        });
        if (!updated) {
            return res.status(404).json({ error: "Discussion not found" });
        }
        return res.json(updated);
    } catch (error) {
        return res.status(400).json({ error: error.message });
    }
};

const deleteDiscussion = async (req, res) => {
    const { id } = req.params;
    if (!isValidObjectId(id)) {
        return res.status(400).json({ error: "Invalid discussion id" });
    }

    try {
        const deleted = await Discussion.findByIdAndDelete(id);
        if (!deleted) {
            return res.status(404).json({ error: "Discussion not found" });
        }
        return res.json({ message: "Discussion deleted successfully" });
    } catch (error) {
        return res.status(500).json({ error: error.message });
    }
};

module.exports = {
    createDiscussion,
    listDiscussions,
    getDiscussionById,
    updateDiscussion,
    deleteDiscussion,
};
