const mongoose = require("mongoose");

const DiscussionSchema = new mongoose.Schema(
    {
        title: { type: String, trim: true, maxlength: 80 },

        participants: {
            type: [Number],
            required: true,
            validate: {
                validator: (arr) =>
                    Array.isArray(arr) &&
                    arr.length >= 2 &&
                    arr.every((n) => Number.isInteger(n) && n > 0),
                message:
                    "participants must be an array of positive integers (min 2)",
            },
        },

        createdBy: { type: Number, required: true, min: 1 },

        lastMessageId: { type: Number, min: 1 },
    },
    { timestamps: true }
);

module.exports = mongoose.model("Discussion", DiscussionSchema);
